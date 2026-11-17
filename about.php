<?php
// ============================================================================
// about.php - About NOTEHUB
// ============================================================================

require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - NOTEHUB</title>
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
        <div class="form-box" style="max-width: 600px; margin: 30px auto; text-align: left;">
            <h2>About NOTEHUB</h2>
            <p style="font-size: 14px; line-height: 1.6; color: #333333;">
                NOTEHUB is a simple web platform that allows college students to upload, browse, and download academic study notes for their semester courses.
            </p>
        </div>
    </main>

    <footer class="footer">
        <p>NOTEHUB | Notes Sharing Website</p>
    </footer>
</body>
</html>
