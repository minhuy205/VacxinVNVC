<?php
session_start();
$base_url = '/websitevacxin/';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kiểm tra ID đơn đặt lịch
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$booking_id = (int)$_GET['id'];

// Kết nối database
require_once 'db_connect.php';

// Lấy thông tin đơn đặt lịch
$sql = "SELECT * FROM bookings WHERE id = $booking_id AND user_id = {$_SESSION['user_id']}";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

$booking = $result->fetch_assoc();

// Lấy danh sách vắc xin đã đặt
$items_sql = "SELECT * FROM booking_items WHERE booking_id = $booking_id";
$items_result = $conn->query($items_sql);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lịch thành công - VNVC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/main.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/home.css">
    <style>
    .success-container {
        max-width: 800px;
        margin: 40px auto;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .success-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .success-icon {
        font-size: 80px;
        color: #27ae60;
        margin-bottom: 20px;
    }

    .success-title {
        font-size: 28px;
        color: #2a388f;
        margin-bottom: 10px;
    }

    .success-message {
        font-size: 18px;
        color: #666;
        margin-bottom: 30px;
    }

    .booking-details {
        background-color: #f9f9f9;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .booking-details h2 {
        color: #2a388f;
        font-size: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .booking-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .booking-info-item {
        margin-bottom: 15px;
    }

    .booking-info-label {
        font-weight: bold;
        color: #555;
        margin-bottom: 5px;
    }

    .booking-info-value {
        color: #333;
    }

    .vaccine-list {
        margin-top: 30px;
    }

    .vaccine-list h2 {
        color: #2a388f;
        font-size: 20px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .vaccine-table {
        width: 100%;
        border-collapse: collapse;
    }

    .vaccine-table th {
        text-align: left;
        padding: 12px;
        background-color: #f5f5f5;
        color: #333;
    }

    .vaccine-table td {
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .vaccine-price {
        color: #f39021;
        font-weight: bold;
    }

    .booking-total {
        text-align: right;
        margin-top: 20px;
        font-size: 18px;
        font-weight: bold;
    }

    .booking-total span {
        color: #f39021;
    }

    .success-actions {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
    }

    .success-btn {
        padding: 12px 25px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .success-btn-primary {
        background-color: #2a388f;
        color: white;
    }

    .success-btn-primary:hover {
        background-color: #1a2570;
    }

    .success-btn-secondary {
        background-color: #f5f5f5;
        color: #333;
    }

    .success-btn-secondary:hover {
        background-color: #e0e0e0;
    }

    @media (max-width: 768px) {
        .booking-info {
            grid-template-columns: 1fr;
        }

        .success-actions {
            flex-direction: column;
            gap: 10px;
        }

        .success-btn {
            display: block;
            text-align: center;
        }
    }
    </style>
</head>

<body>


    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="success-title">Đặt lịch tiêm thành công!</h1>
            <p class="success-message">Cảm ơn bạn đã đặt lịch tiêm vắc xin tại VNVC. Chúng tôi sẽ liên hệ với bạn trong
                thời gian sớm nhất.</p>
        </div>

        <div class="booking-details">
            <h2>Thông tin đặt lịch</h2>
            <div class="booking-info">
                <div class="booking-info-item">
                    <div class="booking-info-label">Mã đơn đặt lịch</div>
                    <div class="booking-info-value">#<?php echo $booking_id; ?></div>
                </div>
                <div class="booking-info-item">
                    <div class="booking-info-label">Ngày đặt lịch</div>
                    <div class="booking-info-value"><?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?>
                    </div>
                </div>
                <div class="booking-info-item">
                    <div class="booking-info-label">Họ và tên</div>
                    <div class="booking-info-value"><?php echo htmlspecialchars($booking['fullname']); ?></div>
                </div>
                <div class="booking-info-item">
                    <div class="booking-info-label">Số điện thoại</div>
                    <div class="booking-info-value"><?php echo htmlspecialchars($booking['phone']); ?></div>
                </div>
                <div class="booking-info-item">
                    <div class="booking-info-label">Email</div>
                    <div class="booking-info-value"><?php echo htmlspecialchars($booking['email']); ?></div>
                </div>
                <div class="booking-info-item">
                    <div class="booking-info-label">Ngày tiêm</div>
                    <div class="booking-info-value"><?php echo date('d/m/Y', strtotime($booking['booking_date'])); ?>
                    </div>
                </div>
                <div class="booking-info-item">
                    <div class="booking-info-label">Địa chỉ</div>
                    <div class="booking-info-value"><?php echo htmlspecialchars($booking['address']); ?></div>
                </div>
                <div class="booking-info-item">
                    <div class="booking-info-label">Trạng thái</div>
                    <div class="booking-info-value">
                        <?php 
                        $status = $booking['status'];
                        $status_text = '';
                        $status_color = '';
                        
                        switch ($status) {
                            case 'pending':
                                $status_text = 'Chờ xác nhận';
                                $status_color = '#f39c12';
                                break;
                            case 'confirmed':
                                $status_text = 'Đã xác nhận';
                                $status_color = '#2980b9';
                                break;
                            case 'completed':
                                $status_text = 'Đã hoàn thành';
                                $status_color = '#27ae60';
                                break;
                            case 'cancelled':
                                $status_text = 'Đã hủy';
                                $status_color = '#e74c3c';
                                break;
                            default:
                                $status_text = 'Chờ xác nhận';
                                $status_color = '#f39c12';
                        }
                        ?>
                        <span
                            style="color: <?php echo $status_color; ?>; font-weight: bold;"><?php echo $status_text; ?></span>
                    </div>
                </div>
            </div>

            <div class="vaccine-list">
                <h2>Danh sách vắc xin</h2>
                <table class="vaccine-table">
                    <thead>
                        <tr>
                            <th>Tên vắc xin</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $items_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['vaccine_name']); ?></td>
                            <td class="vaccine-price"><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td class="vaccine-price">
                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="booking-total">
                    Tổng cộng: <span><?php echo number_format($booking['total_amount'], 0, ',', '.'); ?> đ</span>
                </div>
            </div>
        </div>

        <div class="success-actions">
            <a href="<?php echo $base_url; ?>index.php" class="success-btn success-btn-secondary">
                <i class="fas fa-home"></i> Về trang chủ
            </a>
            <a href="profile.php" class="success-btn success-btn-primary">
                <i class="fas fa-history"></i> Xem lịch sử đặt lịch
            </a>
        </div>
    </div>


</body>

</html>