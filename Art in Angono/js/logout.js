// Get elements
const logoutBox = document.getElementById('logout-box');
const overlay = document.getElementById('overlay');
const btnYes = document.getElementById('btn-yes');
const btnCancel = document.getElementById('btn-cancel');

// Show the logout confirmation box when the page loads
window.onload = function() {
    // Show overlay and logout box
    overlay.style.display = 'block';
    logoutBox.style.display = 'flex';

    // Use requestAnimationFrame for smooth transition
    requestAnimationFrame(() => {
        overlay.style.opacity = '1'; // Fade in the overlay
        logoutBox.style.opacity = '1'; // Fade in the logout box
    });
};

// Redirect to guest-home.html if Yes is clicked
btnYes.addEventListener('click', function() {
    window.location.href = 'guest-home.php';
});

// Close the confirmation box if Cancel is clicked
btnCancel.addEventListener('click', function() {
    // Hide the confirmation box and overlay with a fade-out effect
    overlay.style.opacity = '0'; // Fade out the overlay
    logoutBox.style.opacity = '0'; // Fade out the logout box

    // Wait for the transition to finish before hiding elements
    setTimeout(() => {
        logoutBox.style.display = 'none';
        overlay.style.display = 'none';
    }, 300); // Match the duration of the CSS transition
});
