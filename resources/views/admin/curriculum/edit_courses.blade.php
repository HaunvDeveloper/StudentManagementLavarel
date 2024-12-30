@extends('layouts.app')

@section('title', 'Chương trình đào tạo')

@section('links')
<style>
    .table th {
        color: white !important;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="container mt-3">
    <div class="table-container">
        <h4 class="text-center mb-4 mt-2" style="font-size:35px;font-weight:bold;">CHƯƠNG TRÌNH ĐÀO TẠO</h4>
        <h6 class="my-2">{{ $curriculum->Code }} - {{ $curriculum->Name }}</h6>
        <input type="hidden" id="curriculumId" value="{{ $curriculum->Id }}" />
        <input type="hidden" id="studyYearId" value="{{ $curriculum->StudyYearId }}" />
        <input type="hidden" id="curriculumCode" value="{{ $curriculum->Code }}" />
        <input type="hidden" id="curriculumName" value="{{ $curriculum->Name }}" />

        <table class="table table-hover table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Mã học phần</th>
                    <th>Tên học phần</th>
                    <th>STC</th>
                    <th>Số Tiết</th>
                    <th>Loại HP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listYear as $year)
                    <tr class="table-success">
                        <td colspan="7">Năm học {{ $year->StartYear }} - {{ $year->EndYear }}</td>
                    </tr>
                    @foreach ($year->semesters as $hk)
                        <tr class="table-secondary">
                            <td colspan="7">{{ $hk->Name }}</td>
                        </tr>
                        @foreach ($curriculum->courses->where('SemesterId', $hk->Id) as $course)
                            <tr class="row-course" data-semesterid="{{ $hk->Id }}" data-subjectid="{{ $course->SubjectId }}"
                                data-id="{{ $course->Id }}">
                                <td class="subjectCode">{{ $course->subject->Code }}</td>
                                <td class="subjectName">
                                    <select class="form-control select2 SubjectId"
                                        name="SubjectId_{{ $hk->Id }}_{{ $course->SubjectId }}">
                                        @foreach ($listSubject as $subject)
                                            <option value="{{ $subject->Id }}" @if ($subject->Id == $course->SubjectId) selected @endif>
                                                {{ $subject->Name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="subjectCredits">{{ $course->Credits }}</td>
                                <td class="subjectLesson">
                                    <input class="subjectLessonVal" type="number" min="1" max="200" value="{{ $course->Lesson }}" />
                                </td>
                                <td class="subjectType">
                                    <select class="form-control" name="subjectTypeId">
                                        @foreach ($courseTypes as $type)
                                            <option value="{{ $type->Id }}" @if ($type->Id == $course->TypeId) selected @endif>
                                                {{ $type->Name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="service">
                                    <button class="btn btn-danger" onclick="deleteRow(this)">Xóa</button>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="row-course" data-semesterid="{{ $hk->Id }}" data-id="0">
                            <td class="subjectCode"></td>
                            <td class="subjectName">
                                <select class="form-control select2 SubjectId" name="SubjectId_{{ $hk->Id }}_">
                                    <option value="">Chọn môn học</option>
                                    @foreach ($listSubject as $subject)
                                        <option value="{{ $subject->Id }}">{{ $subject->Name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="subjectCredits"></td>
                            <td class="subjectLesson">
                                <input class="subjectLessonVal" type="number" min="1" max="200" value="1" />
                            </td>
                            <td class="subjectType"></td>
                            <td class="service"></td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
        <div class="text-lg-end">
            <a href="" class="m-2 btn btn-lg btn-danger">Làm mới</a>
            <button id="btn-create" class="m-2 btn btn-lg btn-primary">Lưu thay đổi</button>
        </div>

    </div>
</div>

<div id="dropdownHtml" style="display:none;">
    <select class="form-control select2 SubjectId" name="SubjectId_0">
        <option value="">Chọn môn học</option>
        @foreach ($listSubject as $subject)
            <option value="{{ $subject->Id }}">{{ $subject->Name }}</option>
        @endforeach
    </select>
</div>

@endsection

@section('scripts')
<script>
    $('#btn-create').on('click', function () {
        var courses = [];
        var curriculumId = $('#curriculumId').val();
        $('.row-course').each(function (index, elem) {
            var course = {
                Id: $(elem).attr('data-id'),
                SubjectId: $(elem).attr('data-subjectid'),
                SemesterId: $(elem).attr('data-semesterid'),
                TypeId: $(elem).find('.subjectType select').val(),
                Credits: $(elem).find('.subjectCredits').text(),
                Lesson: $(elem).find('.subjectLessonVal').val()
            };
            if (course.SubjectId) {
                courses.push(course);
            }
        });
        console.log(courses);
        $.ajax({
            url: '{{ route("admin.curriculum.update", $curriculum->Id) }}',
            method: 'PUT',
            data: {
                curriculumId: curriculumId,
                courses: courses,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                alert('Thành công');
                window.location.href = response.redirect;
            }
        });
    });

    var newRow = `
        <tr id="new-row" class="row-course" data-semesterid="1"  data-id="0">
            <td class="subjectCode"></td>
            <td class="subjectName">
               `+ $('#dropdownHtml').html() + `
            </td>
            <td class="subjectCredits"></td>
            <td class="subjectLesson">
                <input class="subjectLessonVal" type="number" min="1" max="200" value="1" />
            </td>
            <td class="subjectType"></td>
            <td class="service"></td>
        </tr>
    `;

    $(document).ready(function () {
        $('.select2').select2({
            placeholder: "Chọn môn học",
        });

        $(document).on('change', '.SubjectId', function () {
            var selectedValue = $(this).val();
            var tr = $(this).closest('tr');
            var htmlId = $(this).attr("id");
            var semesterId = $(tr).attr('data-semesterid');
            $.ajax({
                url: '{{url("/admin/getsubject/")}}/' + selectedValue,
                method: 'get',
                success: function (response) {
                    if (response) {
                        $(tr).attr('data-subjectid', response.id);
                        $(tr).attr('data-id', '0');
                        $(tr).find('.subjectCode').text(response.code);
                        $(tr).find('.SubjectId').attr("id", htmlId + response.id);
                        $(tr).find('.SubjectId').attr("name", htmlId + response.id);
                        $(tr).find('.subjectCredits').text(response.defaultCredits);
                        $(tr).find('.subjectLessonVal').val(response.defaultLesson);
                        $(tr).find('.subjectType').html(`<select class="form-control">` + response.typeOptions + `</select>`);
                        $(tr).find('.service').html(`
                            <button class="btn btn-danger" onclick="deleteRow(this)">Xóa</button>
                        `);
                        $(tr).after(newRow);
                        let addedRow = $('#new-row');
                        $(addedRow).attr('data-semesterid', semesterId);
                        $(addedRow).removeAttr('id');
                        $(addedRow).find('.SubjectId').attr('id', 'Subject_' + semesterId + '_');
                        $(addedRow).find('.SubjectId').attr('name', 'Subject_' + semesterId + '_');
                        $('.select2').select2({
                            placeholder: "Chọn môn học",
                        });
                    }
                }
            });
        });
    });

    function deleteRow(btn) {
        $(btn).closest('tr').remove();
    }
</script>
@endsection