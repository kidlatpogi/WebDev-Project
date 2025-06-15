document.addEventListener('DOMContentLoaded', () => {
  const startTimeSelect = document.getElementById('starttime');
  const endTimeSelect = document.getElementById('endtime');
  const roomSelect = document.getElementById('room');
  const dateInput = document.getElementById('date');

  const allTimes = generateTimeSlots();

  // Generate all possible 30-minute time slots from 7:00 AM to 9:00 PM
  function generateTimeSlots() {
    const times = [];
    for (let hour = 7; hour <= 21; hour++) {
      ['00', '30'].forEach(minute => {
        let hour12 = hour % 12 === 0 ? 12 : hour % 12;
        let ampm = hour < 12 ? 'AM' : 'PM';
        const value = `${hour.toString().padStart(2, '0')}:${minute}`;
        const label = `${hour12}:${minute} ${ampm}`;
        times.push({ value, label });
      });
    }
    return times;
  }

  // Populate a given select element with time options or a default message if no options available
  function populateSelect(selectElement, times, defaultText) {
    selectElement.innerHTML = '';
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = defaultText || 'Select time';
    selectElement.appendChild(defaultOption);

    if (times.length === 0) {
      const option = document.createElement('option');
      option.value = '';
      option.textContent = "Can't Reserve a Room";
      option.disabled = true;
      selectElement.appendChild(option);
      selectElement.disabled = true;
      return;
    }

    selectElement.disabled = false;

    times.forEach(time => {
      const option = document.createElement('option');
      option.value = time.value;
      option.textContent = time.label;
      selectElement.appendChild(option);
    });
  }

  // Fetch occupied time intervals for the selected room and date from get_vacant_time.php
  async function fetchOccupiedTimes() {
    const roomId = roomSelect.value;
    const date = dateInput.value;
    if (!roomId || !date) return [];

    try {
      const response = await fetch(`get_vacant_time.php?room_id=${roomId}&date=${date}`);
      const data = await response.json();
      if (data.error) {
        console.error(data.error);
        return [];
      }
      return data;
    } catch (error) {
      console.error('Fetch error:', error);
      return [];
    }
  }

  // Calculate available time windows by subtracting occupied intervals from the full day 7 ng umaga hanggang 11 pm
  function getAvailableTimeWindows(occupiedIntervals) {
    const dayStart = "07:00";
    const dayEnd = "21:00";

    if (!occupiedIntervals || occupiedIntervals.length === 0) {
      return [{ start: dayStart, end: dayEnd }];
    }

    // Normalize and sort occupied intervals by start time
    const sorted = occupiedIntervals
      .map(i => ({
        start: i.start.slice(0,5),
        end: i.end.slice(0,5)
      }))
      .sort((a, b) => a.start.localeCompare(b.start));

    // Merge overlapping or adjacent intervals into continuous occupied blocks
    const merged = [sorted[0]];
    for (let i = 1; i < sorted.length; i++) {
      const last = merged[merged.length - 1];
      const current = sorted[i];

      if (current.start <= last.end) {
        if (current.end > last.end) {
          last.end = current.end;
        }
      } else {
        merged.push(current);
      }
    }

    // Find gaps (available windows) between merged occupied intervals
    const available = [];
    let current = dayStart;

    for (let interval of merged) {
      if (interval.start > current) {
        available.push({ start: current, end: interval.start });
      }
      current = interval.end > current ? interval.end : current;
    }

    if (current < dayEnd) {
      available.push({ start: current, end: dayEnd });
    }

    return available;
  }

  // Generate all possible start times in 30-minute increments from the available time windows
  function getStartTimesFromGaps(timeWindows) {
    const startTimes = [];

    for (let window of timeWindows) {
      const [startH, startM] = window.start.split(':').map(Number);
      const [endH, endM] = window.end.split(':').map(Number);

      let current = new Date();
      current.setHours(startH, startM, 0, 0);

      const end = new Date();
      end.setHours(endH, endM, 0, 0);

      while ((end - current) >= 30 * 60 * 1000) {
        const hours = current.getHours().toString().padStart(2, '0');
        const minutes = current.getMinutes().toString().padStart(2, '0');
        const value = `${hours}:${minutes}`;

        let hour12 = current.getHours() % 12 || 12;
        let ampm = current.getHours() < 12 ? 'AM' : 'PM';
        const label = `${hour12}:${minutes} ${ampm}`;

        startTimes.push({ value, label });

        current.setMinutes(current.getMinutes() + 30);
      }
    }

    return startTimes;
  }

  // Find the available time window that includes the given start time
  function getMatchingWindow(startTime, timeWindows) {
    return timeWindows.find(window => startTime >= window.start && startTime < window.end);
  }

  // Generate all possible end times (30-minute increments) starting from selected start time up to the end of its available window
  // Parang mas maganda kung 1 hour increments duhhhh  
  function getEndTimesForStart(startTime, window) {
    const endTimes = [];

    const [startH, startM] = startTime.split(':').map(Number);
    const [endH, endM] = window.end.split(':').map(Number);

    let current = new Date();
    current.setHours(startH, startM + 30, 0, 0);

    const end = new Date();
    end.setHours(endH, endM, 0, 0);

    while (current <= end) {
      const hours = current.getHours().toString().padStart(2, '0');
      const minutes = current.getMinutes().toString().padStart(2, '0');
      const value = `${hours}:${minutes}`;

      let hour12 = current.getHours() % 12 || 12;
      let ampm = current.getHours() < 12 ? 'AM' : 'PM';
      const label = `${hour12}:${minutes} ${ampm}`;

      if (value <= window.end) {
        endTimes.push({ value, label });
      }

      current.setMinutes(current.getMinutes() + 30);
    }

    return endTimes;
  }

  // Fetch occupied intervals, compute available start times, and update start and end time selects accordingly
  async function updateTimeOptions() {
    const occupiedIntervals = await fetchOccupiedTimes();
    const timeWindows = getAvailableTimeWindows(occupiedIntervals);
    const availableStartTimes = getStartTimesFromGaps(timeWindows);

    populateSelect(startTimeSelect, availableStartTimes, 'Select start time');
    populateSelect(endTimeSelect, [], 'Select end time');
    endTimeSelect.disabled = true;

    startTimeSelect.value = '';
    endTimeSelect.value = '';
  }

  // When start time changes, update end time options to fit within the matching available window
  startTimeSelect.addEventListener('change', async () => {
    const selectedStart = startTimeSelect.value;
    if (!selectedStart) {
      populateSelect(endTimeSelect, [], 'Select end time');
      endTimeSelect.disabled = true;
      return;
    }

    const occupiedIntervals = await fetchOccupiedTimes();
    const timeWindows = getAvailableTimeWindows(occupiedIntervals);
    const matchingWindow = getMatchingWindow(selectedStart, timeWindows);

    if (!matchingWindow) {
      populateSelect(endTimeSelect, [], 'Select end time');
      endTimeSelect.disabled = true;
      return;
    }

    const endTimes = getEndTimesForStart(selectedStart, matchingWindow);
    populateSelect(endTimeSelect, endTimes, 'Select end time');
    endTimeSelect.disabled = false;
  });

  // Initial setup: disable selects and add default options
  populateSelect(startTimeSelect, [], 'Select start time');
  populateSelect(endTimeSelect, [], 'Select end time');
  endTimeSelect.disabled = true;

  // Update available times whenever room or date selection changes
  roomSelect.addEventListener('change', updateTimeOptions);
  dateInput.addEventListener('change', updateTimeOptions);
});
