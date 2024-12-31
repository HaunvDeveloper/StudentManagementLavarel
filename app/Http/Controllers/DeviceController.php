<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Lesson;
use App\Models\Student;
use App\Models\Studentjoinlesson;
use App\Services\CheckFacePositionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DeviceController extends Controller
{
    public function index()
    {
        $userId = session('Id');
        $device = Device::where('UserId', $userId)->first();
        return view('device.index', compact('device'));
    }

    public function checkActivate()
    {
        $userId = session('Id');
        $device = Device::where('UserId', $userId)->first();
        return response()->json(['success' => $device?->IsActive]);
    }

    public function checkFacePosition(Request $request)
    {
        $data = $request->all();
        
        if (!isset($data['image'])) {
            return response()->json(['error' => true]);
        }

        $image = $data['image'];
        $result = CheckFacePositionService::checkFacePosition($image);
        return response()->json(['success' => trim($result) === 'true']);
    }

    public function verify(Request $request)
    {
        $data = $request->all();

        if (!isset($data['image'])) {
            return response()->json(['success' => false, 'error' => 'Image is null']);
        }

        try {
            $image = $data['image'];
            $base64Data = str_replace('data:image/png;base64,', '', $image);
            $imageBytes = base64_decode($base64Data);

            // Create Temp Directory
            $tempFolder = storage_path('app/public/captured_faces/attendance');
            if (!file_exists($tempFolder)) {
                mkdir($tempFolder, 0777, true);
            }

            // Save Temp Image
            $tempImagePath = $tempFolder . '/tempAttendance.png';
            file_put_contents($tempImagePath, $imageBytes);

            // Execute Python Script
            $scriptPath = base_path('Scripts/Python/mark_attendance.py');
            $pythonPath = 'python';

            $process = new Process([$pythonPath, $scriptPath]);
            $process->run();

            if (!$process->isSuccessful()) {
                return response()->json(['success' => false, 'error' => $process->getErrorOutput()]);
            }

            $output = trim($process->getOutput());
            if ($output === 'None') {
                return response()->json(['success' => false, 'error' => 'No face recognized']);
            }
            dd($output);
            $student = Student::find($output);
            if (!$student) {
                return response()->json(['success' => false, 'error' => 'Student not found', 'output' => $output]);
            }

            $userId = session('Id');
            $device = Device::where('UserId', $userId)->first();
            $lesson = $device?->lesson;

            if (!$lesson) {
                return response()->json(['success' => false, 'error' => 'Lesson is null']);
            }

            $exist = Studentjoinlesson::where('StudentId', $student->id)
                ->where('LessonId', $lesson->id)
                ->first();

            if ($exist) {
                return response()->json(['success' => false, 'error' => 'Already attended']);
            }

            $now = now();
            $attendTime = $lesson->lessoninfo->StartTime;
            $status = $now->format('H:i:s') > $attendTime ? 'Đi trễ' : 'Có mặt';

            Studentjoinlesson::insert([
                'StudentId' => $student->id,
                'LessonId' => $lesson->id,
                'JoinTime' => $now,
                'Status' => $status,
            ]);

            return response()->json(['success' => true, 'id' => $student->Id, 'name' => $student->FullName, 'status' => $status]);
        } catch (\Exception $ex) {
            Log::error($ex);
            return response()->json(['success' => false, 'error' => $ex->getMessage()]);
        }
    }
}
