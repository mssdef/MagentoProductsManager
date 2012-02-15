<?php

//define('MDF_MEMCACHE_TSHORT', 60 * 3);

class Masterdef_Image
{
    protected static $_instance; 
    static function I() { if (self::$_instance === NULL) { self::$_instance = new self(); } return self::$_instance; } 

	function __construct() {
	}

	function resize($src_image, $width, $height) {
		$imageResized = "/media/tmp/catalog/product/" . md5($src_image);
		$f_image_resized	= dirname(__FILE__) . "/../..{$imageResized}";
		$f_src_image		= dirname(__FILE__) . "/../..{$src_image}";
		 
		if (!file_exists($f_image_resized) && file_exists($f_src_image)) :
		    $imageObj = new Varien_Image($f_src_image);
		    $imageObj->constrainOnly(TRUE);
		    $imageObj->keepAspectRatio(TRUE);
		    $imageObj->keepFrame(FALSE);
		    $imageObj->resize($width, $height);
		    $imageObj->save($f_image_resized);
		endif;

		return $imageResized;
	}

    /** 
     * PNG ALPHA CHANNEL SUPPORT for imagecopymerge(); 
     * This is a function like imagecopymerge but it handle alpha channel well
     **/ 
    public function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) 
    { 
        if(!isset($pct)){ 
            return false; 
        } 
        $pct /= 100; 
        // Get image width and height 
        $w = imagesx( $src_im ); 
        $h = imagesy( $src_im ); 
        // Turn alpha blending off 
        imagealphablending( $src_im, false ); 
        // Find the most opaque pixel in the image (the one with the smallest alpha value) 
        $minalpha = 127; 
        for( $x = 0; $x < $w; $x++ ) 
        for( $y = 0; $y < $h; $y++ ){ 
            $alpha = ( imagecolorat( $src_im, $x, $y ) >> 24 ) & 0xFF; 
            if( $alpha < $minalpha ){ 
                $minalpha = $alpha; 
            } 
        } 
        //loop through image pixels and modify alpha for each 
        for( $x = 0; $x < $w; $x++ ){ 
            for( $y = 0; $y < $h; $y++ ){ 
                //get current alpha value (represents the TANSPARENCY!) 
                $colorxy = imagecolorat( $src_im, $x, $y ); 
                $alpha = ( $colorxy >> 24 ) & 0xFF; 
                //calculate new alpha 
                if( $minalpha !== 127 ){ 
                    $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha ); 
                } else { 
                    $alpha += 127 * $pct; 
                } 
                //get the color index with new alpha 
                $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha ); 
                //set pixel with the new color + opacity 
                if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){ 
                    return false; 
                } 
            } 
        } 
        // The image copy 
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h); 
    } 
}

