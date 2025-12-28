<?php
session_start();
require_once "../Configuration/config.php"; // must set $conn (mysqli)

// Simple helper - validate required POST keys
function require_keys($arr, $keys) {
    foreach ($keys as $k) {
        if (!isset($arr[$k]) || $arr[$k] === '') return false;
    }
    return true;
}

class Authentication {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // register
    public function register($name, $email, $password_raw, $status) {
        // basic server-side validation
        if (empty($name) || empty($email) || empty($password_raw) || empty($status)) {
            $_SESSION['register_error'] = "All fields are required.";
            header("Location: Register_Page.php");
            exit();
        }

        // check duplicate email
        $stmt = $this->conn->prepare("SELECT Email FROM User WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        if ($res && $res->num_rows > 0) {
            $_SESSION['register_error'] = "Email is already registered!";
            header("Location: Register_Page.php");
            exit();
        }

        $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

        if ($status === '2') {
            // Save all registration data temporarily in session associative array
            $_SESSION['reg_data'] = [
                'User_name' => $name,
                'Email' => $email,
                'Password' => $password_hashed,
                'Status_user' => $status
            ];
            // Redirect to organizer details page to fill instansi
            header("Location: Organizer_Details.php");
            exit();
        }

        // For other statuses, insert immediately
        if ($this->insertUser($name, $email, $password_hashed, $status)) {
            // If verificator status, add to verificator table
            if ($status == '1') {
                $this->insertVerificator($name, $email);
            }

            // redirect to login with success
            header("Location: Login_Page.php?success=registered");
            exit();
        } else {
            $_SESSION['register_error'] = "Registration failed. Try again.";
            header("Location: Register_Page.php");
            exit();
        }
    }

    // insert user helper
    private function insertUser($name, $email, $password, $status) {
        $stmt = $this->conn->prepare(
            "INSERT INTO User (User_name, Email, Password, Status_user) VALUES (?, ?, ?, ?)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("ssss", $name, $email, $password, $status);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    private function insertVerificator($name, $email) {
        $stmt = $this->conn->prepare(
            "INSERT INTO Verificator (Nama_PJ, Email) VALUES (?, ?)"
        );
        if (!$stmt) return false;
        $stmt->bind_param("ss", $name, $email);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // organizer details submission
    public function addOrganizerDetails($instansi) {
        // ensure reg_data exists
        if (!isset($_SESSION['reg_data']) || !is_array($_SESSION['reg_data'])) {
            $_SESSION['organizer_error'] = "No pending organizer registration found.";
            header("Location: Register_Page.php");
            exit();
        }

        $reg = $_SESSION['reg_data'];
        $name = $reg['User_name'];
        $email = $reg['Email'];
        $password = $reg['Password'];
        $status = $reg['Status_user']; // should be '4'

        // insert user
        $okUser = $this->insertUser($name, $email, $password, $status);

        if (!$okUser) {
            $_SESSION['organizer_error'] = "Failed to create user account. Try again.";
            header("Location: Organizer_Details.php");
            exit();
        }

        // insert organizer
        $stmt = $this->conn->prepare(
            "INSERT INTO Organizer (Nama_organizer, Email, Instansi) VALUES (?, ?, ?)"
        );
        if (!$stmt) {
            $_SESSION['organizer_error'] = "Database error (organizer insert).";
            header("Location: Organizer_Details.php");
            exit();
        }
        $stmt->bind_param("sss", $name, $email, $instansi);
        $okOrg = $stmt->execute();
        $stmt->close();

        if (!$okOrg) {
            // optionally rollback user insertion if your DB supports transactions; for now set error
            $_SESSION['organizer_error'] = "Failed to save organizer details.";
            header("Location: Organizer_Details.php");
            exit();
        }

        // success: clear temporary reg_data and redirect to login with success
        unset($_SESSION['reg_data']);
        header("Location: Login_Page.php?success=organizer");
        exit();
    }

    // login
    public function login($email, $password_raw) {
        if (empty($email) || empty($password_raw)) {
            $_SESSION['login_error'] = "Email & password required.";
            header("Location: Login_Page.php");
            exit();
        }

        $stmt = $this->conn->prepare("SELECT * FROM User WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        if ($res && $res->num_rows > 0) {
            $user = $res->fetch_assoc();

            // If organizer, ensure organizer details exist
            if ($user['Status_user'] == '2') {
                $stmt2 = $this->conn->prepare("SELECT * FROM Organizer WHERE Email = ?");
                $stmt2->bind_param("s", $email);
                $stmt2->execute();
                $orgRes = $stmt2->get_result();
                $stmt2->close();

                if (!$orgRes || $orgRes->num_rows == 0) {
                    $_SESSION['login_error'] = "Please complete your organizer details first.";
                    header("Location: Login_Page.php");
                    exit();
                }
            }

            if (password_verify($password_raw, $user['Password'])) {
                // set session user data
                $_SESSION['User_name'] = $user['User_name'];
                $_SESSION['Email'] = $user['Email'];
                $_SESSION['Status_user'] = $user['Status_user'];

                // redirect based on status
                switch ($user['Status_user']) {
                    case '1':
                        header("Location: ../Dashboard/Verificator/Verificator_Dashboard.php");
                        exit();
                    case '2':
                        header("Location: ../Dashboard/Organizer/Organizer_Dashboard.php");
                        exit();
                    case '3':
                        header("Location: ../Dashboard/User/User_Dashboard.php");
                        exit();
                }
            }
        }

        $_SESSION['login_error'] = "Invalid email or password.";
        header("Location: Login_Page.php");
        exit();
    }
}

// Instantiate
$auth = new Authentication($conn);

// POST handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // sanitize minimal
        $name = trim($_POST['User_name'] ?? '');
        $email = trim($_POST['Email'] ?? '');
        $password = $_POST['Password'] ?? '';
        $status = $_POST['Status_user'] ?? '';

        $auth->register($name, $email, $password, $status);
    }

    if (isset($_POST['organizer_details'])) {
        $instansi = trim($_POST['instansi'] ?? '');
        if ($instansi === '') {
            $_SESSION['organizer_error'] = "Instansi is required.";
            header("Location: Organizer_Details.php");
            exit();
        }
        $auth->addOrganizerDetails($instansi);
    }

    if (isset($_POST['login'])) {
        $email = trim($_POST['Email'] ?? '');
        $password = $_POST['Password'] ?? '';
        $auth->login($email, $password);
    }
}
