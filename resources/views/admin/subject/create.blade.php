@extends('layouts.app')
@section('title', 'Danh sách môn học') 


@section('links')
<link rel="stylesheet" href="~/assets/css/course.css">
@endsection

@section('content')
<div class="container">
    <form action="{{ route('admin.subject.create.post') }}" method="post" class="table-container m-4">
        @csrf
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">TẠO MÔN HỌC</h4>
        <div class="form-group">
            <label for="code">Mã môn</label>
            <input id="code" name="code" class="form-control" required />
        </div>
        <div class="form-group">
            <label for="name">Tên môn</label>
            <input name="name" id="name" class="form-control" required />
        </div>
        <div class="form-group">
            <label for="default_credits">STC</label>
            <input name="default_credits" id="default_credits" type="number" min="1" max="50" value="1" class="form-control" required />
        </div>
        <div class="form-group">
            <label for="default_lesson">Số tiết</label>
            <input name="default_lesson" id="default_lesson" type="number" min="1" max="100" value="1" class="form-control" required />
        </div>
        <div class="form-group">
            <label for="dept_id">Khoa</label>
            <select name="dept_id" id="dept_id" class="form-control select2">
                @foreach ($departments as $department)
                    <option value="{{ $department->Id }}">{{ $department->Name }}</option>
                @endforeach
            </select>
        </div>
        <div class="text-lg-end mt-2">
            <a href="{{ route('admin.subject.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            <button type="reset" class="btn btn-outline-success">Đặt lại</button>
            <button type="submit" class="btn btn-primary">Tạo</button>
        </div>
        <div class="text-danger mt-2">{{$alert ?? ""}}</div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $('.select2').select2({
        placeholder: "Chọn môn học",
    });
    $('form').submit(function (event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: '{{ route("admin.subject.create.post") }}',
            method: 'post',
            data: formData,
            contentType: false, 
            processData: false, 
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