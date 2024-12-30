@extends('layouts.app')

@section('title', 'Thêm lớp học phần')

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
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">THÊM LỚP HỌC PHẦN</h4>
        <div id="form-create" class="container">
            <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
                <div class="d-flex gap-2 align-items-center w-75">
                    <span style="text-wrap: nowrap;">Chọn khóa:</span>
                    <select id="StudyYearDetailId" name="StudyYearDetailId" class="form-control">
                        <option value="">Chọn năm học</option>
                        @foreach($studyYearDetails as $year)
                            <option value="{{ $year['value'] }}">{{ $year['text'] }}</option>
                        @endforeach
                    </select>
                    <span style="text-wrap: nowrap;">Học kỳ:</span>
                    <select id="SemesterId" name="SemesterId" class="form-control" disabled required>
                        <option value="">Chọn học kỳ</option>
                    </select>
                </div>
                <a href="{{ route('admin.courseclass.downloadExcelTemplate') }}" class="btn btn-outline-primary">Tải mẫu danh sách</a>
            </div>
            <hr />
            <div class="info-detail d-none">
                <label for="file">Tải file Excel:</label>
                <input type="file" id="file" class="form-control" />
            </div>
            <div class="mt-3 text-lg-end">
                <a href="{{ route('admin.courseclass.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                <button id="btn-create" type="submit" class="btn btn-primary d-none">Tạo</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#StudyYearDetailId').on('change', function () {
            var yearId = $(this).val();
            if (yearId) {
                $.ajax({
                    url: '{{url("/admin/getsemesterbyyearid")}}/'+yearId,
                    type: 'GET',
                    success: function (data) {
                        if (data && data.length > 0) {
                            var curriculumSelect = $('#SemesterId');
                            curriculumSelect.empty();
                            curriculumSelect.append('<option value="">Chọn học kỳ</option>');

                            $.each(data, function (index, item) {
                                curriculumSelect.append('<option value="' + item.Id + '">' + item.Name + '</option>');
                            });

                            curriculumSelect.prop('disabled', false);
                        } else {
                            $('#SemesterId').prop('disabled', true);
                        }
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu.');
                    }
                });
            } else {
                $('#SemesterId').val(null);
                $('#SemesterId').trigger('change');
                $('#SemesterId').prop('disabled', true);
            }
        });

        $('#SemesterId').on('change', function () {
            var value = $(this).val();
            if (value) {
                $('.info-detail').removeClass("d-none");
                $('#btn-create').removeClass("d-none");
            } else {
                $('.info-detail').addClass("d-none");
                $('#btn-create').addClass("d-none");
            }
        });

        $('#btn-create').on('click', function () {
            const fileInput = $('#file')[0];
            const file = fileInput.files[0];

            if (!file) {
                alert("Vui lòng chọn file trước khi tải lên.");
                return;
            }

            const formData = new FormData();
            formData.append("file", file);
            formData.append("SemesterId", $('#SemesterId').val());

            $.ajax({
                url: '{{ route("admin.courseclass.storeWithList") }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    if (response.success) {
                        showAlert("success","Tạo thành công");
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else {
                        showAlert("error", "Đã có lỗi xảy ra: " + response.error);
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
