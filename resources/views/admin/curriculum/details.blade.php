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
        <div class="d-flex justify-content-between">
            <h5 class="my-2">{{ $curriculum->Code }} - {{ $curriculum->Name }}</h5>
            <a class="btn btn-primary" href="{{ route('admin.curriculum.editCourses', ['id' => $curriculum->Id]) }}">Chỉnh sửa</a>
        </div>
        <table class="table mt-2 table-hover table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>STT</th>
                    <th>Mã học phần</th>
                    <th>Tên học phần</th>
                    <th>STC</th>
                    <th>Số Tiết</th>
                    <th>Loại HP</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php $stt = 1; @endphp
                @foreach ($listYear as $year)
                    @foreach ($year->semesters as $hk)
                        @php
                            $courses = $curriculum->courses->where('SemesterId', $hk->Id);
                        @endphp
                        @if ($courses && $courses->isNotEmpty())
                            <tr class="table-success">
                                <td colspan="7">Năm học {{ $year->StartYear }} - {{ $year->EndYear }} | {{ $hk->Name }}</td>
                            </tr>
                            @foreach ($courses as $course)
                                <tr>
                                    <td>{{ $stt }}</td>
                                    <td>{{ $course->subject->Code }}</td>
                                    <td>{{ $course->subject->Name }}</td>
                                    <td>{{ $course->Credits }}</td>
                                    <td>{{ $course->Lesson }}</td>
                                    <td>{{ $course->coursetype->Name }}</td>
                                    <td>
                                        <a href="#">Xem lớp học phần</a>
                                    </td>
                                </tr>
                                @php $stt++; @endphp
                            @endforeach
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
