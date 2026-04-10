<?php

namespace App\Http\Controllers;

use App\Models\CourseVideo;
use App\Services\CloudFrontUrlSigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseVideoController extends Controller
{
    protected $cloudFrontSigner;

    public function __construct(CloudFrontUrlSigner $urlSigner)
    {
        $this->cloudFrontSigner = $urlSigner;
    }



  


    public function show($videoId)
    {

        // Récupérez les chemins des vidéos auxquelles l'utilisateur a accès
        $videoPaths = [
            '/' . $videoId,

        ];

        // Générer les cookies signés
        $signedCookies = $this->cloudFrontSigner->getSignedCookie($videoPaths);

        // Définir les cookies dans la réponse
        $response = response()->view('videos.show', [
            'videoUrl' => 'https://' . config('services.cloudfront.domain') . '/' . $videoId,

            'videoId' => $videoId
        ]);
        // Ajouter chaque cookie signé à la réponse
        foreach ($signedCookies as $name => $value) {
            $response->cookie(
                $name,
                $value,
                config('services.cloudfront.url_expiration') / 60, // Minutes
                '/',
                config('services.cloudfront.domain'),
                true, // Secure (HTTPS only)
                true  // HttpOnly
            );
        }
        return $response;
    }



    public function testCloudFrontCookies()
    {
        try {
            $videoPaths = [
                'output/video5.m3u8',
            ];

            $signedCookies = $this->cloudFrontSigner->getSignedCookie($videoPaths);
            
            if (isset($signedCookies['CloudFront-Key-Pair-Id'])) {
                $signedCookies['CloudFront-Key-Pair-Id'] = config('services.cloudfront.key_pair_id');
            }
            

            $response = response()->view('test', [
                'cloudFrontDomain' => config('services.cloudfront.domain'),
                'testFiles' => $videoPaths,
                'cookiesData' => $signedCookies
            ]);

            // Définir les cookies avec SameSite=None pour le cross-domain
            foreach ($signedCookies as $name => $value) {
                
                $cookie = cookie(
                    $name,                          // name
                    $value,                         // value
                    config('services.cloudfront.url_expiration') / 60, // minutes
                    '/',                            // path
                    config('services.cloudfront.cookie_domain'),         // domain
                    true,                           // secure (HTTPS)
                    false,                          // httpOnly (false pour debug)
                    false,                          // raw
                    'none'                          // sameSite = CRITIQUE!
                );
                

                $response->withCookie($cookie);
            }
           

            return $response;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

}
