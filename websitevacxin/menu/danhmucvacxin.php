<?php
session_start();
$base_url = '/websitevacxin/';

// Kết nối database
require_once 'db_connect.php';

// Lấy danh mục được chọn (nếu có)
$selected_category = isset($_GET['category']) ? sanitize($conn, $_GET['category']) : '';

// Lấy danh sách vắc xin theo danh mục
$vaccines = [];
if (!empty($selected_category)) {
    $sql = "SELECT * FROM vaccines WHERE category = '$selected_category'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $vaccines[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Mục Vắc Xin - VNVC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/main.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/home.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .section-title h2 {
            color: #2a388f;
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .section-title p {
            color: #666;
            font-size: 16px;
        }
        
        .category-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .category-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        
        .category-icon {
            font-size: 40px;
            color: #2a388f;
            margin-bottom: 15px;
        }
        
        .category-card h3 {
            color: #333;
            font-size: 16px;
            margin: 0;
        }
        
        .vaccine-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .vaccine-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .vaccine-header {
            background-color: #2a388f;
            color: white;
            padding: 15px;
        }
        
        .vaccine-name {
            margin: 0;
            font-size: 18px;
        }
        
        .vaccine-body {
            padding: 15px;
        }
        
        .vaccine-info {
            margin-bottom: 15px;
        }
        
        .vaccine-info p {
            margin: 8px 0;
            font-size: 15px;
            line-height: 1.5;
        }
        
        .vaccine-info-label {
            font-weight: bold;
            color: #2a388f;
            display: inline-block;
            width: 120px;
        }
        
        .vaccine-price {
            color: #f39021;
            font-weight: bold;
            font-size: 20px;
            margin: 15px 0;
            text-align: center;
        }
        
        .vaccine-btn {
            background-color: #2a388f;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 15px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 16px;
        }
        
        .vaccine-btn:hover {
            background-color: #1a2570;
        }
        
        .vaccine-btn.selected {
            background-color: #27ae60;
        }
        
        .no-vaccines {
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px;
            color: #666;
            font-size: 18px;
        }
        
        .category-title {
            margin-bottom: 20px;
            color: #2a388f;
            font-size: 28px;
            border-bottom: 2px solid #f39021;
            padding-bottom: 10px;
            text-align: center;
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
        
        .cart-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #2a388f;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
        }
        
        .cart-button:hover {
            background-color: #1a2570;
            transform: scale(1.1);
        }
        
        .cart-button i {
            font-size: 24px;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #f39021;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 12px;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .category-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .vaccine-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    
    <div class="container">
        <div class="section-title">
            <h2>DANH MỤC VẮC XIN</h2>
            <p>Chọn danh mục để xem các loại vắc xin phù hợp</p>
        </div>
        
        <div class="category-container">
            <a href="?category=Vắc xin cho trẻ em" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-baby"></i>
                </div>
                <h3>Vắc xin cho trẻ em</h3>
            </a>
            
            <a href="?category=Vắc xin cho trẻ em tiền học đường" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-child"></i>
                </div>
                <h3>Vắc xin cho trẻ em tiền học đường</h3>
            </a>
            
            <a href="?category=Vắc xin cho tuổi vị thành niên và thanh niên" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <h3>Vắc xin cho tuổi vị thành niên và thanh niên</h3>
            </a>
            
            <a href="?category=Vắc xin cho người trưởng thành" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <h3>Vắc xin cho người trưởng thành</h3>
            </a>
            
            <a href="?category=Vắc xin cho phụ nữ chuẩn bị trước mang thai" class="category-card">
                <div class="category-icon">
                    <i class="fas fa-female"></i>
                </div>
                <h3>Vắc xin cho phụ nữ chuẩn bị trước mang thai</h3>
            </a>
        </div>
        
        <?php if (!empty($selected_category)): ?>
            <h2 class="category-title" id="info-vx"><?php echo htmlspecialchars($selected_category); ?></h2>
            
            <div id="vaccine-list" class="vaccine-list">
                <?php if (empty($vaccines)): ?>
                    <p class="no-vaccines">Không có vắc xin nào trong danh mục này.</p>
                <?php else: ?>
                    <?php foreach ($vaccines as $vaccine): ?>
                        <div class="vaccine-card" data-id="<?php echo $vaccine['id']; ?>">
                            <div class="vaccine-header">
                                <h3 class="vaccine-name"><?php echo htmlspecialchars($vaccine['name']); ?></h3>
                            </div>
                            <div class="vaccine-body">
                                <div class="vaccine-info">
                                    <p><span class="vaccine-info-label">Loại vắc xin:</span> <?php echo htmlspecialchars($vaccine['name']); ?></p>
                                    <p><span class="vaccine-info-label">Nguồn gốc:</span> <?php echo htmlspecialchars($vaccine['origin']); ?></p>
                                    <p><span class="vaccine-info-label">Phòng bệnh:</span> <?php echo htmlspecialchars($vaccine['disease_prevented']); ?></p>
                                    <p><span class="vaccine-info-label">Danh mục:</span> <?php echo htmlspecialchars($vaccine['category']); ?></p>
                                </div>
                                <div class="vaccine-price"><?php echo number_format($vaccine['price'], 0, ',', '.'); ?> đ</div>
                                <button class="vaccine-btn" onclick="toggleVaccineSelection(<?php echo $vaccine['id']; ?>, '<?php echo addslashes($vaccine['name']); ?>', <?php echo $vaccine['price']; ?>)">
                                    CHỌN VẮC XIN NÀY
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="section-title">
                <p>Vui lòng chọn một danh mục vắc xin ở trên để xem danh sách vắc xin.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Nút giỏ hàng -->
    <a href="<?php echo $base_url; ?>menu/giohang.php" class="cart-button">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cart-count">0</span>
    </a>
    
    <!-- Thông báo -->
    <div id="notification" class="notification"></div>
    
    <script>
        // Khởi tạo giỏ hàng từ localStorage
        let cart = JSON.parse(localStorage.getItem('vaccineCart')) || [];
        
        // Cập nhật số lượng vắc xin trong giỏ hàng
        function updateCartCount() {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = cart.length;
            }
        }
        
        // Kiểm tra xem vắc xin đã có trong giỏ hàng chưa
        function isVaccineInCart(vaccineId) {
            return cart.some(item => item.id === vaccineId);
        }
        
        // Thêm hoặc xóa vắc xin khỏi giỏ hàng
        function toggleVaccineSelection(vaccineId, vaccineName, vaccinePrice) {
            <?php if (!isset($_SESSION['user_id'])): ?>
            // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
            window.location.href = '<?php echo $base_url; ?>menu/login.php';
            return;
            <?php endif; ?>
            
            const index = cart.findIndex(item => item.id === vaccineId);
            const button = document.querySelector(`.vaccine-card[data-id="${vaccineId}"] .vaccine-btn`);
            
            if (index === -1) {
                // Thêm vắc xin vào giỏ hàng
                cart.push({
                    id: vaccineId,
                    name: vaccineName,
                    price: vaccinePrice,
                    quantity: 1
                });
                
                // Cập nhật nút
                if (button) {
                    button.classList.add('selected');
                    button.textContent = 'ĐÃ CHỌN VẮC XIN NÀY';
                }
                
                // Hiển thị thông báo
                showNotification(`Đã thêm ${vaccineName} vào giỏ hàng`, 'success');
                
                // Lưu vắc xin vào cơ sở dữ liệu (nếu người dùng đã đăng nhập)
                <?php if (isset($_SESSION['user_id'])): ?>
                saveVaccineToDatabase(vaccineId);
                <?php endif; ?>
            } else {
                // Xóa vắc xin khỏi giỏ hàng
                cart.splice(index, 1);
                
                // Cập nhật nút
                if (button) {
                    button.classList.remove('selected');
                    button.textContent = 'CHỌN VẮC XIN NÀY';
                }
                
                // Hiển thị thông báo
                showNotification(`Đã xóa ${vaccineName} khỏi giỏ hàng`, 'success');
                
                // Xóa vắc xin khỏi cơ sở dữ liệu (nếu người dùng đã đăng nhập)
                <?php if (isset($_SESSION['user_id'])): ?>
                removeVaccineFromDatabase(vaccineId);
                <?php endif; ?>
            }
            
            // Lưu giỏ hàng vào localStorage
            localStorage.setItem('vaccineCart', JSON.stringify(cart));
            
            // Cập nhật số lượng vắc xin trong giỏ hàng
            updateCartCount();
        }
        
        // Lưu vắc xin vào cơ sở dữ liệu
        function saveVaccineToDatabase(vaccineId) {
            fetch('<?php echo $base_url; ?>menu/save_vaccine.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `vaccine_id=${vaccineId}`
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Lỗi khi lưu vắc xin:', data.message);
                }
            })
            .catch(error => {
                console.error('Lỗi khi gửi yêu cầu:', error);
            });
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
        
        // Hiển thị thông báo
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            if (!notification) return;
            
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
        
        // Cập nhật trạng thái nút khi trang được tải
        document.addEventListener('DOMContentLoaded', function() {
            // Cập nhật số lượng vắc xin trong giỏ hàng
            updateCartCount();
            
            // Cập nhật trạng thái nút cho các vắc xin đã có trong giỏ hàng
            const vaccineCards = document.querySelectorAll('.vaccine-card');
            vaccineCards.forEach(card => {
                const vaccineId = parseInt(card.dataset.id);
                const button = card.querySelector('.vaccine-btn');
                
                if (isVaccineInCart(vaccineId)) {
                    button.classList.add('selected');
                    button.textContent = 'ĐÃ CHỌN VẮC XIN NÀY';
                }
            });
        });
    </script>
</body>
</html>
