@extends('layouts.app')

@section('title', 'Thêm danh sách sinh viên vào lớp học phần')

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">THÊM DANH SÁCH SINH VIÊN VÀO LỚP HỌC</h4>
        <div id="form-create" class="container">
            <input type="hidden" id="Id" value="{{ $courseClass->Id }}">
            <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
                <div class="d-flex gap-2 mt-2 align-items-center w-75">
                    <label class="form-label text-danger" style="text-wrap: nowrap;">Mã lớp học phần:</label>
                    <label class="displayModel form-label">{{ $courseClass->Code }}</label>
                </div>
                <div class="d-flex gap-2 mt-2 align-items-center w-75">
                    <label class="form-label text-danger" style="text-wrap: nowrap;">Tên lớp học phần:</label>
                    <label class="displayModel form-label">{{ $courseClass->Name }}</label>
                </div>
                <a href="{{ route('admin.courseclass.downloadImportStudentList') }}" class="btn btn-outline-primary">Tải mẫu danh sách</a>
            </div>
            <hr />
            <div class="row info-detail">
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Môn học:</label>
                        <label class="displayModel form-label">{{ $courseClass->Subject->Name ?? 'N/A' }}</label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Lớp sinh viên:</label>
                        <label class="displayModel form-label">{{ $courseClass->StudentClass->Code ?? 'N/A' }}</label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Thời khóa biểu:</label>
                        <label class="displayModel form-label">{{ $courseClass->WeakDays ?? 'N/A' }}</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Ngày bắt đầu:</label>
                        <label class="displayModel form-label">{{ $courseClass->StartDate->format('d/m/Y') }}</label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Ngày kết thúc:</label>
                        <label class="displayModel form-label">{{ $courseClass->EndDate->format('d/m/Y') }}</label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Sĩ số tối đa:</label>
                        <label class="displayModel form-label">{{ $courseClass->MaxQuantity }}</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Phòng:</label>
                        <label class="displayModel form-label">{{ $courseClass->DefaultRoom->Name ?? 'N/A' }}</label>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Giảng viên:</label>
                        <label class="displayModel form-label">{{ $courseClass->Lecturer->FullName ?? 'N/A' }}</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="file">Tải file Excel:</label>
                    <input type="file" id="file" class="form-control" />
                </div>
            </div>
            <div class="mt-3 text-lg-end">
                <a href="{{ route('admin.courseclass.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                <button id="btn-create" type="submit" class="btn btn-primary">Thêm</button>
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
            formData.append("Id", $('#Id').val());

            $.ajax({
                url: '{{ route("admin.courseclass.storeImportedStudentList") }}', // URL action
                type: 'POST',
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        showAlert("success", "Tạo thành công");
                        if (response.message) {
                            alert(response.message);
                        }
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
