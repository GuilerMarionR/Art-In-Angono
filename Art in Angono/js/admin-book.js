// admin-book.js

// Function to show/hide content based on the selected section
function showSection(sectionId) {
    const sections = document.querySelectorAll('.tab-content');
    sections.forEach(section => {
        section.style.display = section.id === sectionId ? 'block' : 'none';
    });

    // Load the guest list and guest history when the respective sections are shown
    if (sectionId === 'guest-list') {
        loadGuestList(); // Load guest list when this section is shown
    } else if (sectionId === 'guest-history') {
        loadGuestHistory(); // Load guest history when this section is shown
    }
}

// Function to load the guest list dynamically
function loadGuestList() {
    const guestList = [
        {
            firstName: "Juan",
            middleName: "C.",
            lastName: "Dela Cruz",
            address: "123 Main St, Angono",
            email: "juan@example.com",
            age: 25,
            contactNumber: "09123456789",
            numberOfGuests: 3,
            date: "2024-10-30",
            time: "14:00"
        },
        // Add more guest objects as needed
    ];

    const tbody = document.querySelector('#guest-list-table tbody');
    tbody.innerHTML = ''; // Clear existing rows
    guestList.forEach(guest => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${guest.firstName}</td>
            <td>${guest.middleName}</td>
            <td>${guest.lastName}</td>
            <td>${guest.address}</td>
            <td>${guest.email}</td>
            <td>${guest.age}</td>
            <td>${guest.contactNumber}</td>
            <td>${guest.numberOfGuests}</td>
            <td>${guest.date}</td>
            <td>${guest.time}</td>
        `;
        tbody.appendChild(row);
    });
}

// Function to load the guest history dynamically
function loadGuestHistory() {
    const guestHistory = [
        {
            firstName: "Maria",
            middleName: "J.",
            lastName: "Santos",
            address: "456 Elm St, Angono",
            email: "maria@example.com",
            age: 30,
            contactNumber: "09123456790",
            numberOfGuests: 2,
            date: "2023-09-15",
            time: "10:00"
        },
        // Add more guest history objects as needed
    ];

    const tbody = document.querySelector('#guest-history-table tbody');
    tbody.innerHTML = ''; // Clear existing rows
    guestHistory.forEach(guest => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${guest.firstName}</td>
            <td>${guest.middleName}</td>
            <td>${guest.lastName}</td>
            <td>${guest.address}</td>
            <td>${guest.email}</td>
            <td>${guest.age}</td>
            <td>${guest.contactNumber}</td>
            <td>${guest.numberOfGuests}</td>
            <td>${guest.date}</td>
            <td>${guest.time}</td>
        `;
        tbody.appendChild(row);
    });
}

// Initialize the calendar on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initially load the guest list if desired
    showSection('guest-list'); // Load guest list section by default
});
