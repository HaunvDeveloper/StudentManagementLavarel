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
                <div class="d-flex gap-2 mt-2 align-items-center w-75">
                    <label for="Code" class="form-label text-danger" style="text-wrap: nowrap;">Mã lớp học phần</label>
                    <input id="Code" name="Code" class="form-control" type="text" required>
                </div>
                <div class="d-flex gap-2 mt-2 align-items-center w-75">
                    <label for="Name" class="form-label text-danger" style="text-wrap: nowrap;">Tên lớp học phần</label>
                    <input id="Name" name="Name" class="form-control" type="text" required>
                </div>
            </div>
            <hr />
            <div class="row info-detail d-none">
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label for="SubjectId" class="form-label">Môn học</label>
                        <select id="SubjectId" name="SubjectId" class="form-control select2" required>
                            <option value="">Chọn môn học</option>
                            @foreach($subjects as $Id => $Name)
                                <option value="{{ $Id }}">{{ $Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="StudentClassId" class="form-label">Lớp sinh viên</label>
                        <select id="StudentClassId" name="StudentClassId" class="form-control select2">
                            <option value="">Không có</option>
                            @foreach($studentClasses as $Id => $Code)
                                <option value="{{ $Id }}">{{ $Code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="LessonNo" class="form-label">Số Tiết</label>
                        <input id="LessonNo" class="form-control" type="number" disabled>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label for="StartDate" class="form-label">Ngày bắt đầu</label>
                        <input id="StartDate" name="StartDate" class="form-control" type="date" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="EndDate" class="form-label">Ngày kết thúc</label>
                        <input id="EndDate" name="EndDate" class="form-control" type="date" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="MaxQuantity" class="form-label">Sĩ số tối đa</label>
                        <input id="MaxQuantity" name="MaxQuantity" class="form-control" type="number" required min="1" max="500">
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group mb-3">
                        <label for="DefaultRoomId" class="form-label">Phòng mặc định</label>
                        <select id="DefaultRoomId" name="DefaultRoomId" class="form-control select2" required>
                            @foreach($rooms as $room)
                                <option value="{{ $room['value'] }}">{{ $room['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="LecturerId" class="form-label">Giảng viên</label>
                        <select id="LecturerId" name="LecturerId" class="form-control select2" required>
                            @foreach($lecturers as  $lecturer)
                            <option value="{{ $lecturer['value'] }}">{{ $lecturer['text'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="lessonList d-none">
                <h5 class="text-center text-primary">NGÀY HỌC TRONG TUẦN</h5>
                <table class="table table-sm table-hover table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>STT</th>
                            <th>Thứ</th>
                            <th>Tiết bắt đầu</th>
                            <th>Tiết kết thúc</th>
                            <th>Phòng</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="list-row-weekday">
                       
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-center mt-2">
                                <button onclick="addRow()" type="button" class="btn btn-info">Thêm buổi</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>

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
    $('.select2').select2({
        placeholder: "Chọn môn học",
    });
</script>
<script>
    var newRow = `
        <td class="stt">1</td>
        <td>
            <select class="form-control weekDay" required>
                @foreach($weekDays as $day)
                    <option value="{{ $day->id }}">{{ $day->toString() }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control startLesson" required>
                @foreach($lessons as $lesson)
                    <option value="{{ $lesson['value'] }}">{{ $lesson['text'] }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control endLesson" required>
                @foreach($lessons as $lesson)
                    <option value="{{ $lesson['value'] }}">{{ $lesson['text'] }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <select class="form-control lessonRoom" required>
                @foreach($rooms as $room)
                    <option value="{{ $room['value'] }}">{{ $room['text'] }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteRow(this)">Xoá</button>
        </td>
    `;

    function getDayOfWeek(dateString) {
        const date = new Date(dateString);
        if (isNaN(date)) return "";
        const days = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];

        return days[date.getDay()];
    }

    function deleteRow(elem) {
        $(elem).closest('tr').remove();
        $('tbody tr').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    function addRow() {
        var tbody = $('.list-row-weekday');
        var newTr = document.createElement('tr');
        newTr.innerHTML = newRow;
        $(tbody).append($(newTr));
        $('tbody tr').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    $(document).ready(function () {
        addRow();
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
                $('.lessonList').removeClass("d-none");
            } else {
                $('.info-detail').addClass("d-none");
                $('#btn-create').addClass("d-none");
                $('.lessonList').addClass("d-none");
            }
        });

        $('#EndLesson, #StartLesson').on('change', function () {
            var startLesson = parseInt($('#StartLesson').val());
            var endLesson = parseInt($('#EndLesson').val());
            if (endLesson < startLesson) {
                alert("Tiết đầu luôn phải nhỏ hơn tiết cuối");
                $('#StartLesson').val("1");
                $('#EndLesson').val("2");
            }
        });

        $('#StartDate').on('change', function () {
            const startDate = $(this).val();
            const dayOfWeek = getDayOfWeek(startDate);
            $('#WeakDay').val(dayOfWeek);
        });

        $('#SubjectId').on('change', function () {
            $.ajax({
                url: '{{url("/api/GetNewCodeCourseClass")}}',
                method: 'GET',
                data: { subjectId: $('#SubjectId').val(), semesterId: $('#SemesterId').val() },
                success: function (response) {
                    $('#Code').val(response.code);
                    $('#Name').val(response.name);
                    $('#LessonNo').val(response.lessonNo);
                }
            });
        });

        $('#btn-create').on('click', function (event) {
            var model = {
                SemesterId: $('#SemesterId').val(),
                Code: $('#Code').val(),
                Name: $('#Name').val(),
                SubjectId: $('#SubjectId').val(),
                StudentClassId: $('#StudentClassId').val(),
                StartDate: $('#StartDate').val(),
                EndDate: $('#EndDate').val(),
                MaxQuantity: $('#MaxQuantity').val(),
                DefaultRoomId: $('#DefaultRoomId').val(),
                LecturerId: $('#LecturerId').val()
            };
            var weekDays = [];
            $('.list-row-weekday').find('tr').each(function (index, elem) {
                var ob = {
                    WeekDayId: $(elem).find('.weekDay').val(),
                    StartLessonId: $(elem).find('.startLesson').val(),
                    EndLessonId: $(elem).find('.endLesson').val(),
                    RoomId: $(elem).find('.lessonRoom').val()
                };
                weekDays.push(ob);
            });

            $.ajax({
                url: '{{ route("admin.courseclass.store") }}',
                method: 'POST',
                data: { model, weekDays },
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        alert("Tạo thành công");
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 1500);
                    } else {
                        alert("Tạo thất bại");
                    }
                }
            });
        });
    });
</script>
@endsection
