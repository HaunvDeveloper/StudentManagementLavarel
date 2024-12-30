@extends('layouts.app')

@section('title', 'Thông tin giảng viên')

@section('links')
<link rel="stylesheet" href="{{asset("assets/css/lecturerDashboard.css")}}">
@endsection


@section('content')
<div class="container mt-5">
    <h4 class="text-left mb-4">Thông tin giảng viên</h4>
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <img src="{{asset("assets/images/img150.jpg")}}" class="rounded-circle mb-3" alt="Avatar">
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông tin cá nhân</h5>
                    <p><strong>Mã giảng viên:</strong> {{ $lecturer->Id }}</p>
                    <p><strong>Họ tên:</strong> {{ $lecturer->FullName }}</p>
                    <p><strong>Giới tính:</strong> {{ $lecturer->Sex }}</p>
                    <p><strong>Email:</strong> {{ $lecturer->Email }}</p>
                    <p><strong>Địa chỉ thường trú:</strong> {{ $lecturer->StreetAddress }}, {{ $lecturer->WardCodeNavigation->Name ?? '' }}, {{ $lecturer->DistrictCodeNavigation->Name ?? '' }}, {{ $lecturer->ProvinceCodeNavigation->Name ?? '' }}</p>
                    <p><strong>Địa chỉ liên lạc:</strong> {{ $lecturer->StreetAddress }}, {{ $lecturer->WardCodeNavigation->Name ?? '' }}, {{ $lecturer->DistrictCodeNavigation->Name ?? '' }}, {{ $lecturer->ProvinceCodeNavigation->Name ?? '' }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Thông tin cơ bản</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>CMND/CCCD:</strong> {{ $lecturer->NationId }}</p>
                            <p><strong>Ngày cấp:</strong> 28/12/2021</p>
                            <p><strong>Nơi cấp:</strong> Cục Cảnh sát quản lý hành chính về trật tự xã hội.</p>
                            <p><strong>Điện thoại:</strong> {{ $lecturer->PhoneNo }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày sinh:</strong> {{ \Carbon\Carbon::parse($lecturer->DayOfBirth)->format('d/m/Y') }}</p>
                            <p><strong>Nơi sinh:</strong> {{ $lecturer->BirthPlace }}</p>
                            <p><strong>Dân tộc:</strong> {{ $lecturer->Nation }}</p>
                            <p><strong>Tôn giáo:</strong> {{ $lecturer->Religion }}</p>
                        </div>
                    </div>

                    <h5 class="card-title">Thông tin chức vụ</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Học hàm:</strong> Giảng viên</p>
                            <p><strong>Học vị:</strong> Thạc Sĩ</p>
                            <p><strong>Chức vụ:</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Loại giảng viên:</strong> Hợp đồng thỉnh giảng</p>
                            <p><strong>Chuyên ngành:</strong> {{ $lecturer->Dept->Name ?? '' }}</p>
                            <p><strong>Khoa:</strong> {{ $lecturer->Dept->Name ?? '' }}</p>
                        </div>
                    </div>

                    <h5 class="card-title">Thông tin ngân hàng</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Số tài khoản:</strong></p>
                            <p><strong>Mã số thuế:</strong></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tên ngân hàng:</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
