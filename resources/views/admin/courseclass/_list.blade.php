
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr class="table-primary">
                <th class="text-center text-light">Mã lớp học phần</th>
                <th class="text-center text-light">Tên lớp học phần</th>
                <th class="text-center text-light">Sĩ số</th>
                <th class="text-center text-light">Sĩ số tối đa</th>
                <th class="text-center text-light">Giảng viên</th>
                <th class="text-center text-light">Thời khóa biểu</th>
                <th class="text-center text-light">Ngày bắt đầu</th>
                <th class="text-center text-light">Ngày kết thúc</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courseClasses as $item)
                <tr>
                    <td>{{ $item->Code }}</td>
                    <td>{{ $item->Name }}</td>
                    <td>{{ $item->StudentJoinClasses->count() }}</td>
                    <td>{{ $item->MaxQuantity }}</td>
                    <td>{{ $item->Lecturer->FullName ?? '' }}</td>
                    <td>{{ $item->WeakDays }}</td>
                    <td>{{ $item->StartDate->format('d/m/Y') }}</td>
                    <td>{{ $item->EndDate->format('d/m/Y') }}</td>
                    <td class="text-center" style="text-wrap:nowrap;">
                        <a href="{{ route('admin.courseclass.viewStudentList', ['id' => $item->Id]) }}" class="btn-service btn btn-info mx-1">
                            <img src="{{ asset('assets/images/bars-solid.svg') }}" height="20" alt="">
                        </a>
                        <a href="{{ route('admin.courseclass.edit', ['id' => $item->Id]) }}" class="btn-service btn btn-primary mx-1">
                            <img src="{{ asset('assets/images/pen-solid.svg') }}" height="20" alt="">
                        </a>
                        <button data-id="{{ $item->Id }}" onclick="deleteRow(this)" class="btn-service btn btn-danger">
                            <img src="{{ asset('assets/images/trash-solid.svg') }}" height="20" alt="">
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

