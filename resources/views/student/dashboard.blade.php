@extends('layouts.app')

@section('title', 'Thông tin sinh viên')

@section('links')
<link rel="stylesheet" href="{{asset("assets/css/studentDashboard.css")}}">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <!-- Left Column -->
        <div class="col-md-4">
            <!-- Profile Card -->
            <div class="card profile-card text-center">
                <img src="{{asset("assets/images/img150.jpg")}}" alt="Student Avatar" class="img-fluid rounded-circle mt-3">
                <div class="card-body">
                    <h5>{{ $student->FullName }}</h5>
                </div>
            </div>

            <!-- Progress Card -->
            <div class="card progress-card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Tiến độ đi học</h5>
                    <p id="totalDd">Số buổi đi học: 70/124 (Đã điểm danh/Tổng buổi)</p>
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="right-column">
            <!-- Academic Info -->
            <div class="card academic-card">
                <div class="card-body">
                    <h5>Chương trình đào tạo</h5>
                    <select class="form-select mb-3">
                        <option>{{ $curriculum->Name }}</option>
                    </select>

                    <label for="StudyYearDetailId">Niên học:</label>
                    @php
                        use Carbon\Carbon;
                        $now = Carbon::now(); // Get the current date and time
                    @endphp
                    <select id="StudyYearDetailId" name="StudyYearDetailId" class="form-control mb-3">
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

                    <label for="SemesterId">Học kỳ:</label>
                    <select id="SemesterId" name="SemesterId" class="form-control mb-3">
                        <option value="">Chọn học kỳ</option>
                    </select>
                </div>
            </div>

            <!-- Attendance Statistics -->
            <div class="card">
                <div class="card-body">
                    <h5>Thống kê điểm danh theo môn học</h5>
                    <div class="attendance-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Môn học</th>
                                    <th>Đi học</th>
                                    <th>Đi trễ</th>
                                    <th>Vắng</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <!-- Rows will be populated via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <canvas id="attendanceBarChart" class="mt-3"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        $('#SemesterId').on('change', function () {
            var semesterId = $(this).val();
            if (semesterId) {
                $.ajax({
                    url: '{{route("student.getChartData")}}',
                    type: 'POST',
                    data: { semesterId : $('#SemesterId').val()},
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        console.log(data);
                        createChart(
                            data.blockChartData,
                            data.pieChartData.attended,
                            data.pieChartData.absent,
                            data.pieChartData.late
                        );
                    },
                    error: function () {
                        alert('Không thể lấy dữ liệu.');
                    }
                });
            }
        });



        $('#StudyYearDetailId').on('change', function () {
            var yearId = $(this).val();
            if (yearId) {
                $.ajax({
                    url: '{{ url("/admin/getsemesterbyyearid") }}/' + yearId,
                    type: 'GET',
                    success: function (data) {
                        if (data && data.length > 0) {
                            var curriculumSelect = $('#SemesterId');
                            curriculumSelect.empty();

                            var now = new Date(); // Ngày hiện tại

                            $.each(data, function (index, item) {
                                var startDate = new Date(item.StartDate);
                                var endDate = new Date(item.EndDate);

                                // Kiểm tra ngày hiện tại thuộc khoảng [startDate, endDate]
                                var isSelected = now >= startDate && now <= endDate ? ' selected' : '';

                                curriculumSelect.append('<option value="' + item.Id + '"' + isSelected + '>' + item.Name + '</option>');
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

<script>
    var attendanceBarChartInstance = null;
    var pieChart = null;
    function createChart(attendanceData, totalAttended, totalAbsent, totalLate) {
        let coMat = totalAttended + totalLate;
        let koCoMat = totalAttended + totalLate + totalAbsent;
        $('#totalDd').text('Số buổi đi học: ' + coMat + '/' + koCoMat + '(Đã điểm danh/Tổng buổi)');
        const tableBody = document.getElementById('attendanceTableBody');
        tableBody.innerHTML = "";
        attendanceData.forEach(record => {
            const row = document.createElement('tr');

            const subjectCell = document.createElement('td');
            subjectCell.textContent = record.subject;
            row.appendChild(subjectCell);

            const attendedCell = document.createElement('td');
            attendedCell.textContent = record.attended;
            row.appendChild(attendedCell);

            const lateCell = document.createElement('td');
            lateCell.textContent = record.late;
            row.appendChild(lateCell);

            const absentCell = document.createElement('td');
            absentCell.textContent = record.absent;
            row.appendChild(absentCell);

            tableBody.appendChild(row);
        });

        const attendanceBarChartCtx = document.getElementById('attendanceBarChart').getContext('2d');

        if (attendanceBarChartInstance) {
            attendanceBarChartInstance.destroy();
        }


        attendanceBarChartInstance = new Chart(attendanceBarChartCtx, {
            type: 'bar',
            data: {
                labels: attendanceData.map(record => record.subject),
                datasets: [
                    {
                        label: 'Đi học',
                        data: attendanceData.map(record => record.attended),
                        backgroundColor: '#124874',
                    },
                    {
                        label: 'Đi trễ',
                        data: attendanceData.map(record => record.late),
                        backgroundColor: '#FABF07',
                    },
                    {
                        label: 'Vắng',
                        data: attendanceData.map(record => record.absent),
                        backgroundColor: '#CF373D',
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Học phần'
                        },
                        ticks: {
                            display: false
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Số buổi'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        const progressChartCtx = document.getElementById('progressChart').getContext('2d');
        if (pieChart){
            pieChart.destroy();
        }
        pieChart = new Chart(progressChartCtx, {
            type: 'pie',
            data: {
                labels: ['Đi học', 'Đi trễ', 'Vắng'],
                datasets: [
                    {
                        data: [totalAttended, totalLate, totalAbsent],
                        backgroundColor: ['#124874', '#FABF07', '#CF373D'], //xanh, vàng, đỏ
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.raw;
                                const percentage = ((value / total) * 100).toFixed(2);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
</script>
@endsection