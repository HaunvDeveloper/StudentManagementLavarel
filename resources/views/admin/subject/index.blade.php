@extends('layouts.app') {{-- Kế thừa layout chính --}}

@section('title', 'Danh sách môn học') {{-- Title trang --}}

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:35px;font-weight:bold;">DANH SÁCH MÔN HỌC</h4>

        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex gap-2">
                <form action="{{ route('admin.subject.index') }}" method="GET" id="filterForm">
                    <select name="deptId" class="form-control" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Chọn khoa</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->Id }}" {{ $selectedDeptId == $department->Id ? 'selected' : '' }}>
                                {{ $department->Name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <a href="{{route('admin.subject.create')}}"  class="btn btn-add-course">+ Thêm môn</a>
        </div>

        {{-- Bảng danh sách giảng viên --}}
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="table-primary">
                    <th class="text-center text-light">ID</th>
                    <th class="text-center text-light">Mã môn</th>
                    <th class="text-center text-light">Tên môn</th>
                    <th class="text-center text-light">STC</th>
                    <th class="text-center text-light">Số tiết</th>
                    <th class="text-center text-light">Khoa</th>
                    <th width="13%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $subject)
                <tr>
                    <td>{{ $subject->Id }}</td>
                    <td>{{ $subject->Code }}</td>
                    <td>{{ $subject->Name }}</td>
                    <td>{{ $subject->DefaultCredits }}</td>
                    <td>{{ $subject->DefaultLesson }}</td>
                    <td>{{ $departments->where('Id', $subject->DeptId)->first()->Name }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.subject.edit', ['id'=>$subject->Id]) }}" class="btn-service btn btn-primary mx-1">
                            <img src="{{ asset('assets/images/pen-solid.svg') }}" height="20" alt="">
                        </a>
                        <button data-id="{{ $subject->Id }}" onclick="deleteRow(this)" class="btn-service btn btn-danger">
                            <img src="{{ asset('assets/images/trash-solid.svg') }}" height="20" alt="">
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>


    </div>
</div>

@endsection

@section('scripts')
<script>
    async function deleteRow(element) {
        var conf = await confirmAction();
        if (conf) {
            
            try {
                const vid = element.getAttribute('data-id');
                var url = `{{url("/admin/subject/destroy/")}}/`+vid;
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                });
                const result = await response.json();
                if (result.success) {
                    showAlert("success", "Xóa thành công!");
                    element.closest('tr').remove();
                } else {
                    showAlert("error", "Xóa thất bại!");
                }
            } catch (error) {
                console.error(error);
                alert("Có lỗi xảy ra!");
            }
        }
    }

</script>
@endsection
