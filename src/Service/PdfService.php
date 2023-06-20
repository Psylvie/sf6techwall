<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{

    private $domPdf;

    public function __construct()
    {
        $this->domPdf = new Dompdf();
        $pdfOptions = new Options();

        $pdfOptions->set('defaultFont', 'Arial');
        $this->domPdf->setOptions($pdfOptions);
    }

    /**
     * @return Dompdf
     */
    public function showPdfFile($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream('details.pdf', [
            'Attachment' => false
        ]);

    }
    public function generateBinaryPdf($html)
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }

}