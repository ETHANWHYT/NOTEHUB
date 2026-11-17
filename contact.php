<?php
// ============================================================================
// contact.php - Contact Form
// ============================================================================

require_once 'db.php';

$sent = false;
$error = '';
$name = '';
$email = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $sent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - NOTEHUB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">NOTEHUB</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="browse_notes.php">Browse Notes</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><span class="user-text"><?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <main class="container">
        <div class="form-box" style="max-width: 450px;">
            <h2>Contact Us</h2>

            <?php if ($sent): ?>
                <div class="alert alert-success">Message sent. Thank you.</div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="contact.php" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" class="form-control" required><?php echo htmlspecialchars($message); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Send Message</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>NOTEHUB | Notes Sharing Website</p>
    </footer>
</body>
</html>
