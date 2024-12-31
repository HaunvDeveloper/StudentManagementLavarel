<?php

namespace App\ViewModels;
use App\ViewModels\PieChartData;
use App\ViewModels\BlockChartData;


class ChartViewModel
{
    public $pieChartData;
    public $blockChartData;

    public function __construct()
    {
        $this->pieChartData = new PieChartData();
        $this->blockChartData = new BlockChartData();
    }
}
