<?php
session_start();
$base_url = '/websitevacxin/';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../menu/login.php");
    exit;
}

// Kết nối database
require_once('../menu/db_connect.php'); 

// Lấy số liệu thống kê
// Tổng số người dùng
$users_sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$users_result = $conn->query($users_sql);
$total_users = $users_result->fetch_assoc()['total'];

// Tổng số vắc xin
$vaccines_sql = "SELECT COUNT(*) as total FROM vaccines";
$vaccines_result = $conn->query($vaccines_sql);
$total_vaccines = $vaccines_result->fetch_assoc()['total'];

// Tổng số đơn đặt lịch
$bookings_sql = "SELECT COUNT(*) as total FROM bookings";
$bookings_result = $conn->query($bookings_sql);
$total_bookings = $bookings_result ? $bookings_result->fetch_assoc()['total'] : 0;

// Đơn đặt lịch mới nhất
$recent_bookings_sql = "SELECT b.*, u.full_name FROM bookings b 
                        JOIN users u ON b.user_id = u.id 
                        ORDER BY b.created_at DESC LIMIT 5";
$recent_bookings_result = $conn->query($recent_bookings_sql);
$recent_bookings = [];
if ($recent_bookings_result) {
    while ($row = $recent_bookings_result->fetch_assoc()) {
        $recent_bookings[] = $row;
    }
}

$page_title = "Tổng quan";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VNVC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #2a388f;
            --secondary-color: #f39021;
            --dark-color: #1a2570;
            --light-color: #f5f5f5;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --text-color: #333;
            --light-text: #666;
            --white: #fff;
            --border-radius: 8px;
            --box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: var(--white);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: var(--transition);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 14px;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: var(--white);
            text-decoration: none;
            transition: var(--transition);
            border-left: 4px solid transparent;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--secondary-color);
        }

        .menu-item i {
            margin-right: 10px;
            font-size: 18px;
            width: 25px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .sidebar-footer a {
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            opacity: 0.8;
            transition: var(--transition);
        }

        .sidebar-footer a:hover {
            opacity: 1;
        }

        .sidebar-footer i {
            margin-right: 10px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: var(--transition);
        }

        .header {
            background-color: var(--white);
            padding: 15px 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
            color: var(--primary-color);
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-info .user-name {
            font-weight: bold;
        }

        .user-info .user-role {
            font-size: 12px;
            color: var(--light-text);
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            display: flex;
            align-items: center;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 15px;
            font-size: 24px;
        }

        .card-icon.primary {
            background-color: rgba(42, 56, 143, 0.1);
            color: var(--primary-color);
        }

        .card-icon.success {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }

        .card-icon.warning {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }

        .card-icon.danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }

        .card-info h3 {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 5px;
        }

        .card-info p {
            font-size: 24px;
            font-weight: bold;
            color: var(--text-color);
        }

        /* Content */
        .content {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
            margin-bottom: 20px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .content-header h2 {
            font-size: 18px;
            color: var(--primary-color);
        }

        /* Tables */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        table th {
            background-color: #f9f9f9;
            font-weight: 600;
            color: var(--primary-color);
        }

        table tr:hover {
            background-color: #f5f5f5;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }

        .status-confirmed {
            background-color: rgba(41, 128, 185, 0.1);
            color: #2980b9;
        }

        .status-completed {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }

        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: visible;
            }

            .sidebar-header h2, .sidebar-header p, .menu-item span, .sidebar-footer span {
                display: none;
            }

            .menu-item {
                justify-content: center;
                padding: 15px;
            }

            .menu-item i {
                margin-right: 0;
                font-size: 20px;
            }

            .main-content {
                margin-left: 70px;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>VNVC Admin</h2>
            <p>Quản lý hệ thống</p>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item active">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tổng quan</span>
            </a>
            <a href="vaccines.php" class="menu-item">
                <i class="fas fa-syringe"></i>
                <span>Quản lý vắc xin</span>
            </a>
            <a href="users.php" class="menu-item">
                <i class="fas fa-users"></i>
                <span>Quản lý người dùng</span>
            </a>
            <a href="bookings.php" class="menu-item">
                <i class="fas fa-calendar-check"></i>
                <span>Quản lý đặt lịch</span>
            </a>
            <a href="../index.php" class="menu-item">
                <i class="fas fa-home"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
        <div class="sidebar-footer">
            <a href="../menu/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
<!-- Thêm nút quay lại trang chủ vào phần header -->
<div class="header">
    <h1>Tổng quan</h1>
    <div class="user-info">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=2a388f&color=fff" alt="User Avatar">
        <div>
            <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
            <div class="user-role">Quản trị viên</div>
        </div>
    </div>
</div>
        
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-info">
                    <h3>Tổng số người dùng</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-icon success">
                    <i class="fas fa-syringe"></i>
                </div>
                <div class="card-info">
                    <h3>Tổng số vắc xin</h3>
                    <p><?php echo $total_vaccines; ?></p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-icon warning">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-info">
                    <h3>Tổng số đặt lịch</h3>
                    <p><?php echo $total_bookings; ?></p>
                </div>
            </div>
        </div>
        
        <div class="content">
            <div class="content-header">
                <h2>Đơn đặt lịch gần đây</h2>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Ngày tiêm</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_bookings)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Không có đơn đặt lịch nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_bookings as $booking): ?>
                                <tr>
                                    <td>#<?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['full_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($booking['created_at'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?></td>
                                    <td><?php echo number_format($booking['total_amount'], 0, ',', '.'); ?> đ</td>
                                    <td>
                                        <?php 
                                        $status = $booking['status'];
                                        $status_class = '';
                                        $status_text = '';
                                        
                                        switch ($status) {
                                            case 'pending':
                                                $status_class = 'status-pending';
                                                $status_text = 'Chờ xác nhận';
                                                break;
                                            case 'confirmed':
                                                $status_class = 'status-confirmed';
                                                $status_text = 'Đã xác nhận';
                                                break;
                                            case 'completed':
                                                $status_class = 'status-completed';
                                                $status_text = 'Đã hoàn thành';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'status-cancelled';
                                                $status_text = 'Đã hủy';
                                                break;
                                            default:
                                                $status_class = 'status-pending';
                                                $status_text = 'Chờ xác nhận';
                                        }
                                        ?>
                                        <span class="status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
