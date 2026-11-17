<?php
// ============================================================================
// upload.php - Upload Notes
// ============================================================================

require_once 'db.php';

// Authentication guard
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$title = '';
$subject = '';
$course = '';
$semester = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $subject     = trim($_POST['subject'] ?? '');
    $course      = trim($_POST['course'] ?? '');
    $semester    = trim($_POST['semester'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($subject) || empty($course) || empty($semester)) {
        $error = "Please fill in all required fields.";
    } elseif (!isset($_FILES['note_file']) || $_FILES['note_file']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Please select a file to upload.";
    } else {
        $file = $_FILES['note_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = "File upload error occurred.";
        } else {
            $max_size = 5 * 1024 * 1024; // 5 MB
            if ($file['size'] > $max_size) {
                $error = "File exceeds maximum size of 5 MB.";
            } else {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'doc', 'docx'];

                if (!in_array($ext, $allowed)) {
                    $error = "Only PDF, DOC, and DOCX files are allowed.";
                } else {
                    $upload_dir = __DIR__ . '/uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $safe_filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    $target_path = $upload_dir . $safe_filename;

                    if (move_uploaded_file($file['tmp_name'], $target_path)) {
                        $user_id = (int)$_SESSION['user_id'];
                        $query = "INSERT INTO notes (user_id, title, description, subject, course, semester, file_name, downloads) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
                        $stmt = mysqli_prepare($conn, $query);
                        mysqli_stmt_bind_param($stmt, "issssss", $user_id, $title, $description, $subject, $course, $semester, $safe_filename);

                        if (mysqli_stmt_execute($stmt)) {
                            header("Location: index.php?msg=uploaded");
                            exit;
                        } else {
                            @unlink($target_path);
                            $error = "Failed to save note details in database.";
                        }
                    } else {
                        $error = "Failed to save file on server.";
                    }
                }
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
    <title>Upload Notes - NOTEHUB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">NOTEHUB</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="browse_notes.php">Browse Notes</a></li>
                <li><span class="user-text"><?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </header>

    <main class="container">
        <div class="form-box" style="max-width: 450px;">
            <h2>Upload Notes</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Note Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>

                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-control" value="<?php echo htmlspecialchars($subject); ?>" required>
                </div>

                <div class="form-group">
                    <label for="course">Course</label>
                    <input type="text" id="course" name="course" class="form-control" placeholder="e.g. BCA, B.Sc, B.Tech" value="<?php echo htmlspecialchars($course); ?>" required>
                </div>

                <div class="form-group">
                    <label for="semester">Semester</label>
                    <input type="text" id="semester" name="semester" class="form-control" placeholder="e.g. 1st Sem, 2nd Sem" value="<?php echo htmlspecialchars($semester); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description (optional)</label>
                    <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="note_file">File (PDF, DOC, DOCX - Max 5 MB)</label>
                    <input type="file" id="note_file" name="note_file" class="form-control" accept=".pdf,.doc,.docx" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Upload</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>NOTEHUB | Notes Sharing Website</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
