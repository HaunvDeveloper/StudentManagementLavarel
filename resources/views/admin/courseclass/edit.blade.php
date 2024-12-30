@extends('layouts.app')

@section('title', 'Sửa lớp học phần')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
<style>
    .form-group {
        display: flex;
        flex-direction: column;
    }

    th {
        color: white !important;
        text-align: center !important;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">SỬA THÔNG TIN LỚP HỌC PHẦN</h4>
        <form id="form-create" action="{{ route('admin.courseclass.update', $model->Id) }}" method="post" class="container">
            @csrf
            @method('PUT')
            <input type="hidden" name="Id" value="{{ $model->Id }}">
            <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
                <div class="d-flex gap-2 mt-2 align-items-center w-75">
                    <label for="Code" class="form-label text-danger" style="text-wrap: nowrap;">Mã lớp học phần</label>
                    <input type="text" name="Code" id="Code" value="{{ $model->Code }}" class="form-control" required>
                </div>
                <div class="d-flex gap-2 mt-2 align-items-center w-75">
                    <label for="Name" class="form-label text-danger" style="text-wrap: nowrap;">Tên lớp học phần</label>
                    <input type="text" name="Name" id="Name" value="{{ $model->Name }}" class="form-control" required>
                </div>
            </div>
            <hr />
            <div class="row info-detail">
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label for="SubjectId" class="form-label">Môn học</label>
                        <select name="SubjectId" id="SubjectId" class="form-control select2" required>
                            @foreach($subjects as $id => $name)
                                <option value="{{ $id }}" {{ $id == $model->SubjectId ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="StudentClassId" class="form-label">Lớp sinh viên</label>
                        <select name="StudentClassId" id="StudentClassId" class="form-control select2" required>
                            <option value="">Không có</option>
                            @foreach($studentClasses as $id => $code)
                                <option value="{{ $id }}" {{ $id == $model->StudentClassId ? 'selected' : '' }}>{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="MaxQuantity" class="form-label">Sĩ số tối đa</label>
                        <input type="number" name="MaxQuantity" id="MaxQuantity" value="{{ $model->MaxQuantity }}" class="form-control" required min="1" max="500">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label for="StartDate" class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="StartDate" id="StartDate" value="{{ $model->StartDate->format('Y-m-d') }}" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="EndDate" class="form-label">Ngày kết thúc</label>
                        <input type="date" name="EndDate" id="EndDate" value="{{ $model->EndDate->format('Y-m-d') }}" class="form-control" required>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label for="DefaultRoomId" class="form-label">Phòng</label>
                        <select name="DefaultRoomId" id="DefaultRoomId" class="form-control select2" required>
                            @foreach($rooms as $room)
                                <option value="{{ $room['value'] }}" {{ $id == $model->DefaultRoomId ? 'selected' : '' }}>{{ $room['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="LecturerId" class="form-label">Giảng viên</label>
                        <select name="LecturerId" id="LecturerId" class="form-control select2" required>
                            @foreach($lecturers as $lecturer)
                                <option value="{{ $lecturer['value'] }}" {{ $id == $model->LecturerId ? 'selected' : '' }}>{{ $lecturer['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <hr />
            <div class="lessonList">
                <h5 class="text-center text-primary">CÁC BUỔI HỌC</h5>
                <table class="table table-sm table-hover table-bordered">
                    <thead class="table-primary table-hover table-striped">
                        <tr>
                            <th>STT</th>
                            <th>Tiết bắt đầu</th>
                            <th>Tiết kết thúc</th>
                            <th>Ngày</th>
                            <th>Phòng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $stt = 0; @endphp
                        @foreach($model->Lessons as $lesson)
                        <tr>
                            <input type="hidden" name="Lessons[{{ $stt }}][Id]" value="{{ $lesson->Id }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <select name="Lessons[{{ $stt }}][StartLesson]" class="form-control-sm startLesson">
                                    @foreach($lessons as $item)
                                        <option value="{{ $item['value'] }}" {{ $item['value'] == $lesson->StartLesson ? 'selected' : '' }}>{{ $item['text'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="Lessons[{{ $stt }}][EndLesson]" class="form-control-sm endLesson">
                                    @foreach($lessons as $item)
                                    <option value="{{ $item['value'] }}" {{ $item['value'] == $lesson->EndLesson ? 'selected' : '' }}>{{ $item['text'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="date" name="Lessons[{{ $stt }}][Date]" value="{{ $lesson->Date->format('Y-m-d') }}" class="form-control">
                            </td>
                            <td>
                                <select name="Lessons[{{ $stt }}][RoomId]" class="form-control-sm roomId select2" required>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room['value'] }}" {{ $room['value'] == $lesson->RoomId ? 'selected' : '' }}>{{ $room['text'] }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)">Xoá</button>
                            </td>
                        </tr>
                        @php $stt++; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-lg-end">
                <a href="{{ route('admin.courseclass.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                <button id="btn-create" type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $('.select2').select2({
        placeholder: "Chọn môn học",
    });

    function getDayOfWeek(dateString) {
        const date = new Date(dateString);
        if (isNaN(date)) return "";
        const days = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];

        return days[date.getDay()];
    }

    function deleteRow(elem) {
        // Xóa hàng hiện tại
        $(elem).closest('tr').remove();

        // Cập nhật lại các chỉ số stt
        $('tbody tr').each(function (index) {
            $(this).find('input, select').each(function () {
                let name = $(this).attr('name');
                let id = $(this).attr('id');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
                if (id) {
                    $(this).attr('id', id.replace(/\_\d+\_/, `_${index}_`));
                }
            });

            $(this).find('td:first').text(index + 1);
        });
    }

    $(document).ready(function () {
        $('#StudyYearDetailId').on('change', function () {
            var yearId = $(this).val();
            if (yearId) {
                $.ajax({
                    url: '/api/API/GetSemesterByYearDetail?yearDetailId=' + yearId,
                    type: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        if (data && data.length > 0) {
                            var curriculumSelect = $('#SemesterId');
                            curriculumSelect.empty();
                            curriculumSelect.append('<option value="">Chọn học kỳ</option>');

                            $.each(data, function (index, item) {
                                curriculumSelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                            });

                            curriculumSelect.prop('disabled', false);  // Kích hoạt dropdown Chọn ngành
                        } else {
                            $('#SemesterId').prop('disabled', true);  // Nếu không có ngành thì disable dropdown
                        }
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu.');
                    }
                });
            } else {
                $('#SemesterId').val(null);
                $('#SemesterId').trigger('change');
                $('#SemesterId').prop('disabled', true);  // Nếu không chọn khoa thì disable dropdown Chọn ngành
            }
        });

        $("#SemesterId").on('change', function () {
            var value = $(this).val();
            if (value) {
                $('.info-detail').removeClass("d-none");
                $('#btn-create').removeClass("d-none");
            } else {
                $('.info-detail').addClass("d-none");
                $('#btn-create').addClass("d-none");
            }
        });

        $("#EndLesson, #StartLesson").on('change', function () {
            var startLesson = parseInt($("#StartLesson").val());
            var endLesson = parseInt($('#EndLesson').val());
            if (endLesson < startLesson) {
                alert("Tiết đầu luôn phải nhỏ hơn tiết cuối.");
                $("#StartLesson").val("1");
                $('#EndLesson').val("2");
            }
        });

        $("#StartDate").on("change", function () {
            const startDate = $(this).val(); // Lấy giá trị ngày bắt đầu
            const dayOfWeek = getDayOfWeek(startDate); // Tính thứ trong tuần
            $("#WeakDay").val(dayOfWeek); // Điền vào trường WeakDay
        });

        $('#StartDate').trigger('change');

        $('#form-create').submit(function (event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: '{{ route("admin.courseclass.update", $model->Id) }}',
                method: 'post',
                data: formData,
                success: function (response) {
                    if (response.success) {
                        showAlert("success", "Sửa thành công");
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else {
                        showAlert("error", "Sửa thất bại");
                    }
                }
            });
        });
    });
</script>
@endsection
