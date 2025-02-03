<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AgendaExport
{
    protected $users;
    protected $activityName;

    public function __construct($users, $activityName)
    {
        $this->users = $users;
        $this->activityName = $activityName;
    }

    public function export()
    {
        // Clear output buffer to prevent corruption
        if (ob_get_length()) {
            ob_end_clean();
        }

        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Headers
            $headers = ['Naam', 'Tussenvoegsel', 'Achternaam', 'Email', 'Aanwezig', 'Datum'];
            $sheet->fromArray([$headers], NULL, 'A1');

            // Add data
            $row = 2;
            foreach ($this->users as $user) {
                $presenceStatus = match ($user["presence"]) {
                    'present' => 'Aangemeld',
                    'absent'  => 'Afgemeld',
                    default   => 'Niet gemeld',
                };

                $presenceDate = !empty($user["date"]) ? \Carbon\Carbon::parse($user["date"])->format('d-m-Y H:i') : '-';

                $rowData = [
                    $user["name"],
                    $user["infix"] ?? '',
                    $user["last_name"] ?? '',
                    $user["email"] ?? '',
                    $presenceStatus,
                    $presenceDate,
                ];
                $sheet->fromArray([$rowData], NULL, 'A' . $row);
                $row++;
            }

            // Auto size columns
            foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Set auto filter
            $lastColumn = $sheet->getHighestColumn();
            $sheet->setAutoFilter("A1:{$lastColumn}1");

            // **Save the file locally first for inspection**
            $filename = 'presence_export_' . date('d-m-Y_H-i-s') . '.xlsx';
            $filePath = storage_path('app/public/' . $filename); // Save to storage

            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);

            // Check if the file was created and is valid
            if (file_exists($filePath)) {
                return response()->download($filePath, $filename)->deleteFileAfterSend(true);
            }

            return response()->json(['error' => 'Failed to create Excel file.'], 500);

        } catch (\Exception $e) {
            // Log any errors
            \Log::error('Excel export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to export Excel file.'], 500);
        }
    }
}
