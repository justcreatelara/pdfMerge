<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use ReflectionClass;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class PDFController extends Controller
{
    /**
     * @throws \ReflectionException
     */
    public function generatePDF()
    {
        $path = public_path('media/merged.pdf');
        if(File::exists($path)) {
            File::delete($path);
        }

        $pdf = PDFMerger::init();
        $this->imageToPdf();
        $files = File::allFiles(public_path('media'));

        foreach($files as $file) {
            if ($file->getExtension() == 'pdf') {
                $pdf->addPDF($file, 'all');
            }
        }
        $pdf->merge();
        $pdf->save($path);
    }

    /**
     * @throws \ReflectionException
     */
    public function imageToPdf()
    {
        $files = File::allFiles(public_path('media'));
        foreach($files as $file) {
            if ($file->getExtension() != 'pdf') {
                $image = public_path('media/' . $file->getFilename());
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
