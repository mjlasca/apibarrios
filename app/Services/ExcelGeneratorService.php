<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelGeneratorService
{
    private string $pythonBinary = 'python3';

    public function generateDownload(
        Collection $data,
        array $headers,
        string $filename,
        array $columnKeys,
    ): StreamedResponse {
        $tempDir = sys_get_temp_dir();
        $jsonPath = $tempDir . '/' . uniqid('report_', true) . '.json';
        $xlsxPath = $tempDir . '/' . uniqid('report_', true) . '.xlsx';
        $scriptPath = __DIR__ . '/../../resources/scripts/generate_xlsx.py';

        try {
            $payload = [
                'headers' => $headers,
                'rows' => $data->map(fn (array $row) => array_map(fn ($v) => $v ?? '', array_values(collect($row)->only($columnKeys)->toArray())))->values()->toArray(),
            ];

            File::put($jsonPath, json_encode($payload, JSON_UNESCAPED_UNICODE));

            $command = sprintf(
                '%s %s %s %s 2>&1',
                $this->pythonBinary,
                escapeshellarg($scriptPath),
                escapeshellarg($jsonPath),
                escapeshellarg($xlsxPath),
            );

            $output = [];
            $exitCode = 0;
            exec($command, $output, $exitCode);

            if ($exitCode !== 0 || ! File::exists($xlsxPath)) {
                throw new \RuntimeException(
                    'Excel generation failed: ' . implode("\n", $output)
                );
            }

            $content = File::get($xlsxPath);

            return response()->streamDownload(function () use ($content) {
                echo $content;
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } finally {
            @unlink($jsonPath);
            @unlink($xlsxPath);
        }
    }
}
