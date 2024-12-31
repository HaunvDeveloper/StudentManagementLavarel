<?php

namespace App\Http\Controllers;

use App\Models\Courseclass;
use App\Models\Curriculum;
use App\Models\Lesson;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Studentjoinclass;
use App\Models\Studentjoinlesson;
use App\Models\Studyyeardetail;
use App\Services\StudyYearService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        $userId = session('Id');
        $student = Student::with('curriculum')->where('UserId', $userId)->first();

        if (!$student) {
            return redirect()->route('error.page')->with('error', 'Student not found');
        }

        $studyYearDetails = Studyyeardetail::query()
            ->orderBy('StartYear', 'asc')
            ->get();

        return view('student.dashboard', [
            'studyYearDetails' => $studyYearDetails,
            'student' => $student,
            'curriculum' => $student->curriculum,
        ]);
    }



    public function info()
    {
        $userId = session('Id');
        $student = Student::with(['ward', 'district', 'province'])->where('UserId', $userId)->first();

        if (!$student) {
            return abort(404, 'Student not found');
        }

        return view('student.info', compact('student'));
    }

    public function editInfo()
    {
        $userId = session('Id');
        $student = Student::where('UserId', $userId)->first();

        if (!$student) {
            return abort(404, 'Student not found');
        }

        $nationFilePath = public_path('Data/nation.json');
        $nationList = json_decode(file_get_contents($nationFilePath), true);
        $religionFilePath = public_path('Data/religion.json');
        $religionList = json_decode(file_get_contents($religionFilePath), true);
        return view('student.edit_info', [
            'student' => $student,
            'nationNames' => $nationList,
            'religionNames' => $religionList,
        ]);
    }

    public function updateInfo(Request $request)
    {
        $request->validate([
            'DayOfBirth' => 'required|date',
            'Nation' => 'required|string',
            'Province' => 'required|integer',
            'District' => 'required|integer',
            'Ward' => 'required|integer',
            'StreetAddress' => 'required|string',
            'PhoneNo' => 'required|string',
            'Email' => 'required|email',
            'Religion' => 'required|string',
            'BirthPlace' => 'required|string',
        ]);

        $userId = session('Id');
        $student = Student::where('UserId', $userId)->first();

        if (!$student) {
            return abort(404, 'Student not found');
        }

        $student->DayOfBirth = $request->DayOfBirth;
        $student->Email = $request->Email;
        $student->BirthPlace = $request->BirthPlace;
        $student->Nation = $request->Nation;
        $student->PhoneNo = $request->PhoneNo;
        $student->DistrictCode = $request->District;
        $student->WardCode = $request->Ward;
        $student->StreetAddress = $request->StreetAddress;
        $student->ProvinceCode = $request->Province;
        $student->Religion = $request->Religion;

        $student->save();

        return redirect()->route('student.info')->with('success', 'Information updated successfully!');
    }


    public function listCourse()
    {
        $userId = session('Id');
        $student = Student::where('UserId', $userId)->first();

        if (!$student) {
            return abort(404, 'Student not found');
        }

        $curriculum = Curriculum::with('studyyear')->find($student->CurriculumId);

        if (!$curriculum) {
            return abort(404, 'Curriculum not found');
        }

        $listSemester = Semester::whereBetween('SchoolYearDetailId', [$curriculum->studyyear->StartYearId, $curriculum->studyyear->EndYearId])->get();

        $courseClass = Studentjoinclass::where('StudentId', $student->Id)
            ->with('courseclass') // Include courseClass relationship
            ->get()
            ->pluck('courseclass'); // Extract courseClass from the results

        return view('student.courses.list', [
            'curriculum' => $curriculum,
            'semesters' => $listSemester,
            'courseClasses' => $courseClass,
        ]);
    }

    public function courseInfo($id)
    {
        $userId = session('Id');
        $student = Student::where('UserId', $userId)->first();

        if (!$student) {
            return abort(404, 'Student not found');
        }

        $courseClass = Courseclass::find($id);

        if (!$courseClass) {
            return abort(404, 'Course class not found');
        }

        $studentJoined = StudentJoinLesson::whereHas('lesson', function ($query) use ($id) {
            $query->where('CourseClassId', $id);
        })
        ->where('StudentId', $student->Id)
        ->with('lesson')
        ->get();

        return view('student.courses.info', [
            'courseClass' => $courseClass,
            'listJoined' => $studentJoined,
        ]);
    }




    public function schedules(Request $request)
    {
        $currentSemester = StudyYearService::getCurrentSemester();

        $studyYearDetails = Studyyeardetail::query()
            ->orderBy('StartYear', 'asc')
            ->get();

        return view('student.schedules', [
            'studyYearDetails' => $studyYearDetails,
            'currentSemesterId' => $currentSemester->SchoolYearDetailId ?? null,
        ]);
    }

    public function getSchedules(Request $request)
    {
        $userId = session('Id');
        $student = Student::where('UserId', $userId)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $startDate = Carbon::parse($request->input('startDate'))->toDateString();
        $endDate = Carbon::parse($request->input('endDate'))->toDateString();

        $lessons = Lesson::with(['courseclass.studentjoinclasses'])
            ->whereHas('courseclass.studentjoinclasses', function ($query) use ($student) {
                $query->where('StudentId', $student->Id);
            })
            ->whereBetween('Date', [$startDate, $endDate])
            ->orderBy('Date')
            ->get();

        return view('student._schedules', compact('lessons'));
    }









    public function getChartData(Request $request)
    {
        $semesterId = $request->input('semesterId');
        $userId = session('Id');
        $student = Student::where('UserId', $userId)->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $studentJoinLesson = Studentjoinlesson::with(['lesson.courseClass'])
            ->where('StudentId', $student->Id)
            ->whereHas('lesson.courseClass', function ($query) use ($semesterId) {
                $query->where('SemesterId', $semesterId);
            })
            ->get();

        $pieChartData = [
            'attended' => $studentJoinLesson->where('Status', 'Có mặt')->count(),
            'late' => $studentJoinLesson->where('Status', 'Đi trễ')->count(),
            'absent' => $studentJoinLesson->whereIn('Status', ['Có phép', 'Không phép'])->count(),
        ];

        $blockChartData = $studentJoinLesson
            ->groupBy(function ($item) {
                return $item->lesson->courseClass->subject->Name ?? 'Unknown';
            })
            ->map(function ($group, $subject) {
                return [
                    'subject' => $subject,
                    'attended' => $group->where('Status', 'Có mặt')->count(),
                    'late' => $group->where('Status', 'Đi trễ')->count(),
                    'absent' => $group->whereIn('Status', ['Có phép', 'Không phép'])->count(),
                ];
            })
            ->values()
            ->toArray();

        return response()->json([
            'pieChartData' => $pieChartData,
            'blockChartData' => $blockChartData,
        ]);
    }

}
