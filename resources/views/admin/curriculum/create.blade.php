@extends('layouts.app')

@section('title', 'Tạo chương trình đào tạo')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <form action="{{ route('admin.curriculum.store') }}" method="post" class="table-container m-4">
        @csrf
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">TẠO CHƯƠNG TRÌNH ĐÀO TẠO</h4>
        <div class="form-group">
            <label for="Code">Mã chương trình</label>
            <input type="text" name="Code" id="Code" class="form-control" required />
        </div>
        <div class="form-group">
            <label for="Name">Tên chương trình</label>
            <input type="text" name="Name" id="Name" class="form-control" required />
        </div>
        <div class="form-group">
            <label for="StudyYearId">Khóa</label>
            <select name="StudyYearId" id="StudyYearId" class="form-control" required>
                <option value="" disabled selected>Chọn khóa</option>
                @foreach ($studyYears as $item)
                    <option value="{{ $item->Id }}">{{ $item->Number }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="MajorId">Ngành</label>
            <select name="MajorId" id="MajorId" class="form-control" required>
                <option value="" disabled selected>Chọn ngành</option>
                @foreach ($majors as $item)
                    <option value="{{ $item->Id }}">{{ $item->Name }}</option>
                @endforeach
            </select>
        </div>
        <div class="text-lg-end mt-2">
            <a href="{{ route('admin.curriculum.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            <button type="reset" class="btn btn-outline-success">Đặt lại</button>
            <button type="submit" class="btn btn-primary">Tạo</button>
        </div>
        @if (isset($alert))
            <div class="text-danger mt-2">{{ $alert ?? "" }}</div>
        @endif
    </form>
</div>
@endsection
