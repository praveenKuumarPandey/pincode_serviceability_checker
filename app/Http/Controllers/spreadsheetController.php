<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class spreadsheetController extends Controller
{
    //

    public function exceltoArrayProductAttributes($productAttributeSheet)
    {

        // $inputFileName = storage_path().'\app\public\downloads\collectionTemplateDownload.xlsx';
        $inputFileName = $productAttributeSheet;

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($productAttributeSheet->getRealPath());
        // $metaFieldController = new MetafieldController();

        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        // dd($sheetData[0]);
        $dataArray = [];

        // dd($dataArray);
        // dd($sheetData);
        for ($index = 1; $index < count($sheetData); $index++) {

            $rowdata = [
                'delivery_partner' => trim($sheetData[$index][0]),
                'pincode' => trim($sheetData[$index][1]),
                'cod' => trim($sheetData[$index][2]),
                'delivery' => trim($sheetData[$index][3]),
                'tat' => trim($sheetData[$index][4]),


            ];


            $dataArray[] = $rowdata;
        }

        // dd($dataArray[0]);
        return $dataArray;
    }
}
