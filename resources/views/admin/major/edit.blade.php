@extends('layouts.app')
@section('title', 'Sửa ngành học') 


@section('links')
<link rel="stylesheet" href="~/assets/css/course.css">
@endsection

@section('content')
<div class="container">
    <form action="{{ route('admin.major.update', $major->Id) }}" method="post" class="table-container m-4">
        @csrf
        @method('PUT')
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">SỬA NGÀNH HỌC</h4>
        <input type="hidden" name="id" value="{{$major->Id}}" />
        <div class="form-group">
            <label for="code">Mã ngành</label>
            <input id="code" name="code" class="form-control" value="{{$major->Code}}" required />
        </div>
        <div class="form-group">
            <label for="name">Tên ngành</label>
            <input name="name" id="name" class="form-control" value="{{$major->Name}}" required />
        </div>
        <div class="form-group">
            <label for="dept_id">Khoa</label>
            <select name="dept_id" id="dept_id" class="form-control select2">
                @foreach ($departments as $department)
                    <option value="{{ $department->Id }}" {{ $major->DeptId == $department->Id ? 'selected' : '' }}>
                        {{ $department->Name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="text-lg-end mt-2">
            <a href="{{ route('admin.major.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            <button type="submit" class="btn btn-primary">Sửa</button>
        </div>
        <div class="text-danger mt-2">{{$alert ?? ""}}</div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $('.select2').select2({
        placeholder: "Chọn ngành học",
    });
    
</script>
@endsection