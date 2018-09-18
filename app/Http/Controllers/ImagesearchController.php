<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Controller;
use Image;

class ImagesearchController extends Controller {

    public $result = array();
    public $resultSimilar = array();

    public function contact() {
        ini_set('max_execution_time', 300);
    }
    public function image_search_index(Request $Request)
    {
        $dirname = public_path().'/images/search/';
        $images = glob($dirname."*");
        return view('uploded_images')->with('images', $images);
    }
    public function image_search_add(Request $Request)
    {
        return view('upload_image');
    }


    public function image_search_store(Request $Request)
    {
        $images = array();
        $images = $Request->file('upload');
        $this->validate($Request,[
            'upload.*' => 'required|mimes:jpeg,png,jpg,tif'
        ]);
        if(empty($images)) {
            session()->flash('error', "Image required");
            return redirect()->back();
        } 
        else 
        {
            foreach ($images as $imgKey => $image) {
                $ext = $image->getClientOriginalExtension();
                $images_name = $image->getClientOriginalName();
                $image_name = substr($images_name, 0, strrpos($images_name, "."));
                $upload_path = public_path()."/images/search";
                $mimeType = $image->getClientOriginalExtension();
                $orgName ="";
                if($mimeType == "tiff" || $mimeType == "tif") {
                    $orgName = $image_name.'_'.time().".";
                    Image::make($image->getRealPath())
                        ->encode('png')
                        ->resize(1500, null, function ($constraint) {
                            $constraint->aspectRatio(); })
                        ->save($upload_path.'/'.$orgName.'png');
                }
                else {
                    $image_name = $image_name.'_'.time().'.'.$ext;
                    $image->move($upload_path, $image_name);
                }
            }
            return Redirect::to('/uploaded_images');
        }
    }
    public function store(Request $request) {
        $image_search_path = public_path('images/search');
        $image_search_url = url('/images/search');
        $image_upload_path = storage_path('app/public/');

        if ($request->hasFile(key($request->file()))) {

            if (!is_dir($image_search_path)) {
                return view('welcome');
            }
            $imageName = time() . '_imagesearch.' . $request->file(key($request->file()))->getClientOriginalExtension();


            $request->file(key($request->file()))->move($image_upload_path, $imageName);

            $imagePath = $image_upload_path . $imageName;

            chmod($imagePath, 0777);
            chown($imagePath, 'www-data');
            $result = $this->dirToArray($image_search_path, $imagePath, $image_search_url);

            return view('result')->with('results', $result);
        }
        return view('welcome');
    }

    public function dirToArray($dir, $imagePath, $image_search_url) {
        $result = array();
        $cdir = scandir($dir);
        $i = 0;
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value, $imagePath, $image_search_url . DIRECTORY_SEPARATOR . $value);
                } else {
                    $match = $this->compare($imagePath, $dir . DIRECTORY_SEPARATOR . $value);
                    if ($match <= 5) {
                        $this->result[] = $image_search_url . DIRECTORY_SEPARATOR . $value;
                    } elseif ($match > 5 && $match < 15) {
                        $this->resultSimilar[] = $image_search_url . DIRECTORY_SEPARATOR . $value;
                    }
                }
            }
        }
        return array('result' => $this->result, 'similar' => $this->resultSimilar);
    }

    private function mimeType($i) {
        /* returns array with mime type and if its jpg or png. Returns false if it isn't jpg or png */
        $mime = getimagesize($i);
        $return = array($mime[0], $mime[1]);

        switch ($mime['mime']) {
            case 'image/jpeg':
                $return[] = 'jpg';
                return $return;
            case 'image/png':
                $return[] = 'png';
                return $return;
            default:
                return false;
        }
    }

    private function createImage($i) {
        /* retuns image resource or false if its not jpg or png */
        $mime = $this->mimeType($i);

        if ($mime[2] == 'jpg') {
            return imagecreatefromjpeg($i);
        } else if ($mime[2] == 'png') {
            return imagecreatefrompng($i);
        } else {
            return false;
        }
    }

    private function resizeImage($i, $source) {
        /* resizes the image to a 8x8 squere and returns as image resource */
        $mime = $this->mimeType($source);

        $t = imagecreatetruecolor(8, 8);

        $source = $this->createImage($source);

        imagecopyresized($t, $source, 0, 0, 0, 0, 8, 8, $mime[0], $mime[1]);

        return $t;
    }

    private function colorMeanValue($i) {
        /* returns the mean value of the colors and the list of all pixel's colors */
        $colorList = array();
        $colorSum = 0;
        for ($a = 0; $a < 8; $a++) {

            for ($b = 0; $b < 8; $b++) {

                $rgb = imagecolorat($i, $a, $b);
                $colorList[] = $rgb & 0xFF;
                $colorSum += $rgb & 0xFF;
            }
        }

        return array($colorSum / 64, $colorList);
    }

    private function bits($colorMean) {
        /* returns an array with 1 and zeros. If a color is bigger than the mean value of colors it is 1 */
        $bits = array();

        foreach ($colorMean[1] as $color) {
            $bits[] = ($color >= $colorMean[0]) ? 1 : 0;
        }

        return $bits;
    }

    public function compare($a, $b) {
        /* main function. returns the hammering distance of two images' bit value */
        $i1 = $this->createImage($a);
        $i2 = $this->createImage($b);

        if (!$i1 || !$i2) {
            return 100;
        }

        $i1 = $this->resizeImage($i1, $a);
        $i2 = $this->resizeImage($i2, $b);

        imagefilter($i1, IMG_FILTER_GRAYSCALE);
        imagefilter($i2, IMG_FILTER_GRAYSCALE);

        $colorMean1 = $this->colorMeanValue($i1);
        $colorMean2 = $this->colorMeanValue($i2);

        $bits1 = $this->bits($colorMean1);
        $bits2 = $this->bits($colorMean2);

        $hammeringDistance = 0;

        for ($a = 0; $a < 64; $a++) {

            if ($bits1[$a] != $bits2[$a]) {
                $hammeringDistance++;
            }
        }

        return $hammeringDistance;
    }

}
