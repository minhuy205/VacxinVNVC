<?php
session_start();
$base_url = '/websitevacxin/';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: menu/login.php");
    exit;
}

// Lấy tên trang hiện tại
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - VNVC</title>
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

        .user-dropdown {
            position: relative;
            cursor: pointer;
        }

        .user-dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 200px;
            display: none;
            z-index: 1000;
        }

        .user-dropdown:hover .user-dropdown-menu {
            display: block;
        }

        .user-dropdown-menu a {
            display: block;
            padding: 10px 15px;
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .user-dropdown-menu a:hover {
            background-color: var(--light-color);
        }

        .user-dropdown-menu a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Content */
        .content {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 20px;
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

        /* Actions */
        .actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--white);
            text-decoration: none;
            transition: var(--transition);
        }

        .action-btn.edit {
            background-color: var(--warning-color);
        }

        .action-btn.edit:hover {
            background-color: #e67e22;
        }

        .action-btn.delete {
            background-color: var(--danger-color);
        }

        .action-btn.delete:hover {
            background-color: #c0392b;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-color);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(42, 56, 143, 0.1);
            outline: none;
        }

        .btn {
            padding: 10px 15px;
            border-radius: var(--border-radius);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--dark-color);
        }

        .btn-success {
            background-color: var(--success-color);
            color: var(--white);
        }

        .btn-success:hover {
            background-color: #219653;
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: var(--white);
        }

        .btn-warning:hover {
            background-color: #e67e22;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: var(--white);
        }

        .btn-danger:hover {
            background-color: #c0392b;
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
            <a href="admin/dashboard.php" class="menu-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tổng quan</span>
            </a>
            <a href="admin/users.php" class="menu-item <?php echo $current_page === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Quản lý người dùng</span>
            </a>
            <a href="admin/vaccines.php" class="menu-item <?php echo $current_page === 'vaccines' ? 'active' : ''; ?>">
                <i class="fas fa-syringe"></i>
                <span>Quản lý vắc xin</span>
            </a>
            <a href="admin/bookings.php" class="menu-item <?php echo $current_page === 'bookings' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i>
                <span>Quản lý đặt lịch</span>
            </a>
            <a href="index.php" class="menu-item">
                <i class="fas fa-home"></i>
                <span>Về trang chủ</span>
            </a>
        </div>
        <div class="sidebar-footer">
            <a href="menu/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
            <div class="user-dropdown">
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=2a388f&color=fff" alt="User Avatar">
                    <div>
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                        <div class="user-role">Quản trị viên</div>
                    </div>
                </div>
                <div class="user-dropdown-menu">
                    <a href="admin/profile.php"><i class="fas fa-user-circle"></i> Thông tin cá nhân</a>
                    <a href="admin/settings.php"><i class="fas fa-cog"></i> Cài đặt</a>
                    <a href="menu/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                </div>
            </div>
        </div>
        
        <div class="content">
            <!-- Nội dung trang sẽ được đặt ở đây -->
