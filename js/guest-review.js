function getURLParameters() {
    const params = {};
    const queryString = window.location.search.substring(1);
    const regex = /([^&=]+)=([^&]*)/g;
    let m;

    while (m = regex.exec(queryString)) {
        params[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
    }

    return params;
}

// Get the parameters and display them
const params = getURLParameters();
document.getElementById('lastName').textContent = params.lastName || '';
document.getElementById('firstName').textContent = params.firstName || '';
document.getElementById('middleName').textContent = params.middleName || '';
document.getElementById('address').textContent = params.address || '';
document.getElementById('email').textContent = params.email || '';
document.getElementById('age').textContent = params.age || '';
document.getElementById('contactNumber').textContent = params.contactNumber || '';
document.getElementById('numGuests').textContent = params.numGuests || '';
document.getElementById('date').textContent = params.date || '';
document.getElementById('time').textContent = params.time || '';


// Function to handle the cancel button click
document.getElementById('cancelButton').addEventListener('click', function() {
    // Go back to the guest-forms.html while preserving information in local storage
    window.location.href = 'guest-form.php';
});

// Function to handle the submit button click
document.getElementById('submitButton').addEventListener('click', function() {
    // Gather the receipt information
    const receiptData = {
        lastName: document.getElementById('lastName').innerText,
        firstName: document.getElementById('firstName').innerText,
        middleName: document.getElementById('middleName').innerText,
        address: document.getElementById('address').innerText,
        email: document.getElementById('email').innerText,
        age: document.getElementById('age').innerText,
        contactNumber: document.getElementById('contactNumber').innerText,
        numGuests: document.getElementById('numGuests').innerText,
        date: document.getElementById('date').innerText,
        time: document.getElementById('time').innerText,
        museumName: document.getElementById('museumName').innerText // Add museumName to receipt data
    };

    // Set hidden form values based on receipt data
    document.querySelector("input[name='last_name']").value = receiptData.lastName;
    document.querySelector("input[name='first_name']").value = receiptData.firstName;
    document.querySelector("input[name='middle_name']").value = receiptData.middleName;
    document.querySelector("input[name='Address']").value = receiptData.address;
    document.querySelector("input[name='Email']").value = receiptData.email;
    document.querySelector("input[name='Age']").value = receiptData.age;
    document.querySelector("input[name='ContactNumber']").value = receiptData.contactNumber;
    document.querySelector("input[name='NumberOfGuests']").value = receiptData.numGuests;
    document.querySelector("input[name='AppointmentDate']").value = receiptData.date;
    document.querySelector("input[name='AppointmentTime']").value = receiptData.time;
    document.querySelector("input[name='MuseumName']").value = receiptData.museumName; // Assign museumName

    // Submit the form
    document.getElementById('receipt-form').submit();
});


    // Save the receipt data to local storage
    localStorage.setItem('receiptData', JSON.stringify(receiptData));

    // Render the receipt to canvas
    const canvas = document.getElementById('receiptCanvas');
    const ctx = canvas.getContext('2d');
    canvas.width = 600;  // Set canvas width
    canvas.height = 400; // Set canvas height

    // Set background and text styles
    ctx.fillStyle = '#fff';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = '#333';
    ctx.font = '20px Arial';

    // Render receipt text
    const lines = [
        "Appointment Receipt",
        `Last Name: ${receiptData.lastName}`,
        `First Name: ${receiptData.firstName}`,
        `Middle Name: ${receiptData.middleName}`,
        `Address: ${receiptData.address}`,
        `Email: ${receiptData.email}`,
        `Age: ${receiptData.age}`,
        `Contact Number: ${receiptData.contactNumber}`,
        `Number of Guests: ${receiptData.numGuests}`,
        `Date: ${receiptData.date}`,
        `Time: ${receiptData.time}`,
        "Thank you for booking with us! We look forward to seeing you soon."
    ];

    lines.forEach((line, index) => {
        ctx.fillText(line, 20, 40 + index * 30);
    });

    // Download the receipt as a JPEG image
    const image = canvas.toDataURL('image/jpeg', 1.0); // Change to 'image/jpeg'
    const a = document.createElement('a');
    a.href = image;
    a.download = 'e-receipt.jpg'; // Set the filename to .jpg
    a.click();

    // Send email with receipt (pseudo-code, implement with your backend)
    const email = receiptData.email; // Email to send to
    const imageBase64 = image.split(',')[1]; // Extract base64 part

    // Example of using a fetch request to send email to your backend service
    fetch('YOUR_BACKEND_URL/send-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: email,
            image: imageBase64, // Send the image as base64
        }),
    }).then(response => {
        if (response.ok) {
            alert('Receipt saved and emailed successfully!');
        } else {
            alert('There was an error sending the email.');
        }
    }).catch(error => {
        console.error('Error:', error);
        alert('Failed to send email.');
    });
