document.querySelector('.review-button').addEventListener('click', function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Get the values from the form inputs
    const lastName = document.querySelector('input[placeholder="Enter Last Name"]').value;
    const firstName = document.querySelector('input[placeholder="Enter First Name"]').value;
    const middleName = document.querySelector('input[placeholder="Enter Middle Name"]').value;
    const address = document.querySelector('input[placeholder="Enter Address"]').value;
    const email = document.querySelector('input[placeholder="Enter Email"]').value;
    const age = document.querySelector('input[placeholder="Enter Age"]').value;
    const contactNumber = document.querySelector('input[placeholder="Enter Contact Number"]').value;
    const numGuests = document.querySelector('input[placeholder="Enter Number of Guests"]').value;

    // Get the selected date and time
    const date = document.getElementById('date').value;
    const timeOptions = document.getElementsByName('time');
    let selectedTime = '';

    // Find the selected time option
    for (let option of timeOptions) {
        if (option.checked) {
            selectedTime = option.value;
            break;
        }
    }

    // Construct the URL for the receipt page with query parameters
    const receiptUrl = `guest-review.php?lastName=${encodeURIComponent(lastName)}&firstName=${encodeURIComponent(firstName)}&middleName=${encodeURIComponent(middleName)}&address=${encodeURIComponent(address)}&email=${encodeURIComponent(email)}&age=${encodeURIComponent(age)}&contactNumber=${encodeURIComponent(contactNumber)}&numGuests=${encodeURIComponent(numGuests)}&date=${encodeURIComponent(date)}&time=${encodeURIComponent(selectedTime)}`;

    // Redirect to the receipt page
    window.location.href = receiptUrl;
});
