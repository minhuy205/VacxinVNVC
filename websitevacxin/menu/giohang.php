<?php
session_start();
$base_url = '/websitevacxin/';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Kết nối database
require_once 'db_connect.php';

// Lấy thông tin người dùng
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của tôii - VNVC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/main.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/home.css">
    <style>
    .cart-container {
        max-width: 1000px;
        margin: 40px auto;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        padding: 30px;
        font-size: 22px;
    }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .cart-header h1 {
        color: #2a388f;
        font-size: 28px;
        margin: 0;
    }

    .cart-empty {
        text-align: center;
        padding: 50px 0;
    }

    .cart-empty i {
        font-size: 60px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .cart-empty p {
        font-size: 18px;
        color: #666;
        margin-bottom: 30px;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cart-table th {
        text-align: left;
        padding: 15px;
        background-color: #f5f5f5;
        color: #333;
        font-weight: bold;
    }

    .cart-table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .cart-item-name {
        font-weight: bold;
        color: #2a388f;
    }

    .cart-item-price {
        color: #f39021;
        font-weight: bold;
    }

    .cart-remove {
        color: #e74c3c;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .cart-remove:hover {
        color: #c0392b;
    }

    .cart-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
        font-size: 27px;
    }

    .cart-total {
        font-size: 20px;
        font-weight: bold;
    }

    .cart-total span {
        color: #f39021;
    }

    .cart-actions {
        display: flex;
        gap: 15px;
    }

    .cart-btn {
        padding: 12px 25px;
        border-radius: 5px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: inline-block;
    }

    .cart-btn-primary {
        background-color: #2a388f;
        color: white;
    }

    .cart-btn-primary:hover {
        background-color: #1a2570;
    }

    .cart-btn-secondary {
        background-color: #f5f5f5;
        color: #333;
    }

    .cart-btn-secondary:hover {
        background-color: #e0e0e0;
    }

    .booking-form {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #eee;
    }

    .booking-form h2 {
        color: #2a388f;
        margin-bottom: 20px;
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        flex: 1;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
    }

    .form-control:focus {
        border-color: #2a388f;
        outline: none;
    }

    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease;
        max-width: 300px;
    }

    .notification.success {
        background-color: #27ae60;
    }

    .notification.error {
        background-color: #e74c3c;
    }

    .notification.show {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
            gap: 15px;
        }

        .cart-actions {
            flex-direction: column;
            gap: 10px;
        }

        .cart-btn {
            width: 100%;
        }
    }
    </style>
</head>

