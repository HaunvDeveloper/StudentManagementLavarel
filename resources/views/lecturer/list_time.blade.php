@extends('layouts.app')

@section('title', 'Danh sách lớp học phần')

@section('links')
<link rel="stylesheet" href="{{ asset('css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:30px;font-weight:bold;">DANH SÁCH LỚP HỌC PHẦN</h4>
        <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex gap-2 align-items-center w-100">
                    <span style="text-wrap: nowrap;">Chọn niên học:</span>
                    @php
                        use Carbon\Carbon;
                        $now = Carbon::now(); // Get the current date and time
                    @endphp
                    <select id="StudyYearDetailId" name="StudyYearDetailId" class="form-control">
                    @foreach($studyYearDetails as $detail)
                        @php
                            // Check if the current date is within the StartYear and EndYear range
                            $startYear = Carbon::create($detail->StartYear, 1, 1);
                            $endYear = Carbon::create($detail->EndYear, 12, 31);
                            $isSelected = $now->between($startYear, $endYear) ? 'selected' : '';
                        @endphp
                        <option value="{{ $detail->Id }}" {{ $isSelected }}>
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
        </div>

        <div class="table-data table-responsive">
            <!-- Table data will be dynamically loaded here -->
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showLesson(courseClassId, element) {
        var isPick = $(element).attr('data-pick');
        if (isPick === "0") {
            $(element).attr('data-pick', '1');
            $.ajax({
                url: '{{ route("lecturer.getListLesson") }}',
                method: 'GET',
                data: { courseClassId: courseClassId },
                success: function (response) {
                    var tr = document.createElement('tr');
                    $(tr).html(`
                        <td colspan="7">
                        ${response}
                        </td>
                    `);
                    $(element).closest('tr').after(tr);
                },
                error: function (err) {
                    console.error(err);
                }
            });
        } else {
            $(element).attr('data-pick', '0');
            $(element).closest('tr').next('tr').remove();
        }
    }

    $(document).ready(function () {
        $('#SemesterId').on('change', function () {
            $('.table-data').html('');
            $.ajax({
                url: '{{ route("lecturer._getListTime") }}',
                method: 'GET',
                data: { semesterId: $('#SemesterId').val() },
                success: function (response) {
                    $('.table-data').html(response);
                },
                error: function (err) {
                    console.error(err);
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
                        if (data.length > 0) {
                            var semesterSelect = $('#SemesterId');
                            semesterSelect.empty();
                            var now = new Date();

                            data.forEach(function (item) {
                                var startDate = new Date(item.startDate);
                                var endDate = new Date(item.endDate);
                                var isSelected = now >= startDate && now <= endDate ? ' selected' : '';
                                semesterSelect.append(`<option value="${item.Id}"${isSelected}>${item.Name}</option>`);
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
