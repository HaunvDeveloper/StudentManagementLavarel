<?php
namespace App\Services;

use App\Models\Semester;
use Carbon\Carbon;

class StudyYearService
{
    /**
     * Get the current semester based on the current date.
     *
     * @return Semester|null
     */
    public static function getCurrentSemester()
    {
        $now = Carbon::now();

        // Query the database for a semester that includes the current date
        return Semester::where('StartDate', '<=', $now)
            ->where('EndDate', '>=', $now)
            ->first();
    }
}
