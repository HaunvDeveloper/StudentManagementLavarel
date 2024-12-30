
<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
        <a href="<?=route('admin.dashboard') ?>" class="nav-link">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>
                Trang Chủ
            </p>
        </a>
    </li>
    <li class="nav-header">Nhân sự</li>
    <li class="nav-item">
        <a href="{{route('admin.department.index')}}" class="nav-link">
            <i class="nav-icon bi bi-building"></i>
            <p>Khoa/Bộ môn</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.lecturer.index')}}" class="nav-link">
            <i class="nav-icon bi bi-people-fill"></i>
            <p>Giảng viên</p>
        </a>
    </li>
    <li class="nav-header">Chương trình giáo dục</li>
    <li class="nav-item">
        <a href="{{route('admin.major.index')}}" class="nav-link">
            <i class="nav-icon bi bi-mortarboard"></i>
            <p>Ngành học</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.subject.index')}}"  class="nav-link">
            <i class="nav-icon bi bi-journal"></i>
            <p>Môn học</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{route('admin.curriculum.index')}}" class="nav-link ">
            <i class="nav-icon bi bi-list-task"></i>
            <p>Chương trình đào tạo</p>
        </a>
    </li>

    <li class="nav-header">Học phần</li>
    <li class="nav-item">
        <a href="{{route('admin.courseclass.index')}}" class="nav-link">
            <i class="nav-icon bi bi-grid"></i>
            <p>Lớp học phần</p>
        </a>
    </li>
    <li class="nav-header">Sinh viên</li>
    <li class="nav-item">
        <a href="{{route('admin.student.index')}}" class="nav-link">
            <i class="nav-icon bi bi-people-fill"></i>
            <p>Sinh viên</p>
        </a>
    </li>
    
    
</ul>