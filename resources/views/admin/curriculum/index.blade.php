@extends('layouts.app')

@section('title', 'Danh sách chương trình đào tạo')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">DANH SÁCH CHƯƠNG TRÌNH ĐÀO TẠO</h4>
        <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex gap-2 align-items-center w-100">
                    <span style="text-wrap: nowrap;">Chọn khóa:</span>
                    {!! Form::select('StudyYearId', $studyYears, null, ['class' => 'form-control', 'placeholder' => 'Tất cả', 'id' => 'StudyYearId']) !!}

                    <span style="text-wrap: nowrap;">Chọn ngành:</span>
                    {!! Form::select('MajorId', $majors, null, ['class' => 'form-control', 'placeholder' => 'Tất cả', 'id' => 'MajorId']) !!}

                    <button id="btn-search" class="btn btn-primary">LỌC</button>
                </div>
            </div>
            <a href="{{ route('admin.curriculum.create') }}" class="btn btn-add-course">+ Thêm chương trình</a>
        </div>

        <div class="table-data">
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#btn-search').on('click', function () {
            var studyYearId = $('#StudyYearId').val();
            var majorId = $('#MajorId').val();
            $.ajax({
                url: '{{ route("admin.curriculum.getList") }}',
                data: { yearId: studyYearId, majorId: majorId },
                type: 'GET',
                success: function (response) {
                    $('.table-data').html(response);
                },
                error: function (err) {
                    $('.table-data').html(err);
                }
            });
        });
        $('#btn-search').trigger('click');
    });
</script>
@endsection
