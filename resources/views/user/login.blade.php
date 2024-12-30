<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sedgwick+Ave&display=swap" rel="stylesheet">
    <title>Đăng Nhập</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Sedgwick+Ave&display=swap" rel="stylesheet">
    <!-- BOXICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


</head>

<body>
    <main>

        <div class="container">
            <div class="left-section">
                <div class="information">
                    <img src="assets/images/logo-HCMUE.png" alt="Logo" class="logo" >
                    <h1>ĐĂNG NHẬP</h1>
                    <h2>HỆ THỐNG QUẢN LÝ SINH VIÊN</h2>
                </div>
                
                <img src="assets/images/Toa-nha-A-01.png" alt="Building Illustration" class="building">
            </div>
            <div class="right-section">
                <div class="login-box">
                    <h3>ĐĂNG NHẬP</h3>
                    <form id="login-form">
                        @csrf
                        <div class="input-group">
                            <input id="Username" name="Username" placeholder="Tên đăng nhập" type="text" required/>
                        </div>
                        <div class="input-group">
                            <input type="password" id="Password" name="Password" placeholder="Mật khẩu" required>
                            <i class="fa-solid fa-eye" id="show-password"></i>
                        </div>

                        <div class="forgot-password">
                            <a asp-action="ForgetPassword" asp-controller="User">Quên mật khẩu?</a>
                        </div>
                        <div id="error-message" style="color:red;margin-bottom:10px;"></div>
                        <button type="button" class="login-btn" id="submit-login">Đăng nhập</button>
                    </form>

                </div>
            </div>
        </div>
        <footer>
            <p>By Díp Dồ Team</p>
            <img src="assets/images/logo.png" alt="">
        </footer>
        
    </main>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#submit-login').click(function (e) {
        e.preventDefault();

        $.ajax({
            url: '{{ route("login.post") }}',
            type: 'POST',
            data: {
                Username: $('#Username').val(),
                Password: $('#Password').val()
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    window.location.href = response.redirect_url;
                } else {
                    $('#error-message').text(response.message);
                }
            },
            error: function (xhr) {
                const errorMessage = xhr.responseJSON?.message || 'Đăng nhập thất bại. Vui lòng thử lại.';
                $('#error-message').text(errorMessage);
            }
        });
    });
});

    </script>

    <script>

        const showPassword = document.querySelector("#show-password");
        const passwordField = document.querySelector("#Password");
        
        showPassword.addEventListener("click", function () {
            if (this.classList.contains("fa-eye")) {
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);
        });
        
    </script>

</body>

</html>