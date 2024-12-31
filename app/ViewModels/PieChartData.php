<?php

namespace App\ViewModels;

class PieChartData
{
    public int $Attended;
    public int $Absent;
    public int $Late;

    public function __construct()
    {
        $this->Attended = 0;
        $this->Absent = 0;
        $this->Late = 0;
    }
}
