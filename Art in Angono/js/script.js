// GUEST NAVBAR
const navLinks = [
    { text: "HOME", href: "guest-home.php" },
    { text: "MUSEUMS", href: "guest-museums.php" },
    { text: "NEWS & EVENTS", href: "guest-news.php" },
    { text: "ARTWORKS", href: "guest-art.php" },
    { text: "BOOK A TOUR", href: "guest-book.php" },
    { text: "LOGIN", href: "login.php" },
];

function generateNavLinks() {
    const navContainer = document.getElementById('nav-links');
    navLinks.forEach(link => {
        const anchor = document.createElement('a'); 
        anchor.href = link.href; 
        anchor.textContent = link.text; 
        navContainer.appendChild(anchor); 
    });
}

// GUEST MUSEUM




// GUEST MUSEUM INFO
// GUEST 360

// GUEST NEWS
// Load guest events from localStorage or set to an empty array if not available
let guestEvents = JSON.parse(localStorage.getItem('guestEvents')) || [];

// Function to render events for the guest page in a row with horizontal scroll
function renderGuestEvents() {
    const guestEventsContainer = document.getElementById('guestEventsContainer');
    guestEventsContainer.innerphp = ''; // Clear the container first

    if (guestEvents.length === 0) {
        guestEventsContainer.innerphp = '<p>No events available.</p>';
        return;
    }

    // Create a wrapper to hold the event cards
    const eventsWrapper = document.createElement('div');
    eventsWrapper.classList.add('events-grid');

    // Create event cards
    guestEvents.forEach((event) => {
        const eventCard = document.createElement('div');
        eventCard.classList.add('event-card');

        eventCard.innerphp = `
            <img src="${event.image || 'placeholder.jpg'}" alt="Event Image" class="event-image">
            <div class="event-details">
                <h3>${event.title}</h3>
                <p><strong>Date:</strong> ${event.date}</p>
                <p><strong>Museum:</strong> ${event.museumName}</p>
            </div>
        `;

        // Add click event to redirect to the preview page
        eventCard.addEventListener('click', function() {
            // Store the clicked event in localStorage
            localStorage.setItem('previewEvent', JSON.stringify(event));
            
            // Redirect to the preview.php page
            window.location.href = 'guest-event-preview.php'; // Ensure guestpreview.php exists
        });

        eventsWrapper.appendChild(eventCard);
    });

    // Add the events wrapper to the guestEventsContainer
    guestEventsContainer.appendChild(eventsWrapper);

    // Add left and right arrows after the wrapper is appended
    addScrollArrows(guestEventsContainer, eventsWrapper);
}

// Function to render upcoming events for the guest page in a row with horizontal scroll
function renderGuestUpcomingEvents() {
    const guestUpcomingEventsContainer = document.getElementById('guestUpcomingEventsContainer');
    guestUpcomingEventsContainer.innerphp = ''; // Clear the container first

    if (guestEvents.length === 0) {
        guestUpcomingEventsContainer.innerphp = '<p>No upcoming events available.</p>';
        return;
    }

    // Create a wrapper to hold the upcoming event cards
    const upcomingEventsWrapper = document.createElement('div');
    upcomingEventsWrapper.classList.add('upcoming-events-grid');

    // Filter upcoming events
    const upcomingEvents = guestEvents.filter(event => new Date(event.date) > new Date());

    // Create upcoming event cards
    upcomingEvents.forEach((event) => {
        const upcomingEventCard = document.createElement('div');
        upcomingEventCard.classList.add('upcoming-event-card');

        upcomingEventCard.innerphp = `
            <span class="event-date">
                ${new Date(event.date).toLocaleDateString('en-US', { day: 'numeric', month: 'short' }).split(' ').reverse().join(' ').toUpperCase()}</span>
            <div class="event-details">
             <h3>${event.title}</h3>
            <p class="museum-name">${event.museumName}</p>
            <p class="event-time">${new Date(event.date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</p>
        `;

        // Add click event to redirect to the preview page
        upcomingEventCard.addEventListener('click', function() {
            // Store the clicked event in localStorage
            localStorage.setItem('previewEvent', JSON.stringify(event));

            // Redirect to the preview.php page
            window.location.href = 'guest-event-preview.php'; // Ensure guestpreview.php exists
        });

        upcomingEventsWrapper.appendChild(upcomingEventCard);
    });

    // Add the upcoming events wrapper to the container
    guestUpcomingEventsContainer.appendChild(upcomingEventsWrapper);

    // Add left and right arrows after the wrapper is appended
    addScrollArrows(guestUpcomingEventsContainer, upcomingEventsWrapper);
}

