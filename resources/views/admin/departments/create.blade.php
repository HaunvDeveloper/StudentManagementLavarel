@extends('layouts.app') {{-- Kế thừa layout chính --}}

@section('title', 'Tạo khoa')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <form action="{{ route('admin.department.create.post') }}" method="POST" class="table-container m-4">
        @csrf {{-- Laravel CSRF token để bảo vệ form --}}
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">TẠO KHOA/BỘ MÔN</h4>
        
        <div class="form-group">
            <label for="code">Mã khoa</label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" required />
        </div>
        
        <div class="form-group">
            <label for="name">Tên khoa</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required />
        </div>
        
        <div class="text-lg-end mt-2">
            <a href="{{ route('admin.department.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            <button type="reset" class="btn btn-outline-success">Đặt lại</button>
            <button type="submit" class="btn btn-primary">Tạo</button>
        </div>
        
        @if(session('alert'))
        <div class="text-danger mt-2">{{ session('alert') }}</div>
        @endif
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
