<?php
session_start();

// Ensure we have pending registration data
if (!isset($_SESSION['reg_data']) || !is_array($_SESSION['reg_data'])) {
    header("Location: Register_Page.php");
    exit();
}

$reg = $_SESSION['reg_data'];

$organizerError = $_SESSION['organizer_error'] ?? '';
$organizerSuccess = $_SESSION['organizer_success'] ?? '';

// Clear flash after reading
unset($_SESSION['organizer_error'], $_SESSION['organizer_success']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../styling/style_loginRegister.css">
    <link rel="icon" href="../../images/logo web.png">
</head>
<body>
    <div class="container">
        <div class="form-box active">
            <form action="Mapping.php" method="POST">
                <h2 id="header">Register</h2>

                <?php if (!empty($organizerError)): ?>
                    <p class="error-message"><?= htmlspecialchars($organizerError); ?></p>
                <?php endif; ?>

                <?php if (!empty($organizerSuccess)): ?>
                    <p class="success-message"><?= htmlspecialchars($organizerSuccess); ?></p>
                <?php endif; ?>

                <p><strong>Username:</strong> <?= htmlspecialchars($reg['User_name']); ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($reg['Email']); ?></p>

                <label>Instansi</label>
                <input type="text" name="instansi" placeholder="Enter Instansi" required>

                <button type="submit" name="organizer_details">Complete Registration</button>
            </form>
        </div>
    </div>
</body>
</html>
