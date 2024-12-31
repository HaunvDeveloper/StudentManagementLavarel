@php
    $stt = 1;
    $studentJoined = $lessonJoined; // Assuming $lessonJoined is passed from the controller.
    $dayOfWeek = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
@endphp

<table id="table-student" class="table table-bordered table-hover">
    <thead class="table-primary">
        <tr>
            <th class="text-center" rowspan="2">STT</th>
            <th class="text-center" rowspan="2">Mã số sinh viên</th>
            <th class="text-center" rowspan="2">Họ và tên</th>
            <th class="text-center" rowspan="2">Ngày sinh</th>
            <th class="text-center" rowspan="2">Lớp</th>
            <th class="text-center" rowspan="2">Có mặt</th>
            <th class="text-center" colspan="2">Vắng</th>
            <th class="text-center" rowspan="2">Đi trễ</th>
            <th class="text-center" rowspan="2">Số tiết trễ</th>
            <th class="text-center" rowspan="2">Ghi chú</th>
        </tr>
        <tr>
            <th class="text-center" style="background-color:rgb(9, 107, 187);">Có phép</th>
            <th class="text-center" style="background-color:rgb(9, 107, 187);">Không phép</th>
        </tr>
    </thead>
    <tbody class="list-student">
        @foreach ($listStudent as $item)
            @php
                $status = $studentJoined->firstWhere('StudentId', $item->Id);
            @endphp
            <tr class="row-student" data-id="{{ $item->Id }}">
                <td>{{ $stt++ }}</td>
                <td>{{ $item->Id }}</td>
                <td>{{ $item->FullName }}</td>
                <td>{{ $item->studentclass->Code ?? '' }}</td>
                <td>{{ \Carbon\Carbon::parse($item->DayOfBirth)->format('d/m/Y') }}</td>
                <td class="text-center">
                    <div class="checkbox-wrapper-31">
                        <input type="checkbox" name="phep_{{ $item->Id }}" value="Có mặt" {{ $status?->Status == 'Có mặt' ? 'checked' : '' }} />
                        <svg viewBox="0 0 35.6 35.6">
                            <circle class="background" cx="17.8" cy="17.8" r="17.8"></circle>
                            <circle class="stroke" cx="17.8" cy="17.8" r="14.37"></circle>
                            <polyline class="check" points="11.78 18.12 15.55 22.23 25.17 12.87"></polyline>
                        </svg>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox-wrapper-31">
                        <input type="checkbox" name="phep_{{ $item->Id }}" value="Có phép" {{ $status?->Status == 'Có phép' ? 'checked' : '' }} />
                        <svg viewBox="0 0 35.6 35.6">
                            <circle class="background" cx="17.8" cy="17.8" r="17.8"></circle>
                            <circle class="stroke" cx="17.8" cy="17.8" r="14.37"></circle>
                            <polyline class="check" points="11.78 18.12 15.55 22.23 25.17 12.87"></polyline>
                        </svg>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox-wrapper-31">
                        <input type="checkbox" name="phep_{{ $item->Id }}" value="Không phép" {{ $status?->Status == 'Không phép' ? 'checked' : '' }} />
                        <svg viewBox="0 0 35.6 35.6">
                            <circle class="background" cx="17.8" cy="17.8" r="17.8"></circle>
                            <circle class="stroke" cx="17.8" cy="17.8" r="14.37"></circle>
                            <polyline class="check" points="11.78 18.12 15.55 22.23 25.17 12.87"></polyline>
                        </svg>
                    </div>
                </td>
                <td class="text-center">
                    <div class="checkbox-wrapper-31">
                        <input type="checkbox" name="phep_{{ $item->Id }}" value="Đi trễ" {{ $status?->Status == 'Đi trễ' ? 'checked' : '' }} />
                        <svg viewBox="0 0 35.6 35.6">
                            <circle class="background" cx="17.8" cy="17.8" r="17.8"></circle>
                            <circle class="stroke" cx="17.8" cy="17.8" r="14.37"></circle>
                            <polyline class="check" points="11.78 18.12 15.55 22.23 25.17 12.87"></polyline>
                        </svg>
                    </div>
                </td>
                <td>
                    @php
                        $isDisabled = $status?->Status != 'Đi trễ';
                    @endphp
                    <input type="number" min="0" max="100" class="lateLesson form-control-sm" name="lateLesson_{{ $item->Id }}" {{ $isDisabled ? 'disabled' : '' }} placeholder="..........." value="{{ $status?->LateLessons }}" />
                </td>
                <td>
                    <input type="text" class="description form-control-sm" name="description_{{ $item->Id }}" placeholder="..........." value="{{ $status?->Description }}" />
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
