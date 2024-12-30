@extends('layouts.app')

@section('title', 'Danh sách lớp học phần')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">DANH SÁCH LỚP HỌC PHẦN</h4>
        <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex gap-2 align-items-center w-100">
                    <span style="text-wrap: nowrap;">Chọn khoa:</span>
                    <select id="DeptId" class="form-control">
                        <option value="">Tất cả</option>
                        @foreach($depts as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    <span style="text-wrap: nowrap;">Chọn niên học:</span>
                    <select id="StudyYearDetailId" class="form-control">
                        <option value="">Tất cả</option>
                        @foreach($studyYearDetails as $year)
                            <option value="{{ $year['value'] }}">{{ $year['text'] }}</option>
                        @endforeach
                    </select>

                    <span style="text-wrap: nowrap;">Học kỳ:</span>
                    <select id="SemesterId" class="form-control" disabled>
                        <option value="">Chọn học kỳ</option>
                    </select>
                    <button id="filter" class="btn btn-primary">LỌC</button>
                </div>
            </div>
            <button class="btn btn-add-course" data-bs-toggle="modal" data-bs-target="#myModal">+ Thêm lớp học phần</button>
        </div>

        <div class="table-data table-responsive">
            <!-- Nội dung bảng danh sách lớp học phần sẽ được hiển thị ở đây -->
        </div>
    </div>
</div>

<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Thêm Lớp học phần</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="row">
                    <div class="col-6">
                        <a href="{{ route('admin.courseclass.create') }}" class="btn btn-lg btn-primary">Tạo thủ công</a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.courseclass.createWithList') }}" class="btn btn-lg btn-danger">Tải danh sách</a>
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
                var url = `{{url("/admin/courseclass/destroy/")}}/`+vid;
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
    $(document).ready(function () {
        $('#filter').on('click', function () {
            $('.table-data').html('');
            $.ajax({
                url: '{{ route("admin.courseclass.getListClass") }}',
                method: 'GET',
                data: {
                    deptId: $('#DeptId').val(),
                    yearDetailId: $('#StudyYearDetailId').val(),
                    semesterId: $('#SemesterId').val()
                },
                success: function (response) {
                    $('.table-data').html(response);
                },
                error: function (err) {
                    $('.table-data').html(err);
                }
            });
        });

        $('#StudyYearDetailId').on('change', function () {
            var yearId = $(this).val();
            if (yearId) {
                $.ajax({
                    url: '{{url("/admin/getsemesterbyyearid")}}/' + yearId,
                    type: 'GET',
                    data: { yearDetailId: yearId },
                    success: function (data) {
                        if (data && data.length > 0) {
                            var semesterSelect = $('#SemesterId');
                            semesterSelect.empty();
                            semesterSelect.append('<option value="">Chọn học kỳ</option>');
                            $.each(data, function (index, item) {
                                semesterSelect.append('<option value="' + item.Id + '">' + item.Name + '</option>');
                            });
                            semesterSelect.prop('disabled', false);
                        } else {
                            $('#SemesterId').prop('disabled', true);
                        }
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu.');
                    }
                });
            } else {
                $('#SemesterId').prop('disabled', true);
            }
        });
    });
</script>
@endsection
