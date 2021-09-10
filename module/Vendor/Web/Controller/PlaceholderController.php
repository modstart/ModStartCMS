<?php


namespace Module\Vendor\Web\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;

class PlaceholderController extends Controller
{
    public function index($width, $height)
    {
        $width = min($width, 2000);
        $width = max($width, 10);
        $height = min($height, 2000);
        $height = max($height, 10);

        $img = Image::canvas($width, $height, '#CCC');
        $img->text($width . 'x' . $height, ($width / 2), ($height / 2), function ($font) use ($width, $height) {
            $fontSize = min($width, $height) / 10;
            $font->size($fontSize);
            $font->color('#666666');
            $font->align('center');
            $font->valign('center');
        });
        return Response::make($img->encode('png'))->header('Content-Type', 'image/png');
    }
}
