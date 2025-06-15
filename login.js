document.addEventListener("DOMContentLoaded", function () {
    // Set a demo account if none exists
    if (!localStorage.getItem('user-email')) {
        localStorage.setItem('user-email', 'demo@example.com');
        localStorage.setItem('user-password', 'demo1234');
    }

    // Signup logic
    const signupForm = document.querySelector('.signup-form form');
    if (signupForm) {
        signupForm.onsubmit = function (e) {
            e.preventDefault();

            const inputs = signupForm.querySelectorAll('input');
            const fname = inputs[0].value.trim();
            const lname = inputs[1].value.trim();
            const email = inputs[2].value.trim();
            const password = inputs[3].value;
            const confirm = inputs[4].value;

            if (!fname || !lname || !email || !password || !confirm) {
                alert('Please fill in all fields.');
                return;
            }

            if (password !== confirm) {
                alert('Passwords do not match.');
                return;
            }

            // Save user credentials (demo only)
            localStorage.setItem('user-email', email);
            localStorage.setItem('user-password', password);
            alert('Sign up successful! You can now log in.');

            // Flip back to login form
            document.getElementById('flip').checked = false;
        };
    }

    // Login logic
const loginForm = document.querySelector('.login-form form');
if (loginForm) {
    loginForm.onsubmit = function (e) {
        e.preventDefault();

        const inputs = loginForm.querySelectorAll('input');
        const email = inputs[0].value.trim();
        const password = inputs[1].value;

        const storedEmail = localStorage.getItem('user-email');
        const storedPassword = localStorage.getItem('user-password');

        if (email === storedEmail && password === storedPassword) {
            alert('Login successful! Redirecting...');
            setTimeout(() => {
                window.location.href = "nav-rooms.html"; // Redirect to rooms page
            }, 500); // short delay to let user see the alert
        } else {
            alert('Invalid email or password.');
        }
    };
}

});
