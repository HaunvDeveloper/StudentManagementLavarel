<?php

namespace App\Http\Controllers;

use App\Models\Courseclass;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Semester;
use App\Models\Student;
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


    public function attendance_index()
    {
        $currentSemester = StudyYearService::getCurrentSemester();

        $studyYearDetails = Studyyeardetail::query()
            ->orderBy('StartYear', 'asc')
            ->get();

        return view('lecturer.attendance.index', [
            'studyYearDetails' => $studyYearDetails,
            'currentSchoolYearDetailId' => $currentSemester->SchoolYearDetailId ?? null,
        ]);
    }

    public function attendance_getListStudent(Request $request)
    {
        $userId = session('Id');
        $lecturer = Lecturer::where('UserId', $userId)->first();
        $classId = request()->input('classId');
        $lessonId = request()->input('lessonId');
        if (!$lecturer) {
            return response()->json(['error' => 'Lecturer not found'], 404);
        }

        $courseClass = Courseclass::with(['studentjoinclasses.student'])
            ->where('Id', $classId)
            ->where('LecturerId', $lecturer->Id)
            ->first();


        $listStudent = Student::with('studentclass')->whereHas('studentjoinclasses', function ($query) use ($classId) {
            $query->whereHas('courseclass', function ($query) use ($classId) {
                $query->where('Id', $classId);
            });
        })->get();


        if (!$courseClass) {
            return response()->json(['error' => 'Class not found or unauthorized access'], 404);
        }

        $lessonJoined = Studentjoinlesson::with('lesson')
            ->where('LessonId', $lessonId)
            ->whereHas('lesson', function ($query) use ($classId) {
                $query->where('CourseClassId', $classId);
            })
            ->get();


        return view('lecturer.attendance.student_list', [
            'courseClass' => $courseClass,
            'lessonJoined' => $lessonJoined,
            'listStudent' => $listStudent
        ]);
    }



    public function attendance_getClasses(Request $request)
    {
        $userId = session('Id'); // Assuming session('Id') contains the logged-in user's ID
        $lecturer = Lecturer::where('UserId', $userId)->first();
        $semesterId = $request->input('semesterId');
        if (!$lecturer) {
            return response()->json(['error' => 'Lecturer not found'], 404);
        }

        $listClass = CourseClass::where('LecturerId', $lecturer->Id)
            ->where('SemesterId', $semesterId)
            ->get();

        return response()->json($listClass->map(function ($class) {
            return [
                'Id' => $class->Id,
                'Name' => $class->Code . ' - ' . $class->Name,
            ];
        }));
    }


    public function attendance_getLessons(Request $request)
    {
        $userId = session('Id'); // Assuming session('Id') contains the logged-in user's ID
        $lecturer = Lecturer::where('UserId', $userId)->first();
        $classId = $request->input('classId');
        if (!$lecturer) {
            return response()->json(['error' => 'Lecturer not found'], 404);
        }

        $courseClass = CourseClass::with('lessons.room')
            ->where('Id', $classId)
            ->where('LecturerId', $lecturer->Id)
            ->first();

        if (!$courseClass) {
            return response()->json(null);
        }

        $dayOfWeek = ["Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];

        $lessons = $courseClass->lessons->map(function ($lesson) use ($dayOfWeek) {
            $dayIndex = $lesson->Date->dayOfWeek; // Assuming Date is a Carbon instance
            return [
                'Id' => $lesson->Id,
                'Name' => $lesson->Date->format('d/m/Y') . ' - ' . $dayOfWeek[$dayIndex] . ' - ' . ($lesson->room->Code ?? ''),
                'Date' => $lesson->Date->toDateString(),
            ];
        });

        return response()->json($lessons);
    }

    public function attendance_save(Request $request)
    {
        try {
            $list = $request->input('list', []); // Nhận danh sách dữ liệu từ yêu cầu
            
            foreach ($list as $item) {
                $exist = Studentjoinlesson::where('LessonId', $item['LessonId'])
                    ->where('StudentId', $item['StudentId'])
                    ->first();

                if ($exist) {
                    if (empty($item['Status'])) {
                        // Xóa bản ghi nếu trạng thái trống
                        $exist->delete();
                    } else {
                        $exist->Status = $item['Status'];
                        $exist->LateLessons = $item['LateLessons'] ?? null;
                        $exist->Description = $item['Description'] ?? null;
                        $exist->save();
                    }
                } elseif (!empty($item['Status'])) {
                    Studentjoinlesson::create([
                        'LessonId' => $item['LessonId'],
                        'StudentId' => $item['StudentId'],
                        'Status' => $item['Status'],
                        'LateLessons' => $item['LateLessons'] ?? null,
                        'Description' => $item['Description'] ?? null,
                        'JoinTime' => now()
                    ]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $ex) {
            return response()->json(['success' => false, 'error' => $ex->getMessage()]);
        }
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



    public function listTime()
    {
        $currentSemester = Semester::where('StartDate', '<=', now())
            ->where('EndDate', '>=', now())
            ->first();

        $studyYearDetails = Studyyeardetail::query()
            ->orderBy('StartYear', 'asc')
            ->get();

        return view('lecturer.list_time', [
            'studyYearDetails' => $studyYearDetails,
            'currentSchoolYearDetailId' => $currentSemester->SchoolYearDetailId ?? null,
        ]);
    }

    public function _getListTime(Request $request)
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

        return view('lecturer.list_classes', compact('listClass'));
    }

    public function getListLesson(Request $request)
    {
        $userId = session('Id');
        $lecturer = Lecturer::where('UserId', $userId)->first();
        $courseClassId = $request->input('courseClassId');
        if (!$lecturer) {
            return response()->json(['error' => 'Lecturer not found'], 404);
        }

        $courseClass = Courseclass::with('lessons')
            ->where('Id', $courseClassId)
            ->where('LecturerId', $lecturer->Id)
            ->first();

        if (!$courseClass) {
            return response()->json(['error' => 'Course class not found or unauthorized access'], 404);
        }

        return view('lecturer.list_lessons', compact('courseClass'));
    }

}
