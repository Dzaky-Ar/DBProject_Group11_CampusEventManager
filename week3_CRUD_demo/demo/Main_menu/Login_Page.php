<?php
session_start();

// Get possible flash messages (either from GET or session flash)
$successGet = $_GET['success'] ?? '';
$flash = $_SESSION['flash_message'] ?? '';
$loginError = $_SESSION['login_error'] ?? '';

if (!empty($flash)) {
    // prefer session flash if exists
    $message = $flash;
    unset($_SESSION['flash_message']);
} else {
    if ($successGet === 'registered') {
        $message = "Email successfully registered. Please login.";
    } elseif ($successGet === 'organizer') {
        $message = "Registration is completed. Please login.";
    } else {
        $message = '';
    }
}

// Clear only login_error after reading
if (!empty($loginError)) {
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../../styling/style_loginRegister.css">
    <link rel="icon" href="../../images/logo web.png">
</head>
<body>
    <div class="container">
        <div class="form-box active">
            <form action="Mapping.php" method="POST">
                <h2 id="header">Login</h2>

                <?php if (!empty($message)): ?>
                    <p class="success-message"><?= htmlspecialchars($message); ?></p>
                <?php endif; ?>

                <?php if (!empty($loginError)): ?>
                    <p class="error-message"><?= htmlspecialchars($loginError); ?></p>
                <?php endif; ?>

                <label>Email</label>
                <input type="email" name="Email" placeholder="Enter email" required>

                <label>Password</label>
                <input type="password" name="Password" placeholder="Enter Password" required>

                <button type="submit" name="login">Login</button>
                <p>Don't have an account? <a href="Register_Page.php">Register</a></p>
            </form>
        </div>
    </div>
</body>
</html>
