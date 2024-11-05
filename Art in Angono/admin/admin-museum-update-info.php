<?php
session_start();
include '../includes/db_connections.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../logins/login.php");
    exit();
}

$museumName = $_SESSION['username'];
$updateSuccess = false;

$stmt = $conn->prepare("SELECT history, description FROM museums WHERE name = ?");
$stmt->bind_param("s", $museumName);
$stmt->execute();
$result = $stmt->get_result();

$currentHistory = '';
$currentDescription = '';

if ($result->num_rows > 0) {
    $museum = $result->fetch_assoc();
    $currentHistory = $museum['history'] ?? '';
    $currentDescription = $museum['description'] ?? '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateMuseum'])) {
    $history = $_POST['history'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE museums SET history = ?, description = ? WHERE name = ?");
    $stmt->bind_param("sss", $history, $description, $museumName);

    if ($stmt->execute()) {
        $updateSuccess = true;
    } else {
        echo "<p class='alert alert-danger'>Error updating data in the database.</p>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Museum Information</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #ff0000, #ffffff); /* Red to white gradient */
            padding: 20px;
        }

        .museum-details {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 600px;
            margin: 40px auto;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .alert {
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .btn.cancel {
            background-color: grey; /* Grey background for cancel button */
            color: white;
        }

        .btn.cancel:hover {
            opacity: 0.8; /* Slightly transparent on hover */
        }

        .btn.save {
            background-color: red; /* Red background for update button */
            color: white;
        }

        .btn:hover {
            opacity: 0.9; /* Slightly transparent on hover */
        }
    </style>
</head>
<body>
<div class="museum-background"></div>
<?php include '../includes/navigation-admin.php'; ?>

<div class="museum-details">
    <h2>Update Museum Information</h2>

    <?php if ($updateSuccess): ?>
        <div class="alert alert-success">Data updated successfully!</div>
    <?php endif; ?>

    <form method="POST" id="updateMuseumForm">
        <div class="form-group">
            <label for="history">History</label>
            <textarea class="form-control" id="museum-history" name="history" required><?php echo htmlspecialchars($currentHistory); ?></textarea>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="museum-description" name="description" required><?php echo htmlspecialchars($currentDescription); ?></textarea>
        </div>

        <div class="form-buttons">
            <a href="admin-museums.php" class="btn cancel">Cancel</a>
            <button type="submit" name="updateMuseum" class="btn save">Update Information</button>
        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
