<?php
session_start();
$base_url = '/websitevacxin/';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kết nối database
require_once('db_connect.php');

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    // Nếu không tìm thấy thông tin người dùng, đăng xuất và chuyển hướng đến trang đăng nhập
    session_destroy();
    header("Location: login.php?error=user_not_found");
    exit;
}

// Lấy lịch sử đặt lịch
$bookings_sql = "SELECT * FROM bookings WHERE user_id = $user_id ORDER BY created_at DESC";
$bookings_result = $conn->query($bookings_sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - VNVC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/main.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/home.css">
    <style>
    .profile-container {
        max-width: 1000px;
        margin: 40px auto;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
        font-size: 20px;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .profile-header h1 {
        color: #2a388f;
        font-size: 28px;
        margin: 0;
    }

    .back-btn {
        display: inline-block;
        padding: 8px 15px;
        background-color: #2a388f;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        background-color: #1a2570;
    }

    .profile-tabs {
        display: flex;
        border-bottom: 1px solid #eee;
        margin-bottom: 30px;
    }

    .profile-tab {
        padding: 15px 20px;
        font-weight: bold;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .profile-tab.active {
        color: #2a388f;
        border-bottom-color: #2a388f;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .profile-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-item {
        margin-bottom: 15px;
    }

    .info-label {
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
    }

    .info-value {
        color: #333;
    }

    .booking-history {
        margin-top: 30px;
    }

    .booking-table {
        width: 100%;
        border-collapse: collapse;
    }

    .booking-table th {
        text-align: left;
        padding: 12px;
        background-color: #f5f5f5;
        color: #333;
    }

    .booking-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .booking-status {
        font-weight: bold;
    }

    .status-pending {
        color: #f39c12;
    }

    .status-confirmed {
        color: #2980b9;
    }

    .status-completed {
        color: #27ae60;
    }

    .status-cancelled {
        color: #e74c3c;
    }

    .booking-actions {
        display: flex;
        gap: 10px;
    }

    .booking-btn {
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .booking-btn-primary {
        background-color: #2a388f;
        color: white;
    }

    .booking-btn-primary:hover {
        background-color: #1a2570;
    }

    .booking-btn-danger {
        background-color: #e74c3c;
        color: white;
    }

    .booking-btn-danger:hover {
        background-color: #c0392b;
    }

    @media (max-width: 768px) {
        .profile-info {
            grid-template-columns: 1fr;
        }

        .profile-tabs {
            flex-wrap: wrap;
        }

        .profile-tab {
            flex: 1 0 50%;
            text-align: center;
        }
    }
    </style>
</head>

<body>
    <div class="profile-container">
        <!-- Thêm nút quay lại trang chủ vào phần header -->
        <div class="profile-header">
            <h1>Thông tin cá nhân</h1>
            <a href="../menu/noidung.php" class="back-btn"><i class="fas fa-arrow-left"></i> Quay lại
                trang chủ</a>
        </div>

        <div class="profile-tabs">
            <div class="profile-tab active" onclick="showTab('info')">Thông tin cá nhân</div>
            <div class="profile-tab" onclick="showTab('bookings')">Lịch sử đặt lịch</div>
        </div>

        <div id="info-tab" class="tab-content active">
            <div class="profile-info">
                <div class="info-item">
                    <div class="info-label">Họ và tên</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tên đăng nhập</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ngày tham gia</div>
                    <div class="info-value">
                        <?php echo isset($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : 'Không có thông tin'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div id="bookings-tab" class="tab-content">
            <div class="booking-history">
                <h2>Lịch sử đặt lịch tiêm</h2>

                <?php if (!$bookings_result || $bookings_result->num_rows == 0): ?>
                <p>Bạn chưa có lịch đặt tiêm nào.</p>
                <?php else: ?>
                <table class="booking-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Ngày tiêm</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $booking['id']; ?></td>
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
                                <span
                                    class="booking-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td>
                                <div class="booking-actions">
                                    <a href="dat_lich_thanh_cong.php?id=<?php echo $booking['id']; ?>"
                                        class="booking-btn booking-btn-primary">Xem chi tiết</a>
                                    <?php if ($status == 'pending'): ?>
                                    <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>"
                                        class="booking-btn booking-btn-danger"
                                        onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt lịch này?')">Hủy</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function showTab(tabId) {
        // Ẩn tất cả các tab
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Bỏ active tất cả các nút tab
        document.querySelectorAll('.profile-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Hiển thị tab được chọn
        document.getElementById(tabId + '-tab').classList.add('active');

        // Active nút tab tương ứng
        document.querySelector(`.profile-tab[onclick="showTab('${tabId}')"]`).classList.add('active');
    }
    </script>
</body>

</html>