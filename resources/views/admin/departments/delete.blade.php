@extends('layouts.app') {{-- Kế thừa layout chính --}}

@section('title', 'Xóa khoa')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
<style>
    .displayModel {
        font-size: 18px;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="container">
    <form action="{{ route('admin.department.destroy', ['id' => $department->Id]) }}" method="POST" class="table-container m-4">
        @csrf {{-- Laravel CSRF token để bảo vệ form --}}
        @method('DELETE') {{-- Phương thức DELETE --}}
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">XÓA KHOA/BỘ MÔN</h4>
        
        <input type="hidden" name="id" value="{{ $department->Id }}">
        
        <div class="form-group">
            <label for="code">Mã khoa</label>
            <label class="displayModel">{{ $department->Code }}</label>
        </div>
        
        <div class="form-group">
            <label for="name">Tên khoa</label>
            <label class="displayModel">{{ $department->Name }}</label>
        </div>
        
        <div class="text-lg-end mt-2">
            <a href="{{ route('admin.department.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            <button type="submit" class="btn btn-danger">Xoá</button>
        </div>
        
        @if(session('alert'))
            <div class="text-danger mt-2">{{ session('alert') }}</div>
        @endif
    </form>
</div>
@endsection

@section('scripts')
{{-- Thêm script nếu cần --}}
@endsection
