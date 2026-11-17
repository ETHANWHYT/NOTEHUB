<?php
// ============================================================================
// download.php - Simple File Download Handler
// ============================================================================

require_once 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$note_id = (int)$_GET['id'];

// Query note details using MySQLi prepared statement
$stmt = mysqli_prepare($conn, "SELECT file_name, title FROM notes WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $note_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$note = mysqli_fetch_assoc($result);

if ($note) {
    $file_path = __DIR__ . '/uploads/' . basename($note['file_name']);

    if (file_exists($file_path)) {
        // Increment download counter
        $up_stmt = mysqli_prepare($conn, "UPDATE notes SET downloads = downloads + 1 WHERE id = ?");
        mysqli_stmt_bind_param($up_stmt, "i", $note_id);
        mysqli_stmt_execute($up_stmt);

        // Stream file download to browser
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($note['file_name']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        readfile($file_path);
        exit;
    }
}

// Redirect if note or file does not exist
header("Location: index.php?msg=not_found");
exit;
?>