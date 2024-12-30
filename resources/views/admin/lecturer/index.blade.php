@extends('layouts.app') {{-- Kế thừa layout chính --}}

@section('title', 'Danh sách giảng viên')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:35px;font-weight:bold;">DANH SÁCH GIẢNG VIÊN</h4>

        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex gap-2">
                <form action="{{ route('admin.lecturer.index') }}" method="GET" id="filterForm">
                    <select name="deptId" class="form-control" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Chọn khoa</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->Id }}" {{ $selectedDeptId == $department->Id ? 'selected' : '' }}>
                                {{ $department->Name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <button onclick="downloadExcel()" class="btn btn-outline-secondary">Export</button>
            </div>
            <button data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-add-course">+ Thêm giảng viên</button>
        </div>

        {{-- Bảng danh sách giảng viên --}}
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="table-primary">
                    <th class="text-center text-light">Mã giảng viên</th>
                    <th class="text-center text-light">Họ và tên</th>
                    <th class="text-center text-light">Email</th>
                    <th class="text-center text-light">Ngày sinh</th>
                    <th class="text-center text-light">Giới tính</th>
                    <th class="text-center text-light">Khoa</th>
                    <th width="13%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lecturers as $lecturer)
                <tr>
                    <td>{{ $lecturer->Id }}</td>
                    <td>{{ $lecturer->FullName }}</td>
                    <td>{{ $lecturer->Email }}</td>
                    <td>{{ $lecturer->DayOfBirth ? $lecturer->DayOfBirth->format('d/m/Y') : '' }}</td>
                    <td>{{ $lecturer->Sex }}</td>
                    <td>{{ $lecturer->Department->Name ?? 'Không có' }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.lecturer.edit', ['id'=>$lecturer->Id]) }}" class="btn-service btn btn-primary mx-1">
                            <img src="{{ asset('assets/images/pen-solid.svg') }}" height="20" alt="">
                        </a>
                        <button data-id="{{ $lecturer->Id }}" onclick="deleteRow(this)" class="btn-service btn btn-danger">
                            <img src="{{ asset('assets/images/trash-solid.svg') }}" height="20" alt="">
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Phân trang --}}
        <div class="pag-container middle">
            {{ $lecturers->appends(['deptId' => $selectedDeptId])->links() }}
        </div>
    </div>
</div>

{{-- Modal thêm giảng viên --}}
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Thêm Giảng viên</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.lecturer.create') }}" class="btn btn-lg btn-primary">Tạo thủ công</a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="btn btn-lg btn-danger">Tải danh sách</a>
                    </div>
                </div>
            </div>

        </div>
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
                var url = `{{url("/admin/lecturer/destroy/")}}/`+vid;
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

    function downloadExcel() {
        const deptId = document.querySelector('[name="deptId"]').value;
        window.location.href = `{{ route('admin.lecturer.export') }}?deptId=${deptId}`;
    }
</script>
@endsection
