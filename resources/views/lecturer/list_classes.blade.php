
<table class="table table-bordered table-hover">
    <thead>
        <tr class="table-primary">
            <th class="text-center text-light">Mã lớp học phần</th>
            <th class="text-center text-light">Tên lớp học phần</th>
            <th class="text-center text-light">Số TC</th>
            <th class="text-center text-light">Lớp sinh viên</th>
            <th class="text-center text-light">Sĩ số</th>
            <th class="text-center text-light">Số tiết</th>
            <th class="text-center text-light">Xem báo giảng</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listClass as $item)
        <tr>
            <td>{{ $item->Code }}</td>
            <td>{{ $item->Name }}</td>
            <td>{{ optional($item->Subject)->DefaultCredits }}</td>
            <td>{{ optional($item->StudentClass)->Code }}</td>
            <td>{{ $item->StudentJoinClasses->count() }}</td>
            <td>{{ optional($item->Subject)->DefaultLesson }}</td>
            <td class="text-center " style="text-wrap:nowrap;">
                <button data-pick="0" onclick="showLesson(<?= $item->Id ?>, this)" class="btn btn-outline-primary">CHI TIẾT</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

