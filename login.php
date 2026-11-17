<?php
// ============================================================================
// login.php - Student Login
// ============================================================================

require_once 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter email and password.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['email']   = $user['email'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NOTEHUB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">NOTEHUB</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="browse_notes.php">Browse Notes</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        <div class="form-box">
            <h2>Login</h2>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">Registration successful. Please log in.</div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <div class="form-footer">
                Don't have an account? <a href="register.php">Register</a>
            </div>
        </div>
    </main>

    <footer class="footer">
        <p>NOTEHUB | Notes Sharing Website</p>
    </footer>
</body>
</html>
