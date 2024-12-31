@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('links')
    <link rel="stylesheet" href="{{ asset('assets/css/infoStudent.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
<div class="_container">
    <div class="left-section">
        <img src="{{ asset('assets/images/user.png') }}" alt="">
        @if($student->FaceData != "Success")
            <a href="{{ route('student.createFaceIdentify') }}" class="face-button"><i class="fa-solid fa-face-grin-wide"></i> Định danh khuôn mặt</a>
        @else
            <button class="btn-disable"><i class="fa-solid fa-face-grin-wide"></i> Đã định danh</button>
        @endif
        <a href="{{ route('student.editInfo') }}" class="update-button"><i class="fa-solid fa-pen"></i> Cập nhật thông tin</a>
    </div>

    <div class="right-section">
        <div class="tabs">
            <button data-tab="student-info" class="active" onclick="showTab('student-info')">Thông tin sinh viên</button>
            <button data-tab="contact-info" onclick="showTab('contact-info')">Thông tin liên lạc</button>
            <button data-tab="course-info" onclick="showTab('course-info')">Thông tin khoá học</button>
        </div>

        <div id="student-info" class="tab-content">
            <table>
                <tr>
                    <th>Họ và tên</th>
                    <td>{{ $student->FullName }}</td>
                    <th>Mã số sinh viên</th>
                    <td>{{ $student->Id }}</td>
                </tr>
                <tr>
                    <th>Ngày sinh</th>
                    <td>{{ \Carbon\Carbon::parse($student->DayOfBirth)->format('d/m/Y') }}</td>
                    <th>Nơi sinh</th>
                    <td>{{ $student->BirthPlace }}</td>
                </tr>
                <tr>
                    <th>Dân tộc</th>
                    <td>{{ $student->Nation }}</td>
                    <th>Tôn giáo</th>
                    <td>{{ $student->Religion }}</td>
                </tr>
                <tr>
                    <th>Giới tính</th>
                    <td>{{ $student->Sex }}</td>
                    <th>CMND/CCCD</th>
                    <td>{{ $student->NationId }}</td>
                </tr>
            </table>
        </div>

        <div id="contact-info" class="tab-content" style="display: none;">
            <table>
                <tr>
                    <th>Quốc gia</th>
                    <td>Việt Nam</td>
                    <th>Tỉnh/Thành</th>
                    <td>{{ $student->province->Name ?? '' }}</td>
                </tr>
                <tr>
                    <th>Quận/Huyện</th>
                    <td>{{ $student->district->Name ?? '' }}</td>
                    <th>Phường/Xã</th>
                    <td>{{ $student->ward->Name ?? '' }}</td>
                </tr>
                <tr>
                    <th>Địa chỉ</th>
                    <td colspan="3">{{ $student->StreetAddress }}</td>
                </tr>
                <tr>
                    <th>Số điện thoại</th>
                    <td>{{ $student->PhoneNo }}</td>
                    <th>Email</th>
                    <td>{{ $student->Email }}</td>
                </tr>
            </table>
        </div>

        <div id="course-info" class="tab-content" style="display: none;">
            <table>
                <tr>
                    <th>Khoa</th>
                    <td>{{ $student->department->Name ?? '' }}</td>
                    <th>Chương trình đào tạo</th>
                    <td>{{ $student->curriculum->Name ?? '' }}</td>
                </tr>
                <tr>
                    <th>Khoá</th>
                    <td>{{ $student->curriculum->studyyear->Number ?? '' }}</td>
                    <th>Lớp</th>
                    <td>{{ $student->studentclass->Code ?? '' }}</td>
                </tr>
                <tr>
                    <th>Cố vấn học tập</th>
                    <td>{{ $student->studentclass->lecturer->FullName ?? '' }}</td>
                    <th>Niên khoá</th>
                    <td>{{ $student->curriculum->studyyear->StartYear ?? '' }} - {{ $student->curriculum->studyyear->EndYear ?? '' }}</td>
                </tr>
                <tr>
                    <th>Hệ</th>
                    <td>Chính quy</td>
                    <th>Tình trạng học</th>
                    <td>{{ $student->Status }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function showTab(tabId) {
        const tabs = document.querySelectorAll('.tab-content');
        const buttons = document.querySelectorAll('.tabs button');
        tabs.forEach(tab => tab.style.display = 'none');
        buttons.forEach(button => button.classList.remove('active'));
        document.getElementById(tabId).style.display = 'block';
        document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');
    }

    window.onload = () => {
        showTab('student-info'); // Default tab
    };
</script>
@endsection
