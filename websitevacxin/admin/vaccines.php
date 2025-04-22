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

// Kiểm tra xem cột description đã tồn tại trong bảng vaccines chưa
$check_column = "SHOW COLUMNS FROM vaccines LIKE 'description'";
$column_exists = $conn->query($check_column);

if ($column_exists->num_rows == 0) {
    // Thêm cột description vào bảng vaccines nếu chưa tồn tại
    $add_column = "ALTER TABLE vaccines ADD COLUMN description TEXT";
    $conn->query($add_column);
}

// Xử lý thêm vắc xin mới
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $name = sanitize($conn, $_POST['name']);
        $origin = sanitize($conn, $_POST['origin']);
        $disease_prevented = sanitize($conn, $_POST['disease_prevented']);
        $category = sanitize($conn, $_POST['category']);
        $price = (float)$_POST['price'];
        $description = sanitize($conn, $_POST['description']);
        
        $sql = "INSERT INTO vaccines (name, origin, disease_prevented, category, price, description) 
                VALUES ('$name', '$origin', '$disease_prevented', '$category', $price, '$description')";
        
        if ($conn->query($sql)) {
            $message = 'Thêm vắc xin mới thành công!';
            $messageType = 'success';
        } else {
            $message = 'Lỗi: ' . $conn->error;
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'edit') {
        $id = (int)$_POST['id'];
        $name = sanitize($conn, $_POST['name']);
        $origin = sanitize($conn, $_POST['origin']);
        $disease_prevented = sanitize($conn, $_POST['disease_prevented']);
        $category = sanitize($conn, $_POST['category']);
        $price = (float)$_POST['price'];
        $description = sanitize($conn, $_POST['description']);
        
        $sql = "UPDATE vaccines SET 
                name = '$name', 
                origin = '$origin', 
                disease_prevented = '$disease_prevented', 
                category = '$category', 
                price = $price, 
                description = '$description' 
                WHERE id = $id";
        
        if ($conn->query($sql)) {
            $message = 'Cập nhật vắc xin thành công!';
            $messageType = 'success';
        } else {
            $message = 'Lỗi: ' . $conn->error;
            $messageType = 'error';
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        
        // Kiểm tra xem vắc xin có đang được sử dụng không
        $check_sql = "SELECT COUNT(*) as count FROM booking_items WHERE vaccine_id = $id";
        $check_result = $conn->query($check_sql);
        $is_used = false;
        
        if ($check_result && $check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            if ($row['count'] > 0) {
                $is_used = true;
            }
        }
        
        if ($is_used) {
            $message = 'Không thể xóa vắc xin này vì đang được sử dụng trong các đơn đặt lịch!';
            $messageType = 'error';
        } else {
            $sql = "DELETE FROM vaccines WHERE id = $id";
            
            if ($conn->query($sql)) {
                $message = 'Xóa vắc xin thành công!';
                $messageType = 'success';
            } else {
                $message = 'Lỗi: ' . $conn->error;
                $messageType = 'error';
            }
        }
    }
}

// Lấy danh sách vắc xin
$sql = "SELECT * FROM vaccines ORDER BY id DESC";
$result = $conn->query($sql);
$vaccines = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $vaccines[] = $row;
    }
}

// Lấy danh sách danh mục vắc xin
$categories = [
    'Vắc xin cho trẻ em',
    'Vắc xin cho trẻ em tiền học đường',
    'Vắc xin cho tuổi vị thành niên và thanh niên',
    'Vắc xin cho người trưởng thành',
    'Vắc xin cho phụ nữ chuẩn bị trước mang thai'
];

$page_title = "Quản lý vắc xin";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý vắc xin - VNVC</title>
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
            <a href="vaccines.php" class="menu-item active">
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
            <h1>Quản lý vắc xin</h1>
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
                <h2>Danh sách vắc xin</h2>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Thêm vắc xin mới
                </button>
            </div>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên vắc xin</th>
                            <th>Nguồn gốc</th>
                            <th>Phòng bệnh</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($vaccines)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Không có vắc xin nào</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($vaccines as $vaccine): ?>
                                <tr>
                                    <td><?php echo $vaccine['id']; ?></td>
                                    <td><?php echo htmlspecialchars($vaccine['name']); ?></td>
                                    <td><?php echo htmlspecialchars($vaccine['origin']); ?></td>
                                    <td><?php echo htmlspecialchars($vaccine['disease_prevented']); ?></td>
                                    <td><?php echo htmlspecialchars($vaccine['category']); ?></td>
                                    <td><?php echo number_format($vaccine['price'], 0, ',', '.'); ?> đ</td>
                                    <td>
                                        <div class="actions">
                                            <a href="javascript:void(0)" class="action-btn edit" onclick="openEditModal(<?php echo $vaccine['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="action-btn delete" onclick="openDeleteModal(<?php echo $vaccine['id']; ?>)">
                                                <i class="fas fa-trash"></i>
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
    
    <!-- Add Vaccine Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Thêm vắc xin mới</h2>
                <span class="close" onclick="closeModal('addModal')">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="name">Tên vắc xin</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="origin">Nguồn gốc</label>
                    <input type="text" id="origin" name="origin" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="disease_prevented">Phòng bệnh</label>
                    <input type="text" id="disease_prevented" name="disease_prevented" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Danh mục</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Giá (VNĐ)</label>
                    <input type="number" id="price" name="price" class="form-control" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm vắc xin</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Vaccine Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Chỉnh sửa vắc xin</h2>
                <span class="close" onclick="closeModal('editModal')">&times;</span>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id" name="id" value="">
                
                <div class="form-group">
                    <label for="edit_name">Tên vắc xin</label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_origin">Nguồn gốc</label>
                    <input type="text" id="edit_origin" name="origin" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_disease_prevented">Phòng bệnh</label>
                    <input type="text" id="edit_disease_prevented" name="disease_prevented" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_category">Danh mục</label>
                    <select id="edit_category" name="category" class="form-control" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_price">Giá (VNĐ)</label>
                    <input type="number" id="edit_price" name="price" class="form-control" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Mô tả</label>
                    <textarea id="edit_description" name="description" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Hủy</button>
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Vaccine Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Xác nhận xóa</h2>
                <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            </div>
            <p>Bạn có chắc chắn muốn xóa vắc xin này không?</p>
            <form method="POST" action="">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" id="delete_id" name="id" value="">
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('deleteModal')">Hủy</button>
                    <button type="submit" class="btn btn-danger">Xóa</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Mở modal thêm vắc xin
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        // Mở modal chỉnh sửa vắc xin
        function openEditModal(id) {
            // Lấy thông tin vắc xin từ dữ liệu
            <?php
            echo "const vaccines = " . json_encode($vaccines) . ";";
            ?>
            
            const vaccine = vaccines.find(v => v.id == id);
            
            if (vaccine) {
                document.getElementById('edit_id').value = vaccine.id;
                document.getElementById('edit_name').value = vaccine.name;
                document.getElementById('edit_origin').value = vaccine.origin;
                document.getElementById('edit_disease_prevented').value = vaccine.disease_prevented;
                document.getElementById('edit_category').value = vaccine.category;
                document.getElementById('edit_price').value = vaccine.price;
                document.getElementById('edit_description').value = vaccine.description || '';
                
                document.getElementById('editModal').style.display = 'block';
            }
        }
        
        // Mở modal xóa vắc xin
        function openDeleteModal(id) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteModal').style.display = 'block';
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
