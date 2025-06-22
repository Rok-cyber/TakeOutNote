<?
/******************************************************************
* =======================================================
* 
*
* 
*
* 
*
* =======================================================
******************************************************************/



/** 
 * $size 원하시는 사이즈를 넣으시면 긴 쪽을 거기에 맞추어 섬네일을 생성한다. 
 * $fixed = true 일때는 무조건 4:3 이나 3:4로 맞추어 생성한다. false이면 
 * 길이에 비례하는 섬네일을 생성한다. 
 * php4에서도 static만 없음 사용 가능하실겁니다. 
 * $file 원본 파일이 있는 경로를 지정한다. 
 * 예: "upload/pic/pic1.gif" 1
 * @return 섬네일 경로 

 
$imagethumb = thumb::create_thumbnail("upload/pic/pic1.gif", 115); 
// 115에 맞는 비율로 정확히 줄인다. 

$imagethumb = thumb::create_thumbnail("upload/pic/pic1.gif", 115, 1); 
// 115로 줄이지만 4:3 이나 3:4이다.

*/ 

//class thumb { 
  //  public function __construct() {} 

    // create thumbnail, will accept jpeg and gif. 

    //public static function createThumbnail($file, $size = false, $fixed = false) { 
	function createThumbnail($file, $size = false, $fixed = false) { 
        // 1 = GIF, 2 = JPEG 
        if(!$size) $size = 100; 
        if(file_exists($file)) {              
            $type = getimagesize($file); 
         
            if(!function_exists('imagegif') && $type[2] == 1) $error = 'Filetype not supported. Thumbnail not created.'; 
            elseif (!function_exists('imagejpeg') && $type[2] == 2) $error = 'Filetype not supported. Thumbnail not created.'; 
			elseif (!function_exists('imagepng') && $type[2] == 3) $error = 'Filetype not supported. Thumbnail not created.'; 
            else {     
                // create the initial copy from the original file 
                if($type[2] == 1) $image = imagecreatefromgif($file); 
                elseif($type[2] == 2) $image = imagecreatefromjpeg($file); 
				elseif($type[2] == 3) $image = imagecreatefrompng($file); 
               
                if(function_exists('imageantialias')) imageantialias($image, TRUE); 
     
                $image_attr = getimagesize($file); 
     
                // figure out the longest side 
     
                if($image_attr[0] > $image_attr[1] || $fixed=='w'): 
                    $image_width = $image_attr[0]; 
                    $image_height = $image_attr[1]; 
                                        
                    if($fixed && $fixed!='w') { 
                        $image_new_width  = $size; 
                        $image_new_height = (int)($size * 3 / 4); // 4:3 ratio 
                    } 
					else { 
                        $image_new_width = $size;          
                        $image_ratio = $image_width / $image_new_width; 
                        $image_new_height = (int) ($image_height / $image_ratio); 
                    } 
                    //width > height 
                else: 
                    $image_width = $image_attr[0]; 
                    $image_height = $image_attr[1]; 
                    if($fixed) { 
                        $image_new_height = $size; 
                        $image_new_width  = round($size * 3 / 4); // 3:4 ratio 
                    } else { 
                        $image_new_height = $size; 
         
                        $image_ratio = $image_height / $image_new_height; 
                        $image_new_width = round($image_width / $image_ratio); 
                    } 
                    //height > width 
                endif; 
     
                $thumbnail = imagecreatetruecolor($image_new_width, $image_new_height); 
                @ imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $image_new_width, $image_new_height, $image_attr[0], $image_attr[1]); 
                 
                $thumb = preg_replace('!(\.[^.]+)?$!', "_{$size}".'$1', basename($file), 1); 
                $thumbpath = str_replace(basename($file), $thumb, $file); 

                // move the thumbnail to it's final destination 
                if($type[2] == 1) { 
                    if (!imagegif($thumbnail, $thumbpath)) $error = 'Thumbnail path invalid';                     
                } 
				elseif($type[2] == 2) { 
                    if (!imagejpeg($thumbnail, $thumbpath)) $error = 'Thumbnail path invalid';                     
                }    
				elseif($type[2] == 3) { 
                    if (!imagepng($thumbnail, $thumbpath)) $error = 'Thumbnail path invalid';                     
                }    
            } 
        } else { 
            $error = 'File not found'; 
        } 
     
        if(!empty($error)) { 
            die($error); 
        } else { 
            return $thumbpath; 
        } 
         
    } 

//}; 
 