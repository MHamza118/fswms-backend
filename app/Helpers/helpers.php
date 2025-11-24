<?php
use Picqer\Barcode\BarcodeGeneratorPNG;

if (!function_exists('successResponse')) {
    function successResponse($message = 'Success', $data = [], $status = 200)
    {
        return response()->json([
            'status' => true,
            'status_code' => $status,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}

if (!function_exists('errorResponse')) {
    function errorResponse($message = 'Something went wrong', $errors = [], $status = 400)
    {
        return response()->json([
            'status' => false,
            'status_code' => $status,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}



if (!function_exists('generateBarcode')) {
    function generateBarcode($barcode)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeImage = base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128));

        return 'data:image/png;base64,' . $barcodeImage;
    }
}

