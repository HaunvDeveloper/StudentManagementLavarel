<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Trang chủ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/homeIndex.css">
</head>
<body>

    <header>
        <div class="logo-container">
            <a class="navbar-brand" href="#" style="cursor:default; pointer-events:none">
                <img src="assets/images/Logo-HCMUE.png" alt="Logo">
                <span>TRƯỜNG ĐẠI HỌC SƯ PHẠM THÀNH PHỐ HỒ CHÍ MINH</span>
            </a>
        </div>

        <nav class="navbar navbar-expand-lg" style="margin-bottom: 65px;">
            <div class="container-fluid">
                <div class="collapse navbar-collapse d-flex justify-content-between">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="/">TRANG CHỦ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">TRA CỨU VĂN BẰNG/CHỨNG CHỈ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">NGÀNH</a>
                        </li>
                    </ul>
                    
                        <a href="{{ route('login') }}" class="btn btn-login">Đăng nhập</a>
                    
                </div>
            </div>
        </nav>
    </header>


    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div id="carouselExampleSlidesOnly" class="carousel slide custom-carousel" data-bs-ride="carousel" data-bs-interval="2000">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="assets/images/638390079030024607IMG_1.jpg" alt="First slide">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="assets/images/638390079165441665IMG_2.jpg" alt="Second slide">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="assets/images/638390079298799819IMG_3.jpg" alt="Third slide">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="assets/images/Logo-HCMUE.png" alt="Logo HCMUE">
            </div>

            <div class="footer-links">
                <a href="https://hcmue.edu.vn/vi/" target="_blank">TRANG CHỦ</a>
                <a href="https://www.facebook.com/HCMUE.VN/" target="_blank">FANPAGE FB</a>
                <a href="https://hcmue.edu.vn/vi/tin-tuc-su-kien/su-kien" target="_blank">SỰ KIỆN</a>
                <a href="https://hcmue.edu.vn/vi/gioi-thieu/lich-su-phat-trien" target="_blank">GIỚI THIỆU</a>
            </div>

            <div class="footer-content">
                <div class="footer-address">
                    <h4><i class="fa-solid fa-house"></i> ĐỊA CHỈ</h4>
                    <p><strong>Trụ sở chính:</strong> 280 An Dương Vương, Phường 4, Quận 5, Thành phố Hồ Chí Minh</p>
                    <p><strong>Các cơ sở đào tạo khác:</strong></p>
                    <ul>
                        <li>Cơ sở 2: 222 Lê Văn Sỹ, Phường 14, Quận 3, Thành phố Hồ Chí Minh</li>
                        <li>Phân hiệu Long An: Số 934 Quốc lộ 1, Phường Khánh Hậu, TP. Tân An, Tỉnh Long An</li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h4><i class="fa-solid fa-envelope"></i> LIÊN HỆ</h4>
                    <p>Email: phongctct@hcmue.edu.vn</p>
                    <p>Hotline: 028 - 38352020</p>
                    <p>Fax: 028 - 38398946</p>
                </div>
            </div>

            <div class="footer-bottom">
                <p>Developed by Díp Dồ Team</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
