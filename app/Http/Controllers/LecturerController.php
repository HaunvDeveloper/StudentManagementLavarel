<?php

namespace App\Http\Controllers;

use App\Models\Courseclass;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Semester;
use App\Models\Studentjoinlesson;
use App\Models\Studyyeardetail;
use App\Services\StudyYearService;
use App\ViewModels\WeekDayService;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    public function dashboard()
    {
        $userId = session('Id');
        $lecturer = Lecturer::where('UserId', $userId)->first();

        return view('lecturer.dashboard', compact('lecturer'));
    }


    public function index()
    {
        $currentSemester = StudyYearService::getCurrentSemester(); // Assuming a similar service exists
        $studyYearDetails = StudyYearDetail::all();
        $currentYearId = $currentSemester ? $currentSemester->SchoolYearDetailId : null;

        return view('lecturer.index', [
            'studyYearDetails' => $studyYearDetails,
            'currentYearId' => $currentYearId
        ]);
    }

    public function getListClass(Request $request)
    {
        $userId = session('Id');
        $lecturer = Lecturer::where('UserId', $userId)->first();
        $semesterId = $request->input('semesterId');
        if (!$lecturer) {
            return response()->json(['error' => 'Lecturer not found'], 404);
        }

        $listClass = Courseclass::where('LecturerId', $lecturer->Id)
            ->where('SemesterId', $semesterId)
            ->get();

        return view('lecturer._list_class', ['listClass' => $listClass]);
    }

    public function viewStudentList($id)
    {
        // Lấy thông tin lớp học phần
        $courseClass = CourseClass::with('studentjoinclasses')
            ->where('Id', $id)
            ->first();

        if (!$courseClass) {
            return abort(404, 'Không tìm thấy lớp học phần.');
        }

        // Lấy danh sách học sinh tham gia buổi học
        $lessonJoined = Studentjoinlesson::join('Lesson', 'StudentJoinLesson.LessonId', '=', 'Lesson.Id')
            ->where('Lesson.CourseClassId', $id)
            ->select('StudentJoinLesson.*')
            ->get();

        return view('lecturer.viewStudentList', [
            'courseClass' => $courseClass,
            'lessonJoined' => $lessonJoined
        ]);
    }



    public function schedules(Request $request)
    {
        $currentSemester = StudyYearService::getCurrentSemester();
        
        $studyYearDetails = Studyyeardetail::query()
            ->orderBy('StartYear', 'asc')
            ->get();

        return view('lecturer.schedules', [
            'studyYearDetails' => $studyYearDetails,
            'currentSemesterId' => $currentSemester->SchoolYearDetailId ?? null,
        ]);
    }

    public function getSchedules(Request $request)
    {
        $userId = session('Id');
        $lecturer = Lecturer::where('UserId', $userId)->first();

        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        $lessons = Lesson::query()
            ->join('courseclass', 'lesson.CourseClassId', '=', 'courseclass.Id')
            ->where('courseclass.LecturerId', $lecturer->Id)
            ->whereBetween('lesson.Date', [$startDate, $endDate])
            ->orderBy('lesson.Date')
            ->get();

        return view('lecturer._schedules', compact('lessons'));
    }

    public function getWeekBySemester(Request $request)
    {
        $semesterId = $request->input('semesterId');

        // Retrieve the semester by its ID
        $semester = Semester::find($semesterId);
        if (!$semester) {
            return response()->json(['error' => 'Semester not found'], 404);
        }

        // Generate the list of weeks
        $weeks = WeekDayService::createListWeek($semester->StartDate, $semester->EndDate);

        return response()->json($weeks);
    }


}
