<?php
session_start();
$base_url = '/websitevacxin/';

// Kết nối database
require_once 'db_connect.php';
$message = '';
$messageType = '';

// Nếu người dùng đã đăng nhập, chuyển hướng đến trang chủ
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($conn, $_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']) ? true : false;
    $role = isset($_POST['role']) ? sanitize($conn, $_POST['role']) : 'user';
    
    // Kiểm tra thông tin đăng nhập
    $sql = "SELECT id, username, password, full_name, role FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'] ?? 'user'; // Mặc định là user nếu không có role
            
            // Lưu thông tin đăng nhập vào cơ sở dữ liệu
            $user_id = $user['id'];
            $login_time = date('Y-m-d H:i:s');
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $user_agent = sanitize($conn, $_SERVER['HTTP_USER_AGENT']);
            
            // Kiểm tra xem bảng login_logs đã tồn tại chưa
            $check_table = "SHOW TABLES LIKE 'login_logs'";
            $table_exists = $conn->query($check_table);
            
            if ($table_exists->num_rows == 0) {
                // Tạo bảng login_logs nếu chưa tồn tại
                $create_table = "CREATE TABLE login_logs (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(11) NOT NULL,
                    login_time DATETIME NOT NULL,
                    ip_address VARCHAR(50) NOT NULL,
                    user_agent TEXT,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                )";
                $conn->query($create_table);
            }
            
            // Lưu log đăng nhập
            $log_sql = "INSERT INTO login_logs (user_id, login_time, ip_address, user_agent) 
                        VALUES ('$user_id', '$login_time', '$ip_address', '$user_agent')";
            $conn->query($log_sql);
            
            // Nếu người dùng chọn "Ghi nhớ đăng nhập"
            if ($remember) {
                // Tạo token ngẫu nhiên
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                // Kiểm tra xem bảng remember_tokens đã tồn tại chưa
                $check_token_table = "SHOW TABLES LIKE 'remember_tokens'";
                $token_table_exists = $conn->query($check_token_table);
                
                if ($token_table_exists->num_rows == 0) {
                    // Tạo bảng remember_tokens nếu chưa tồn tại
                    $create_token_table = "CREATE TABLE remember_tokens (
                        id INT(11) AUTO_INCREMENT PRIMARY KEY,
                        user_id INT(11) NOT NULL,
                        token VARCHAR(64) NOT NULL,
                        expires DATETIME NOT NULL,
                        FOREIGN KEY (user_id) REFERENCES users(id)
                    )";
                    $conn->query($create_token_table);
                }
                
                // Lưu token vào database
                $token_sql = "INSERT INTO remember_tokens (user_id, token, expires) 
                              VALUES ('$user_id', '$token', '$expires')";
                $conn->query($token_sql);
                
                // Lưu token vào cookie
                setcookie('remember_token', $token, strtotime('+30 days'), '/');
            }
            
            // Chuyển hướng dựa trên vai trò
            if ($_SESSION['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            $message = 'Mật khẩu không đúng!';
            $messageType = 'error';
        }
    } else {
        $message = 'Tên đăng nhập không tồn tại!';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - VNVC</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a28ba;
            --secondary-color: #f3f6ff;
            --accent-color: #ff4757;
            --text-color: #333;
            --light-text: #666;
            --white: #fff;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--secondary-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            padding: 0;
        }

        .header {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 25px 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 26px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.8;
        }

        .form-container {
            padding: 30px;
        }

        .message {
            margin-bottom: 20px;
            padding: 10px 15px;
            border-radius: var(--border-radius);
            font-size: 14px;
        }

        .message.error {
            background-color: #ffe0e3;
            color: #d63031;
            border-left: 4px solid #d63031;
        }

        .message.success {
            background-color: #e3ffe2;
            color: #27ae60;
            border-left: 4px solid #27ae60;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
            color: var(--light-text);
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 14px;
            transition: var(--transition);
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 40, 186, 0.2);
            outline: none;
        }

        .form-group .icon {
            position: absolute;
            right: 15px;
            top: 38px;
            color: var(--light-text);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .remember-me {
            display: flex;
            align-items: center;
        }

        .remember-me input {
            margin-right: 8px;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .btn {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            padding: 12px 20px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
        }

        .btn:hover {
            background-color: #141e8e;
        }

        .footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #eee;
            font-size: 14px;
        }

        .footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .role-toggle {
            display: flex;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .role-toggle-btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            background-color: #f5f5f5;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            font-size: 14px;
        }

        .role-toggle-btn.active {
            background-color: var(--primary-color);
            color: var(--white);
        }

        @media (max-width: 576px) {
            .container {
                border-radius: 0;
            }
            
            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Đăng Nhập</h1>
            <p>Đăng nhập để sử dụng dịch vụ của VNVC</p>
        </div>
        
        <div class="form-container">
            <?php if (!empty($message)): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="role-toggle">
                <div class="role-toggle-btn active" onclick="toggleRole('user')">Người dùng</div>
                <div class="role-toggle-btn" onclick="toggleRole('admin')">Quản trị viên</div>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="role" id="role" value="user">
                
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
                    <i class="icon fas fa-user"></i>
                </div>
                
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <i class="icon fas fa-lock"></i>
                </div>
                
                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    <div class="forgot-password">
                        <a href="forgot-password.php">Quên mật khẩu?</a>
                    </div>
                </div>
                
                <button type="submit" class="btn" id="login-btn">Đăng Nhập</button>
            </form>
        </div>
        
        <div class="footer">
            Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
            <br><br>
            <a href="../index.php">Quay lại trang chủ</a>
        </div>
    </div>
    
    <script>
        function toggleRole(role) {
            const userBtn = document.querySelector('.role-toggle-btn:first-child');
            const adminBtn = document.querySelector('.role-toggle-btn:last-child');
            const loginBtn = document.getElementById('login-btn');
            const roleInput = document.getElementById('role');
            
            if (role === 'user') {
                userBtn.classList.add('active');
                adminBtn.classList.remove('active');
                loginBtn.textContent = 'Đăng Nhập';
                roleInput.value = 'user';
            } else {
                userBtn.classList.remove('active');
                adminBtn.classList.add('active');
                loginBtn.textContent = 'Đăng Nhập Quản Trị';
                roleInput.value = 'admin';
            }
        }
    </script>
</body>
</html>
