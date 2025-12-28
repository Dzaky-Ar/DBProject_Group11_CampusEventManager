<?php
session_start();

$registerError = $_SESSION['register_error'] ?? '';
$registerSuccess = $_SESSION['register_success'] ?? '';

// Clear after reading so it behaves like a flash
unset($_SESSION['register_error'], $_SESSION['register_success']);
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

                <?php if (!empty($registerError)): ?>
                    <p class="error-message"><?= htmlspecialchars($registerError); ?></p>
                <?php endif; ?>

                <?php if (!empty($registerSuccess)): ?>
                    <p class="success-message"><?= htmlspecialchars($registerSuccess); ?></p>
                <?php endif; ?>

                <label>Username</label>
                <input type="text" name="User_name" placeholder="Enter Username" required>

                <label>Email</label>
                <input type="email" name="Email" placeholder="Enter Email" required>

                <label>Password</label>
                <input type="password" name="Password" placeholder="Enter Password" required>

                <label>Status</label>
                <select name="Status_user" id="Status_user" required>
                    <option value="">--Select Status--</option>
                    <option value="1">Verificator </option>
                    <option value="2">Organizer</option>
                    <option value="3">Guest/User</option>
                </select>


                <button type="submit" name="register">Confirm</button>
                <p>Already have an account? <a href="Login_Page.php">Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>
