document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('edit-artwork-form');
    const cancelButton = document.querySelector('.btn.cancel');

    // Function to get query parameters from the URL
    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    // Get the artwork title from the URL
    const artworkTitle = getQueryParam('title');

    // Load existing artworks from local storage
    const artworks = JSON.parse(localStorage.getItem('artworks')) || [];
    const artwork = artworks.find(art => art.title === artworkTitle);

    // Populate the form with the artwork details
    if (artwork) {
        form.title.value = artwork.title;
        form.gallery.value = artwork.gallery;
        form.artist.value = artwork.artist;
        form.description.value = artwork.description;
        form.medium.value = artwork.medium;
        form.dimensions.value = artwork.dimensions;
        form.email.value = artwork.email;
        form.link.value = artwork.link;
        form.contact.value = artwork.contact;
    } else {
        alert('Artwork not found!');
    }

    // Handle form submission to save changes
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent default form submission

        // Get updated values from the form
        const updatedArtwork = {
            title: form.title.value,
            gallery: form.gallery.value,
            artist: form.artist.value,
            image: artwork.image, // Keep the existing image if not changed
            description: form.description.value,
            medium: form.medium.value,
            dimensions: form.dimensions.value,
            email: form.email.value,
            link: form.link.value,
            contact: form.contact.value
        };

        // Update the artwork in local storage
        const updatedArtworks = artworks.map(art => {
            return art.title === artworkTitle ? updatedArtwork : art;
        });
        
        // Save the updated artworks back to local storage
        localStorage.setItem('artworks', JSON.stringify(updatedArtworks));

        // Redirect back to the gallery page
        window.location.href = 'admin-my-art.php';
    });

    // Handle cancel button click
    if (cancelButton) {
        cancelButton.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default action
            window.location.href = 'admin-my-art.php'; // Redirect to the main page
        });
    }
});
