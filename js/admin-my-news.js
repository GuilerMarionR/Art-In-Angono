// Global variable to track delete mode state
let deleteMode = false;
let eventToDeleteIndex = null;

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

        eventCard.innerHTML = `
            <img src="${event.image}" alt="${event.title} Image">
            <div class="event-card-content">
                <h3>${event.title}</h3>
                <p>${event.description}</p>
                <div class="event-date-location">Date: ${event.date} | Location: ${event.location}</div>
            </div>
        `;

        // Add delete button if in delete mode
        if (deleteMode) {
            const deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.className = "delete-btn";
            deleteButton.onclick = () => showDeleteModal(index);
            eventCard.appendChild(deleteButton);
        }

        myEventsContainer.appendChild(eventCard);
    });
}

// Function to show the delete modal
function showDeleteModal(index) {
    eventToDeleteIndex = index;
    const modal = document.getElementById("deleteModal");
    modal.style.display = "block";
}

// Function to hide the delete modal
function hideDeleteModal() {
    const modal = document.getElementById("deleteModal");
    modal.style.display = "none";
}

// Function to delete an event
function deleteEvent() {
    const events = JSON.parse(localStorage.getItem('events')) || [];
    events.splice(eventToDeleteIndex, 1);
    localStorage.setItem('events', JSON.stringify(events));
    hideDeleteModal();
    renderEvents();
}

// Function to toggle delete mode
function toggleDeleteMode() {
    deleteMode = !deleteMode;
    renderEvents();
    const toggleButton = document.getElementById('toggleDeleteMode');
    toggleButton.textContent = deleteMode ? 'Exit Delete Mode' : 'Toggle Delete Mode';
}

// Event listeners
document.addEventListener('DOMContentLoaded', () => {
    renderEvents();
    document.getElementById('toggleDeleteMode').addEventListener('click', toggleDeleteMode);
    document.getElementById('confirmDelete').addEventListener('click', deleteEvent);
    document.getElementById('cancelDelete').addEventListener('click', hideDeleteModal);
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById("deleteModal");
        if (event.target == modal) {
            hideDeleteModal();
        }
    };
});