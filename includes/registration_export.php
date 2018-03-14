<?php
error_reporting(0);
ini_set('display_errors', false);
ini_set('display_startup_errors', false);
date_default_timezone_set('Europe/London');
if (PHP_SAPI == 'cli')
    die('This example should only be run from a Web Browser');
/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

require_once('../../../../wp-config.php');
define('WP_USE_THEMES', false);
require ('../../../../wp-blog-header.php');
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysqli_select_db($connection, DB_NAME);

function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}

$query = '';

if(isset($_GET['id'])) {
    $id = explode("|", $_GET['id']);
    foreach ($id as $key => $value) {
        $id[$key] = intval($value);
    }

    $query = ' WHERE id IN (' . implode(',', array_map('intval', $id)) . ')';
}

global $wpdb;
$result = $wpdb->get_results("SELECT * FROM registrations$query ORDER BY `id` DESC");

$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
    )
);

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
for($col = 'A'; $col !== 'K'; $col++) {
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($col)
        ->setAutoSize(true);
}

$objPHPExcel->getDefaultStyle()->applyFromArray($style);

cellColor('A1:J1', '4AB6E0');

$objPHPExcel->getProperties()->setCreator("YouDicover")
                             ->setLastModifiedBy("YouDicover")
                             ->setTitle("Export")
                             ->setSubject("Export")
                             ->setDescription("Exported data.")
                             ->setKeywords("export data")
                             ->setCategory("ExportedData");

                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A1', 'ID')
                            ->setCellValue('B1', 'Wohin')
                            ->setCellValue('C1', "Dauer")
                            ->setCellValue('D1', "Anzahl")
                            ->setCellValue('E1', 'Questions/Answers')
                            ->setCellValue('F1', 'Vorname')
                            ->setCellValue('G1', 'Nachname')
                            ->setCellValue('H1', 'Email')
                            ->setCellValue('I1', 'Telefonnummer')
                            ->setCellValue('J1', 'Created At');

                $xlsRow = 2;
                
                foreach ($result as $print) {                    

                    $answers = unserialize($print->answers);
                    $asks = unserialize($print->asks);
                    $Qtitle = array();
                    $Qanswer = array();
                    foreach ($asks as $key => $value) {
                        $Qtitle[$key] = (get_the_title($value)!="") ? get_the_title($value) : 'Question Title is no longer available';
                        $Qanswer[$key] = (get_post_meta( $value, '_answer'.$answers[$key], true )!="") ? get_post_meta( $value, '_answer'.$answers[$key], true ) : 'Question Answer  is no longer available';

                    }

                    $odgovori = ' | ';
                    foreach ($Qtitle as $key => $value) {
                        $odgovori .= $Qtitle[$key]." ".$Qanswer[$key]."  |  ";
                    }

                    $objPHPExcel->setActiveSheetIndex(0)->getRowDimension(''.$xlsRow)->setRowHeight(30);
                    if($xlsRow%2==0) cellColor('A'.$xlsRow.':J'.$xlsRow.'', 'F9F9F9');

                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A'.$xlsRow, "".stripslashes_deep($print->id))
                                ->setCellValue('B'.$xlsRow, "".stripslashes_deep($print->where))
                                ->setCellValue('C'.$xlsRow, "".stripslashes_deep($print->duration))
                                ->setCellValue('D'.$xlsRow, "".stripslashes_deep($print->count))
                                ->setCellValue('E'.$xlsRow, "".$odgovori)
                                ->setCellValue('F'.$xlsRow, "".stripslashes_deep($print->fname))
                                ->setCellValue('G'.$xlsRow, "".stripslashes_deep($print->lname))
                                ->setCellValue('H'.$xlsRow, "".stripslashes_deep($print->useremail))
                                ->setCellValue('I'.$xlsRow, "".stripslashes_deep($print->usertelefon))
                                ->setCellValue('J'.$xlsRow, "".stripslashes_deep($print->created));

                    $xlsRow++;
                }


$objPHPExcel->getActiveSheet()->setTitle('Export');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Export-'.date("d-m-Y").'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;