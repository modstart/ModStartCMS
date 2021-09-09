<?php


namespace App\Web\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;

class LicenseLogoController extends Controller
{
    public function index()
    {
        $url = Request::headerGet('referer');
        Log::info('LicenseLogo - URL => ' . $url);
        return Response::raw(file_get_contents(__DIR__ . '/license-logo.png'), [
            'Content-Type' => 'image/png',
        ]);
    }
}
