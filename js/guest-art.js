document.addEventListener("DOMContentLoaded", function () {
    const galleryContainer = document.querySelector('.gallery-museum');

    // Function to create artwork card and append to gallery
    function addArtworkCard(artwork) {
        const artworkCard = document.createElement('div');
        artworkCard.classList.add('artwork');

        // Create a unique ID for the artwork (slug)
        const artworkId = artwork.title.replace(/\s+/g, '-').toLowerCase();

        // Create the HTML for the card with limited details initially visible
        artworkCard.innerHTML = `
            <a href="javascript:void(0);" class="artwork-details-link" data-id="${artworkId}">
                <img src="${artwork.image}" alt="${artwork.title}">
            </a>
            <h3>${artwork.title}</h3>
            <p><strong></strong> ${artwork.gallery}</p>
            <p><strong></strong> ${artwork.artist}</p>
        `;

        // Append the card to the gallery
        if (galleryContainer) {
            galleryContainer.appendChild(artworkCard);
        }

        // Add click event to redirect to a different page when artwork is clicked
        const detailsLink = artworkCard.querySelector('.artwork-details-link');
        detailsLink.addEventListener('click', function () {
            window.location.href = `guest-art-info.php?id=${artworkId}`; // Change to your details page URL
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
    } else {
        console.log("Gallery container not found. Skipping gallery-related code.");
    }
});
