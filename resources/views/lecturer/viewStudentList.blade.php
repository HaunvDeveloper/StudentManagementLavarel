@extends('layouts.app')

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center text-danger mb-4 mt-2" style="font-size:35px;font-weight:bold;">
            DANH SÁCH SINH VIÊN LỚP HỌC PHẦN {{ strtoupper($courseClass->Code) }}
        </h4>
        <div class="d-flex justify-content-between mb-3">
            <div class="d-flex gap-2">
                <a href="{{ route('admin.courseclass.exportStudentList', $courseClass->Id) }}" class="btn btn-outline-secondary">Export</a>
            </div>
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
    
</script>
@endsection
