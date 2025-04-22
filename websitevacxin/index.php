<?php
session_start();

// Define the base URL for assets
$base_url = '/websitevacxin/';

// Determine the content to load
$content_page = isset($_GET['page']) ? $_GET['page'] : '/websitevacxin/menu/noidung.php';
$page_title = 'Trang chủ VNVC';

// Map page URLs to titles
$page_titles = [
    '/websitevacxin/menu/noidung.php' => 'Trang chủ VNVC',
    '/websitevacxin/menu/gioithieu.php' => 'Giới thiệu',
    '/websitevacxin/menu/danhmucvacxin.php' => 'Danh Mục Vacxin',
    '/websitevacxin/menu/tintuc.php' => 'Tin Tức',
    '/websitevacxin/menu/FAQ.php' => 'FAQ',
    '/websitevacxin/menu/hotline.php' => 'HOTLINE: 02871026595',
    '/websitevacxin/menu/dangnhapdangki.php' => 'Đăng nhập/Đăng kí'
];

if (array_key_exists($content_page, $page_titles)) {
    $page_title = $page_titles[$content_page];
}

// Sanitize the content page
$content_page = filter_var($content_page, FILTER_SANITIZE_URL);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="<?php echo $base_url; ?>img/logoVNVC.jpg" />
    <title><?php echo htmlspecialchars($page_title . ' - Vacxin VNVC'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500&family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/main.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/base.css">
</head>
<style>
.user-menu ul {
    position: absolute;
    background-color: #1631c5;
    display: none;
    padding: 10px;
    border-radius: 5px;
    list-style: none;
}

.user-menu:hover ul {
    display: block;
}

.user-menu ul li {
    margin: 5px 0;
}

.user-menu ul li a {
    color: #fff;
    text-decoration: none;
}

.user-menu ul li a:hover {
    color: #f39021;
}

.user-menu a {
    color: #fff;
    text-decoration: none;
}

.user-menu a:hover {
    color: #f39021;
}
</style>

<body>
    <script>
    function loadContent(url, title) {
        document.getElementById('content-frame').src = url;
        history.pushState({
            url: url
        }, title, '?page=' + encodeURIComponent(url));
        document.title = title + ' - Vacxin VNVC';
    }

    window.onpopstate = function(event) {
        if (event.state && event.state.url) {
            document.getElementById('content-frame').src = event.state.url;
        }
    };

    history.replaceState({
        url: '<?php echo $content_page; ?>'
    }, '<?php echo $page_title; ?>', '?page=<?php echo urlencode($content_page); ?>');
    </script>
    <div class="app">
        <div>
            <header class="header">
                <nav class="container2">
                    <a href="<?php echo $base_url; ?>index.php" id="logo">
                        <img src="<?php echo $base_url; ?>img/logo.webp" alt="logo">
                    </a>
                    <ul id="main-menu">
                        <li><a href="#"
                                onclick="loadContent('/websitevacxin/menu/gioithieu.php', 'Giới thiệu'); return false;">
                                <i class="fas fa-home"></i> Mon-Sun: 7am-5h30pm</a></li>
                        <!-- <li><a href="#"
                                onclick="loadContent('/websitevacxin/menu/noidung.php', 'Trang chủ VNVC'); return false;">Trang
                                chủ VNVC</a></li> -->
                        <li><a href="#"
                                onclick="loadContent('/websitevacxin/menu/danhmucvacxin.php', 'Danh Mục Vacxin'); return false;"><i
                                    class="fas fa-syringe"></i>Danh Mục Vacxin</a></li>
                        <li><a href="#"
                                onclick="loadContent('/websitevacxin/menu/tintuc.php', 'Tin Tức'); return false;"><i
                                    class="fas fa-newspaper"></i>Tin Tức</a></li>
                        <li><a href="#" onclick="loadContent('/websitevacxin/menu/FAQ.php', 'FAQ'); return false;"><i
                                    class="fas fa-question-circle"></i>FAQ</a></li>
                        <li><a href="#"
                                onclick="loadContent('/websitevacxin/menu/hotline.php', 'HOTLINE: 02871026595'); return false;"><i
                                    class="fas fa-phone"></i>HOTLINE: 02871026595</a></li>
                        <!-- <li><a href="<?php echo $base_url; ?>menu/dangnhapdangki.php">Đăng nhập/Đăng kí</a></li> -->
                        <!-- <li><a href="#"
                                onclick="loadContent('/websitevacxin/menu/dangnhapdangki.php', 'Đăng nhập/Đăng kí'); return false;">Đăng
                                nhập/Đăng kí</a></li> -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="user-menu">
                            <a href="#"><i class="fas fa-user-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['full_name']); ?></a>
                            <ul class="sub-menu">
                                <li><a href="<?php echo $base_url; ?>"
                                        onclick="loadContent('menu/profile.php', 'Thông tin cá nhân'); return false;"><i
                                            class="fas fa-id-card"></i></i>Thông tin cá nhân</a></li>

                                <li><a href="<?php echo $base_url; ?>"
                                        onclick="loadContent('menu/giohang.php', 'Giỏ hàng của tôi'); return false;"><i
                                            class="fas fa-shopping-cart"></i>Giỏ hàng của tôi</a></li>
                                <!-- <li><a href="<?php echo $base_url; ?>menu/giohang.php"><i class="fas fa-shopping-cart"></i> Giỏ hàng của tôi</a></li> -->
                                <li><a href="<?php echo $base_url; ?>menu/logout.php"><i
                                            class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </li>
                        <?php else: ?>
                        <li><a href="<?php echo $base_url; ?>menu/login.php"><i class="fas fa-sign-in-alt"></i> Đăng
                                nhập/<i class="fas fa-user-plus"></i> Đăng ký</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </header>
        </div>
        <div class="container">
            <div class="menu">
                <div class="dropdown">
                    <button class="dropbtn">Danh mục</button>
                    <div class="dropdown-content">
                        <a onclick="showCatelogy(this)" value="Vắc xin cho trẻ em">Vắc xin cho trẻ em</a>
                        <a onclick="showCatelogy(this)" value="Vắc xin cho trẻ em tiền học đường">Vắc xin cho trẻ em
                            tiền học đường</a>
                        <a onclick="showCatelogy(this)" value="Vắc xin cho tuổi vị thành niên và thanh niên">Vắc xin cho
                            tuổi vị thành niên và thanh niên</a>
                        <a onclick="showCatelogy(this)" value="Vắc xin cho người trưởng thành">Vắc xin cho người trưởng
                            thành</a>
                        <a onclick="showCatelogy(this)" value="Vắc xin cho phụ nữ chuẩn bị trước mang thai">Vắc xin cho
                            phụ nữ chuẩn bị trước mang thai</a>
                    </div>
                </div>
                <div class="info">
                    <h1 class="info_text">THÔNG TIN SẢN PHẨM VẮC XIN</h1>
                    <h2 id="info-vx"></h2>
                </div>
            </div>
            <div class="colum">
                <iframe name="content-frame" id="content-frame" src="<?php echo htmlspecialchars($content_page); ?>"
                    width="100%" height="auto" style="border: none;"></iframe>
            </div>
        </div>
        <!-- <div id="footer">
            <p>© Các thông tin trên website vnvc.vn chỉ phục vụ cho mục đích tham khảo, tra cứu, được kiểm soát chuyên
                môn bởi đội ngũ chuyên gia, bác sĩ của VNVC và là tài sản thuộc sở hữu của VNVC. Mọi động thái sao chép
                chưa được sự chấp thuận chính thức của VNVC đều là trái phép và vi phạm quy định về Sở hữu trí tuệ.</p>
        </div> -->
        <footer id="footer">
            <div class="container" style="background-color: #1631c5;">
                <!-- Phần trên của footer -->
                <div class="footer-top">
                    <div class="footer-logo">
                        <a href="https://vnvc.vn" title="Logo VNVC">
                            <img src="https://vnvc.vn/wp-content/uploads/2024/05/vnvc-logo.png" alt="Logo VNVC"
                                class="img-responsive" width="190" height="50">
                        </a>
                        <p class="founded">Thành lập từ năm 2017</p>
                    </div>
                    <div class="footer-slogan">
                        <h3>HỆ THỐNG TRUNG TÂM TIÊM CHỦNG VẮC XIN CHO TRẺ EM & NGƯỜI LỚN</h3>
                        <p>an toàn - uy tín - chất lượng hàng đầu Việt Nam *</p>
                        <span>* Bình chọn của Vietnam Report 2024</span>
                    </div>
                    <div class="footer-contact">
                        <div class="contact-links">
                            <a href="https://vnvc.vn/lien-he/">Liên hệ</a>
                            <a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/">Tìm trung tâm VNVC</a>
                            <p>Hotline: <a href="tel:+842871026595">028 7102 6595</a></p>
                        </div>
                        <div class="working-hours">
                            <p><strong>Mở cửa 7:30 – 17:00 (không nghỉ trưa)</strong></p>
                            <p>Một số Trung tâm có giờ hoạt động riêng</p>
                        </div>
                    </div>
                </div>

                <!-- Danh sách hệ thống trung tâm -->
                <div class="footer-centers">
                    <div class="center-region">
                        <h4>Hệ thống Miền Bắc</h4>
                        <ul>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=ha-noi">Hà Nội</a></li>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=hai-phong">Hải
                                    Phòng</a></li>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=quang-ninh">Quảng
                                    Ninh</a></li>
                            <!-- Thêm các tỉnh khác nếu cần -->
                        </ul>
                    </div>
                    <div class="center-region">
                        <h4>Hệ thống Miền Trung</h4>
                        <ul>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=da-nang">Đà Nẵng</a>
                            </li>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=khanh-hoa">Khánh
                                    Hòa</a></li>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=thanh-hoa">Thanh
                                    Hóa</a></li>
                            <!-- Thêm các tỉnh khác nếu cần -->
                        </ul>
                    </div>
                    <div class="center-region">
                        <h4>Hệ thống Miền Nam</h4>
                        <ul>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=tp-ho-chi-minh">Hồ Chí
                                    Minh</a></li>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=can-tho">Cần Thơ</a>
                            </li>
                            <li><a href="https://vnvc.vn/he-thong-trung-tam-tiem-chung/?province=dong-nai">Đồng Nai</a>
                            </li>
                            <!-- Thêm các tỉnh khác nếu cần -->
                        </ul>
                    </div>
                </div>

                <!-- Phần dưới của footer -->
                <div class="footer-bottom">
                    <hr>
                    <div class="footer-info">
                        <div class="company-info">
                            <p><strong>CÔNG TY CỔ PHẦN VACXIN VIỆT NAM</strong></p>
                            <p>Giấy chứng nhận ĐKKD số 0107631488 do sở Kế hoạch và Đầu tư TP. Hà Nội cấp ngày
                                11/11/2016</p>
                            <p>Địa chỉ: 180 Trường Chinh, phường Khương Thượng, quận Đống Đa, thành phố Hà Nội</p>
                            <p>Email: <a href="mailto:cskh@vnvc.vn">cskh@vnvc.vn</a> | Số điện thoại: <a
                                    href="tel:+842871026595">028 7102 6595</a></p>
                            <p>Chịu trách nhiệm nội dung: Trần Thanh Hằng</p>
                            <p>© 2016 - Bản quyền thuộc về CÔNG TY CỔ PHẦN VACXIN VIỆT NAM</p>
                        </div>
                        <div class="footer-links">
                            <p><a href="https://vnvc.vn/chinh-sach-bao-mat-thong-tin/">Chính sách bảo mật</a></p>
                            <p><a href="https://vnvc.vn/khao-sat-tiem-chung/">Khảo sát tiêm chủng</a></p>
                            <p><a href="https://vnvc.vn/chinh-sach-thanh-toan/">Chính sách thanh toán</a></p>
                        </div>
                        <div class="footer-app">
                            <img src="https://vnvc.vn/wp-content/uploads/2025/02/qr-code-app-vnvc.png"
                                alt="QR Code App VNVC" width="100">
                            <p><a href="https://play.google.com/store/apps/details?id=com.eco.eplus" target="_blank">Tải
                                    ứng dụng Mobile App VNVC</a></p>
                            <p><i>Trợ lý tiêm chủng</i></p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="<?php echo $base_url; ?>main.js"></script>
</body>
<style>
#footer {
    background-color: #1631c5;
    color: white;
    padding: 20px 0;
    font-family: 'Quicksand', sans-serif;
    font-size: 14px;
}

