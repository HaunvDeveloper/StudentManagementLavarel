
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quét Khuôn Mặt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <link rel="stylesheet" href="{{asset("libs/adminlte/css/adminlte.css")}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css" integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset("libs/data-table.css")}}" />
    <link rel="stylesheet" href="{{asset("lib/sweetalert2/themes/default/default.min.css")}}">
    <link rel="stylesheet" href="{{asset("js/sweetalert2-extend.js")}}" />
    <link rel="stylesheet" href="{{asset("assets/css/loader.css")}}">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
        }

        .main-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 100%;
            height: 100%;
            position: relative;
        }

        .content-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

            .content-wrapper.blur {
                filter: blur(5px);
                pointer-events: none; /* Prevent interactions */
            }

        .camera-frame {
            position: relative;
            width: 300px;
            height: 300px;
            border: 5px solid #3498db;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .camera-placeholder {
            position: absolute;
            color: #ccc;
            font-size: 50px;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .confirm-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

            .confirm-button:hover {
                background-color: #c0392b;
            }

        .camera-frame {
            box-shadow: 0 0 15px 5px rgb(223 31 31 / 80%);
        }

            .camera-frame.centered {
                box-shadow: 0 0 15px 5px rgba(0, 255, 0, 0.8); /* Green border when isCentered is true */
            }
    </style>
</head>
<body>
    <div class="main-wrapper">
    @if($device->IsActive)
    <div class="loader" style="display: none; position: absolute; top: 45%; left: 49%; z-index: 100;"></div>
    <div class="content-wrapper">
        <div class="horizontal-layout">
            <div class="camera-frame" onclick="startCamera()">
                <video id="camera" autoplay playsinline></video>
                <div class="camera-placeholder" id="camera-placeholder">
                    <i class="fa-solid fa-camera"></i>
                </div>
            </div>
        </div>
        <a href="/logout" class="confirm-button" onclick="stopCamera()">Thoát</a>
    </div>
@else
    <div class="content-wrapper">
        <h4 class="not-active">Thiết bị chưa được bật</h4>
        <a href="{{route("logout")}}" class="confirm-button" onclick="stopCamera()">Thoát</a>
    </div>
@endif


    </div>
    <script src="{{asset("lib/jquery/dist/jquery.min.js")}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{asset("libs/adminlte/js/adminlte.js")}}"></script> <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
    <script src="{{asset("lib/sweetalert2/dist/sweetalert2.min.js")}}"></script>
    <script src="{{asset("js/sweetalert2-extend.js")}}"></script>
    
    <script>
        $(document).ready(function () {
            var notActive = $('.not-active').length;
            if (notActive > 0) {
                setInterval(async () => {
                    try {
                        const response = await fetch('{{route("device.checkActivate")}}', {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        });

                        const result = await response.json();
                        if (result.success) {
                            window.location.reload();
                        }
                    } catch (error) {
                        console.error("Error capturing image: ", error);
                    } 
                }, 1000);
            }
        });
        let intervalId = null;
        let isProcessing = false;  // Biến theo dõi trạng thái yêu cầu


        async function startCamera() {
            const camera = document.getElementById('camera');
            const placeholder = document.getElementById('camera-placeholder');
            const cameraFrame = document.querySelector('.camera-frame');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                camera.srcObject = stream;
                placeholder.style.display = 'none';

                intervalId = setInterval(async () => {
                    if (isProcessing) return;
                    isProcessing = true;
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    canvas.width = camera.videoWidth;
                    canvas.height = camera.videoHeight;

                    ctx.drawImage(camera, 0, 0, canvas.width, canvas.height);
                    const imageData = canvas.toDataURL('image/png');

                    try {
                        const response = await fetch('{{route("face.checkposition")}}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            body: JSON.stringify({ image: imageData }),
                        });

                        const result = await response.json();
                        if (result.isCentered) {
                            $('.camera-frame').addClass('centered');
                            console.log('center');
                            await verifyFace(imageData);
                        }
                        else {
                            $('.camera-frame').removeClass('centered');
                        }
                    } catch (error) {
                        console.error("Error capturing image: ", error);
                        $('.camera-frame').removeClass('centered');
                    } finally {
                        $('.camera-frame').removeClass('centered');
                        isProcessing = false;
                    }
                }, 1000); // Check every second
            } catch (err) {
                alert('Cannot access the camera: ' + err.message);
            }
        }
        async function verifyFace(imageData) {
            const mainWrapper = document.querySelector('.content-wrapper');
            const loader = document.querySelector('.loader');
            try {
                // Add blur effect and loader
                mainWrapper.classList.add('blur');
                loader.style.display = 'block';
                const response = await fetch('{{route("device.verify")}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({ image: imageData }),
                });

                const result = await response.json();
                if (result.success) {
                    showToast("success", "Đã điểm danh " + result.name);
                }
                else {
                    showToast("error", "Thất bại: " + result.error);
                }
            } catch (error) {
                console.error("Error registering user: ", error);
                alert("Error registering user.");
            } finally {
                // Remove blur effect and loader
                mainWrapper.classList.remove('blur');
                loader.style.display = 'none';

            }
        }
        function stopCamera() {
            if (cameraStream) {
                const tracks = cameraStream.getTracks();
                tracks.forEach(track => track.stop());
            }
        }
    </script>
</body>
</html>