// Function to add left and right arrows for scrolling
function addScrollArrows(container, wrapper) {
    // Add left arrow
    const leftArrow = document.createElement('div');
    leftArrow.classList.add('scroll-arrow', 'left-arrow');
    leftArrow.innerphp = '&#9664;'; // Left arrow symbol
    leftArrow.addEventListener('click', () => {
        wrapper.scrollLeft -= 300; // Scroll left by 300px
    });

    // Add right arrow
    const rightArrow = document.createElement('div');
    rightArrow.classList.add('scroll-arrow', 'right-arrow');
    rightArrow.innerphp = '&#9654;'; // Right arrow symbol
    rightArrow.addEventListener('click', () => {
        wrapper.scrollLeft += 300; // Scroll right by 300px
    });

    // Append arrows to the container
    container.appendChild(leftArrow);
    container.appendChild(rightArrow);
}

// Load guest events when the page loads
window.onload = function() {
    renderGuestEvents();
    renderGuestUpcomingEvents();
};



// GUEST EVENTS
window.onload = function() {
    // Retrieve the stored events from localStorage
    let events = JSON.parse(localStorage.getItem('events')) || [];

    // Sort events by date
    events.sort((a, b) => new Date(a.date) - new Date(b.date));

    // Get the container to display events
    const allEventsContainer = document.getElementById('allEventsContainer');

    // Clear the container before rendering
    allEventsContainer.innerphp = '';

    if (events.length === 0) {
        allEventsContainer.innerphp = '<p>No events available.</p>';
        return;
    }

    // Loop through the events and render each event
    events.forEach((event, index) => {
        // Create a new div for the event card
        const eventCard = document.createElement('div');
        eventCard.classList.add('event-card');

        // Extract the day and month from the event date
        const eventDate = new Date(event.date);
        const day = eventDate.getDate();
        const month = eventDate.toLocaleString('default', { month: 'short' }).toUpperCase();

        // Set the inner php of the event card
        eventCard.innerphp = `
            <div class="event-date">
                <span class="day">${day}</span>
                <span class="month">${month}</span>
            </div>
            <img src="${event.image || 'placeholder.jpg'}" alt="Event Image" class="event-image">
            <div class="event-details">
                <h3>${event.title}</h3>
                <div class="museum-info">
                    ${event.date} | ${event.museumName}
                </div>
            </div>
        `;

        // Add click event to redirect to the event preview page
        eventCard.addEventListener('click', function() {
            // Store the selected event in localStorage under 'previewEvent'
            localStorage.setItem('previewEvent', JSON.stringify(event));

            // Redirect to the event preview page
            window.location.href = 'guest-event-preview.php';
        });

        // Append the event card to the allEventsContainer
        allEventsContainer.appendChild(eventCard);
    });
};

function goBack() {
    window.location.href = 'guestpov.php';
}


// // GUEST EVENT PREVIEW
//         // Load event data from localStorage
//         const event = JSON.parse(localStorage.getItem('previewEvent'));

//         if (event) {
//             // Populate the content with the event data
//             document.getElementById('event-title').innerText = event.title;
//             document.getElementById('event-details').innerText = `${event.date} | ${event.museumName}`;
//             document.getElementById('event-description').innerText = event.description;

//             // Set background image for the preview
//             document.getElementById('image-section').style.backgroundImage = `url(${event.image})`;
//         } else {
//             alert('No event data found');
//             window.location.href = 'guestpov.php'; // Redirect back if no data found
//         }

//         // Back button functionality to return to the main event page
//         document.getElementById('backButton').addEventListener('click', function() {
//             window.location.href = 'guestpov.php'; // Replace 'index.php' with the correct path to your events page
//         });



// GUEST ART
document.addEventListener("DOMContentLoaded", function() {
    // Select all artwork elements
    const artworks = document.querySelectorAll(".artwork");

    // Loop through each artwork and add a click event listener
    artworks.forEach(function(artwork) {
        artwork.addEventListener("click", function() {
            // Redirect to guest-art-info.php
            window.location.href = "guest-art-info.php";
        });
    });
});


// GUEST ART INFO

function redirectToTerms(museumName) {
    // Redirects to the terms page with the selected museum as a query parameter
    window.location.href = `../guests/guest-terms.php?museum=${encodeURIComponent(museumName)}`;
}



// GUEST TERMS
            // AGREE
function redirectToForm() {
    const selectedMuseum = sessionStorage.getItem('selectedMuseum');
    if (selectedMuseum) {
            // CANCEL
        window.location.href = `form.php?museum=${encodeURIComponent(selectedMuseum)}`;
    } else {
        alert('No museum selected!');
        window.location.href = 'guest-book.php';
    }
}

function generateNavLinks() {
}



// GUEST FORM
// GUEST NOTIFY










// ADMIN NAVBAR
// ADMIN HOME
// ADMIN MUSEUM
// ADMIN MUSEUM INFO











// ADMIN GUEST LIST
// ADMIN EDIT CALENDAR
// ADMIN GUEST HISTORY
// ADMIN PASSWORD
// LOGOUT




