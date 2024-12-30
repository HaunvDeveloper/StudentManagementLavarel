@extends('layouts.app')

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center text-danger mb-4 mt-2" style="font-size:35px;font-weight:bold;">
            DANH SÁCH SINH VIÊN LỚP HỌC PHẦN {{ strtoupper($courseClass->Code) }}
        </h4>
        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.courseclass.importStudentList', $courseClass->Id) }}" class="btn btn-outline-secondary">Import</a>
                <a href="{{ route('admin.courseclass.exportStudentList', $courseClass->Id) }}" class="btn btn-outline-secondary">Export</a>
            </div>
            <button data-bs-toggle="modal" data-bs-target="#myModal" class="btn btn-primary">+ Thêm sinh viên</button>
        </div>
        <input type="hidden" id="Id" value="{{ $courseClass->Id }}">
        <table class="table table-bordered table-hover">
            <thead>
                <tr class="table-primary">
                    <th class="text-center text-light">STT</th>
                    <th class="text-center text-light">Lớp sinh viên</th>
                    <th class="text-center text-light">Mã số sinh viên</th>
                    <th class="text-center text-light">Họ và Tên</th>
                    <th class="text-center text-light">Ngày sinh</th>
                    <th class="text-center text-light">Số buổi có mặt</th>
                    <th class="text-center text-light">Số buổi đi trễ</th>
                    <th class="text-center text-light">Số buổi vắng</th>
                    <th width="13%"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($courseClass->StudentJoinClasses as $index => $item)
                @php
                    $coMat = $lessonJoined->where('StudentId', $item->Student->Id)->where('Status', 'Có mặt')->count();
                    $diTre = $lessonJoined->where('StudentId', $item->Student->Id)->where('Status', 'Đi trễ')->count();
                    $vang = $lessonJoined->where('StudentId', $item->Student->Id)->where('Status', 'Vắng')->count();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->Student->StudentClass->Code ?? '' }}</td>
                    <td>{{ $item->Student->Id }}</td>
                    <td>{{ $item->Student->FullName }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->Student->DayOfBirth)->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $coMat }}</td>
                    <td class="text-center">{{ $diTre }}</td>
                    <td class="text-center">{{ $vang }}</td>
                    <td class="text-center">
                        <button onclick="deleteStudent(this)" data-id="{{ $item->Student->Id }}" class="btn-service btn btn-danger">
                            <img src="{{ asset('assets/images/trash-solid.svg') }}" height="20" alt="Delete">
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thêm sinh viên</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="form-group d-flex flex-column">
                    <label for="student-select" class="form-label">Nhập Mã số sinh viên</label>
                    <select id="student-select" class="select2 w-100 form-control"></select>
                </div>
            </div>
            <div class="modal-footer">
                <button id="btn-add" type="button" class="btn btn-primary" data-bs-dismiss="modal">Thêm</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    async function deleteStudent(element) {
        var confirm = await confirmAction();
        if (confirm) {
            $.ajax({
                url: "{{ url('/admin/courseclass/removeStudent') }}/" + $(element).data('id'),
                method: 'delete',
                data: {
                    courseClassId: $('#Id').val(),
                    studentId: $(element).data('id'),
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    if (res.success) {
                        showToast("success", "Xóa thành công!!");
                        $(element).closest('tr').remove();
                    } else {
                        alert("Xóa thất bại!");
                    }
                },
                error: function () {
                    alert("Có lỗi xảy ra.");
                }
            });
        }
    }

    $(document).ready(function () {
        $('#student-select').select2({
            placeholder: 'Tìm kiếm sinh viên...',
            minimumInputLength: 3,
            dropdownParent: $('#myModal'),
            ajax: {
                url: '{{route("admin.getStudentById")}}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { id: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.map(function (student) {
                            return { id: student.Id, text: `${student.Id} - ${student.Name}` };
                        })
                    };
                },
                cache: true
            }
        });

        $('#btn-add').on('click', function () {
            const studentId = $('#student-select').val();
            const courseClassId = $('#Id').val();

            $.ajax({
                url: "{{ route('admin.courseclass.addStudent') }}",
                method: 'post',
                data: {
                    studentId,
                    courseClassId,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    if (response.success) {
                        showAlert("Thêm thành công!");
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        alert("Thêm thất bại!");
                    }
                }
            });
        });
    });
</script>
@endsection
