<?php
// ============================================================================
// logout.php - End User Session
// ============================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset and destroy session
$_SESSION = [];
session_destroy();

// Redirect to homepage
header("Location: index.php");
exit;
