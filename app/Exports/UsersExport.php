<?php

namespace App\Exports;

use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;


class UsersExport
{
    protected $users;
    protected  $type;

    public function __construct($users, $type)
    {
        $this->users = $users;
        $this->type = $type;
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $headers = [
            'Naam', 'Tussenvoegsel', 'Achternaam', 'Email', 'Telefoonnummer', 'Rollen',
            'Geslacht', 'Geboortedatum', 'AVG Toestemming', 'Straat', 'Plaats', 'Postcode', 'Datum sinds lidmaatschap'
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
        foreach ($this->users as $userId) {
            $user = User::find($userId);
            if ($user) {
                $roles = '';
                foreach ($user->roles as $role) {
                    $roles .= $role->role . ', ';
                }
                $roles = rtrim($roles, ', ');

                $avg = 'Nee';

                if($user->avg === 1) {
                    $avg = 'Ja';
                }

                $rowData = [
                    $user->name, $user->infix, $user->last_name, $user->email, $user->phone, $roles,
                    $user->sex, $user->birth_date, $avg, $user->street, $user->city,
                    $user->postal_code, $user->member_data
                ];
                $sheet->fromArray([$rowData], NULL, 'A' . $row);

                $row++;
            }
        }

        // Auto size columns after populating data
        foreach(range('A', $sheet->getHighestColumn()) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Add auto filter
        $lastColumn = $sheet->getHighestColumn();
        $sheet->setAutoFilter('A1:' . $lastColumn . '1');

        // Save the Excel file
        $writer = new Xlsx($spreadsheet);

        $filename = 'mhg-'.$this->type.'-export-' . date('d-m-Y') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
