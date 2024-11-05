// Function to render event cards from local storage
function renderEvents() {
    const myEventsContainer = document.getElementById("myEvents");
    myEventsContainer.innerHTML = ""; // Clear existing content

    // Get existing events from local storage
    const events = JSON.parse(localStorage.getItem('events')) || [];

    // Loop through events and create cards
    events.forEach((event, index) => {
        const eventCard = document.createElement("div");
        eventCard.className = "event-card";

        // Create a checkbox for each event
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.className = "event-checkbox";
        checkbox.dataset.index = index; // Store the index for deletion

        eventCard.innerHTML = `
            <img src="${event.image}" alt="${event.title} Image">
            <div class="event-card-content">
                <h3>${event.title}</h3>
                <p>${event.description}</p>
                <div class="event-date-location">Date: ${event.date} | Location: ${event.location}</div>
            </div>
        `;

        // Append checkbox to the event card
        eventCard.prepend(checkbox); // Prepend checkbox to the event card

        myEventsContainer.appendChild(eventCard);
    });
}

// Function to delete selected events
function deleteSelectedEvents() {
    const checkboxes = document.querySelectorAll('.event-checkbox:checked'); // Get all checked checkboxes
    const events = JSON.parse(localStorage.getItem('events')) || [];

    // Filter out the checked events
    const remainingEvents = events.filter((event, index) => {
        return !Array.from(checkboxes).some(checkbox => checkbox.dataset.index == index);
    });

    // Save the updated events back to local storage
    localStorage.setItem('events', JSON.stringify(remainingEvents));
    renderEvents(); // Re-render events after deletion
}

// Call the function to render events on page load
document.addEventListener('DOMContentLoaded', renderEvents);

// Add event listener to the delete button
const deleteButton = document.getElementById('delete-news'); // Using your existing button ID
if (deleteButton) {
    deleteButton.addEventListener('click', deleteSelectedEvents);
}
