<?php
session_start();
$base_url = '/websitevacxin/';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kiểm tra phương thức gửi form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: giohang.php");
    exit;
}

// Kết nối database
require_once 'db_connect.php';

// Lấy thông tin từ form
$user_id = $_SESSION['user_id'];
$fullname = sanitize($conn, $_POST['fullname']);
$phone = sanitize($conn, $_POST['phone']);
$email = sanitize($conn, $_POST['email']);
$booking_date = sanitize($conn, $_POST['booking_date']);
$address = sanitize($conn, $_POST['address']);
$notes = sanitize($conn, $_POST['notes']);
$cart_data = $_POST['cart_data'];

// Giải mã dữ liệu giỏ hàng
$cart = json_decode($cart_data, true);

if (empty($cart)) {
    header("Location: giohang.php");
    exit;
}

// Tính tổng tiền
$total_amount = 0;
foreach ($cart as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Tạo bảng bookings nếu chưa tồn tại
$check_table = "SHOW TABLES LIKE 'bookings'";
$table_exists = $conn->query($check_table);

if ($table_exists->num_rows == 0) {
    $create_table = "CREATE TABLE bookings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        fullname VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL,
        booking_date DATE NOT NULL,
        address TEXT NOT NULL,
        notes TEXT,
        total_amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        created_at DATETIME NOT NULL,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    $conn->query($create_table);
}

// Tạo bảng booking_items nếu chưa tồn tại
$check_items_table = "SHOW TABLES LIKE 'booking_items'";
$items_table_exists = $conn->query($check_items_table);

if ($items_table_exists->num_rows == 0) {
    $create_items_table = "CREATE TABLE booking_items (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        booking_id INT(11) NOT NULL,
        vaccine_id INT(11) NOT NULL,
        vaccine_name VARCHAR(255) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        quantity INT(11) NOT NULL,
        FOREIGN KEY (booking_id) REFERENCES bookings(id)
    )";
    $conn->query($create_items_table);
}

// Thêm đơn đặt lịch vào cơ sở dữ liệu
$current_time = date('Y-m-d H:i:s');
$sql = "INSERT INTO bookings (user_id, fullname, phone, email, booking_date, address, notes, total_amount, created_at) 
        VALUES ('$user_id', '$fullname', '$phone', '$email', '$booking_date', '$address', '$notes', $total_amount, '$current_time')";

if ($conn->query($sql)) {
    $booking_id = $conn->insert_id;
    
    // Thêm các mục vắc xin vào cơ sở dữ liệu
    foreach ($cart as $item) {
        $vaccine_id = (int)$item['id'];
        $vaccine_name = sanitize($conn, $item['name']);
        $price = (float)$item['price'];
        $quantity = (int)$item['quantity'];
        
        $item_sql = "INSERT INTO booking_items (booking_id, vaccine_id, vaccine_name, price, quantity) 
                    VALUES ($booking_id, $vaccine_id, '$vaccine_name', $price, $quantity)";
        $conn->query($item_sql);
    }
    
    // Xóa giỏ hàng
    echo "<script>localStorage.setItem('vaccineCart', JSON.stringify([]));</script>";
    
    // Chuyển hướng đến trang xác nhận
    header("Location: dat_lich_thanh_cong.php?id=$booking_id");
    exit;
} else {
    // Xử lý lỗi
    header("Location: giohang.php?error=1");
    exit;
}

$conn->close();
?>