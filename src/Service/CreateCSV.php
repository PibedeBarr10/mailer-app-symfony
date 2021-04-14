<?php


namespace App\Service;


class CreateCSV
{
    public function createCSV($file_data): void
    {
        $file = fopen('tasks.csv', 'w');

        foreach ($file_data as $record) {
            fputcsv($file, $record);
        }

        fclose($file);
    }
}