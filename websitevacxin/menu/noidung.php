<?php
$base_url = '/websitevacxin/';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trang chủ VNVC</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/base.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/main.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/themify-icons.css">
</head>

<style>
    body {
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }
</style>
<body>
<div class="hero-section">
            <div class="hero-content">
                <h1>Chào mừng đến với VNVC</h1>
                <p>Hệ thống tiêm chủng vắc xin hàng đầu Việt Nam</p>
                <a href="<?php echo $base_url; ?>menu/danhmucvacxin.php" class="hero-btn">Xem danh mục vắc xin</a>
            </div>
        </div>
</body>

<style>
    .hero-section {
            /* background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo $base_url; ?>img/hero-bg.jpg'); */
            /* background-image: linear-gradient(rgba(22, 49, 197, 0.5), rgba(22, 49, 197, 0.5)), url('<?php echo $base_url; ?>img/hero-bg.jpg'); */
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #1631c5;
        }
        
        .hero-content {
            max-width: 800px;
            padding: 20px;
        }
        
        .hero-content h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .hero-content p {
            font-size: 24px;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .hero-btn {
            display: inline-block;
            background-color: #2a388f;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .hero-btn:hover {
            background-color: #1a2570;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
</style>

</html>