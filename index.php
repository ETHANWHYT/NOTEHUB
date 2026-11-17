<?php
// ============================================================================
// index.php - NOTEHUB Homepage
// ============================================================================

require_once 'db.php';

// Handle Note Deletion (Only owner can delete)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $note_id = (int)$_GET['id'];
    $user_id = (int)$_SESSION['user_id'];

    // Verify ownership
    $stmt = mysqli_prepare($conn, "SELECT file_name FROM notes WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $note_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $note = mysqli_fetch_assoc($result);

    if ($note) {
        $file_path = __DIR__ . '/uploads/' . basename($note['file_name']);
        if (file_exists($file_path)) {
            @unlink($file_path);
        }

        $del_stmt = mysqli_prepare($conn, "DELETE FROM notes WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($del_stmt, "ii", $note_id, $user_id);
        mysqli_stmt_execute($del_stmt);

        header("Location: index.php?msg=deleted");
        exit;
    } else {
        header("Location: index.php?msg=unauthorized");
        exit;
    }
}

// Fetch Notes (with optional Search)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$notes = [];

if ($search !== '') {
    $search_param = "%" . $search . "%";
    $query = "SELECT notes.*, users.name AS uploader_name 
              FROM notes 
              JOIN users ON notes.user_id = users.id 
              WHERE notes.title LIKE ? OR notes.subject LIKE ? 
              ORDER BY notes.id DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $search_param, $search_param);
} else {
    $query = "SELECT notes.*, users.name AS uploader_name 
              FROM notes 
              JOIN users ON notes.user_id = users.id 
              ORDER BY notes.id DESC";
    $stmt = mysqli_prepare($conn, $query);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $notes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTEHUB - Notes Sharing Website</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- 1. Simple Navigation Bar -->
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
        <!-- Status Messages -->
        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'uploaded'): ?>
                <div class="alert alert-success">Note uploaded successfully.</div>
            <?php elseif ($_GET['msg'] === 'deleted'): ?>
                <div class="alert alert-success">Note deleted successfully.</div>
            <?php elseif ($_GET['msg'] === 'unauthorized'): ?>
                <div class="alert alert-error">You cannot delete a note uploaded by someone else.</div>
            <?php elseif ($_GET['msg'] === 'not_found'): ?>
                <div class="alert alert-error">Requested file does not exist.</div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- 2. Small Heading -->
        <div class="home-intro">
            <h1>Notes Sharing Website</h1>
        </div>

        <!-- 3. Simple Search Input -->
        <form action="index.php" method="GET" class="search-bar">
            <input type="text" name="search" class="search-input" placeholder="Search by title or subject..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if ($search !== ''): ?>
                <a href="index.php" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>

        <!-- 4. Browse Notes button & 5. Upload Notes button -->
        <div class="home-buttons">
            <a href="browse_notes.php" class="btn btn-secondary">Browse Notes</a>
            <a href="upload.php" class="btn btn-primary">Upload Notes</a>
        </div>

        <!-- 6. Heading: Available Notes -->
        <div class="section-heading">
            <span>Available Notes</span>
            <?php if (!empty($notes)): ?>
                <span style="font-size: 13px; font-weight: normal; color: #666666;"><?php echo count($notes); ?> note<?php echo count($notes) === 1 ? '' : 's'; ?></span>
            <?php endif; ?>
        </div>

        <!-- 7. Notes Fetched Directly from MySQL -->
        <?php if (empty($notes)): ?>
            <!-- 8. Empty State if no notes exist -->
            <div class="empty-text">
                <p>No notes available.</p>
            </div>
        <?php else: ?>
            <div class="notes-grid">
                <?php foreach ($notes as $n): ?>
                    <div class="note-card">
                        <div>
                            <div class="note-title"><?php echo htmlspecialchars($n['title']); ?></div>
                            <div class="note-info">
                                <strong>Subject:</strong> <?php echo htmlspecialchars($n['subject']); ?><br>
                                <strong>Course:</strong> <?php echo htmlspecialchars($n['course']); ?> (<?php echo htmlspecialchars($n['semester']); ?>)
                                <?php if (!empty($n['description'])): ?>
                                    <div style="margin-top: 4px; color: #666666;"><?php echo htmlspecialchars($n['description']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <div class="note-meta">
                                <span>Uploaded by: <?php echo htmlspecialchars($n['uploader_name']); ?></span>
                                <span>Downloads: <?php echo (int)$n['downloads']; ?></span>
                            </div>

                            <div class="note-card-actions">
                                <a href="download.php?id=<?php echo (int)$n['id']; ?>" class="btn btn-primary btn-sm" style="flex:1;">
                                    Download
                                </a>
                                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $n['user_id']): ?>
                                    <a href="index.php?action=delete&id=<?php echo (int)$n['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this note?');">
                                        Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- 9. Simple Footer -->
    <footer class="footer">
        <p>NOTEHUB | Notes Sharing Website</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
