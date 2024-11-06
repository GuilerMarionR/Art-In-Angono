document.addEventListener('DOMContentLoaded', function () {
    // Function to update the image and overlay content
    function updateImageAndOverlay(imageSrc, overlayContent) {
        const mainImage = document.getElementById('main-image');
        const overlay = document.getElementById('overlay');

        // Update the image source
        mainImage.src = imageSrc; // Set the dynamic image source

        // Update the overlay content
        overlay.innerHTML = overlayContent; // Insert dynamic content into the overlay
    }

    // Add event listener for the "Add Image" button for main image
    document.getElementById('main-add-image-button').addEventListener('click', function () {
        document.getElementById('file-input').click(); // Simulate file input click
    });

    // Add event listener for the file input change (main image)
    document.getElementById('file-input').addEventListener('change', function (event) {
        const file = event.target.files[0]; // Get the selected file
        if (file) {
            const reader = new FileReader(); // Create a FileReader to read the file
            reader.onload = function (e) {
                const mainImage = document.getElementById('main-image');
                mainImage.src = e.target.result; // Set the image source to the selected file
                mainImage.style.display = 'block'; // Ensure the main image is displayed
            };
            reader.readAsDataURL(file); // Read the file as a data URL
            document.getElementById('main-add-image-button').style.display = 'none'; // Hide the button
        }
    });

    // Save button functionality
    document.getElementById('save-button').addEventListener('click', function () {
        // Get the values from the input fields
        const museumName = document.getElementById('museum-name').value;
        const museumHistory = document.getElementById('museum-description').value;

        // Set the displayed information
        document.getElementById('display-name').innerText = museumName;
        document.getElementById('display-history').innerText = "Museum History"; // Static label
        document.getElementById('display-description').innerText = museumHistory;

        // Hide the input fields and labels
        document.querySelectorAll('.input-container, .button-container').forEach(element => {
            element.style.display = 'none';
        });

        // Show the displayed information section
        const displayInfo = document.getElementById('display-info');
        displayInfo.style.display = 'block';
    });

    // Cancel button functionality
    document.getElementById('cancel-button').addEventListener('click', function () {
        // Clear the input fields
        document.getElementById('museum-name').value = '';
        document.getElementById('museum-description').value = '';

        // Hide the input fields and labels
        document.querySelectorAll('.input-container, .button-container').forEach(element => {
            element.style.display = 'none';
        });

        // Show the displayed information section
        const displayInfo = document.getElementById('display-info');
        displayInfo.style.display = 'none';
    });

    // Add functionality for featured collection images
    const addFeaturedImageButton = document.getElementById('featured-add-image-button');
    const imageInput = document.getElementById('image-input');

    // Add click event to the "Add Image" button for featured images
    addFeaturedImageButton.addEventListener('click', function () {
        imageInput.click(); // Trigger file input click for featured images
    });

    // Add change event to the file input for featured images
    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0]; // Get the uploaded file
        if (file) {
            const reader = new FileReader(); // Create a FileReader to read the file
            reader.onload = function (e) {
                const imageScroll = document.getElementById('image-scroll');
                const newImageContainer = document.createElement('div'); // Create a container for the new image and description

                const newImage = document.createElement('img'); // Create new image element
                newImage.src = e.target.result; // Set source to uploaded image
                newImage.alt = "Collection Image"; // Set alt text
                newImage.style.width = '600px'; // Set the desired width
                newImage.style.height = '340px'; // Set the desired height

                const descriptionText = document.getElementById('image-description').value; // Get the description
                const description = document.createElement('p'); // Create a paragraph for description
                description.textContent = descriptionText; // Set the description text

                newImageContainer.appendChild(newImage); // Add new image to the container
                newImageContainer.appendChild(description); // Add description to the container
                imageScroll.appendChild(newImageContainer); // Add the new image container to the scroll area

                // Update the display section with the new image and description
                document.getElementById('display-image').src = e.target.result; // Set the displayed image
                document.getElementById('display-description').innerText = descriptionText; // Set the displayed description
                document.getElementById('display-info').style.display = 'block'; // Show the display info section

                // Clear the description textarea after adding the image
                document.getElementById('image-description').value = '';
            };
            reader.readAsDataURL(file); // Read the file as a data URL
        }
    });
});
