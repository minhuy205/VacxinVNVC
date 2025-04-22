<?php
session_start();
$base_url = '/websitevacxin/';

// Xóa token ghi nhớ đăng nhập nếu có
if (isset($_COOKIE['remember_token'])) {
    // Kết nối database
    require_once 'db_connect.php';
    
    $token = $_COOKIE['remember_token'];
    
    // Xóa token khỏi database
    $sql = "DELETE FROM remember_tokens WHERE token = '$token'";
    $conn->query($sql);
    
    // Xóa cookie
    setcookie('remember_token', '', time() - 3600, '/');
    
    $conn->close();
}

// Lưu vai trò trước khi xóa session
$was_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Xóa tất cả các biến session
$_SESSION = array();

// Xóa cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hủy session
session_destroy();

// Chuyển hướng đến trang đăng nhập hoặc trang chủ tùy thuộc vào vai trò trước đó
if ($was_admin) {
    header("Location: login.php");
} else {
    header("Location: ../index.php");
}
exit;
?>
