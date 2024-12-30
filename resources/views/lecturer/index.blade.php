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
                    <span style="text-wrap: nowrap;">Chọn niên học:</span>
                    <select id="StudyYearDetailId" name="StudyYearDetailId" class="form-control">
                        @foreach ($studyYearDetails as $detail)
                            <option value="{{ $detail->Id }}" 
                                    {{ $currentYearId == $detail->Id ? 'selected' : '' }}>
                                {{ $detail->StartYear }} - {{ $detail->EndYear }}
                            </option>
                        @endforeach
                    </select>

                    <span style="text-wrap: nowrap;">Học kỳ:</span>
                    <select id="SemesterId" name="SemesterId" class="form-control" required>
                        <option value="">Chọn học kỳ</option>
                    </select>
                </div>
            </div>
            <div></div>
        </div>

        <div class="table-data table-responsive">
            <!-- Nội dung bảng danh sách lớp học phần sẽ được hiển thị ở đây -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#SemesterId').on('change', function () {
            $('.table-data').html('');
            $.ajax({
                url: '{{ route("lecturer.getListClass") }}',
                method: 'GET',
                data: { 
                    yearDetailId: $('#StudyYearDetailId').val(), 
                    semesterId: $('#SemesterId').val() 
                },
                success: function (response) {
                    $('.table-data').html(response);
                },
                error: function (err) {
                    $('.table-data').html('<div class="alert alert-danger">Không thể tải dữ liệu</div>');
                }
            });
        });

        $('#StudyYearDetailId').on('change', function () {
            var yearId = $(this).val();
            if (yearId) {
                $.ajax({
                    url: '{{ url("/admin/getsemesterbyyearid") }}/' + yearId,
                    type: 'GET',
                    success: function (data) {
                        if (data && data.length > 0) {
                            var semesterSelect = $('#SemesterId');
                            semesterSelect.empty();
                            var now = new Date();

                            $.each(data, function (index, item) {
                                var startDate = new Date(item.startDate);
                                var endDate = new Date(item.endDate);
                                var isSelected = now >= startDate && now <= endDate ? ' selected' : '';

                                semesterSelect.append('<option value="' + item.Id + '"' + isSelected + '>' + item.Name + '</option>');
                            });
                            $('#SemesterId').trigger('change');
                        }
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu.');
                    }
                });
            }
            $('#SemesterId').trigger('change');
        });

        $('#StudyYearDetailId').trigger('change');
    });
</script>
@endsection
