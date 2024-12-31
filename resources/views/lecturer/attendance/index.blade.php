@php
    $title = "Điểm danh online";
@endphp

@extends('layouts.app')

@section('links')
    <link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/checkbox.css') }}">
    <style>
        .form-control-sm {
            outline: none;
            border: none;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">ĐIỂM DANH LỚP HỌC PHẦN</h4>
        <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex gap-2 flex-wrap align-items-center w-100">
                    <div>
                        <span style="text-wrap: nowrap;">Chọn niên học:</span>
                        @php
                            use Carbon\Carbon;
                            $now = Carbon::now(); // Get the current date and time
                        @endphp
                        <select id="StudyYearDetailId" name="StudyYearDetailId" class="form-control">
                        @foreach($studyYearDetails as $detail)
                            @php
                                // Check if the current date is within the StartYear and EndYear range
                                $startYear = Carbon::create($detail->StartYear, 1, 1);
                                $endYear = Carbon::create($detail->EndYear, 12, 31);
                                $isSelected = $now->between($startYear, $endYear) ? 'selected' : '';
                            @endphp
                            <option value="{{ $detail->Id }}" {{ $isSelected }}>
                                {{ $detail->StartYear }} - {{ $detail->EndYear }}
                            </option>
                        @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <span style="text-wrap: nowrap;">Học kỳ:</span>
                        <select id="SemesterId" name="SemesterId" class="form-control" required>
                            <option value="">Chọn học kỳ</option>
                        </select>
                    </div>
                    
                    <div>
                        <span style="text-wrap: nowrap;">Lớp học phần:</span>
                        <select id="CourseClassId" name="CourseClassId" class="form-control" required>
                        </select>
                    </div>

                    <div>
                        <span style="text-wrap: nowrap;">Buổi học:</span>
                        <select id="LessonId" name="LessonId" class="form-control" required>
                        </select>
                    </div>
                    
                    <button class="activate mt-4 btn btn-info">Kích hoạt chức năng điểm danh</button>
                </div>
            </div>
            <div></div>
        </div>

        <div class="table-data table-responsive">
            <!-- Nội dung bảng danh sách sinh viên sẽ được hiển thị ở đây -->
        </div>
        <div class="text-lg-end">
            <button id="save" class="btn btn-primary">LƯU</button>
        </div>
    </div>
</div>
@endsection


@section("scripts")
    <script src="https://cdnjs.cloudflare.com/ajax/libs/microsoft-signalr/6.0.0/signalr.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            $('.activate').on('click', function () {
                const isActive = $(this).hasClass('btn-info');
                const lessonId = $('#LessonId').val();

                $.ajax({
                    url: '{{ route("api.lecturer.activate") }}',
                    method: 'POST',
                    data: { active: isActive, lessonId },
                    success: function (response) {
                        if (response.success) {
                            if (!isActive) {
                                showToast("success", "Tắt thành công");
                                $('.activate').text('Kích hoạt chức năng điểm danh').addClass('btn-info').removeClass('btn-danger');
                            } else {
                                showToast("success", "Kích hoạt thành công");
                                $('.activate').text('Tắt thiết bị').removeClass('btn-info').addClass('btn-danger');
                            }
                        } else {
                            showToast("error", response.error || "Kích hoạt thất bại");
                        }
                    },
                    error: function () {
                        showToast("error", "Kích hoạt thất bại");
                    }
                });
            });

            $('#save').on('click', function () {
                const list = [];
                $('.row-student').each(function () {
                    list.push({
                        StudentId: $(this).data('id'),
                        LessonId: $('#LessonId').val(),
                        Status: $(this).find('input[type=checkbox]:checked').val(),
                        LateLessons: $(this).find('.lateLesson').val(),
                        Description: $(this).find('.description').val()
                    });
                });

                $.ajax({
                    url: '{{ route("lecturer.attendance.save") }}',
                    method: 'POST',
                    data: { list },
                    success: function (response) {
                        if (response.success) {
                            showToast("success", "Lưu thành công");
                        } else {
                            showToast("error", "Lưu thất bại");
                        }
                    },
                    error: function () {
                        showToast("error", "Lưu thất bại");
                    }
                });
            });

            $(document).on('change', 'input[type=checkbox]', function () {
                const name = $(this).attr('name');

                if ($(this).is(':checked')) {
                    $(`input[type=checkbox][name="${name}"]`).not(this).prop('checked', false);

                    if ($(this).val() === "Đi trễ") {
                        $(this).closest('tr').find('.lateLesson').prop('disabled', false);
                    } else {
                        $(this).closest('tr').find('.lateLesson').prop('disabled', true).val('');
                    }
                } else {
                    $(this).closest('tr').find('.lateLesson').prop('disabled', true).val('');
                }
            });

            $('#LessonId').on('change', function () {
                $('.table-data').html('');
                const classId = $('#CourseClassId').val();
                const lessonId = $(this).val();

                $.ajax({
                    url: '{{ route("partial.lecturer.getListStudent") }}',
                    method: 'GET',
                    data: { classId, lessonId },
                    success: function (response) {
                        $('.table-data').html(response);
                    },
                    error: function () {
                        $('.table-data').html('<p>Không thể tải danh sách sinh viên.</p>');
                    }
                });
            });

            $('#CourseClassId').on('change', function () {
                $('.table-data').html('');
                const classId = $(this).val();

                $.ajax({
                    url: '{{ route("api.lecturer.getLessons") }}',
                    method: 'GET',
                    data: { classId },
                    success: function (data) {
                        const lessonDropdown = $('#LessonId').empty();
                        if (data.length > 0) {
                            data.forEach(item => lessonDropdown.append(new Option(item.Name, item.Id)));
                            lessonDropdown.trigger('change');
                        } else {
                            lessonDropdown.trigger('change');
                        }
                    },
                    error: function () {
                        $('.table-data').html('<p>Không thể tải danh sách buổi học.</p>');
                    }
                });
            });

            $('#SemesterId').on('change', function () {
                $('.table-data').html('');
                const semesterId = $(this).val();

                $.ajax({
                    url: '{{ route("api.lecturer.getClasses") }}',
                    method: 'GET',
                    data: { semesterId },
                    success: function (data) {
                        const classDropdown = $('#CourseClassId').empty();
                        if (data.length > 0) {
                            data.forEach(item => classDropdown.append(new Option(item.Name, item.Id)));
                            classDropdown.trigger('change');
                        } else {
                            classDropdown.trigger('change');
                        }
                    },
                    error: function () {
                        $('.table-data').html('<p>Không thể tải danh sách lớp học phần.</p>');
                    }
                });
            });

            $('#StudyYearDetailId').on('change', function () {
                const yearId = $(this).val();
                if (yearId) {
                    $.ajax({
                        url: '{{ url("/admin/getsemesterbyyearid") }}/' + yearId,
                        method: 'GET',
                        success: function (data) {
                            const semesterDropdown = $('#SemesterId').empty();
                            if (data.length > 0) {
                                const now = new Date();
                                data.forEach(item => {
                                    const startDate = new Date(item.StartDate);
                                    const endDate = new Date(item.EndDate);
                                    const isSelected = now >= startDate && now <= endDate;
                                    semesterDropdown.append(new Option(item.Name, item.Id, false, isSelected));
                                });
                                semesterDropdown.trigger('change');
                            }
                        },
                        error: function () {
                            showToast("error", "Không thể tải danh sách học kỳ.");
                        }
                    });
                }
            });

            $('#StudyYearDetailId').trigger('change');
        });
    </script>
@endsection
