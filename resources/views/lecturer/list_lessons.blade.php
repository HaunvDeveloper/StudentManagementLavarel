@php
    $stt = 1;
    $dayOfWeek = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
    $lessons = $courseClass->lessons->sortBy('Date');
@endphp

<h4>Lịch giảng dạy chi tiết của HP: {{ $courseClass->Name }}</h4>
<table class="table table-sm table-hover">
    <thead>
        <tr class="table-primary">
            <th class="text-center text-light">STT</th>
            <th class="text-center text-light">Ngày</th>
            <th class="text-center text-light">Thứ</th>
            <th class="text-center text-light">Số Tiết</th>
            <th class="text-center text-light">Phòng</th>
            <th class="text-center text-light">Tổng số tiết</th>
            <th>Ghi chú</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lessons as $item)
            <tr>
                <td class="text-center">{{ $stt++ }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->Date)->format('d/m/Y') }}</td>
                <td class="text-center">{{ $dayOfWeek[\Carbon\Carbon::parse($item->Date)->dayOfWeek] }}</td>
                <td class="text-center">{{ $item->StartLesson }} - {{ $item->EndLesson }}</td>
                <td class="text-center">{{ $item->Room->Name }}</td>
                <td class="text-center">{{ $item->EndLesson - $item->StartLesson + 1 }}</td>
                <td class="text-center" style="text-wrap:nowrap;">
                    <!-- Add notes or actions here if needed -->
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
