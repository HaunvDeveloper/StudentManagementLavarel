@extends('layouts.app')

@section('title', 'Chỉnh sửa thông tin')

@section('links')
    <link rel="stylesheet" href="{{ asset('assets/css/editInfo.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endsection

@section('content')
<div class="_container">
    <div class="header-title"><i class="fa-solid fa-pen"></i> Chỉnh sửa thông tin</div>
    <form action="{{ route('student.updateInfo') }}" method="POST" class="scrollable">
        @csrf
        <div class="section-title">Thông tin cá nhân</div>
        <div class="form-group">
            <label for="fullName">Họ và tên</label>
            <input type="text" id="fullName" value="{{ $student->FullName }}" disabled>
        </div>
        <div class="form-group">
            <label for="birthDate">Ngày sinh</label>
            <input name="DayOfBirth" type="date" id="birthDate" value="{{ $student->DayOfBirth->format('Y-m-d') }}">
        </div>
        <div class="form-group">
            <label for="birthPlace">Nơi sinh</label>
            <input name="BirthPlace" type="text" id="BirthPlace" value="{{ $student->BirthPlace }}">
        </div>
        <div class="form-group">
            <label for="ethnicity">Dân tộc</label>
            <select name="Nation" id="ethnicity" class="form-control">
                @foreach ($nationNames as $nation)
                    <option value="{{ $nation['EthnicName'] }}" {{ $student->Nation == $nation['EthnicName'] ? 'selected' : '' }}>{{ $nation['EthnicName'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="religion">Tôn giáo</label>
            <select name="Religion" id="religion" class="form-control">
                @foreach ($religionNames as $religion)
                    <option value="{{ $religion['ReligionName'] }}" {{ $student->Religion == $religion['ReligionName'] ? 'selected' : '' }}>{{ $religion['ReligionName'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="idCard">CMND/CCCD</label>
            <input type="text" id="idCard" value="{{ $student->NationId }}" disabled>
        </div>

        <div class="section-title">Thông tin liên lạc</div>
        <div class="form-group">
            <label for="province">Tỉnh/Thành</label>
            <select id="province" class="form-control" name="Province" required>
                <option value="">Chọn Tỉnh/Thành</option>
            </select>
        </div>
        <div class="form-group">
            <label for="district">Quận/Huyện</label>
            <select id="district" class="form-control" name="District" required disabled>
                <option value="">Chọn Quận/Huyện</option>
            </select>
        </div>
        <div class="form-group">
            <label for="ward">Xã/Phường</label>
            <select id="ward" class="form-control" name="Ward" required disabled>
                <option value="">Chọn Xã/Phường</option>
            </select>
        </div>
        <div class="form-group">
            <label for="address">Địa chỉ</label>
            <input name="StreetAddress" type="text" id="address" value="{{ $student->StreetAddress }}">
        </div>
        <div class="form-group">
            <label for="phone">Số điện thoại</label>
            <input name="PhoneNo" type="text" id="phone" value="{{ $student->PhoneNo }}">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input name="Email" type="email" id="email" value="{{ $student->Email }}">
        </div>

        <div class="actions">
            <button type="submit" class="save-button btn btn-primary">Lưu</button>
            <a href="{{ route('student.info') }}" class="cancel-button btn btn-danger">Huỷ</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    var deProvince = "{{ $student->ProvinceCode }}";
    var deDistrict = "{{ $student->DistrictCode }}";
    var deWard = "{{ $student->WardCode }}";

    $(document).ready(function () {
        const apiBaseUrl = '{{url("/api/address")}}/';

        let selectedProvinceName = "";
        let selectedDistrictName = "";
        let selectedWardName = "";

        // Load tỉnh/thành
        $.get(apiBaseUrl + "provinces", function (provinces) {
            provinces.forEach(function (province) {
                if (deProvince == province.Code) {
                    $('#province').append(`<option selected value="${province.Code}">${province.Name}</option>`);
                } else {
                    $('#province').append(`<option value="${province.Code}">${province.Name}</option>`);
                }
            });
            $('#province').trigger('change');
        });

        // Khi tỉnh/thành thay đổi
        $('#province').on('change', function () {
            const provinceCode = $(this).val();
            selectedProvinceName = $(this).find('option:selected').text();
            $('#district').html('<option value="">Chọn Quận/Huyện</option>');
            $('#ward').html('<option value="">Chọn Xã/Phường</option>');
            $('#district').prop('disabled', true);
            $('#ward').prop('disabled', true);

            if (provinceCode) {
                $.get(apiBaseUrl + `districts/${provinceCode}`, function (districts) {
                    districts.forEach(function (district) {
                        $('#district').append(`<option value="${district.Code}" ${deDistrict == district.Code ? 'selected' : ''}>${district.Name}</option>`);
                    });
                    $('#district').prop('disabled', false);
                    $('#district').trigger('change');
                });
            }
        });

        // Khi quận/huyện thay đổi
        $('#district').on('change', function () {
            const districtCode = $(this).val();
            selectedDistrictName = $(this).find('option:selected').text();
            $('#ward').html('<option value="">Chọn Xã/Phường</option>');
            $('#ward').prop('disabled', true);

            if (districtCode) {
                $.get(apiBaseUrl + `wards/${districtCode}`, function (wards) {
                    wards.forEach(function (ward) {
                        $('#ward').append(`<option value="${ward.Code}" ${deWard == ward.Code ? 'selected' : ''}>${ward.Name}</option>`);
                    });
                    $('#ward').prop('disabled', false);
                });
            }
        });

        if (deDistrict) $('#district').trigger('change');
        if (deWard) $('#ward').trigger('change');
    });
</script>
@endsection
