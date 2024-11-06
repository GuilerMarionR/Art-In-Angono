// LOGIN
document.addEventListener('DOMContentLoaded', function() {
    // Select the form element
    const loginForm = document.getElementById('loginForm');
    const displayErrorMessage = document.getElementById('error-message'); // Ensure this is fetched after DOM loads
    
    // Check if the form and error-message elements exist before adding listeners
    if (loginForm && displayErrorMessage) {
        // Add submit event listener
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form from reloading
            
            // Get the input values
            const inputEmail = document.getElementById('email').value.trim();
            const inputPassword = document.getElementById('password').value.trim();

            // Debugging: Check if event is firing and values are being captured
            console.log("Form Submitted");
            console.log("Email entered: ", inputEmail);
            console.log("Password entered: ", inputPassword);

            // Define correct login credentials
            const adminEmail = 'admin@example.com';
            const adminPassword = 'admin';

            // Clear any previous error message
            displayErrorMessage.textContent = '';

            // Check if entered credentials match admin credentials
            if (inputEmail === adminEmail && inputPassword === adminPassword) {
                console.log("Login successful! Redirecting...");
                // Redirect to admin-home.html if login is successful
                window.location.href = 'admin-home.php';
            } else {
                // Display error message if login details are incorrect
                displayErrorMessage.textContent = 'Incorrect email or password. Please try again.';
                console.log("Login failed: Incorrect email or password.");
            }
        });
    } else {
        console.log("Form or error message element not found");
    }
});
