<?php
session_start();
$base_url = '/websitevacxin/';

// Kiểm tra đăng nhập và vai trò
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Kết nối database
require_once('../menu/db_connect.php'); 

// Xử lý cập nhật trạng thái đơn đặt lịch
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $id = (int)$_POST['id'];
        $status = sanitize($conn, $_POST['status']);
        
        $sql = "UPDATE bookings SET status = '$status' WHERE id = $id";
        
        if ($conn->query($sql)) {
            $message = 'Cập nhật trạng thái đơn đặt lịch thành công!';
            $messageType = 'success';
        } else {
            $message = 'Lỗi: ' . $conn->error;
            $messageType = 'error';
        }
    }
}

// Lấy danh sách đơn đặt lịch
$sql = "SELECT b.*, u.full_name, u.email, u.username 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.created_at DESC";
$result = $conn->query($sql);
$bookings = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$page_title = "Quản lý đặt lịch";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đặt lịch - VNVC</title>
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

        /* Message */
        .message {
            padding: 10px 15px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
        }

        .message.success {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .message.error {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
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

        /* Status Badge */
        .status {
            display: inline-block;
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

        .action-btn.view {
            background-color: var(--primary-color);
        }

        .action-btn.view:hover {
            background-color: var(--dark-color);
        }

        .action-btn.edit {
            background-color: var(--warning-color);
        }

        .action-btn.edit:hover {
            background-color: #e67e22;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: var(--white);
            margin: 50px auto;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            width: 80%;
            max-width: 600px;
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .modal-header h2 {
            font-size: 18px;
            color: var(--primary-color);
        }

        .close {
            color: var(--light-text);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: var(--text-color);
        }

        .modal-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Forms */
        .form-group {
            margin-bottom: 15px;
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

            .modal-content {
                width: 95%;
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
            <a href="dashboard.php" class="menu-item">
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
            <a href="bookings.php" class="menu-item active">
                <i class="fas fa-calendar-check"></i>
                <span>Quản lý đặt lịch</span>
            </a>
        </div>
        <div class="sidebar-footer">
            <a href="../logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Đăng xuất</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Thêm nút quay lại trang chủ vào phần header -->
<div class="header">
    <h1>Quản lý đặt lịch</h1>
    <div class="user-info">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['full_name']); ?>&background=2a388f&color=fff" alt="User Avatar">
        <div>
            <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
            <div class="user-role">Quản trị viên</div>
        </div>
    </div>
</div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="content">
            <div class="content-header">
                <h2>Danh sách đơn đặt lịch</h2>
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
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Không có đơn đặt lịch nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
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
                                    <td>
                                        <div class="actions">
                                            <a href="javascript:void(0)" class="action-btn view" onclick="openViewModal(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="action-btn edit" onclick="openEditModal(<?php echo $booking['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- View Booking Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Chi tiết đơn đặt lịch</h2>
                <span class="close" onclick="closeModal('viewModal')">&times;</span>
            </div>
            <div id="booking-details">
                <!-- Chi tiết đơn đặt lịch sẽ được hiển thị ở đây -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="closeModal('viewModal')">Đóng</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Booking Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Cập nhật trạng thái đơn đặt lịch</h2>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" id="edit_id" name="id" value="">
                
                <div class="form-group">
                    <label for="edit_status">Trạng thái</label>
                    <select id="edit_status" name="status" class="form-control" required>
                        <option value="pending">Chờ xác nhận</option>
                        <option value="confirmed">Đã xác nhận</option>
                        <option value="completed">Đã hoàn thành</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Hủy</button>
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Mở modal xem chi tiết đơn đặt lịch
        function openViewModal(id) {
            // Lấy thông tin đơn đặt lịch từ dữ liệu
            <?php
            echo "const bookings = " . json_encode($bookings) . ";";
            ?>
            
            const booking = bookings.find(b => b.id == id);
            
            if (booking) {
                let statusText = '';
                let statusClass = '';
                
                switch (booking.status) {
                    case 'pending':
                        statusClass = 'status-pending';
                        statusText = 'Chờ xác nhận';
                        break;
                    case 'confirmed':
                        statusClass = 'status-confirmed';
                        statusText = 'Đã xác nhận';
                        break;
                    case 'completed':
                        statusClass = 'status-completed';
                        statusText = 'Đã hoàn thành';
                        break;
                    case 'cancelled':
                        statusClass = 'status-cancelled';
                        statusText = 'Đã hủy';
                        break;
                    default:
                        statusClass = 'status-pending';
                        statusText = 'Chờ xác nhận';
                }
                
                const createdDate = new Date(booking.created_at);
                const bookingDate = new Date(booking.booking_date);
                
                const detailsHTML = `
                    <div style="margin-bottom: 20px;">
                        <h3 style="margin-bottom: 15px; color: #2a388f;">Thông tin đơn đặt lịch #${booking.id}</h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                            <div>
                                <p><strong>Khách hàng:</strong> ${booking.full_name}</p>
                                <p><strong>Email:</strong> ${booking.email}</p>
                                <p><strong>Tên đăng nhập:</strong> ${booking.username}</p>
                            </div>
                            <div>
                                <p><strong>Ngày đặt:</strong> ${createdDate.toLocaleDateString('vi-VN')}</p>
                                <p><strong>Ngày tiêm:</strong> ${bookingDate.toLocaleDateString('vi-VN')}</p>
                                <p><strong>Trạng thái:</strong> <span class="status ${statusClass}">${statusText}</span></p>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <p><strong>Địa chỉ:</strong> ${booking.address || 'Không có'}</p>
                            <p><strong>Ghi chú:</strong> ${booking.notes || 'Không có'}</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h4 style="margin-bottom: 10px; color: #2a388f;">Thông tin thanh toán</h4>
                            <p><strong>Tổng tiền:</strong> ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(booking.total_amount)}</p>
                        </div>
                    </div>
                `;
                
                document.getElementById('booking-details').innerHTML = detailsHTML;
                document.getElementById('viewModal').style.display = 'block';
            }
        }
        
        // Mở modal chỉnh sửa đơn đặt lịch
        function openEditModal(id) {
            // Lấy thông tin đơn đặt lịch từ dữ liệu
            <?php
            echo "const bookings = " . json_encode($bookings) . ";";
            ?>
            
            const booking = bookings.find(b => b.id == id);
            
            if (booking) {
                document.getElementById('edit_id').value = booking.id;
                document.getElementById('edit_status').value = booking.status;
                
                document.getElementById('editModal').style.display = 'block';
            }
        }
        
        // Đóng modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Đóng modal khi click bên ngoài
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
