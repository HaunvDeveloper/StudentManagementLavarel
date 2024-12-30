@extends('layouts.app')

@section('title', 'Danh sách sinh viên')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">DANH SÁCH SINH VIÊN</h4>
        <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex gap-2 align-items-center w-100">
                    <span style="text-wrap: nowrap;">Chọn khóa:</span>
                    <select id="StudyYearId" class="form-control">
                        <option value="">Tất cả</option>
                        @foreach ($studyYears as $id => $number)
                            <option value="{{ $id }}">{{ $number }}</option>
                        @endforeach
                    </select>

                    <span style="text-wrap: nowrap;">Chọn khoa:</span>
                    <select id="DeptId" class="form-control">
                        <option value="">Tất cả</option>
                        @foreach ($departments as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>

                    <span style="text-wrap: nowrap;">Chọn ngành:</span>
                    <select id="SpecializationId" class="form-control" disabled>
                        <option value="">Chọn ngành</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mt-4 align-items-center">
                    <span style="text-wrap: nowrap;">Tìm kiếm:</span>
                    <input type="text" name="keyword" id="keyword" class="form-control" placeholder="Nhập nội dung tìm kiếm...." />
                    <button id="btn-search" onclick="search(1)" class="btn btn-primary">LỌC</button>
                </div>
            </div>
            <button class="btn btn-outline-secondary" onclick="downloadExcel()">Export</button>
            <a href="{{ route('admin.student.createWithList') }}" class="btn btn-add-course">+ Thêm sinh viên</a>
        </div>

        <div class="table-data table-responsive">
            <!-- Nội dung bảng danh sách sinh viên sẽ được hiển thị ở đây -->
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
                var url = `{{url("/admin/student/destroy/")}}/`+vid;
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
        const params = {
            StudyYearId: $('#StudyYearId').val(),
            DeptId: $('#DeptId').val(),
            SpecializationId: $('#SpecializationId').val()
        };

        $.ajax({
            url: '{{ route("admin.student.downloadList") }}',
            method: 'GET',
            data: params,
            xhrFields: {
                responseType: 'blob' 
            },
            success: function (data, status, xhr) {
                const blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
                const downloadUrl = window.URL.createObjectURL(blob);

                const a = document.createElement('a');
                a.href = downloadUrl;

                const disposition = xhr.getResponseHeader('Content-Disposition');
                let fileName = 'Download.xlsx';
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    const matches = /filename="([^"]*)"/.exec(disposition);
                    if (matches != null && matches[1]) fileName = matches[1];
                }

                a.download = fileName;
                document.body.appendChild(a);
                a.click();

                document.body.removeChild(a);
                window.URL.revokeObjectURL(downloadUrl);
            },
            error: function () {
                alert('Có lỗi xảy ra trong quá trình tải xuống.');
            }
        });
    }

    function search(p) {
        let data = {
            StudyYearId: $('#StudyYearId').val(),
            DeptId: $('#DeptId').val(),
            SpecializationId: $('#SpecializationId').val(),
            keyword: $('#keyword').val(),
            p: p
        };
        $.ajax({
            url: '{{ route("admin.student.getList") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: data,
            success: function (response) {
                $('.table-data').html(response);
                attachPaginationEvents();
            }
        });
    }

    function attachPaginationEvents() {
        $('.pagination a').off('click').on('click', function (e) {
            e.preventDefault();
            var page = $(this).data('page');
            search(page);
        });
    }

    $(document).ready(function () {
        search(1);

        $('#DeptId').on('change', function () {
            var deptId = $(this).val();

            if (deptId) {
                var specializationSelect = $('#SpecializationId');
                specializationSelect.empty();
                specializationSelect.append('<option value="">Chọn ngành</option>');
                $.ajax({
                    url: '{{ route("api.specializations.byDept") }}?deptid=' + deptId,
                    type: 'GET',
                    success: function (data) {
                        if (data && data.length > 0) {
                            $.each(data, function (index, spec) {
                                specializationSelect.append('<option value="' + spec.Id + '">' + spec.Name + '</option>');
                            });

                            specializationSelect.prop('disabled', false);
                        } else {
                            specializationSelect.prop('disabled', true);
                        }
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu ngành.');
                    }
                });
            } else {
                $('#SpecializationId').prop('disabled', true);
            }
        });
    });
</script>
@endsection
