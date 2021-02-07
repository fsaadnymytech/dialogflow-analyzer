<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelGenerator{

	public function getExcelFile($dataArray, $columnsNames, $fileName){
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		#Primero, se agregan los nombres de las columnas:
		$styleArray = array(
		    'font'  => array(
		        'bold'  => true,
		        'color' => array('rgb' => 'F97602'),
		    ),
		    'borders' => array(
            'outline' => array(
            	'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'A9D9E9')
            )
		);
		$excel_columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P');
		for($i=0; $i<count($columnsNames); $i++){		
			$sheet->setCellValue($excel_columns[$i]."1", $columnsNames[$i]);
			#Algunos estilos:
			$sheet->getColumnDimension($excel_columns[$i])->setWidth(30);
			$sheet->getStyle($excel_columns[$i]."1")->applyFromArray($styleArray);
		}

		#Ahora el contenido de cada columna:
		$styleArray = array(
		    'borders' => array(
            'outline' => array(
            	'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => array('rgb' => '000000')
            )),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'A9D9E9')
            )
		);
		$i=2;
		foreach($dataArray as $usersaysData){
			$j=0;
			foreach($usersaysData as $fieldValue){
				$sheet->setCellValue($excel_columns[$j]."".$i, $fieldValue);
				$sheet->getStyle($excel_columns[$j]."".$i)->applyFromArray($styleArray);
				$j=$j+1;
			}
			$i =$i+1;
		}

		#Para que se descargue el archivo, se tienen que agregar los headers:
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename='.$fileName.'');
		header('Cache-Control:max-age=0');
		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
	}
}