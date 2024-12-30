@extends('layouts.app')

@section('title', 'Danh sách chương trình đào tạo')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">DANH SÁCH CHƯƠNG TRÌNH
            ĐÀO TẠO</h4>
        <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex gap-2 align-items-center w-100">
                    <span style="text-wrap: nowrap;">Chọn khóa:</span>
                    <select name="StudyYearId" id="StudyYearId" class="form-control">
                        <option value="" selected>Tất cả</option>
                        @foreach ($studyYears as $item)
                            <option value="{{ $item->Id }}">{{ $item->Number }}</option>
                        @endforeach
                    </select>

                    <span style="text-wrap: nowrap;">Chọn ngành:</span>
                    <select name="MajorId" id="MajorId" class="form-control">
                        <option value="" selected>Tất cả</option>
                        @foreach ($majors as $item)
                            <option value="{{ $item->Id }}">{{ $item->Name }}</option>
                        @endforeach
                    </select>

                    <button id="btn-search" class="btn btn-primary">LỌC</button>
                </div>

            </div>
            <a href="{{ route('admin.curriculum.create') }}" class="btn btn-add-course">+ Thêm chương trình</a>
        </div>

        <div class="table-data">
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
                var url = `{{url("/admin/curriculum/destroy/")}}/`+vid;
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
        $('#btn-search').on('click', function () {
            var studyYearId = $('#StudyYearId').val();
            var majorId = $('#MajorId').val();
            $.ajax({
                url: '{{ route("admin.curriculum.getList") }}',
                data: { yearId: studyYearId, majorId: majorId },
                type: 'GET',
                success: function (response) {
                    $('.table-data').html(response);
                },
                error: function (err) {
                    $('.table-data').html(err);
                }
            });
        });
        $('#btn-search').trigger('click');
    });
</script>
@endsection