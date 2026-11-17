<?php
// ============================================================================
// register.php - Student Registration
// ============================================================================

require_once 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$name  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check for duplicate email
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $insert_stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert_stmt, "sss", $name, $email, $hashed_password);

            if (mysqli_stmt_execute($insert_stmt)) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - NOTEHUB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">NOTEHUB</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="browse_notes.php">Browse Notes</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        <div class="form-box">
            <h2>Registration</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="register.php" method="POST" id="registerForm">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>

            <div class="form-footer">
                Already registered? <a href="login.php">Login</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>NOTEHUB | Notes Sharing Website</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
