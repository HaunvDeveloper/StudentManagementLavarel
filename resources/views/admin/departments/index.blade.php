@extends('layouts.app') {{-- Kế thừa layout chính của Admin --}}

@section('title', 'Danh sách khoa')

@section('links')
<link rel="stylesheet" href="{{ asset('assets/css/course.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="table-container m-4">
        <h4 class="text-center mb-4 mt-2 text-danger" style="font-size:35px;font-weight:bold;">DANH SÁCH KHOA/BỘ MÔN</h4>
        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('admin.department.create') }}" class="btn btn-add-course">+ Thêm khoa</a>
        </div>

        <table class="table table-bordered table-hover">
            <thead>
                <tr class="table-primary">
                    <th class="text-center text-light">STT</th>
                    <th class="text-center text-light">Mã khoa</th>
                    <th class="text-center text-light">Tên khoa</th>
                    <th width="13%"></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $stt = ($currentPage - 1) * $pageSize + 1;
                @endphp
                @foreach ($departments as $department)
                <tr>
                    <td>{{ $stt++ }}</td>
                    <td>{{ $department->Code }}</td>
                    <td>{{ $department->Name }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.department.edit', $department->Id) }}" class="btn-service btn btn-primary mx-1">
                            <img src="{{ asset('assets/images/pen-solid.svg') }}" height="20" alt="">
                        </a>
                        <a href="{{ route('admin.department.delete', $department->Id) }}" class="btn-service btn btn-danger">
                            <img src="{{ asset('assets/images/trash-solid.svg') }}" height="20" alt="">
                        </a>
                    </td>
                </tr>   
                @endforeach
            </tbody>
        </table>

        <div class="pag-container middle">
            <div class="pagination">
                <ul>
                    @if ($currentPage > 1)
                        <a href="{{ route('admin.department.index', ['page' => $currentPage - 1]) }}" class="move previous">
                            <img width="35" height="35" src="{{ asset('assets/images/circle-chevron-right-solid.svg') }}" alt="">
                        </a>
                    @else
                        <a href="#" class="move previous disabled">
                            <img width="35" height="35" src="{{ asset('assets/images/circle-chevron-right-solid.svg') }}" alt="">
                        </a>
                    @endif

                    @for ($i = 1; $i <= $totalPages; $i++)
                        <li class="{{ $i == $currentPage ? 'active' : '' }}">
                            <a href="{{ route('admin.department.index', ['page' => $i]) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    @if ($currentPage < $totalPages)
                        <a href="{{ route('admin.department.index', ['page' => $currentPage + 1]) }}" class="move next">
                            <img width="35" height="35" src="{{ asset('assets/images/circle-chevron-right-solid.svg') }}" alt="">
                        </a>
                    @else
                        <a href="#" class="move next disabled">
                            <img width="35" height="35" src="{{ asset('assets/images/circle-chevron-right-solid.svg') }}" alt="">
                        </a>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
   
        @if (isset($success))
        <script>
            showToast("success", "{{ $success }}");
            </script>
        @endif
    
@endsection