.footer-top {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 20px;
}

.footer-logo {
    text-align: center;
}

.footer-logo img {
    max-width: 190px;
    height: auto;
}

.footer-logo .founded {
    font-size: 12px;
    margin-top: 5px;
}

.footer-slogan {
    text-align: center;
    max-width: 400px;
}

.footer-slogan h3 {
    font-size: 16px;
    font-weight: 500;
    margin: 0;
}

.footer-slogan p {
    font-size: 14px;
    margin: 5px 0;
}

.footer-slogan span {
    font-size: 12px;
    color: #f39021;
}

.footer-contact {
    text-align: center;
}

.footer-contact a {
    color: #f39021;
    text-decoration: none;
}

.footer-contact a:hover {
    color: white;
}

.contact-links p {
    margin: 5px 0;
}

.working-hours p {
    margin: 5px 0;
}

.footer-centers {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    padding: 20px 0;
}

.center-region {
    flex: 1;
    min-width: 200px;
    margin: 10px;
}

.center-region h4 {
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 10px;
}

.center-region ul {
    list-style: none;
    padding: 0;
}

.center-region ul li {
    margin: 5px 0;
}

.center-region ul li a {
    color: white;
    text-decoration: none;
}

.center-region ul li a:hover {
    color: #f39021;
}

.footer-bottom hr {
    border: none;
    height: 1px;
    background-color: rgba(255, 255, 255, 0.5);
    margin: 20px 0;
}

.footer-info {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

.company-info,
.footer-links,
.footer-app {
    flex: 1;
    min-width: 200px;
    margin: 10px;
}

.company-info p,
.footer-links p {
    margin: 5px 0;
}

.company-info a {
    color: #f39021;
    text-decoration: none;
}

.footer-links a {
    color: #f39021;
    text-decoration: none;
}

.footer-links a:hover {
    color: white;
}

.footer-app img {
    max-width: 100px;
    margin-bottom: 10px;
}

.footer-app a {
    color: #f39021;
    text-decoration: none;
    font-weight: 500;
}

.footer-app a:hover {
    color: white;
}



@media (max-width: 768px) {

    .footer-top,
    .footer-centers,
    .footer-info {
        flex-direction: column;
        text-align: center;
    }
}
</style>

</html>