<?php

namespace App\ViewModels;

class WeekDayViewModel
{
    public int $id;

    public function __construct(int $id = 1)
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        switch ($this->id) {
            case 1:
                return 'Chủ Nhật';
            case 2:
                return 'Thứ Hai';
            case 3:
                return 'Thứ Ba';
            case 4:
                return 'Thứ Tư';
            case 5:
                return 'Thứ Năm';
            case 6:
                return 'Thứ Sáu';
            case 7:
                return 'Thứ Bảy';
            default:
                return 'Không hợp lệ';
        }
    }

    public static function getAll(): array
    {
        return [
            new WeekDayViewModel(1),
            new WeekDayViewModel(2),
            new WeekDayViewModel(3),
            new WeekDayViewModel(4),
            new WeekDayViewModel(5),
            new WeekDayViewModel(6),
            new WeekDayViewModel(7),
        ];
    }
}


