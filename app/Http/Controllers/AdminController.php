<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Coursetype;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\Lecturer;
use App\Models\Major;
use App\Models\Studyyear;
use App\Models\Subject;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }


    public function curriculum_index()
    {
        $studyYears = Studyyear::all();
        $majors = Major::all();
        return view('admin.curriculum.index', compact('studyYears', 'majors'));
    }

    public function curriculum_getList(Request $request)
    {
        $query = Curriculum::query();

        if ($request->has('year_id')) {
            $query->where('study_year_id', $request->input('year_id'));
        }

        if ($request->has('major_id')) {
            $query->where('major_id', $request->input('major_id'));
        }

        $list = $query->get();
        return view('admin.curriculum._list', compact('list'));
    }

    public function curriculum_create()
    {
        $studyYears = StudyYear::all();
        $majors = Major::all();
        return view('admin.curriculum.create', compact('studyYears', 'majors'));
    }

    public function curriculum_store(Request $request)
    {
        $data = $request->all();
        $data['created_date'] = now();

        try {
            $curriculum = Curriculum::create($data);
            return redirect()->route('admin.curriculum.editCourses', $curriculum->id);
        } catch (\Exception $e) {
            $studyYears = StudyYear::all();
            $majors = Major::all();
            return view('admin.curriculum.create', compact('studyYears', 'majors'))->with('alert', $e->getMessage());
        }
    }

    public function curriculum_editCourses($id)
    {
        $curriculum = Curriculum::with(['courses', 'studyYear'])->find($id);

        if (!$curriculum) {
            abort(404);
        }

        $listYear = StudyYear::whereBetween('id', [$curriculum->studyYear->start_year_id, $curriculum->studyYear->end_year_id])->get();
        $subjects = Subject::all();
        $courseTypes = Coursetype::all();

        return view('admin.curriculum.edit_courses', compact('curriculum', 'listYear', 'subjects', 'courseTypes'));
    }

    public function curriculum_updateCourses(Request $request, $curriculumId)
    {
        $curriculum = Curriculum::with('courses')->find($curriculumId);

        if (!$curriculum) {
            return response()->json(['success' => false, 'message' => 'Curriculum not found.']);
        }

        $courses = $request->input('courses', []);
        $existingCourses = $curriculum->courses;

        $newCourses = collect($courses)->where('id', 0);
        $updatedCourses = collect($courses)->where('id', '!=', 0);
        $deletedCourses = $existingCourses->whereNotIn('id', collect($courses)->pluck('id'));

        foreach ($newCourses as $course) {
            $course['curriculum_id'] = $curriculumId;
            Course::create($course);
        }

        foreach ($updatedCourses as $courseData) {
            $course = $existingCourses->where('id', $courseData['id'])->first();
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

        $listYear = StudyYear::whereBetween('id', [$curriculum->studyYear->start_year_id, $curriculum->studyYear->end_year_id])->get();

        return view('admin.curriculum.details', compact('curriculum', 'listYear'));
    }

    

    public function curriculum_deleteConfirmed($id)
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
