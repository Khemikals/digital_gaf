<?php
// Start the session to gain access to current applicant data
session_start();

// Unset all session variables to clear applicant data
$_SESSION = array();

// Destroy the session cookie to fully clean the browser state
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Permanently destroy the session on the server
session_destroy();

// Redirect the applicant back to the portal homepage
header("Location: index.php");
exit();
?>