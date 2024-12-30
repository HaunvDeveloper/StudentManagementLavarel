@php
    $currentPage = $pagination['currentPage'] ?? 1;
    $totalPages = $pagination['totalPages'] ?? 1;
@endphp

<table id="table-student" class="table table-bordered table-hover">
    <thead>
        <tr class="table-primary">
            <th class="text-center text-light">Mã số sinh viên</th>
            <th class="text-center text-light">Họ và tên</th>
            <th class="text-center text-light">Email</th>
            <th class="text-center text-light">Ngày sinh</th>
            <th class="text-center text-light">Ngành</th>
            <th class="text-center text-light">Khoa</th>
            <th width="13%"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $student)
            <tr>
                <td>{{ $student->Id }}</td>
                <td>{{ $student->FullName }}</td>
                <td>{{ $student->Email }}</td>
                <td>{{ \Carbon\Carbon::parse($student->DayOfBirth)->format('d/m/Y') }}</td>
                <td>{{ $student->Curriculum->Name ?? 'N/A' }}</td>
                <td>{{ $student->department->Name ?? 'N/A' }}</td>
                <td class="text-center">
                    <a href="{{ route('admin.student.edit', $student->Id) }}" class="btn-service btn btn-primary mx-1">
                        <img src="{{ asset('assets/images/pen-solid.svg') }}" height="20" alt="">
                    </a>
                    <button data-id="{{ $student->Id }}" onclick="deleteRow(this)" class="btn-service btn btn-danger">
                        <img src="{{ asset('assets/images/trash-solid.svg') }}" height="20" alt="">
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Phân trang -->
<div class="pag-container middle">
    <div class="pagination">
        <ul>
            @if ($currentPage > 1)
                <a href="#" data-page="{{ $currentPage - 1 }}" class="move previous">
                    <img width="35" height="35" src="{{ asset('assets/images/circle-chevron-right-solid.svg') }}" alt="">
                </a>
            @else
                <a href="#" class="move previous disabled">
                    <img width="35" height="35" src="{{ asset('assets/images/circle-chevron-right-solid.svg') }}" alt="">
                </a>
            @endif

            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="{{ $i == $currentPage ? 'active' : '' }}">
                    <a href="#" data-page="{{ $i }}">{{ $i }}</a>
                </li>
            @endfor

            @if ($currentPage < $totalPages)
                <a href="#" data-page="{{ $currentPage + 1 }}" class="move next">
                    <img src="{{ asset('assets/images/circle-chevron-right-solid.svg') }}" alt="">
                </a>
            @else
                <a href="#" class="move next disabled">
                    <img src="{{ asset('assets/images/circle-chevron-right-solid.svg') }}" alt="">
                </a>
            @endif
        </ul>
    </div>
</div>
