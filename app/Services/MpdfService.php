<?php

namespace App\Services;

use Illuminate\Http\Response;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class MpdfService
{
    /**
     * @param array{0:float|int,1:float|int} $format
     */
    public function streamView(
        string $view,
        array $data,
        string $fileName = 'document.pdf',
        array $format = [106, 353],
    ): Response {
        $tempDir = storage_path('app/mpdf-temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $config = (new ConfigVariables())->getDefaults();
        $fontConfig = (new FontVariables())->getDefaults();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => $format,
            'tempDir' => $tempDir,
            'fontDir' => array_merge($config['fontDir'], [public_path('fonts')]),
            'fontdata' => $fontConfig['fontdata'] + [
                'myanmarunicode' => ['R' => 'Pyidaungsu-Regular.ttf'],
                'myanmarzawgyi' => ['R' => 'ZawgyiOne.ttf'],
            ],
            'default_font' => 'myanmarunicode',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->useSubstitutions = true;
        $mpdf->WriteHTML(view($view, $data)->render());

        return response(
            $mpdf->Output($fileName, Destination::STRING_RETURN),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$fileName}\"",
            ]
        );
    }
}
