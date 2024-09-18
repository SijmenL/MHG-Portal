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
        // Ensure $users is a collection of User models
        $this->users = $users;
        $this->activityName = $activityName;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $headers = [
            'Naam', 'Tussenvoegsel', 'Achternaam', 'Email', 'Aanwezig'
        ];
        $sheet->fromArray([$headers], NULL, 'A1');

        // Apply header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0092df'],
            ],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;

        foreach ($this->users as $user) {



            // Map presence to human-readable format
            $presenceStatus = 'Niet gemeld';
            switch ($user["presence"]) {
                case 'present':
                    $presenceStatus = 'Aangemeld';
                    break;
                case 'absent':
                    $presenceStatus = 'Afgemeld';
                    break;
            }

            $rowData = [
                $user["name"],
                $user["infix"],
                $user["last_name"],
                $user["email"],
                $presenceStatus,
            ];
            $sheet->fromArray([$rowData], NULL, 'A' . $row);

            // Apply cell styling based on presence status
            $cell = 'E' . $row;
            if ($presenceStatus === 'Aangemeld') {
                $sheet->getStyle($cell)->getFill()->applyFromArray([
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'c6dbd2'] // Green for present
                ]);
            } elseif ($presenceStatus === 'Afgemeld') {
                $sheet->getStyle($cell)->getFill()->applyFromArray([
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'f2cecc'] // Red for absent
                ]);
            }

            $row++;
        }

        // Auto size columns after populating data
        foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Add auto filter
        $lastColumn = $sheet->getHighestColumn();
        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        // Save the Excel file
        $writer = new Xlsx($spreadsheet);

        $filename = $this->activityName.' aanwezigheid ' . date('d-m-Y') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
