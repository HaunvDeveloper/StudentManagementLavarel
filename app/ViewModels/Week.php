<?php
namespace App\ViewModels;
class Week
{
    public int $thuTuTuan;
    public \DateTime $ngayDauTuan;
    public \DateTime $ngayCuoiTuan;

    public function __construct(int $thuTuTuan, \DateTime $ngayDauTuan, \DateTime $ngayCuoiTuan)
    {
        $this->thuTuTuan = $thuTuTuan;
        $this->ngayDauTuan = $ngayDauTuan;
        $this->ngayCuoiTuan = $ngayCuoiTuan;
    }
}