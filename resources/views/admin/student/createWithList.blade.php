@extends('layouts.app')

@section('title', 'Thêm danh sách sinh viên')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
<style>
    .form-group {
        display: flex;
        flex-direction: column;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">THÊM DANH SÁCH SINH VIÊN</h4>
        <div id="form-create" class="container">
            <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
                <a href="{{ route('admin.student.downloadExcelFile') }}" class="btn btn-outline-primary">Tải mẫu danh sách</a>
            </div>
            <hr />
            <div class="info-detail">
                <label for="file">Tải file Excel:</label>
                <input type="file" id="file" class="form-control" />
            </div>
            <div class="mt-3 text-lg-end">
                <a href="{{ route('admin.student.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                <button id="btn-create" type="submit" class="btn btn-primary">Tạo</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $("#btn-create").on("click", function () {
            const fileInput = $("#file")[0];
            const file = fileInput.files[0];

            if (!file) {
                alert("Vui lòng chọn file trước khi tải lên.");
                return;
            }

            const formData = new FormData();
            formData.append("file", file);

            $.ajax({
                url: '{{ route("admin.student.storeWithList") }}', // Đường dẫn tới action
                type: 'POST',
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },  
                data: formData,
                success: function (response) {
                    if (response.success) {
                        showAlert("success", "Tạo thành công");
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else {
                        alert("Đã có lỗi xảy ra: " + response.error);
                    }
                },
                error: function () {
                    alert("Có lỗi xảy ra khi gửi yêu cầu.");
                }
            });
        });
    });
</script>
@endsection
