document.addEventListener("DOMContentLoaded", function () {
    const artworkTitle = new URLSearchParams(window.location.search).get('id'); // Extract the artwork ID from the URL

    // Load existing artworks from local storage
    const artworks = JSON.parse(localStorage.getItem('artworks')) || [];
    const artwork = artworks.find(art => art.title.replace(/\s+/g, '-').toLowerCase() === artworkTitle);

    if (artwork) {
        // Populate artwork details in the page
        document.querySelector('.artwork-title').textContent = artwork.title;
        document.querySelector('.artwork-gallery').textContent = artwork.gallery;
        document.querySelector('.artwork-artist').textContent = artwork.artist;
        document.querySelector('.artwork-image').src = artwork.image;
        document.querySelector('.artwork-description').textContent = artwork.description;
        document.querySelector('.artwork-medium').textContent = artwork.medium;
        document.querySelector('.artwork-dimensions').textContent = artwork.dimensions;
        document.querySelector('.artwork-email').textContent = artwork.email;
        document.querySelector('.artwork-link').textContent = artwork.link;
        document.querySelector('.artwork-link').href = artwork.link;
        document.querySelector('.artwork-contact').textContent = artwork.contact;

        // Set the href of the edit button to include the artwork title as a URL parameter for editing
        const editButton = document.querySelector('.btn.edit');
        if (editButton) {
            editButton.href = `edit-artwork.php?title=${encodeURIComponent(artwork.title)}`; // Pass artwork title to the edit page
        }
    } else {
        alert('Artwork not found!');
    }
});
