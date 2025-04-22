<?php
// Thông tin kết nối database
$db_host = 'localhost';
$db_user = 'root';
$db_password = ''; // Để trống vì không có mật khẩu
$db_name = 'vnvc';

// Tạo kết nối
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Đặt charset
$conn->set_charset("utf8mb4");

// Hàm để tránh SQL injection
function sanitize($conn, $input) {
    if (is_array($input)) {
        foreach ($input as $key => $value) {
            $input[$key] = sanitize($conn, $value);
        }
        return $input;
    }
    return $conn->real_escape_string($input);
}
?>