document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('artworks');
    const cancelButton = document.querySelector('.btn.cancel');
    const galleryContainer = document.querySelector('.gallery-museum');
    const deleteButton = document.getElementById('delete-artworks');

    // Function to create artwork card and append to gallery
    function addArtworkCard(artwork) {
        const artworkCard = document.createElement('div');
        artworkCard.classList.add('artwork');

        // Create a unique ID for the artwork (slug)
        const artworkId = artwork.title.replace(/\s+/g, '-').toLowerCase();

        // Create the HTML for the card with limited details initially visible
        artworkCard.innerHTML = `
            <input type="checkbox" class="artwork-checkbox" id="${artworkId}" name="artworks" value="${artwork.title}" style="display: none;">
            <a href="javascript:void(0);" class="artwork-details-link" data-id="${artworkId}">
                <img src="${artwork.image}" alt="${artwork.title}">
            </a>
            <h3>${artwork.title}</h3>
            <p><strong>Gallery:</strong> ${artwork.gallery}</p>
            <p><strong>Artist:</strong> ${artwork.artist}</p>
            <div class="artwork-details" style="display: none;">
                <p><strong>Description:</strong> ${artwork.description}</p>
                <p><strong>Medium:</strong> ${artwork.medium}</p>
                <p><strong>Dimensions:</strong> ${artwork.dimensions}</p>
                <p><strong>Email:</strong> ${artwork.email}</p>
                <p><strong>Website:</strong> <a href="${artwork.link}" target="_blank">${artwork.link}</a></p>
                <p><strong>Contact Number:</strong> ${artwork.contact}</p>
            </div>
        `;

        // Append the card to the gallery
        if (galleryContainer) {
            galleryContainer.appendChild(artworkCard);
        }

        // Add click event to redirect to a different page when artwork is clicked
        const detailsLink = artworkCard.querySelector('.artwork-details-link');
        detailsLink.addEventListener('click', function () {
            window.location.href = `admin-art-preview.php?id=${artworkId}`; // Change to your details page URL
        });
    }

    if (galleryContainer) {
        console.log("Gallery container found:", galleryContainer);

        // Load existing artworks from local storage
        function loadArtworks() {
            const artworks = JSON.parse(localStorage.getItem('artworks')) || [];
            artworks.forEach(artwork => addArtworkCard(artwork));
        }

        // Load artworks on page load
        loadArtworks();

        // Flag to track delete mode
        let deleteMode = false;

        // Function to toggle delete mode
        function toggleDeleteMode() {
            deleteMode = !deleteMode;

            // Show or hide the checkboxes depending on delete mode
            document.querySelectorAll('.artwork-checkbox').forEach(checkbox => {
                checkbox.style.display = deleteMode ? 'inline-block' : 'none';
            });

            // Change delete button text
            deleteButton.textContent = deleteMode ? 'Confirm Delete' : 'Delete Artwork';
        }

        // Handle delete button click
        deleteButton.addEventListener('click', function () {
            if (deleteMode) {
                // If in delete mode, allow user to select artworks to delete
                const checkboxes = document.querySelectorAll('.artwork-checkbox:checked');
                const selectedArtworks = Array.from(checkboxes).map(checkbox => checkbox.value);

                if (selectedArtworks.length === 0) {
                    alert('Please select artworks to delete.');
                    return;
                }

                // Retrieve existing artworks
                const storedArtworks = JSON.parse(localStorage.getItem('artworks')) || [];
                const updatedArtworks = storedArtworks.filter(artwork => !selectedArtworks.includes(artwork.title));

                // Save the updated list back to local storage
                localStorage.setItem('artworks', JSON.stringify(updatedArtworks));

                // Reload the gallery
                galleryContainer.innerHTML = '';  // Clear existing artworks
                loadArtworks(); // Reload artworks
            } else {
                toggleDeleteMode();  // Toggle delete mode
            }
        });
    } else {
        console.log("Gallery container not found. Skipping gallery-related code.");
    }

    // Handle form submission to add new artwork
    if (form) {
        console.log("Form found:", form);

        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            const title = form.title.value;
            const gallery = form.gallery.value;
            const artist = form.artist.value;
            const image = form.image.files[0];
            const description = form.description.value;
            const medium = form.medium.value;
            const dimensions = form.dimensions.value;
            const email = form.email.value;
            const link = form.link.value;
            const contact = form.contact.value;

            const reader = new FileReader();
            reader.onload = function (e) {
                const artwork = {
                    title,
                    gallery,
                    artist,
                    image: e.target.result, // Store the image data URL
                    description,
                    medium,
                    dimensions,
                    email, // Store email
                    link, // Store website link
                    contact
                };

                // Save to local storage
                const artworks = JSON.parse(localStorage.getItem('artworks')) || [];
                artworks.push(artwork);
                localStorage.setItem('artworks', JSON.stringify(artworks));

                // Add the artwork card to the gallery
                addArtworkCard(artwork); // Ensure artwork is added only if gallery exists

                // Clear the form
                form.reset();

                console.log("Artwork added successfully. Redirecting...");

                // Redirect after artwork is successfully added and processed
                window.location.href = 'admin-my-art.php';
            };

            if (image) {
                reader.readAsDataURL(image);
            } else {
                console.error('No image selected');
            }
        });


        // Handle cancel button click
        const cancelButton = document.querySelector('.btn.cancel');

        if (cancelButton) {
            cancelButton.addEventListener('click', function () {
                window.location.href = 'admin-my-art.php'; // Redirect to the main page
            });
        }
    } else {
        console.log("Form not found. Skipping form-related code.");
    }
});


