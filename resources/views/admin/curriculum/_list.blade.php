@php
    $stt = 1;
@endphp

<table class="table table-hover table-bordered">
    <thead class="table-primary">
        <tr>
            <th>STT</th>
            <th>Mã chương trình</th>
            <th>Tên chương trình</th>
            <th>Khóa</th>
            <th>Ngành</th>
            <th>Tổng số tín chỉ</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($curricula as $item)
        <tr>
            <td>{{ $stt }}</td>
            <td>{{ $item->Code }}</td>
            <td>{{ $item->Name }}</td>
            <td>{{ $item->studyyear->Number }}</td>
            <td>{{ $item->major->Name }}</td>
            <td>{{ $item->courses->sum('Credits') }}</td>
            <td>
                <a href="{{ route('admin.curriculum.details', ['id' => $item->Id]) }}" class="btn btn-primary">Chi tiết</a>
                <button data-id="{{ $item->Id }}" onclick="deleteRow(this)" class="btn btn-danger">Xóa</button>
            </td>
        </tr>
        @php
            $stt++;
        @endphp
        @endforeach
    </tbody>
</table>
