<?php

use App\Http\Controllers\CourseVideoController;
use App\Services\CloudFrontUrlSigner;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/videos/{video}', [CourseVideoController::class, 'show']);


Route::get('/player/test-cloudfront', function (CloudFrontUrlSigner $signer) {
    try {
        $signedUrl = $signer->getSignedUrl('image.png');
        
        return response()->json([
            'success' => true,
            'signed_url' => $signedUrl,
            'message' => 'URL signée générée avec succès!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});


Route::get('/player/test-cloudfront-view', [CourseVideoController::class, 'testCloudFrontCookies']);

