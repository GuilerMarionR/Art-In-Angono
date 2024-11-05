const imageSection = document.getElementById('image-section');
const imageUpload = document.getElementById('image-upload');
const textSection = document.getElementById('text-section');

// Trigger file upload when clicking the image section
imageSection.addEventListener('click', () => {
    imageUpload.click();
});

// Stop click propagation when clicking inside the text section
textSection.addEventListener('click', (event) => {
    event.stopPropagation();
});

// Update the background image in real-time
imageUpload.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            imageSection.style.backgroundImage = `url(${e.target.result})`;
        };
        reader.readAsDataURL(file);
    }
});

// Save event data and redirect to main page
document.getElementById('saveEventBtn').addEventListener('click', function () {
    const title = document.getElementById('editable-title').innerText.trim();
    const details = document.getElementById('editable-details').innerText.trim();
    const description = document.getElementById('editable-description').innerText.trim();
    const image = imageSection.style.backgroundImage.slice(5, -2); // Get image URL without "url(" and ")"

    // Split details to capture date and museum name
    const detailsArray = details.split('|');
    const date = detailsArray[0] ? detailsArray[0].trim() : '';
    const museumName = detailsArray[1] ? detailsArray[1].trim() : '';

    // Basic validation
    if (!title || !date || !museumName || !description || !image) {
        alert('Please fill in all fields and upload an image.');
        return;
    }

    // Save to localStorage
    let events = JSON.parse(localStorage.getItem('events')) || [];
    events.push({ title, date, museumName, description, image });
    localStorage.setItem('events', JSON.stringify(events));

    // Redirect back to the main events page
    window.location.href = 'admin-my-news.php'; // Adjust this to your main events page path
});

// Close button functionality
document.getElementById('closeEventBtn').addEventListener('click', function() {
    window.location.href = 'admin-my-news.php'; // Redirect back to the main events page
});
