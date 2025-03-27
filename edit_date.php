<!-- filepath: c:\xampp\htdocs\kpi system\edit_date.php -->
<?php
// Include database connection
include 'dbconnect.php';

// Handle form submission to update the edit date range
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];

    // Validate the dates
    if (strtotime($startDate) > strtotime($endDate)) {
        $error = "Start date cannot be later than end date.";
    } else {
        // Update the edit date range in the database
        $sql = "UPDATE edit_date SET start_date = ?, end_date = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $startDate, $endDate);

        if ($stmt->execute()) {
            $success = "Edit date range updated successfully.";
        } else {
            $error = "Error updating edit date range: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch the current edit date range
$sql = "SELECT start_date, end_date FROM edit_date WHERE id = 1";
$result = $conn->query($sql);
$editDate = $result->fetch_assoc();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Date Range</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #2f813d;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input[type="date"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #2f813d;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #27632d;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>TARIKH UNTUK MASUKKAN DATA</h2>
        <?php if (isset($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="start_date">MULA:</label>
            <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($editDate['start_date'] ?? '') ?>" required>
            
            <label for="end_date">TAMAT:</label>
            <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($editDate['end_date'] ?? '') ?>" required>
            
            <button type="submit">KEMASKINI TARIKH</button>
        </form>
    </div>
    <a href="homepage.html" class="back-button">Kembali</a>
</body>
</html>