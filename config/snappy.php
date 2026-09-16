<?php

$winPdfBinary   = public_path('wkhtmltopdf/bin/wkhtmltopdf.exe');
$winImgBinary   = public_path('wkhtmltopdf/bin/wkhtmltoimage.exe');
$envPdfBinary   = env('WKHTMLTOPDF_BINARY');
$envImgBinary   = env('WKHTMLTOIMAGE_BINARY');

if (str_contains(PHP_OS_FAMILY, 'Windows')) {
    if (file_exists($winPdfBinary)) {
        $pdfBinary = $winPdfBinary;
    } elseif ($envPdfBinary && file_exists($envPdfBinary)) {
        $pdfBinary = $envPdfBinary;
    } else {
        $pdfBinary = base_path('bin/wkhtmltopdf.exe');
    }
} else {
    if ($envPdfBinary && file_exists($envPdfBinary)) {
        $pdfBinary = $envPdfBinary;
    } else {
        $pdfBinary = base_path('bin/wkhtmltopdf');
    }
}

if (str_contains(PHP_OS_FAMILY, 'Windows')) {
    if (file_exists($winImgBinary)) {
        $imgBinary = $winImgBinary;
    } elseif ($envImgBinary && file_exists($envImgBinary)) {
        $imgBinary = $envImgBinary;
    } else {
        $imgBinary = base_path('bin/wkhtmltoimage.exe');
    }
} else {
    if ($envImgBinary && file_exists($envImgBinary)) {
        $imgBinary = $envImgBinary;
    } else {
        $imgBinary = base_path('bin/wkhtmltoimage');
    }
}

return [

    /*
    |--------------------------------------------------------------------------
    | Snappy PDF / Image Configuration
    |--------------------------------------------------------------------------
    */

    'pdf' => [
        'enabled' => true,
        'binary'  => $pdfBinary,
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

    'image' => [
        'enabled' => true,
        'binary'  => $imgBinary,
        'timeout' => false,
        'options' => [],
        'env'     => [],
    ],

];

