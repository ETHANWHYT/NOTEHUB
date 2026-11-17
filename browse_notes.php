<?php
// ============================================================================
// browse_notes.php - Browse Notes Page
// ============================================================================

require_once 'db.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$subject = isset($_GET['subject']) ? trim($_GET['subject']) : '';

// Build query safely
$query = "SELECT notes.*, users.name AS uploader_name 
          FROM notes 
          JOIN users ON notes.user_id = users.id 
          WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $query .= " AND (notes.title LIKE ? OR notes.description LIKE ?)";
    $search_param = "%" . $search . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if ($subject !== '') {
    $query .= " AND notes.subject LIKE ?";
    $subject_param = "%" . $subject . "%";
    $params[] = $subject_param;
    $types .= "s";
}

$query .= " ORDER BY notes.id DESC";

$stmt = mysqli_prepare($conn, $query);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Notes - NOTEHUB</title>
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
        <h2 style="margin-bottom: 15px;">Browse Notes</h2>

        <!-- Search & Filter Form -->
        <form action="browse_notes.php" method="GET" style="display:flex; gap:8px; margin-bottom: 20px; flex-wrap:wrap;">
            <input type="text" name="search" class="search-input" placeholder="Search by title..." value="<?php echo htmlspecialchars($search); ?>">
            <input type="text" name="subject" class="search-input" placeholder="Filter by subject..." value="<?php echo htmlspecialchars($subject); ?>" style="width:180px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if ($search !== '' || $subject !== ''): ?>
                <a href="browse_notes.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>

        <?php if (empty($notes)): ?>
            <div class="empty-text">
                <p>No notes found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="notes-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Uploaded By</th>
                            <th>Downloads</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notes as $n): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($n['title']); ?></strong>
                                    <?php if (!empty($n['description'])): ?>
                                        <div style="font-size: 12px; color: #666666; margin-top: 2px;">
                                            <?php echo htmlspecialchars($n['description']); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($n['subject']); ?></td>
                                <td><?php echo htmlspecialchars($n['course']); ?></td>
                                <td><?php echo htmlspecialchars($n['semester']); ?></td>
                                <td><?php echo htmlspecialchars($n['uploader_name']); ?></td>
                                <td><?php echo (int)$n['downloads']; ?></td>
                                <td>
                                    <a href="download.php?id=<?php echo (int)$n['id']; ?>" class="btn btn-primary btn-sm">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <footer class="footer">
        <p>NOTEHUB | Notes Sharing Website</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
