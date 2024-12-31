<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Student;
use App\Services\CheckFacePositionService;

class FaceRecognitionController extends Controller
{
    public function createFaceIdentify()
    {
        return view('face.create');
    }

    public function checkFacePosition(Request $request)
    {
        try {


            if (!$request->has('image')) {
                return response()->json(['error' => true], 400);
            }

            $imageBase64 = $request->input('image');
            $result = CheckFacePositionService::checkFacePosition($imageBase64);
            $isCentered = trim($result) === 'true';
            return response()->json(['isCentered' => $isCentered]);

        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 500);
        }
    }

    public function registerUser(Request $request)
    {
        $images = $request->input('images');
        if (!$images || count($images) < 5) {
            return response()->json(['message' => 'Insufficient images for registration.'], 400);
        }

        $userId = session('Id'); // Assuming Laravel authentication is used
        $student = Student::where('UserId', $userId)->first();

        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }

        $tempFolder = public_path("TempImages/{$student->Id}");
        if (!file_exists($tempFolder)) {
            mkdir($tempFolder, 0777, true);
        }

        foreach ($images as $index => $image) {
            $base64Data = str_replace('data:image/png;base64,', '', $image);
            $imageData = base64_decode($base64Data);
            $imagePath = $tempFolder . "/image_" . ($index + 1) . ".png";
            file_put_contents($imagePath, $imageData);
        }

        $scriptPath = base_path('Scripts/Python/register.py');
        $result = $this->runPythonScript($scriptPath, $student->Id);

        if ($result['success'] && strpos($result['output'], 'successCode:ABCDE') !== false) {
            $student->FaceData = 'Success';
            $student->save();
            return response()->json(['success' => true, 'redirect' => route('student.info')]);
        }

        return response()->json([
            'message' => 'Python script returned an unsuccessful result.',
            'details' => $result['error'] ?? 'Unknown error.',
        ], 500);
    }

    private function runPythonScript($scriptPath, $args)
    {
        try {
            // Xây dựng lệnh chạy Python
            $pythonPath = 'python'; // Đường dẫn Python, có thể cấu hình trong .env
            $command = escapeshellcmd("$pythonPath $scriptPath $args");

            // Ghi nhận output và error
            $output = [];
            $returnVar = 0;

            // Thực thi lệnh
            exec($command, $output, $returnVar);

            // Kiểm tra kết quả
            if ($returnVar !== 0) {
                return [
                    'success' => false,
                    'error' => "Command failed with return code $returnVar. Output: " . implode("\n", $output)
                ];
            }
            // Trả về kết quả thành công
            return [
                'success' => true,
                'output' => implode("\n", $output)
            ];
        } catch (\Exception $ex) {
            return [
                'success' => false,
                'error' => $ex->getMessage()
            ];
        }
    }

}
