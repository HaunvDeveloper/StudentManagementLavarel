﻿
<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
        <a asp-action="Index" asp-controller="Home" asp-area="Lecturer" class="nav-link">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>
                Trang Chủ
            </p>
        </a>
    </li>
    <li class="nav-header">QUẢN LÝ GIẢNG DẠY</li>
    <li class="nav-item">
        <a asp-action="Schedules" asp-controller="CourseClass" asp-area="Lecturer" class="nav-link">
            <i class="nav-icon bi bi-building"></i>
            <p>Thời khóa biểu</p>
        </a>
    </li>
    <li class="nav-item">
        <a asp-action="Index" asp-controller="CourseClass" asp-area="Lecturer" class="nav-link">
            <i class="nav-icon bi bi-people-fill"></i>
            <p>Danh sách lớp đăng ký</p>
        </a>
    </li>
    <li class="nav-item">
        <a asp-action="ListTime" asp-controller="CourseClass" asp-area="Lecturer" class="nav-link">
            <i class="nav-icon bi bi-people-fill"></i>
            <p>Tổng hợp giờ giảng dạy</p>
        </a>
    </li>
    
    <li class="nav-header">CHỨC NĂNG TRỰC TUYẾN</li>
    <li class="nav-item">
        <a asp-action="Index" asp-controller="Attendance" asp-area="Lecturer" class="nav-link">
            <i class="nav-icon bi bi-grid"></i>
            <p>Điểm danh Online</p>
        </a>
    </li>
    
    
</ul>