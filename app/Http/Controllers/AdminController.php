<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Courseclass;
use App\Models\Coursetype;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\Lecturer;
use App\Models\Lesson;
use App\Models\Lessoninfo;
use App\Models\Major;
use App\Models\Room;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Studentclass;
use App\Models\Studentjoinclass;
use App\Models\Studentjoinlesson;
use App\Models\Studyyear;
use App\Models\Studyyeardetail;
use App\Models\Subject;
use App\ViewModels\WeekDayInputViewModel;
use App\ViewModels\WeekDayService;
use App\ViewModels\WeekDayViewModel;
use GuzzleHttp\Psr7\Response;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB as FacadesDB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }




    public function student_index()
    {
        $studyYears = Studyyear::all()->pluck('Number', 'Id');
        $departments = Department::all()->pluck('Name', 'Id');
    
        return view('admin.student.index', compact('studyYears', 'departments'));
    }
    


    public function student_getList(Request $request)
    {
        $query = Student::query();

        $keyword = $request->input('keyword');
        $studyYearId = $request->input('StudyYearId');
        $deptId = $request->input('DeptId');
        $specializationId = $request->input('SpecializationId');
        $page = $request->input('p', 1);
        $pageSize = $request->input('s', 20);

        // Apply filters
        if (!empty($keyword)) {
            $keyword = strtolower($keyword);
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(FullName) LIKE ?', ["%{$keyword}%"])
                ->orWhereRaw('LOWER(Id) LIKE ?', ["%{$keyword}%"]);
            });
        }

        if (!empty($studyYearId)) {
            $query->whereHas('curriculum', function ($q) use ($studyYearId) {
                $q->where('StudyYearId', $studyYearId);
            });
        }

        if (!empty($deptId)) {
            $query->where('DeptId', $deptId);
        }

        if (!empty($specializationId)) {
            $query->where('MajorId', $specializationId);
        }

        // Count total records for pagination
        $totalRecords = $query->count();

        // Fetch paginated results
        $students = $query->with(['curriculum', 'major', 'department'])
            ->orderBy('Id', 'desc')
            ->skip(($page - 1) * $pageSize)
            ->take($pageSize)
            ->get();

        // Prepare pagination data
        $pagination = [
            'currentPage' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($totalRecords / $pageSize),
            'totalRecords' => $totalRecords,
        ];

        return view('admin.student._list',[
            'students' => $students,
            'pagination' => $pagination,
        ]);
    }


    public function student_createWithList()
    {
        return view('admin.student.createWithList');
    }


    public function student_storeWithList(Request $request)
    {
        $file = $request->file('file');

        if (!$file || !$file->isValid()) {
            return response()->json(['success' => false, 'error' => 'File không hợp lệ!!!']);
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rowCount = $worksheet->getHighestRow();

            $students = [];
            $studentClasses = StudentClass::all();
            $curriculums = Curriculum::all();
            $depts = Department::all();
            $majors = Major::all();

            $errors = [];
            $successCount = 0;

            for ($row = 6; $row <= $rowCount; $row++) {
                try {
                    $studentId = trim($worksheet->getCell("C$row")->getValue());
                    if (empty($studentId)) {
                        break;
                    }

                    $studentClassId = optional($studentClasses->firstWhere('Code', trim($worksheet->getCell("J$row")->getValue())))->Id;
                    $curriculumId = optional($curriculums->firstWhere('Code', trim($worksheet->getCell("K$row")->getValue())))->Id;
                    $deptId = optional($depts->firstWhere('Code', trim($worksheet->getCell("L$row")->getValue())))->Id;
                    $majorId = optional($majors->firstWhere('Code', trim($worksheet->getCell("M$row")->getValue())))->Id;

                    if (!$studentClassId || !$curriculumId || !$deptId || !$majorId) {
                        $errors[] = "Mã dữ liệu nhập không tìm thấy trong cơ sở dữ liệu";
                        continue;
                    }

                    $students[] = [
                        'Id' => $studentId,
                        'FullName' => trim($worksheet->getCell("D$row")->getValue()) . " " . trim($worksheet->getCell("E$row")->getValue()),
                        'DayOfBirth' => \Carbon\Carbon::parse(trim($worksheet->getCell("F$row")->getValue())),
                        'Email' => trim($worksheet->getCell("G$row")->getValue()),
                        'StudentClassId' => $studentClassId,
                        'CurriculumId' => $curriculumId,
                        'DeptId' => $deptId,
                        'MajorId' => $majorId,
                        'PhoneNo' => trim($worksheet->getCell("H$row")->getValue()),
                        'Sex' => trim($worksheet->getCell("I$row")->getValue()),
                        'NationId' => trim($worksheet->getCell("N$row")->getValue()),
                        'BirthPlace' => trim($worksheet->getCell("O$row")->getValue()),
                        'StreetAddress' => trim($worksheet->getCell("P$row")->getValue()),
                        'Nation' => trim($worksheet->getCell("Q$row")->getValue()),
                        'Religion' => trim($worksheet->getCell("R$row")->getValue()),
                        'Status' => "Còn học"
                    ];

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "$studentId has error: " . $e->getMessage();
                    continue;
                }
            }

            Student::insert($students);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.student.index'),
                'message' => $successCount > 0 ? "Successfully added $successCount students with some errors: " . implode("\n", $errors) : implode("\n", $errors),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function student_downloadExcelFile()
    {
        $filePath = public_path('Data/MauSV.xlsx');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File không tồn tại.'], 404);
        }

        return response()->download($filePath, 'MauSV.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }


    public function student_destroy($id)
    {
        try {
            $student = Student::find($id);

            if ($student) {
                $student->delete();
            }

            return response()->json([
                'success' => true,
                'redirect' => route('admin.student.index')
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function getSpecializationByDept(Request $request)
    {
        $deptId = $request->input('deptid');
    
        if (!$deptId) {
            return response()->json(['error' => 'Department ID is required.'], 400);
        }
    
        // Fetch specializations from the database based on DeptId
        $specializations = Major::where('DeptId', $deptId)
            ->select('Id', 'Name')
            ->get();
    
        return response()->json($specializations);
    }

    public function student_downloadList(Request $request)
    {
        $studyYearId = $request->input('StudyYearId');
        $deptId = $request->input('DeptId');
        $specializationId = $request->input('SpecializationId');

        $query = Student::query();
        $studyYear = null;
        $department = null;
        $major = null;

        if ($studyYearId) {
            $query->whereHas('curriculum.studyyear', function ($q) use ($studyYearId) {
                $q->where('Id', $studyYearId);
            });
            $studyYear = StudyYear::find($studyYearId);
        }

        if ($deptId) {
            $query->where('DeptId', $deptId);
            $department = Department::find($deptId);
        }

        if ($specializationId) {
            $query->where('MajorId', $specializationId);
            $major = Major::find($specializationId);
        }

        $students = $query->with(['curriculum.studyyear', 'studentclass', 'department', 'major'])->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Title row
        $sheet->mergeCells('A1:R1');
        $sheet->setCellValue('A1', 'DANH SÁCH SINH VIÊN');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $row = 2;

        // Filters
        if ($studyYearId) {
            $sheet->setCellValue("A{$row}", 'Khóa');
            $sheet->setCellValue("B{$row}", "{$studyYear->Number} | {$studyYear->StartYear} - {$studyYear->EndYear}");
            $row++;
        }

        if ($deptId) {
            $sheet->setCellValue("A{$row}", 'Khoa');
            $sheet->setCellValue("B{$row}", $department->Name);
            $row++;
        }

        if ($specializationId) {
            $sheet->setCellValue("A{$row}", 'Ngành');
            $sheet->setCellValue("B{$row}", $major->Name);
            $row++;
        }

        // Table headers
        $headers = [
            'STT', 'Khóa', 'Mã số sinh viên', 'Họ lót', 'Tên', 'Ngày sinh', 'Email',
            'SĐT', 'Giới tính', 'Mã lớp sinh viên', 'Mã CTDT', 'Mã khoa', 'Mã ngành',
            'CCCD', 'Nơi sinh', 'Địa chỉ', 'Dân tộc', 'Tôn giáo'
        ];

        foreach ($headers as $col => $header) {
            // Convert the column index to a letter (1 -> A, 2 -> B, ...)
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue("{$columnLetter}{$row}", $header);
        }
        

        $sheet->getStyle("A{$row}:R{$row}")->getFont()->setBold(true);
        $row++;

        // Student data
        foreach ($students as $index => $student) {
            $nameParts = explode(' ', trim($student->FullName));
            $ho = implode(' ', array_slice($nameParts, 0, count($nameParts) - 1));
            $ten = end($nameParts);

            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", optional($student->curriculum->studyyear)->Number);
            $sheet->setCellValue("C{$row}", $student->Id);
            $sheet->setCellValue("D{$row}", $ho);
            $sheet->setCellValue("E{$row}", $ten);
            $sheet->setCellValue("F{$row}", $student->DayOfBirth->format('Y-m-d'));
            $sheet->setCellValue("G{$row}", $student->Email);
            $sheet->setCellValue("H{$row}", $student->PhoneNo);
            $sheet->setCellValue("I{$row}", $student->Sex);
            $sheet->setCellValue("J{$row}", optional($student->studentclass)->Code);
            $sheet->setCellValue("K{$row}", $student->curriculum->Code ?? '');
            $sheet->setCellValue("L{$row}", $student->department->Code ?? '');
            $sheet->setCellValue("M{$row}", $student->major->Code ?? '');
            $sheet->setCellValue("N{$row}", $student->NationId);
            $sheet->setCellValue("O{$row}", $student->BirthPlace);
            $sheet->setCellValue("P{$row}", $student->StreetAddress);
            $sheet->setCellValue("Q{$row}", $student->Nation);
            $sheet->setCellValue("R{$row}", $student->Religion);
            $row++;
        }

        // Auto fit columns
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'DanhSachSinhVien.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    //COURSECLASS 
    public function courseclass_index()
    {
        $studyYearDetails = StudyYearDetail::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => $item->StartYear . ' - ' . $item->EndYear
            ];
        });

        $depts = Department::pluck('Name', 'Id');

        return view('admin.courseclass.index', compact('studyYearDetails', 'depts'));
    }

    public function getListClass(Request $request)
    {
        $query = Courseclass::query();

        if ($request->filled('semesterId')) {
            $query->where('SemesterId', $request->semesterId);
        } elseif ($request->filled('yearDetailId')) {
            $query->whereHas('semester', function ($query) use ($request) {
                $query->where('SchoolYearDetailId', $request->yearDetailId);
            });
        }

        if ($request->filled('deptId')) {
            $query->whereHas('subject', function ($query) use ($request) {
                $query->where('DeptId', $request->deptId);
            });
        }

        $courseClasses = $query->get();

        return view('admin.courseclass._list', compact('courseClasses'));
    }

    public function getClassBySubject($subjectId, $semesterId)
    {
        $subject = Subject::findOrFail($subjectId);

        $list = CourseClass::where('SubjectId', $subjectId)->get();

        return view('admin.courseclass.subject_classes', compact('subject', 'list'));
    }

    public function courseclass_create()
    {
        $studyYearDetails = StudyYearDetail::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => $item->StartYear . ' - ' . $item->EndYear
            ];
        });

        $studentClasses = Studentclass::pluck('Code', 'Id');
        $subjects = Subject::pluck('Name', 'Id');
        $lessons = Lessoninfo::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => 'Tiết ' . $item->Id . ' | ' . date('H:i', strtotime($item->StartTime)) . ' - ' . date('H:i', strtotime($item->EndTime))
            ];
        });
        $rooms = Room::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => $item->Name . ' - ' . $item->Address
            ];
        });
        $weekDays = WeekDayViewModel::getAll();
        $lecturers = Lecturer::all()->map(callback: function ($item) {
            return [
                'value' => $item->Id,
                'text' => $item->Id . ' - ' . $item->FullName
            ];
        });

        return view('admin.courseclass.create', compact('studyYearDetails', 'studentClasses', 'subjects', 'lessons', 'rooms', 'weekDays', 'lecturers'));
    }

    public function courseclass_store(Request $request)
    {
        try {
            $temp = $request->input('model');
            $model = new Courseclass();
            $model->Code = $temp['Code'];
            $model->Name = $temp['Name'];
            $model->SemesterId = $temp['SemesterId'];
            $model->StartDate = $temp['StartDate'];
            $model->EndDate = $temp['EndDate'];
            $model->MaxQuantity = $temp['MaxQuantity'];
            $model->SubjectId = $temp['SubjectId'];
            $model->StudentClassId = $temp['StudentClassId'];
            $model->LecturerId = $temp['LecturerId'];
            $model->DefaultRoomId = $temp['DefaultRoomId'];
            $model->CurrentQuantity = 0;

            $weekDays = $request->input('weekDays', []);
            $tkb = [];
            $lessons = []; // Tạm thời lưu các Lesson

            foreach ($weekDays as $weekDay) {
                $startDate = new \DateTime($model->StartDate);
                $date = WeekDayService::findNearestWeekDay($startDate, $weekDay['WeekDayId']);
                $room = Room::findOrFail($weekDay['RoomId']);
                $wtmp = new WeekDayViewModel($weekDay['WeekDayId']);
                $tkb[] = $wtmp->toString() . ", Tiết " . $weekDay['StartLessonId'] . " - " . $weekDay['EndLessonId'] . ", Phòng " . $room->Name;

                while ($date <= new \DateTime($model->EndDate)) {
                    $lessons[] = new Lesson([
                        'Date' => $date->format('Y-m-d'),
                        'StartLesson' => $weekDay['StartLessonId'],
                        'EndLesson' => $weekDay['EndLessonId'],
                        'RoomId' => $weekDay['RoomId'],
                    ]);
                    $date->modify('+7 days');
                }
            }

            $model->WeakDays = implode(' || ', $tkb);
            $model->save(); // Lưu lớp học phần trước để lấy ID

            // Gắn `CourseClassId` cho từng Lesson
            foreach ($lessons as $lesson) {
                $lesson->CourseClassId = $model->Id; // Gắn khóa ngoại
            }
            // /dd($lessons);
            // Liên kết các Lesson với lớp học phần
            $model->lessons()->saveMany($lessons);

            return response()->json(['success' => true, 'redirect' => route('admin.courseclass.index')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function courseclass_destroy($id)
    {
        try {
            // Find the CourseClass by ID
            $courseClass = CourseClass::find($id);
    
            if ($courseClass) {
                // Get all associated StudentJoinClass records
                $studentJoinClasses = StudentJoinClass::where('CourseClassId', $courseClass->Id)->get();
    
                // Delete associated StudentJoinClass records
                StudentJoinClass::destroy($studentJoinClasses->pluck('Id')->toArray());
    
                // Delete the CourseClass
                $courseClass->delete();
            }
    
            return response()->json([
                'success' => true,
                'redirect' => route('admin.courseclass.index')
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'error' => $ex->getMessage()
            ]);
        }
    }



    public function exportStudentList($id)
    {
        $filePath = public_path('Data/DanhSachSVLHP.xlsx');

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Tệp không tồn tại.'], 404);
        }

        // Fetching data from the database
        $courseClass = CourseClass::with('Semester', 'Semester.studyyeardetail', 'subject', 'room', 'lecturer')
            ->findOrFail($id);

        $students = StudentJoinClass::with('Student', 'Student.StudentClass')
            ->where('CourseClassId', $id)
            ->get();

        $lessonJoins = StudentJoinLesson::with('Lesson')
            ->whereHas('Lesson', function ($query) use ($id) {
                $query->where('CourseClassId', $id);
            })
            ->get();

        $file = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Filling general course class data
        $worksheet->setCellValue("D4", $courseClass->Semester->Name ?? '');
        $worksheet->setCellValue("I4", $courseClass->Semester->studyyeardetail->StartYear . " - " . $courseClass->Semester->studyyeardetail->EndYear ?? '');
        $worksheet->setCellValue("E5", $courseClass->Subject->Code . " - " . $courseClass->Name ?? '');
        $worksheet->setCellValue("W5", $courseClass->Code);
        $worksheet->setCellValue("A6", "Thời gian học: Bắt đầu: " . $courseClass->StartDate->format('d/m/Y') . " - Kết thúc: " . $courseClass->EndDate->format('d/m/Y'));
        $worksheet->setCellValue("B7", $courseClass->WeakDays);

        // Adding student data
        $startRow = 10;
        $currentRow = $startRow;
        $stt = 1;

        foreach ($students as $studentJoin) {
            $student = $studentJoin->student;
            $nameParts = explode(' ', $student->FullName);
            $ho = implode(' ', array_slice($nameParts, 0, -1));
            $ten = end($nameParts);

            $worksheet->setCellValue("A$currentRow", $stt++);
            $worksheet->setCellValue("C$currentRow", $student->Id);
            $worksheet->setCellValue("F$currentRow", $student->StudentClass->Code ?? '');
            $worksheet->setCellValue("H$currentRow", $ho);
            $worksheet->setCellValue("M$currentRow", $ten);
            $worksheet->setCellValue("Q$currentRow", $student->DayOfBirth->format('d/m/Y'));

            $coMat = $lessonJoins->where('StudentId', $student->Id)->where('Status', 'Có mặt')->count();
            $diTre = $lessonJoins->where('StudentId', $student->Id)->where('Status', 'Đi trễ')->count();
            $vang = $lessonJoins->where('StudentId', $student->Id)->where('Status', 'Vắng')->count();

            $worksheet->setCellValue("U$currentRow", $coMat);
            $worksheet->setCellValue("X$currentRow", $diTre);
            $worksheet->setCellValue("AB$currentRow", $vang);

            $currentRow++;
        }

        // Applying border styles
        $range = "A9:AD" . ($currentRow - 1);
        $worksheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Save and return the file
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = "DanhSachSinhVien_ChinhSua.xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }



    public function courseclass_createWithList()
    {
        $studyYearDetails = StudyYearDetail::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => $item->StartYear . ' - ' . $item->EndYear
            ];
        });

        return view('admin.courseclass.createWithList', compact('studyYearDetails'));
    }

    public function courseclass_storeWithList(Request $request)
    {
        $file = $request->file('file');
        $semesterId = $request->input('SemesterId');

        if (!$file || !$file->isValid()) {
            return response()->json(['success' => false, 'error' => 'File không hợp lệ!!!']);
        }

        try {
            $stream = $file->getRealPath();
            $spreadsheet = IOFactory::load($stream);
            $worksheet = $spreadsheet->getActiveSheet();
            $rowCount = $worksheet->getHighestRow();

            $lessonInfos = Lessoninfo::all();
            $rooms = Room::all();

            for ($row = 8; $row <= $rowCount; $row++) {
                $subjectCode = trim($worksheet->getCell("B$row")->getValue());
                if (empty($subjectCode)) {
                    continue;
                }

                $studentClassCode = trim($worksheet->getCell("F$row")->getValue()) ?: null;
                $subjectId = Subject::where('Code', $subjectCode)->value('Id');
                $studentClassId = $studentClassCode ? Studentclass::where('Code', $studentClassCode)->value('Id') : null;
                $roomCode = trim($worksheet->getCell("H$row")->getValue());
                $roomId = $rooms->firstWhere('Code', $roomCode)->Id ?? 0;

                $courseClass = Courseclass::create([
                    'Code' => trim($worksheet->getCell("C$row")->getValue()),
                    'Name' => trim($worksheet->getCell("D$row")->getValue()),
                    'SemesterId' => $semesterId,
                    'StartDate' => date('Y-m-d', strtotime($worksheet->getCell("I$row")->getValue())),
                    'EndDate' => date('Y-m-d', strtotime($worksheet->getCell("J$row")->getValue())),
                    'MaxQuantity' => (int)trim($worksheet->getCell("G$row")->getValue()),
                    'CurrentQuantity' => 0,
                    'LecturerId' => (int)trim($worksheet->getCell("K$row")->getValue()),
                    'SubjectId' => $subjectId,
                    'StudentClassId' => $studentClassId,
                    'DefaultRoomId' => $roomId,
                ]);

                $tkb = [];
                $soBuoi = (int)trim($worksheet->getCell("M$row")->getValue());
                $char = 'N';
                for ($i = 1; $i <= $soBuoi; $i++) {
                    try {
                        $weekDayId = (int)trim($worksheet->getCell($char . $row)->getValue());
                        $startLessonId = (int)trim($worksheet->getCell(chr(ord($char) + 1) . $row)->getValue());
                        $endLessonId = (int)trim($worksheet->getCell(chr(ord($char) + 2) . $row)->getValue());
                        $roomCode = trim($worksheet->getCell(chr(ord($char) + 3) . $row)->getValue());
                        $roomId = $rooms->firstWhere('Code', $roomCode)->Id ?? 0;

                        $date = WeekDayService::findNearestWeekDay(new \DateTime($courseClass->StartDate), $weekDayId);
                        $room = $rooms->firstWhere('Id', $roomId);
                        $tkb[] = sprintf('%s, Tiết %d - %d, Phòng %s',
                            (new WeekDayViewModel($weekDayId))->getName(),
                            $startLessonId,
                            $endLessonId,
                            $room->Name ?? '');

                        while ($date <= new \DateTime($courseClass->EndDate)) {
                            $lesson = new Lesson([
                                'Date' => $date->format('Y-m-d'),
                                'StartLesson' => $startLessonId,
                                'EndLesson' => $endLessonId,
                                'RoomId' => $roomId,
                            ]);
                            $courseClass->lessons()->save($lesson);
                            $date->modify('+7 days');
                        }

                        $char = chr(ord($char) + 4);
                    } catch (\Exception $e) {
                        break;
                    }
                }
                $courseClass->update(['WeakDays' => implode(' || ', $tkb)]);
            }

            return response()->json(['success' => true, 'redirect' => route('admin.courseclass.index')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    public function courseclass_downloadExcelFile(): BinaryFileResponse
    {
        $filePath = public_path('Data/MauLHP.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại.');
        }

        return response()->download($filePath, 'MauLHP.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function GetSemesterByYearDetail($yearDetailId)
    {
        $semester = Semester::where('SchoolYearDetailId', $yearDetailId)->get();
        return response()->json($semester);
    }

    public function getNewCodeCourseClass(Request $request)
    {
        $subjectId = $request->input('subjectId');
        $semesterId = $request->input('semesterId');

        // Tìm Subject
        $subject = Subject::where('Id', $subjectId)->first();

        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }

        // Đếm số lượng CourseClass theo SubjectId và SemesterId
        $count = CourseClass::where('SubjectId', $subjectId)
            ->where('SemesterId', $semesterId)
            ->count() + 1;

        // Trả về thông tin mã lớp học phần mới
        return response()->json([
            'code' => $subject->Code . str_pad($count, 2, '0', STR_PAD_LEFT),
            'name' => $subject->Name,
            'lessonNo' => $subject->DefaultLesson,
        ]);
    }

    public function courseclass_edit($id)
    {
        $model = Courseclass::with('lessons')->find($id);
        if (!$model) {
            abort(404, 'Course Class not found.');
        }

        $studyYearDetails = Studyyeardetail::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => $item->StartYear . ' - ' . $item->EndYear
            ];
        });

        $studentClasses = Studentclass::pluck('Code', 'Id');
        $subjects = Subject::pluck('Name', 'Id');
        $lessons = Lessoninfo::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => "Tiết {$item->Id} | {$item->StartTime} - {$item->EndTime}"
            ];
        });
        $rooms = Room::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => "{$item->Name} - {$item->Address}"
            ];
        });
        $lecturers = Lecturer::all()->map(function ($item) {
            return [
                'value' => $item->Id,
                'text' => "{$item->Id} - {$item->FullName}"
            ];
        });

        return view('admin.courseclass.edit', compact(
            'model',
            'studyYearDetails',
            'studentClasses',
            'subjects',
            'lessons',
            'rooms',
            'lecturers'
        ));
    }


    public function courseclass_update(Request $request, $id)
    {
        try {
            $model = Courseclass::with('lessons')->find($id);

            if (!$model) {
                return response()->json(['success' => false, 'error' => 'Course Class not found.']);
            }

            // Cập nhật thông tin lớp học phần
            $model->Code = $request->input('Code');
            $model->Name = $request->input('Name');
            $model->LecturerId = $request->input('LecturerId');
            $model->StartDate = $request->input('StartDate');
            $model->EndDate = $request->input('EndDate');
            $model->StudentClassId = $request->input('StudentClassId');
            $model->MaxQuantity = $request->input('MaxQuantity');

            $newLessons = $request->input('Lessons', []);
            $existingLessons = $model->Lessons;

            // Xóa những Lesson không còn trong danh sách mới
            $lessonsToRemove = $existingLessons->whereNotIn('Id', collect($newLessons)->pluck('Id'))->all();
            foreach ($lessonsToRemove as $lesson) {
                $lesson->delete();
            }

            // Cập nhật những Lesson đã tồn tại
            foreach ($newLessons as $lessonData) {
                if (!empty($lessonData['Id'])) {
                    $lesson = $existingLessons->firstWhere('Id', $lessonData['Id']);
                    if ($lesson) {
                        $lesson->update([
                            'StartLesson' => $lessonData['StartLesson'],
                            'EndLesson' => $lessonData['EndLesson'],
                            'Date' => $lessonData['Date'],
                            'RoomId' => $lessonData['RoomId'],
                        ]);
                    }
                }
            }

            // Thêm mới các Lesson chưa tồn tại
            $lessonsToAdd = collect($newLessons)->whereNull('Id');
            foreach ($lessonsToAdd as $lessonData) {
                $model->Lessons()->create([
                    'StartLesson' => $lessonData['StartLesson'],
                    'EndLesson' => $lessonData['EndLesson'],
                    'Date' => $lessonData['Date'],
                    'RoomId' => $lessonData['RoomId'],
                ]);
            }

            $model->save();

            return response()->json(['success' => true, 'redirect' => route('admin.courseclass.index')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
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

        return view('admin.courseclass.viewStudentList', [
            'courseClass' => $courseClass,
            'lessonJoined' => $lessonJoined
        ]);
    }
    public function getStudentById(Request $request)
    {
        $id = $request->input('id');

        // Kiểm tra nếu `id` không hợp lệ hoặc ngắn hơn 3 ký tự
        if (empty($id) || strlen($id) < 3) {
            return response()->json([
                'message' => 'Vui lòng nhập ít nhất 3 ký tự.'
            ], 400); // Bad Request
        }

        // Lấy danh sách sinh viên dựa trên tiêu chí tìm kiếm
        $students = Student::where('FullName', 'like', "%{$id}%")
            ->orWhere('Id', 'like', "%{$id}%")
            ->select('Id', 'FullName as Name') // Chỉ lấy các cột cần thiết
            ->take(50) // Giới hạn số lượng kết quả
            ->get();

        return response()->json($students);
    }


    public function addStudentToClass(Request $request)
    {
        $studentId = $request->input('studentId');
        $courseClassId = $request->input('courseClassId');

        try {
            // Kiểm tra nếu sinh viên đã tồn tại trong lớp học phần
            $exists = Studentjoinclass
                ::where('CourseClassId', $courseClassId)
                ->where('StudentId', $studentId)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'error' => 'Sinh viên đã tồn tại'
                ]);
            }

            Studentjoinclass::insert([
                'StudentId' => $studentId,
                'CourseClassId' => $courseClassId,
                'DateJoin' => now()
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.courseclass.viewStudentList', ['id' => $courseClassId])
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function removeStudent(Request $request)
    {
        $studentId = $request->input('studentId');
        $courseClassId = $request->input('courseClassId');

        try {
            // Tìm bản ghi phù hợp trong bảng `student_join_classes`
            $model = Studentjoinclass::where('StudentId', $studentId)
                ->where('CourseClassId', $courseClassId)
                ->first();

            if ($model) {
                Studentjoinclass::where('StudentId', $studentId)
                    ->where('CourseClassId', $courseClassId)
                    ->delete();
            }

            // Trả về phản hồi thành công
            return response()->json([
                'success' => true,
                'redirect' => route('admin.courseclass.viewStudentList', ['id' => $courseClassId])
            ]);
        } catch (\Exception $ex) {
            // Trả về lỗi nếu có ngoại lệ
            return response()->json([
                'success' => false,
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function importStudentList($id)
    {
        $courseClass = Courseclass::find($id);
        if (!$courseClass) {
            abort(404, 'Lớp học phần không tồn tại.');
        }
        return view('admin.courseclass.import-student-list', compact('courseClass'));
    }

    public function storeImportedStudentList(Request $request)
    {
        $file = $request->file('file');
        $courseClassId = $request->input('Id');

        if (!$file || !$file->isValid()) {
            return response()->json(['success' => false, 'error' => 'File không hợp lệ!']);
        }

        $courseClass = Courseclass::find($courseClassId);
        if (!$courseClass) {
            return response()->json(['success' => false, 'error' => 'Lớp học phần không tồn tại!']);
        }

        try {
            $stream = $file->getRealPath();
            $spreadsheet = IOFactory::load($stream);
            $worksheet = $spreadsheet->getActiveSheet();
            $rowCount = $worksheet->getHighestRow();

            $studentsToAdd = [];
            $errors = [];
            $failCount = 0;

            for ($row = 5; $row <= $rowCount; $row++) {
                try {
                    $studentId = trim($worksheet->getCell("B{$row}")->getValue());

                    if (empty($studentId)) {
                        break;
                    }

                    // Kiểm tra tồn tại của sinh viên
                    $studentExists = Student::where('Id', $studentId)->exists();
                    if (!$studentExists) {
                        $failCount++;
                        $errors[] = "{$studentId} không tồn tại.";
                        continue;
                    }

                    // Kiểm tra sinh viên đã tồn tại trong lớp học phần
                    $studentInClass = StudentJoinClass::where('StudentId', $studentId)
                        ->where('CourseClassId', $courseClassId)
                        ->exists();
                    if ($studentInClass) {
                        $failCount++;
                        $errors[] = "{$studentId} đã tồn tại trong lớp học phần.";
                        continue;
                    }

                    $studentsToAdd[] = [
                        'CourseClassId' => $courseClassId,
                        'StudentId' => $studentId,
                        'DateJoin' => now(),
                    ];
                } catch (\Exception $e) {
                    $errors[] = "{$studentId} có lỗi: " . $e->getMessage();
                    continue;
                }
            }

            Studentjoinclass::insert($studentsToAdd);
            $courseClass->CurrentQuantity += count($studentsToAdd);
            $courseClass->save();

            $message = $failCount > 0 ? "Đã xảy ra một số lỗi:\n" . implode("\n", $errors) : null;
            return response()->json([
                'success' => true,
                'redirect' => route('admin.courseclass.index'),
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }   

    public function downloadImportStudentList(){
        $filePath = public_path('Data/MauThemSV_LHP.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại.');
        }

        return response()->download($filePath, 'MauThemSV_LHP.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }















    // CURRICULUM 

    public function curriculum_index()
    {
        $studyYears = Studyyear::all();
        $majors = Major::all();
        return view('admin.curriculum.index', compact('studyYears', 'majors'));
    }

    public function curriculum_getList(Request $request)
    {
        $query = Curriculum::query();

        if ($request['yearId']) {
            $query->where('StudyYearId', $request->input('yearId'));
        }

        if ($request['majorId']) {
            $query->where('MajorId', $request->input('majorId'));
        }

        $list = $query->get();
        return view('admin.curriculum._list', [
            'curricula' => $list,
        ]);
    }

    public function curriculum_create()
    {
        $studyYears = StudyYear::all();
        $majors = Major::all();
        return view('admin.curriculum.create', compact('studyYears', 'majors'));
    }

    public function curriculum_store(Request $request)
    {
        $data = [
            'Code' => $request['Code'],
            'Name' => $request['Name'],
            'StudyYearId' => $request['StudyYearId'],
            'MajorId' => $request['MajorId']
        ];
        $data['CreatedDate'] = now();

        try {
            $curriculum = Curriculum::create($data);
            return redirect()->route('admin.curriculum.editCourses', $curriculum->Id);
        } catch (\Exception $e) {
            $studyYears = StudyYear::all();
            $majors = Major::all();
            return view('admin.curriculum.create', compact('studyYears', 'majors'))->with('alert', $e->getMessage());
        }
    }

    public function curriculum_editCourses($id)
    {
        $curriculum = Curriculum::with(['courses', 'studyyear'])->find($id);

        if (!$curriculum) {
            abort(404);
        }

        $listYear = Studyyeardetail::whereBetween('Id', [$curriculum->studyyear->StartYearId, $curriculum->studyyear->EndYearId])->get();
        $listSubject = Subject::all();
        $courseTypes = Coursetype::all();

        return view('admin.curriculum.edit_courses', compact('curriculum', 'listYear', 'listSubject', 'courseTypes'));
    }

    public function curriculum_updateCourses(Request $request, $curriculumId)
    {
        $curriculum = Curriculum::with('courses')->find($curriculumId);

        if (!$curriculum) {
            return response()->json(['success' => false, 'message' => 'Curriculum not found.']);
        }

        $courses = $request->input('courses', []);
        $existingCourses = $curriculum->courses;

        $newCourses = collect($courses)->where('Id', 0);
        $updatedCourses = collect($courses)->where('Id', '!=', 0);
        $deletedCourses = $existingCourses->whereNotIn('Id', collect($courses)->pluck('Id'));

        foreach ($newCourses as $course) {
            $course['CurriculumId'] = $curriculumId;
            Course::create($course);
        }

        foreach ($updatedCourses as $courseData) {
            $course = $existingCourses->where('Id', $courseData['Id'])->first();
            if ($course) {
                $course->update($courseData);
            }
        }

        foreach ($deletedCourses as $course) {
            $course->delete();
        }

        return response()->json(['success' => true, 'redirect' => route('admin.curriculum.details', $curriculumId)]);
    }

    public function curriculum_details($id)
    {
        $curriculum = Curriculum::with(['courses', 'studyYear'])->find($id);

        if (!$curriculum) {
            abort(404);
        }

        $listYear = Studyyeardetail::whereBetween('Id', [$curriculum->studyyear->StartYearId, $curriculum->studyyear->EndYearId])->get();
        return view('admin.curriculum.details', compact('curriculum', 'listYear'));
    }

    

    public function curriculum_destroy($id)
    {
        try {
            $model = Curriculum::find($id);

            if ($model) {
                $model->delete();

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Không tìm thấy ctdt']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    public function getSubject($id){
        $subjects = Subject::find($id);
        $model = [
            'id' => $subjects->Id,
            'name' => $subjects->Name,
            'code' => $subjects->Code,
            'defaultCredits' => $subjects->DefaultCredits,
            'defaultLesson' => $subjects->DefaultLesson,
            'typeOptions' => '<option value="1" selected="">Bắt buộc</option><option value="2">Tự chọn</option> '
        ];
        return response()->json($model);
    }



















    public function subject_index(Request $request)
    {
        $deptId = $request->input('deptId'); // Lấy giá trị deptId từ query string
        

        $query = Subject::query();

        if (!empty($deptId)) {
            $query->where('DeptId', $deptId);
        }

        $departments = Department::all();

        return view('admin.subject.index', [
            'subjects' => $query->get(),
            'departments' => $departments,
            'selectedDeptId' => $deptId,
        ]);
    }

    public function subject_create()
    {
        $departments = Department::all();


        return view('admin.subject.create', [
            'departments' => $departments,
        ]);
    }
    public function subject_destroy($id)
    {
        try {
            $model = Subject::find($id);

            if ($model) {
                $model->delete();

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Không tìm thấy ngành học']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function subject_createpost(Request $request)
    {
        try {
            // Tạo mới giảng viên
            $model = [
                'Id' => request('id'),
                'DeptId' => request('dept_id'),
                'Name' => request('name'),
                'Code' => request('code'),
                'DefaultCredits' => request('default_credits'),
                'DefaultLesson' => request('default_lesson'),

            ];

            $major = Subject::create($model);

        return response()->json([
            'success' => true,
            'redirect' => route('admin.subject.index'),
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function subject_edit($id)
    {
        $departments = Department::all();
        $major = Subject::find($id);

        // Nếu không tìm thấy, trả về lỗi 404
        if (!$major) {
            abort(404, 'Subject not found');
        }

        // Trả về view chỉnh sửa với dữ liệu
        return view('admin.subject.edit', [
            'subject' => $major,
            'departments' => $departments,
        ]);
    }
    public function subject_update(Request $request, $id)
    {
        try {
            // Lấy bản ghi cần sửa
            $major = Subject::find($id);

            // Nếu không tìm thấy, trả về lỗi 404
            if (!$major) {
                abort(404, 'Subject not found');
            }

            // Validate dữ liệu đầu vào
            $model = [
                'Name' => $request['name'],
                'Code' => $request['code'],
                'DeptId' => $request['dept_id'],
                'DefaultCredits' => $request['default_credits'],
                'DefaultLesson' => $request['default_lesson'],
            ];
            

            // Cập nhật dữ liệu
            $major->update($model);

            // Thông báo thành công
            return redirect()->route('admin.subject.index')->with('success', 'Sửa thành công!');
        } catch (\Exception $e) {
            // Nếu lỗi, hiển thị thông báo lỗi
            return back()->withInput()->with('alert', $e->getMessage());
        }
    }










    public function major_index(Request $request)
    {
        $deptId = $request->input('deptId'); // Lấy giá trị deptId từ query string
        $pageSize = 10; // Kích thước mỗi trang

        $query = Major::query();

        if (!empty($deptId)) {
            $query->where('DeptId', $deptId);
        }

        $totalLecturers = $query->count();

        $lecturers = $query->paginate($pageSize);

        $departments = Department::all();

        return view('admin.major.index', [
            'majors' => $lecturers,
            'currentPage' => $lecturers->currentPage(),
            'totalPages' => $lecturers->lastPage(),
            'departments' => $departments,
            'selectedDeptId' => $deptId,
        ]);
    }

    public function major_create()
    {
        $departments = Department::all();


        return view('admin.major.create', [
            'departments' => $departments,
        ]);
    }
    public function major_destroy($id)
    {
        try {
            $model = Major::find($id);

            if ($model) {
                $model->delete();

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Không tìm thấy ngành học']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    public function major_createpost(Request $request)
    {
        try {
            // Tạo mới giảng viên
            $model = [
                'Id' => request('id'),
                'DeptId' => request('dept_id'),
                'Name' => request('name'),
                'Code' => request('code'),
            ];

            $major = Major::create($model);

        return response()->json([
            'success' => true,
            'redirect' => route('admin.major.index'),
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function major_edit($id)
    {
        $departments = Department::all();
        $major = Major::find($id);

        // Nếu không tìm thấy, trả về lỗi 404
        if (!$major) {
            abort(404, 'major not found');
        }

        // Trả về view chỉnh sửa với dữ liệu
        return view('admin.major.edit', [
            'major' => $major,
            'departments' => $departments,
        ]);
    }
    public function major_update(Request $request, $id)
    {
        try {
            // Lấy bản ghi cần sửa
            $major = Major::find($id);

            // Nếu không tìm thấy, trả về lỗi 404
            if (!$major) {
                abort(404, 'major not found');
            }

            // Validate dữ liệu đầu vào
            $model = [
                'Name' => $request['name'],
                'Code' => $request['code'],
                'DeptId' => $request['dept_id'],
            ];
            

            // Cập nhật dữ liệu
            $major->update($model);

            // Thông báo thành công
            return redirect()->route('admin.major.index')->with('success', 'Sửa thành công!');
        } catch (\Exception $e) {
            // Nếu lỗi, hiển thị thông báo lỗi
            return back()->withInput()->with('alert', $e->getMessage());
        }
    }












    public function lecturer_index(Request $request)
    {
        $deptId = $request->input('deptId'); // Lấy giá trị deptId từ query string
        $pageSize = 10; // Kích thước mỗi trang

        $query = Lecturer::query();

        if (!empty($deptId)) {
            $query->where('DeptId', $deptId);
        }

        $totalLecturers = $query->count();

        $lecturers = $query->paginate($pageSize);

        $departments = Department::all();

        return view('admin.lecturer.index', [
            'lecturers' => $lecturers,
            'currentPage' => $lecturers->currentPage(),
            'totalPages' => $lecturers->lastPage(),
            'departments' => $departments,
            'selectedDeptId' => $deptId,
        ]);
    }


    public function lecturer_destroy($id)
    {
        try {
            // Tìm giảng viên theo ID
            $lecturer = Lecturer::find($id);

            if ($lecturer) {
                // Xóa giảng viên
                $lecturer->delete();

                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false, 'error' => 'Không tìm thấy giảng viên']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function lecturer_create()
    {
        $departments = Department::all();

        $newId = Lecturer::max('id') + 1;

        $nationFilePath = public_path('Data\nation.json');
        $religionFilePath = public_path('Data\religion.json');
        $nationList = json_decode(file_get_contents($nationFilePath), true);
        $religionList = json_decode(file_get_contents($religionFilePath), true);
        $nationList = $nationList ?? [];
        $religionList = $religionList ?? [];


        return view('admin.lecturer.create', [
            'departments' => $departments,
            'newId' => $newId,
            'nationList' => $nationList,
            'religionList' => $religionList,
        ]);
    }



    public function lecturer_createpost(Request $request)
    {
        try {
            // Tạo mới giảng viên
            $model = [
                'Id' => request('id'),
                'DeptId' => request('dept_id'),
                'FullName' => request('last_name') . ' ' . request('first_name'),
                'Email' => request('email'),
                'Sex' => request('sex'),
                'Nation' => request('nation'),
                'NationId' => request('nation_id'),
                'Religion' => request('religion'),
                'DayOfBirth' => request('day_of_birth'),
                'BirthPlace' => request('birth_place'),
                'StreetAddress' => request('street_address'),
                'PhoneNo' => request('phone_no'),
                'HiredDate' => now(),
            ];

            $lecturer = Lecturer::create($model);

        return response()->json([
            'success' => true,
            'redirect' => route('admin.lecturer.index'),
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }


    public function downloadListLecturer(Request $request)
    {
        $deptId = $request->input('dept_id');
        $query = Lecturer::query();
        $department = null;

        // Lọc theo khoa nếu được cung cấp
        if (!empty($deptId)) {
            $query->where('DeptId', $deptId);
            $department = Department::find($deptId);
        }

        $lecturers = $query->get();

        // Tạo Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Tiêu đề
        $sheet->mergeCells('A1:N1');
        $sheet->setCellValue('A1', 'DANH SÁCH GIẢNG VIÊN');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 2;

        if ($deptId) {
            $sheet->setCellValue("A{$row}", 'Khoa');
            $sheet->setCellValue("B{$row}", $department->Name ?? 'Không xác định');
            $row++;
        }

        // Header
        $headers = [
            'STT', 'Mã số giảng viên', 'Họ lót', 'Tên', 'Ngày sinh', 'Email',
            'SĐT', 'Giới tính', 'Mã khoa', 'CCCD', 'Nơi sinh', 'Địa chỉ',
            'Dân tộc', 'Tôn giáo'
        ];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$col}{$row}", $header);
            $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
            $col++;
        }

        $row++;

        // Dữ liệu giảng viên
        $index = 1;
        foreach ($lecturers as $lecturer) {
            $nameParts = explode(' ', trim($lecturer->FullName));
            $ho = implode(' ', array_slice($nameParts, 0, -1));
            $ten = end($nameParts);

            $sheet->setCellValue("A{$row}", $index++);
            $sheet->setCellValue("B{$row}", $lecturer->Id);
            $sheet->setCellValue("C{$row}", $ho);
            $sheet->setCellValue("D{$row}", $ten);
            $sheet->setCellValue("E{$row}", $lecturer->DayOfBirth ? $lecturer->DayOfBirth->format('Y-m-d') : '');
            $sheet->setCellValue("F{$row}", $lecturer->Email);
            $sheet->setCellValue("G{$row}", $lecturer->PhoneNo);
            $sheet->setCellValue("H{$row}", $lecturer->Sex);
            $sheet->setCellValue("I{$row}", $lecturer->Dept->Code ?? '');
            $sheet->setCellValue("J{$row}", $lecturer->NationId);
            $sheet->setCellValue("K{$row}", $lecturer->BirthPlace);
            $sheet->setCellValue("L{$row}", "{$lecturer->StreetAddress},". ($lecturer->WardCodeNavigation->Name ?? "") .",". ($lecturer->DistrictCodeNavigation->Name ?? '')."," .($lecturer->ProvinceCodeNavigation->Name ?? '')."\"");
            $sheet->setCellValue("M{$row}", $lecturer->Nation);
            $sheet->setCellValue("N{$row}", $lecturer->Religion);
            $row++;
        }

        // Căn chỉnh cột
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tạo file Excel
        $fileName = 'DanhSachGiangVien.xlsx';
        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$fileName}\"");

        return $response;
    }





    public function department_index(Request $request)
    {
        // Nhận tham số trang từ request hoặc mặc định là 1
        $page = $request->input('page', 1);
        $pageSize = 10;

        // Truy vấn dữ liệu từ bảng Departments
        $query = Department::query();

        $total = $query->count(); // Tổng số bản ghi

        $departments = $query
            ->skip(($page - 1) * $pageSize) // Bỏ qua các bản ghi của trang trước
            ->take($pageSize)              // Lấy số lượng bản ghi cho trang hiện tại
            ->get();                       // Lấy dữ liệu

        // Chuẩn bị dữ liệu trả về cho view
        $model = [
            'departments' => $departments,
            'currentPage' => $page,
            'totalPages' => ceil($total / $pageSize),
            'pageSize' => $pageSize,    
        ];

        // Trả về view với dữ liệu
        return view('admin.departments.index', $model);
    }

    public function department_create()
    {
        return view('admin.departments.create');
    }

    public function department_createpost(Request $request)
    {
        try {

            // Tạo mới Department
            $department = new Department();
            $department->Code = $request['code'];
            $department->Name = $request['name'];
            $department->DateFound = now(); 
            $department->save();

            

            return redirect()->route('admin.department.index')
                ->with('success', 'Thêm mới thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('alert', $e->getMessage());
        }
    }

    

    public function department_delete($id)
    {
        if (!$id) {
            abort(404, 'ID không được truyền vào');
        }
    
        $department = Department::find($id);

        // Nếu không tìm thấy, trả về lỗi 404
        if (!$department) {
            abort(404, 'Department not found');
        }

        // Trả về view xác nhận xóa
        return view('admin.departments.delete', compact('department'));
    }

    public function department_destroy($id)
    {
        // Tìm department theo ID
        $department = Department::find($id);

        // Nếu không tìm thấy, trả về lỗi 404
        if (!$department) {
            abort(404, 'Department not found');
        }

        // Xóa department
        $department->delete();

        // Chuyển hướng về danh sách và thông báo thành công
        return redirect()->route('admin.department.index')->with('success', 'Khoa đã được xóa thành công!');
    }


    public function department_edit($id)
    {
        // Tìm bản ghi theo ID
        $department = Department::find($id);

        // Nếu không tìm thấy, trả về lỗi 404
        if (!$department) {
            abort(404, 'Department not found');
        }

        // Trả về view chỉnh sửa với dữ liệu
        return view('admin.departments.edit', compact('department'));
    }
    public function department_update(Request $request, $id)
    {
        try {
            // Lấy bản ghi cần sửa
            $department = Department::find($id);

            // Nếu không tìm thấy, trả về lỗi 404
            if (!$department) {
                abort(404, 'Department not found');
            }

            // Validate dữ liệu đầu vào
            $model = [
                'Name' => $request['name'],
                'Code' => $request['code'],
                'DateFound' => $request['date_found'],
            ];
            

            // Cập nhật dữ liệu
            $department->update($model);

            // Thông báo thành công
            return redirect()->route('admin.department.index')->with('success', 'Sửa thành công!');
        } catch (\Exception $e) {
            // Nếu lỗi, hiển thị thông báo lỗi
            return back()->withInput()->with('alert', $e->getMessage());
        }
    }


}