<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Giỏ hàng của tôi</h1>
            <a href="<?php echo $base_url; ?>menu/danhmucvacxin.php" class="cart-btn cart-btn-secondary">
                <i class="fas fa-arrow-left"></i> Tiếp tục chọn vắc xin
            </a>
        </div>

        <div id="cart-content">
            <!-- Nội dung giỏ hàng sẽ được cập nhật bằng JavaScript -->
            <div class="cart-empty">
                <i class="fas fa-shopping-cart"></i>
                <p>Giỏ hàng của bạn đang trống</p>
                <a href="<?php echo $base_url; ?>menu/danhmucvacxin.php" class="cart-btn cart-btn-primary">Chọn vắc xin
                    ngay</a>
            </div>
        </div>

        <div id="booking-form" class="booking-form" style="display: none;">
            <h2>Thông tin đặt lịch tiêm</h2>
            <form id="checkout-form" method="POST" action="xu_ly_dat_lich.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="fullname">Họ và tên</label>
                        <input type="text" id="fullname" name="fullname" class="form-control"
                            value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Số điện thoại</label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                            value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="booking_date">Ngày tiêm</label>
                        <input type="date" id="booking_date" name="booking_date" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ</label>
                    <input type="text" id="address" name="address" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="notes">Ghi chú</label>
                    <textarea id="notes" name="notes" class="form-control" rows="4"></textarea>
                </div>

                <input type="hidden" id="cart_data" name="cart_data" value="">

                <div class="cart-footer" style="font-size: 50px;">
                    <div class="cart-total">
                        Tổng cộng: <span id="booking-total">0 đ</span>
                    </div>
                    <div class="cart-actions">
                        <button type="button" class="cart-btn cart-btn-secondary" onclick="hideBookingForm()">Quay
                            lại</button>
                        <button type="submit" class="cart-btn cart-btn-primary">Xác nhận đặt lịch</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Định dạng tiền tệ
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }

    // Lấy giỏ hàng từ localStorage
    function getCart() {
        return JSON.parse(localStorage.getItem('vaccineCart')) || [];
    }

    // Cập nhật hiển thị giỏ hàng
    function updateCartDisplay() {
        const cart = getCart();
        const cartContent = document.getElementById('cart-content');

        if (cart.length === 0) {
            cartContent.innerHTML = `
                    <div class="cart-empty">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Giỏ hàng của bạn đang trống</p>
                        <a href="<?php echo $base_url; ?>menu/danhmucvacxin.php" class="cart-btn cart-btn-primary">Chọn vắc xin ngay</a>
                    </div>
                `;
            return;
        }

        let total = 0;
        let tableHTML = `
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Vắc xin</th>
                            <th>Giá</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
            `;

        cart.forEach(item => {
            total += item.price * item.quantity;
            tableHTML += `
                    <tr>
                        <td class="cart-item-name">${item.name}</td>
                        <td class="cart-item-price">${formatCurrency(item.price)}</td>
                        <td><i class="fas fa-trash cart-remove" onclick="removeFromCart(${item.id})"></i></td>
                    </tr>
                `;
        });

        tableHTML += `
                    </tbody>
                </table>
                <div class="cart-footer">
                    <div class="cart-total">
                        Tổng cộng: <span>${formatCurrency(total)}</span>
                    </div>
                    <div class="cart-actions">
                        <button class="cart-btn cart-btn-secondary" onclick="clearCart()">Xóa giỏ hàng</button>
                        <button class="cart-btn cart-btn-primary" onclick="showBookingForm()">Đặt lịch tiêm</button>
                    </div>
                </div>
            `;

        cartContent.innerHTML = tableHTML;

        // Cập nhật tổng tiền trong form đặt lịch
        const bookingTotal = document.getElementById('booking-total');
        if (bookingTotal) {
            bookingTotal.textContent = formatCurrency(total);
        }

        // Cập nhật dữ liệu giỏ hàng trong form
        const cartDataInput = document.getElementById('cart_data');
        if (cartDataInput) {
            cartDataInput.value = JSON.stringify(cart);
        }
    }

    // Xóa vắc xin khỏi giỏ hàng
    function removeFromCart(vaccineId) {
        let cart = getCart();

        // Lấy tên vắc xin trước khi xóa
        const vaccine = cart.find(item => item.id === vaccineId);
        const vaccineName = vaccine ? vaccine.name : '';

        cart = cart.filter(item => item.id !== vaccineId);
        localStorage.setItem('vaccineCart', JSON.stringify(cart));

        // Cập nhật hiển thị giỏ hàng
        updateCartDisplay();

        // Hiển thị thông báo
        showNotification(`Đã xóa ${vaccineName} khỏi giỏ hàng`, 'success');

        // Xóa vắc xin khỏi cơ sở dữ liệu
        removeVaccineFromDatabase(vaccineId);
    }

    // Xóa vắc xin khỏi cơ sở dữ liệu
    function removeVaccineFromDatabase(vaccineId) {
        fetch('<?php echo $base_url; ?>menu/remove_vaccine.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `vaccine_id=${vaccineId}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Lỗi khi xóa vắc xin:', data.message);
                }
            })
            .catch(error => {
                console.error('Lỗi khi gửi yêu cầu:', error);
            });
    }

    // Xóa toàn bộ giỏ hàng
    function clearCart() {
        if (confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?')) {
            localStorage.setItem('vaccineCart', JSON.stringify([]));
            updateCartDisplay();

            // Hiển thị thông báo
            showNotification('Đã xóa toàn bộ giỏ hàng', 'success');

            // Xóa tất cả vắc xin khỏi cơ sở dữ liệu
            clearCartInDatabase();
        }
    }

    // Xóa tất cả vắc xin khỏi cơ sở dữ liệu
    function clearCartInDatabase() {
        fetch('<?php echo $base_url; ?>menu/clear_cart.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Lỗi khi xóa giỏ hàng:', data.message);
                }
            })
            .catch(error => {
                console.error('Lỗi khi gửi yêu cầu:', error);
            });
    }

    // Hiển thị form đặt lịch
    function showBookingForm() {
        document.getElementById('cart-content').style.display = 'none';
        document.getElementById('booking-form').style.display = 'block';

        // Đặt ngày mặc định là ngày mai
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('booking_date').min = tomorrow.toISOString().split('T')[0];
        document.getElementById('booking_date').value = tomorrow.toISOString().split('T')[0];
    }

    // Ẩn form đặt lịch
    function hideBookingForm() {
        document.getElementById('cart-content').style.display = 'block';
        document.getElementById('booking-form').style.display = 'none';
    }

    // Hiển thị thông báo
    function showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = `notification ${type}`;

        // Hiển thị thông báo
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);

        // Ẩn thông báo sau 3 giây
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }

    // Khởi tạo khi trang được tải
    document.addEventListener('DOMContentLoaded', function() {
        updateCartDisplay();

        // Xử lý form đặt lịch
        const checkoutForm = document.getElementById('checkout-form');
        if (checkoutForm) {
            checkoutForm.addEventListener('submit', function(event) {
                const cart = getCart();
                if (cart.length === 0) {
                    event.preventDefault();
                    showNotification('Giỏ hàng của bạn đang trống', 'error');
                }
            });
        }
    });
    </script>
</body>

</html>