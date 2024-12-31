@extends('layouts.app')

@section('title', 'Danh sách học phần')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/studentCourse.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
<div class="table-container">
    <table class="table-hover">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã học phần</th>
                <th>Tên học phần</th>
                <th>Số Tín chỉ</th>
                <th>Số Tiết</th>
                <th>Điểm danh</th>
            </tr>
        </thead>
        <tbody>
            @php $stt = 1; @endphp
            @foreach ($semesters as $semester)
                @php
                    $currentCourses = $courseClasses->filter(fn($course) => $course->SemesterId === $semester->Id);
                @endphp
                @if ($currentCourses->isNotEmpty())
                    <tr class="semester-header">
                        <td colspan="6" style="text-align: left;">Năm học {{ $semester->studyyeardetail->StartYear }} - {{ $semester->studyyeardetail->EndYear }} | {{ $semester->Name }}</td>
                    </tr>
                    @foreach ($currentCourses as $course)
                        <tr>
                            <td>{{ $stt++ }}</td>
                            <td>{{ $course->subject->Code ?? '' }}</td>
                            <td>{{ $course->subject->Name ?? '' }}</td>
                            <td>{{ $course->subject->DefaultCredits ?? '' }}</td>
                            <td>{{ $course->subject->DefaultLesson ?? '' }}</td>
                            <td>
                                <a href="{{ route('student.course.info', ['id' => $course->Id]) }}" class="btn btn-primary">
                                    <i class="fa-solid fa-eye"></i>
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<!-- Add any custom scripts here if necessary -->
@endsection
