
<table class="table table-bordered table-hover">
    <thead>
        <tr class="table-primary">
            <th class="text-center text-light">Mã lớp học phần</th>
            <th class="text-center text-light">Tên lớp học phần</th>
            <th class="text-center text-light">Lớp sinh viên</th>
            <th class="text-center text-light">Sĩ số</th>
            <th class="text-center text-light">Thời khóa biểu</th>
            <th class="text-center text-light">Ngày bắt đầu</th>
            <th class="text-center text-light">Ngày kết thúc</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($listClass as $class)
        <tr>
            <td>{{ $class->Code }}</td>
            <td>{{ $class->Name }}</td>
            <td>{{ $class->studentClass->Code ?? '' }}</td>
            <td>{{ $class->studentJoinClasses->count() }}</td>
            <td>{{ $class->WeakDays }}</td>
            <td>{{ $class->StartDate->format('d/m/Y') }}</td>
            <td>{{ $class->EndDate->format('d/m/Y') }}</td>
            <td class="text-center" style="text-wrap:nowrap;">
                <a href="{{ route('lecturer.viewStudentList', ['id' => $class->Id]) }}" class="btn-service btn btn-info mx-1">
                    <img src="{{ asset('assets/images/bars-solid.svg') }}" height="20" alt="">
                </a>
                <a href="{{ route('admin.courseclass.exportStudentList', ['id' => $class->Id]) }}" class="btn-service btn btn-warning">
                    <img src="{{ asset('assets/images/file-export-solid.svg') }}" height="20" alt="">
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

