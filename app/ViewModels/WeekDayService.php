<?php
namespace App\ViewModels;
class WeekDayService
{
    public static function findNearestWeekDay(\DateTime $d, int $weekDays): \DateTime
    {
        $targetDay = ($weekDays - 1) % 7;

        if ($d->format('w') == $targetDay) {
            return $d;
        }

        $forwardDistance = ($targetDay - $d->format('w') + 7) % 7;

        return $d->modify("+{$forwardDistance} days");
    }

    public static function createListWeek(\DateTime $startDate, \DateTime $endDate): array
    {
        $danhSachTuan = [];

        $current = clone $startDate;
        while ($current->format('w') != 1) {
            $current->modify('+1 day');
        }

        $thuTuTuan = 1;
        while ($current <= $endDate) {
            $ngayDauTuan = clone $current;
            $ngayCuoiTuan = (clone $current)->modify('+6 days');

            if ($ngayDauTuan > $endDate) {
                break;
            }

            if ($ngayCuoiTuan > $endDate) {
                $ngayCuoiTuan = $endDate;
            }

            $danhSachTuan[] = new Week($thuTuTuan, $ngayDauTuan, $ngayCuoiTuan);

            $thuTuTuan++;
            $current->modify('+7 days');
        }

        return $danhSachTuan;
    }

    public static function timTuanHienTai(array $danhSachTuan, \DateTime $ngayHienTai): int
    {
        foreach ($danhSachTuan as $tuan) {
            if ($ngayHienTai >= $tuan->ngayDauTuan && $ngayHienTai <= $tuan->ngayCuoiTuan) {
                return $tuan->thuTuTuan;
            }
        }
        return -1;
    }
}
