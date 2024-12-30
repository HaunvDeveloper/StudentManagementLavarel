<?php
namespace App\ViewModels;
class WeekDayInputViewModel
{
    public int $weekDayId;
    public int $startLessonId;
    public int $endLessonId;
    public int $roomId;

    public function __construct(array $data = [])
    {
        $this->weekDayId = $data['weekDayId'] ?? 0;
        $this->startLessonId = $data['startLessonId'] ?? 0;
        $this->endLessonId = $data['endLessonId'] ?? 0;
        $this->roomId = $data['roomId'] ?? 0;
    }
}