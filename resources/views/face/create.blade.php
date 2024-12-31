@extends('layouts.app')

@section('title', 'Tạo định danh khuôn mặt')

@section('links')
    <link rel="stylesheet" href="{{ asset('assets/css/faceIdentify.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/loader.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
@endsection

@section('content')
<div class="main-wrapper">
    <div class="loader" style="    display: none;
    position: absolute;
    top: 45%;
    left: 49%;
    z-index: 100;"></div>
    <div class="header-bar">
        <span class="header-title"><i class="fa-solid fa-face-grin-wide"></i>Định danh khuôn mặt</span>
    </div>
    <div class="content-wrapper">
        <div class="horizontal-layout">
            <div class="instruction-left-box">
                <span>
                    Di chuyển chậm khuôn mặt của bạn để hoàn thành vòng tròn.
                </span>
            </div>

            <div class="camera-frame" onclick="startCamera()">
                <video id="camera" autoplay playsinline></video>
                <div class="camera-placeholder" id="camera-placeholder">
                    <i class="fa-solid fa-face-grin-wide"></i>
                </div>
            </div>  

            <div class="instruction-right-box">
                <span>
                    Di chuyển chậm khuôn mặt của bạn để hoàn thành vòng tròn.
                </span>
            </div>
        </div>
        <h4 id="captureNo">0/5</h4>

        <button class="confirm-button">Xong</button>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        let intervalId = null;
        let isProcessing = false;
        let capturedImages = [];
        const maxImages = 5;

        async function startCamera() {
            const camera = document.getElementById('camera');
            const placeholder = document.getElementById('camera-placeholder');
            const cameraFrame = document.querySelector('.camera-frame');
            const captureNo = document.getElementById('captureNo');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                camera.srcObject = stream;
                placeholder.style.display = 'none';

                intervalId = setInterval(async () => {
                    if (isProcessing || capturedImages.length >= maxImages) return;

                    isProcessing = true;

                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    canvas.width = camera.videoWidth;
                    canvas.height = camera.videoHeight;

                    ctx.drawImage(camera, 0, 0, canvas.width, canvas.height);
                    const imageData = canvas.toDataURL('image/png');

                    try {
                        const response = await fetch('{{ route("face.checkposition") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ image: imageData }),
                        });

                        const result = await response.json();
                        if (result.isCentered) {
                            capturedImages.push(imageData);
                            cameraFrame.classList.add('centered');
                            captureNo.textContent = capturedImages.length + "/5";
                            if (capturedImages.length >= maxImages) {
                                clearInterval(intervalId);
                                console.log("Captured all images, sending to server...");
                                await registerUser(capturedImages);
                            }
                        } else {
                            cameraFrame.classList.remove('centered');
                        }
                    } catch (error) {
                        console.error("Error capturing image: ", error);
                    } finally {
                        isProcessing = false;
                    }
                }, 1000);
            } catch (err) {
                alert('Cannot access the camera: ' + err.message);
            }
        }

        async function registerUser(images) {
            const mainWrapper = document.querySelector('.content-wrapper');
            const loader = document.querySelector('.loader');
            try {
                mainWrapper.classList.add('blur');
                loader.style.display = 'block';
                const response = await fetch('{{ route("face.register") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ images }),
                });

                const result = await response.json();
                if (result.success) {
                    alert("Định danh khuôn mặt thành công!");
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error("Error registering user: ", error);
                alert("Lỗi khi đăng ký khuôn mặt.");
            } finally {
                mainWrapper.classList.remove('blur');
                loader.style.display = 'none';
            }
        }
    </script>
@endsection
