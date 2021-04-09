<?php


namespace App\Service;


class CreateCSV
{
    public function createCSV($file_data)
    {
        $file = fopen('tasks.csv', 'w');

        foreach ($file_data as $record)
        {
            $array_temp = [];
            foreach ($record as $element)
            {
                if(!$element) {
                    $element = 0;
                }
                $array_temp[] = $element;
            }

            fputcsv($file, $array_temp);
        }

        fclose($file);
    }
}