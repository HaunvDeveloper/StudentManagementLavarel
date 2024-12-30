@extends('layouts.app') {{-- Kế thừa layout chính --}}

@section('title', 'Sửa khoa')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <form action="{{ route('admin.department.update', ['id' => $department->Id]) }}" method="POST" class="table-container m-4">
        @csrf {{-- Laravel CSRF Token --}}
        @method('PUT') {{-- Phương thức PUT cho cập nhật --}}
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">SỬA THÔNG TIN KHOA/BỘ MÔN</h4>

        {{-- Hidden Fields --}}
        <input type="hidden" name="date_found" value="{{ old('date_found', $department->DateFound) }}">
        <input type="hidden" name="id" value="{{ $department->Id }}">

        {{-- Field: Code --}}
        <div class="form-group">
            <label for="code">Mã khoa</label>
            <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $department->Code) }}" required>
        </div>

        {{-- Field: Name --}}
        <div class="form-group">
            <label for="name">Tên khoa</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $department->Name) }}" required>
        </div>

        {{-- Buttons --}}
        <div class="text-lg-end mt-2">
            <a href="{{ route('admin.department.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            <button type="submit" class="btn btn-primary">Lưu</button>
        </div>

        {{-- Alert for Errors --}}
        @if (session('alert'))
        <div class="text-danger mt-2">
            {{ session('alert') }}
        </div>
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
