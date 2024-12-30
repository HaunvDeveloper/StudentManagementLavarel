<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
        <a asp-action="Index" asp-controller="Home" asp-area="Student" class="nav-link ">
            <i class="nav-icon bi bi-house-fill"></i>
            <p>
                Trang Chủ
            </p>
        </a>
    </li>
    <li class="nav-header">TRANG CÁ NHÂN</li>
    <li class="nav-item">
        <a asp-action="Info" asp-controller="Student" asp-area="Student" class="nav-link ">
            <i class="nav-icon bi bi-building"></i>
            <p>Thông tin cá nhân</p>
        </a>
    </li>
   
    <li class="nav-header">HỌC TẬP</li>
    <li class="nav-item">
        <a asp-action="List" asp-controller="Course" asp-area="Student" class="nav-link ">
            <i class="nav-icon bi bi-mortarboard"></i>
            <p>Học phần đăng ký</p>
        </a>
    </li>
    <li class="nav-item">
        <a asp-action="Schedules" asp-controller="Course" asp-area="Student" class="nav-link ">
            <i class="nav-icon bi bi-journal"></i>
            <p>Lịch học</p>
        </a>
    </li>
    
    <li class="nav-header">CHƯƠNG TRÌNH ĐÀO TẠO</li>
    <li class="nav-item">
        <a asp-action="List" asp-controller="Curriculum" asp-area="Student" class="nav-link ">
            <i class="nav-icon bi bi-people-fill"></i>
            <p>Chương trình đào tạo</p>
        </a>
    </li>
    
</ul>
    