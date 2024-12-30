@extends('layouts.app')
@section('title', 'Sửa môn học') 


@section('links')
<link rel="stylesheet" href="~/assets/css/course.css">
@endsection

@section('content')
<div class="container">
    <form action="{{ route('admin.subject.update', $subject->Id) }}" method="post" class="table-container m-4">
        @csrf
        @method('PUT')
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">SỬA MÔN HỌC</h4>
        <input type="hidden" name="id" value="{{$subject->Id}}" />
        <div class="form-group">
            <label for="code">Mã môn</label>
            <input id="code" name="code" class="form-control" value="{{$subject->Code}}" required />
        </div>
        <div class="form-group">
            <label for="name">Tên môn</label>
            <input name="name" id="name" class="form-control" value="{{$subject->Name}}" required />
        </div>
        <div class="form-group">
            <label for="default_credits">STC</label>
            <input name="default_credits" id="default_credits" type="number" class="form-control" value="{{$subject->DefaultCredits}}" required />
        </div>
        <div class="form-group">
            <label for="default_lesson">Số tiết</label>
            <input name="default_lesson" id="default_lesson" class="form-control" type="number" value="{{$subject->DefaultLesson}}" required />
        </div>
        <div class="form-group">
            <label for="dept_id">Khoa</label>
            <select name="dept_id" id="dept_id" class="form-control select2">
                @foreach ($departments as $department)
                    <option value="{{ $department->Id }}" {{ $subject->DeptId == $department->Id ? 'selected' : '' }}>
                        {{ $department->Name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="text-lg-end mt-2">
            <a href="{{ route('admin.subject.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            <button type="submit" class="btn btn-primary">Sửa</button>
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
    
</script>
@endsection