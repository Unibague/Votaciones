<?php

namespace App\Helpers;

class CSV2Seeders
{
    public static function generateSeederArray($sourcePath, $separator, $columnTranslations)
    {

        if (!file_exists($sourcePath) || !is_readable($sourcePath)) {
            return false;
        }
        //Open CSV
        $handle = fopen($sourcePath, 'rb');

        //Get original header
        $tempHeader = fgetcsv($handle, 1000, $separator);
        //Flip to get keys
        $flippedHeader = array_flip($tempHeader);

        //Get translation position
        foreach ($columnTranslations as $key => $columnTranslationItem) {
            $columnTranslations[$key]['position'] = $flippedHeader[$columnTranslationItem['translation']] ?? null;
        }
        //Set result as header
        $header = $columnTranslations;
        $data = array();
        while (($row = fgetcsv($handle, 1000, $separator)) !== false) {
            $tempData = array();
            foreach ($header as $headerKey => $headerItem) {
                $tempData[$headerKey] = $row[$headerItem['position']] ?? null;
            }
            $data[] = $tempData;
        }
        fclose($handle);

        return $data;
    }
}
