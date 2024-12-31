@extends('layouts.app')

@section('title', 'Thông tin học phần')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/courseInfo.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
<div class="info-section">
    <h5>Thông tin học phần: <span style='font-weight: normal;'>({{ $courseClass->StartDate->format('d/m/Y') }} - {{ $courseClass->EndDate->format('d/m/Y') }})</span></h5>
    <table class="info-table">
        <tr>
            <td>Mã HP</td>
            <td>{{ $courseClass->subject->Code ?? '' }}</td>
            <td>Tên HP</td>
            <td>{{ $courseClass->Name }}</td>
        </tr>
        <tr>
            <td>Số tiết</td>
            <td>{{ $courseClass->subject->DefaultLesson ?? '' }}</td>
            <td>Giảng viên</td>
            <td>{{ $courseClass->lecturer->FullName ?? '' }}</td>
        </tr>
        <tr>
            <td>Thời gian</td>
            <td>{{ $courseClass->WeakDays }}</td>
            <td>Phòng</td>
            <td>{{ $courseClass->room->Name ?? '' }} - Cơ sở {{ $courseClass->room->Address ?? '' }}</td>
        </tr>
        <tr>
            @php
                $ddd = count($listJoined);
                $tb = count($courseClass->lessons);
            @endphp
            <td>Điểm danh (buổi)</td>
            <td>
                {{ $ddd }}/{{ $tb }}
            </td>
        </tr>
    </table>
</div>

<div class="attendance-section">
    <table class="attendance-table">
        <thead>
            <tr>
                <th>Số buổi</th>
                <th>Thời gian</th>
                <th>Ngày học</th>
                <th>Trạng thái điểm danh</th>
            </tr>
        </thead>
        <tbody>
            @php
                $stt = 1;
                $statusDic = [
                    'Có mặt' => 'status-present',
                    'Có phép' => 'status-absent',
                    'Không phép' => 'status-absent',
                    'Đi trễ' => 'status-late',
                ];
                $statusDisplay = [
                    'Có mặt' => 'Có mặt',
                    'Có phép' => 'Vắng có phép',
                    'Không phép' => 'Vắng không phép',
                    'Đi trễ' => 'Trễ',
                ];
            @endphp
            @foreach ($courseClass->lessons->sortBy('Date') as $lesson)
                @php
                    $status = $listJoined->firstWhere('LessonId', $lesson->Id);
                @endphp
                @if ($status)
                    <tr class="{{ $statusDic[$status->Status] ?? 'default-status' }}">
                        <td>{{ $stt++ }}</td>
                        <td>{{ $status->Status !== 'Có phép' && $status->Status !== 'Không phép' ? $status->JoinTime->format('H:i') : ' - ' }}</td>
                        <td>{{ $lesson->Date->format('d/m/Y') }}</td>
                        <td>{{ $statusDisplay[$status->Status] ?? '' }}</td>
                    </tr>
                @else
                    <tr class="default-status">
                        <td>{{ $stt++ }}</td>
                        <td> - </td>
                        <td>{{ $lesson->Date->format('d/m/Y') }}</td>
                        <td>Chưa điểm danh</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<!-- Add custom scripts here if necessary -->
@endsection
