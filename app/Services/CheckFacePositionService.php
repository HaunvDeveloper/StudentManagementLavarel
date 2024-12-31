<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CheckFacePositionService
{
    public static function checkFacePosition($imageBase64)
    {
        try {
            if (empty($imageBase64)) {
                return "Error: No image data provided.";
            }

            // Decode the base64 image and save it to a temporary directory
            $base64Data = str_replace('data:image/png;base64,', '', $imageBase64);
            $imageData = base64_decode($base64Data);

            // Ensure the TempImages directory exists
            $tempFolder = public_path('TempImages');
            if (!file_exists($tempFolder)) {
                mkdir($tempFolder, 0777, true);
            }

            // Create a temporary file for the image
            $tempImagePath = $tempFolder . DIRECTORY_SEPARATOR . 'temp.png';
            file_put_contents($tempImagePath, $imageData);

            // Define the Python script path
            $scriptPath = base_path('Scripts/Python/check.py');

            // Run the Python script
            $result = CheckFacePositionService::runPythonScript($scriptPath, $tempImagePath);

            // Return the result
            return $result;
        } catch (\Exception $ex) {
            return "Error: " . $ex->getMessage();
        }
    }

    private static function runPythonScript($scriptPath, $imagePath)
    {
        try {
            $pythonPath = 'python'; // Đường dẫn Python, có thể cấu hình trong .env
            $command = escapeshellcmd("$pythonPath $scriptPath $imagePath");

            $output = [];
            $returnVar = 0;

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                return "Error: Command failed with return code $returnVar. Output: " . implode("\n", $output);
            }

            return implode("\n", $output);
        } catch (\Exception $ex) {
            return "Error: " . $ex->getMessage();
        }
    }

}
