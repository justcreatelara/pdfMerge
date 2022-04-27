<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use ReflectionClass;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class PDFController extends Controller
{
    public function generatePDF()
    {
        $path = storage_path('app/documents/merged.pdf');

        if(File::exists($path)) {
            File::delete($path);
        }

        $pdf = PDFMerger::init();
        $this->imageToPdf();
        $files = File::allFiles(storage_path('app/documents'));

        foreach($files as $file) {
            if ($file->getExtension() == 'pdf') {
                $pdf->addPDF($file, 'all');
            }
        }
        $pdf->merge();
        $pdf->save($path);
    }

    public function imageToPdf()
    {
        $files = File::allFiles(storage_path('app/documents'));
        foreach($files as $file) {
            if ($file->getExtension() != 'pdf') {
                $image = storage_path('app/documents/' . $file->getFilename());
                $withoutExt = preg_replace('/\.[^.\s]{3,4}$/', '', $image);
                if(defined('FPDF_VERSION')) {
                    $reflection = new ReflectionClass("FPDF");
                    $instance = $reflection->newInstanceWithoutConstructor();
                    $pdf = new $instance();
                }
                $pdf->AddPage();
                $pdf->Image($image, 10, 10, -260);
                $pdf->Output('F', $withoutExt . ".pdf");
            }
        }
    }
}
