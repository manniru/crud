<?php

namespace Drupal\crud\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Google\Cloud\Firestore\FirestoreClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Link;
use Drupal\Core\Url;

use Drupal\Core\Database\Database;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use Drupal\brilliantedu\Mannir\PDF_Code39;

// use Drupal\brilliantedu\Mannir\PDF_Code39;
use Drupal\brilliantedu\Mannir\qrcode\QRcode;

// use Drupal\mannirigr\Mannir\PDF_Code39;
// use Drupal\mannirigr\Mannir\qrcode\QRcode;

class DefaultController extends ControllerBase {

  public function pdf1() {

    $pdf = new \FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','B',16);
    $pdf->Cell(0,10,'BAYERO UNIVERSITY KANO', '', '', 'C');
    $pdf->Output();
    exit();

    return [
      '#type' => 'markup',
      '#markup' => $this->t('pd1')
    ];
  }





  public function excel1() {
    // Retrieve students from the database
    $search = $_SESSION['search'];

    if ($search) {
      $students = \Drupal::database()->query("SELECT * FROM _students where gender = '$search'")->fetchAll();

    }

    else {
      $students = \Drupal::database()->query("SELECT * FROM _students")->fetchAll();

    }

    // Initialize PhpSpreadsheet objects
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set document properties
    $spreadsheet->getProperties()
                ->setCreator('IGR Platform')
                ->setLastModifiedBy('IGR Platform')
                ->setTitle('IGR Platform')
                ->setSubject('igr.ng')
                ->setDescription('IGR Platform - IGR.NG')
                ->setKeywords('IGR Platform Revenue')
                ->setCategory('IGR');

    // Define sheet name and set initial headers
    $sheetName = 'Students';
    $spreadsheet->getActiveSheet()->setTitle($sheetName);
    $headers = ['ID', 'Name', 'Email', 'Gender', 'Age']; // Adjust based on your database columns
    $sheet->fromArray($headers, NULL, 'A1');
    $sheet->getStyle('A1:E1')->getFont()->setBold(true);

    // Write data to the sheet
    $rowNum = 2;
    foreach ($students as $student) {
        $data = [$student->id, $student->name, $student->email, $student->gender, $student->age]; // Adjust based on available fields
        $sheet->fromArray($data, NULL, 'A' . $rowNum++);
    }

    // Auto-size columns
    foreach (range('A', 'E') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Generate file path
    $path = \Drupal::service('file_system')->realpath(\Drupal::config('system.file')->get('default_scheme') . "://");
    $filename = "StudentsList_" . date('Y-m-d_H-i-s') . ".xlsx"; // Dynamic filename to prevent overwriting
    $filePath = $path . "/" . $filename;

    // Save the Excel file
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save($filePath);

    // Prepare and return the response
    $response = new Response();
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filePath) . '"');
    $response->headers->set('Cache-Control', 'max-age=0');
    $response->setContent(file_get_contents($filePath));
    return $response;
}

}
