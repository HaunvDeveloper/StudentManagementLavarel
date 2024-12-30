@extends('layouts.app')

@section('title', 'Thêm giảng viên')

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
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">THÊM GIẢNG VIÊN</h4>
        <form action="{{ route('admin.lecturer.create.post') }}" method="POST" id="form-create" class="container">
            @csrf
            <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
                <div class="d-flex gap-2 align-items-center w-75">
                    <span style="text-wrap: nowrap;">Mã giảng viên:</span>
                    <input name="id" class="form-control" value="{{ $newId }}" type="text" required />
                </div>
                <div class="d-flex gap-2 mt-2 align-items-center w-75">
                    <span style="text-wrap: nowrap;">Họ lót</span>
                    <input name="last_name" class="form-control" required />
                    <span style="text-wrap: nowrap;">Tên</span>
                    <input name="first_name" class="form-control" required />
                </div>
                <div class="d-flex gap-2 mt-2 align-items-center w-75">
                    <span style="text-wrap: nowrap;">Khoa</span>
                    <select name="dept_id" class="form-control select2">
                        @foreach ($departments as $department)
                        <option value="{{ $department->Id }}">{{ $department->Name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <hr />
            <div class="row info-detail">
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label for="cccd">CCCD</label>
                        <input name="nation_id" class="form-control" type="text" required />
                    </div>
                    <div class="form-group mb-3">
                        <label for="day_of_birth">Ngày sinh</label>
                        <input name="day_of_birth" class="form-control" type="date" required />
                    </div>
                    <div class="form-group mb-3">
                        <label for="birth_place">Nơi sinh</label>
                        <input name="birth_place" class="form-control" type="text" />
                    </div>
                    <div class="form-group mb-3">
                        <label for="sex">Giới tính</label>
                        <select name="sex" class="form-control">
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="phone_no">Số điện thoại</label>
                        <input name="phone_no" class="form-control" type="text" />
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label for="ethnicity">Dân tộc</label>
                        <select name="nation" class="form-control select2">
                            @foreach ($nationList as $nation)
                            <option value="{{ $nation['EthnicName'] }}">{{ $nation['EthnicName'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="religion">Tôn giáo</label>
                        <select name="religion" class="form-control select2">
                            @foreach ($religionList as $religion)
                            <option value="{{ $religion['ReligionName'] }}">{{ $religion['ReligionName'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input name="email" class="form-control" type="email" required />
                    </div>
                    <div class="form-group mb-3">
                        <label for="street_address">Địa chỉ nhà</label>
                        <input name="street_address" class="form-control" type="text" required />
                    </div>
                </div>
            </div>

            <div class="mt-3 text-lg-end">
                <a href="{{ route('admin.lecturer.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                <button id="btn-create" type="submit" class="btn btn-primary ">Tạo</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('form').submit(function (event) {
    event.preventDefault(); // Ngăn form gửi theo cách mặc định

    // Tạo đối tượng FormData từ form
    var formData = new FormData(this);

    // Thêm trường FullName
    var fullName = $('#LastName').val() + ' ' + $('#FirstName').val();
    formData.append("FullName", fullName);

    // Gửi AJAX
    $.ajax({
        url: '{{ route("admin.lecturer.create.post") }}',
        method: 'post',
        data: formData,
        contentType: false, // Không đặt contentType để FormData hoạt động chính xác
        processData: false, // Không xử lý dữ liệu vì FormData sẽ làm việc này
        success: function (response) {
            if (response.success) {
                showAlert("success", "Tạo thành công");
                setTimeout(function () {
                    window.location.href = response.redirect;
                }, 1500);
            } else {
                showAlert("error", response.error);
            }
        },
        error: function () {
            showAlert("error", "Có lỗi xảy ra khi gửi yêu cầu.");
        }
    });
});
</script>
@endsection
