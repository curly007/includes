<?
/*
void make_thumbnail($file_type, $orig_filename, $thumbnail_filename="", $max_x=-1, $max_y=-1, $force_resize=false, $quality=75)

Purpose:    To make a thumbnail of a jpeg, gif, png, or wireless bmp image

Parameters: $file_type          - Type of file
                                  Valid types are "jpeg", "gif", "png", and "wbmp"
            $orig_filename      - Filename of image thumbnail will be made from
            $thumbnail_filename - optional, Filename thumbnail will saved to
                                  if blank, the raw image stream will be output directly
            $max_x              - optional, Maximum width of thumbnail, ignored if -1
            $max_y              - optional, Maximum height of thumbnail, ignored if -1
            $force_resize       - optional, force resizing the image if the thumbnail will be bigger that the original
            $quality            - optional, JPEG quality, valid values are 0(worst) - 100(best)
                                  used for JPEGs only, 

Returns:    true on success, false otherwise

Notes:      if $max_x and $max_y are both -1, default values will be used
*/
function make_thumbnail($file_type, $orig_filename, $thumbnail_filename="", $max_x=-1, $max_y=-1, $force_resize=false, $quality=75) {
	#open big photo
	if (!file_exists($orig_filename))
		return false;

	switch (strtolower($file_type)) {
	case "jpg":
	case "jpeg":
		if (!(imagetypes()&IMG_JPG)) {
			trigger_error("JPEG support is not compiled for PHP", E_USER_ERROR);
			return false;
		}

		$big_img = ImageCreateFromJpeg($orig_filename);
		break;
	case "gif":
		if (!(imagetypes()&IMG_GIF)) {
			trigger_error("GIF support is not compiled for PHP", E_USER_ERROR);
			return false;
		}

		$big_img = ImageCreateFromGif($orig_filename);
		break;
	case "png":
		if (!(imagetypes()&IMG_PNG)) {
			trigger_error("PNG support is not compiled for PHP", E_USER_ERROR);
			return false;
		}

		$big_img = ImageCreateFromPng($orig_filename);
		break;
	case "wbmp":
		if (!(imagetypes()&IMG_WBMP)) {
			trigger_error("WBMP (Wireless Bitmap) support is not compiled for PHP", E_USER_ERROR);
			return false;
		}

		$big_img = ImageCreateFromWbmp($orig_filename);
		break;
	default:
		trigger_error("make_thumbnail does not support '$file_type' files", E_USER_ERROR);
		return false;
	}

	if (!$big_img)
		return false;
	
	$orig_x = ImageSX($big_img);
	$orig_y = ImageSY($big_img);

	#detemine how much to scale the thumbnail down by
	if ($max_x<=0 && $max_y<=0) {
		#no size constraints specified, use default values
		$max_x = 100;
		$max_y = 100;
	}

	#if only max height was specified
	if ($max_x<=0) {
		$scale = $orig_y/$max_y;
	}
	#if only max width was specified
	elseif ($max_y<=0) {
		$scale = $orig_x/$max_x;
	}
	#if both max height and max width were specified
	else {
		$scale = max($orig_x/$max_x, $orig_y/$max_y);
	}

	#if thumbnail will be bigger that the original and we want to prevent that
	if ($scale<1 && !$force_resize) {
		#keep thumbnail same size as original
		$new_x = $orig_x;
		$new_y = $orig_y;
	}
	else {
		#find dimensions of thumbnail
		$new_x = floor($orig_x/$scale);
		$new_y = floor($orig_y/$scale);
	}

	$small_img = ImageCreateTrueColor($new_x, $new_y);
	
	if (!$small_img)
		return false;

	if (!ImageCopyResampled($small_img, $big_img, 0, 0, 0, 0, $new_x, $new_y, $orig_x, $orig_y)) {
		imagedestroy($big_img);
		return false;
	}

	//imagedestroy($big_img);

	#force a valid value for quality
	if ($quality<0)
		$quality=0;

	if ($quality>100)
		$quality=100;

	switch(strtolower($file_type)) {
	case "jpg":
	case "jpeg":
		ImageJpeg($small_img, $thumbnail_filename, $quality);
		break;
	case "gif":
		ImageGif($small_img, $thumbnail_filename);
		break;
	case "png":
		ImagePng($small_img, $thumbnail_filename);
		break;
	case "wbmp":
		ImageWbmp($small_img, $thumbnail_filename);
		break;
	}

	imagedestroy($small_img);

	return true;
}
?>
