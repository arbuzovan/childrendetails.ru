<?php
	class custom extends def_module {
		public function cms_callMethod($method_name, $args) {
			return call_user_func_array(Array($this, $method_name), $args);
		}
		
		public function __call($method, $args) {
			throw new publicException("Method " . get_class($this) . "::" . $method . " doesn't exist");
		}
                
		public function makeThumbnail($path, $width, $height, $template = "default", $returnArrayOnly = false, $fixHeight = false, $alt_text = '') {

                    if(!$template){
                        $template = "default";
                    }

                    $thumbs_path = CURRENT_WORKING_DIR."/images/.tmb/";
                    $path = CURRENT_WORKING_DIR.$path;

                    $image = new umiImageFile($path);
                    $file_name = $image->getFileName();
                    $file_ext = $image->getExt();
                    $thumbPath = sha1($image->getDirName());

                    if (!is_dir($thumbs_path.$thumbPath)) {
                        mkdir($thumbs_path.$thumbPath, 0755);
                    }

                    $file_ext = strtolower($file_ext);
                    $allowedExts = Array('gif', 'jpeg', 'jpg', 'png', 'bmp');
                    if(!in_array($file_ext, $allowedExts)){
                        return "";
                    }

                    $file_name = substr($file_name, 0, (strlen($file_name) - (strlen($file_ext) + 1)) );
                    $file_name_new = $file_name . "_" . $width . "_" . $height . "." . $file_ext;
                    $path_new = $thumbs_path .$thumbPath."/". $file_name_new;

                    if(!file_exists($path_new) || filemtime($path_new) < filemtime($path)) {
                        if(file_exists($path_new)) {
                            unlink($path_new);
                        }
                        $width_src = $image->getWidth();
                        $height_src = $image->getHeight();

                        if($width_src <= $width && $height_src <= $height) {
                            copy($path, $path_new);
                            $real_width = $width;
                            $real_height = $height;
                        } else {

                        if ($width == "auto" && $height == "auto"){
                            $real_height = $height_src;
                            $real_width = $width_src;
                        }elseif ($width == "auto" || $height == "auto"){
                            if ($height == "auto"){
                                $real_width = (int) $width;
                                $real_height = (int) round($height_src * ($width / $width_src));
                            }elseif($width == "auto"){
                                $real_height = (int) $height;
                                $real_width = (int) round($width_src * ($height / $height_src));
                            }
                        }else{
                            //для фона
                            if($fixHeight){
                                $real_width = $width;// для макс заданного контура
                                $real_height = $height;// для макс заданного контура
                            }

                            //определяем размеры картинки
                            if($width_src > $height_src) {//горизонт
                                $real_width = $width;
                                $real_height = (int) round($height_src * ($width / $width_src));
                                if($real_height > (int) $height){
                                    $real_height = (int) $height;
                                    $real_width = (int) round($width_src * ($real_height / $height_src));
                                }
                            }
                            else{
                                $real_height = (int) $height;
                                $real_width = (int) round($width_src * ($height / $height_src));
                                if($real_width > $width){
                                    $real_width = (int) $width;
                                    $real_height = (int) round($height_src * ($real_width / $width_src));
                                }
                            }
                        }

                        if($fixHeight){
                            $thumb = imagecreatetruecolor($width, $height);//width для макс заданного контура
                        }
                        else{
                            $thumb = imagecreatetruecolor($real_width, $real_height);
                        }

                        if($image->getExt() == "gif") {
                            $source = imagecreatefromgif($path);

                            $thumb_white_color = imagecolorallocate($thumb, 255, 255, 255);
                            imagefill($thumb, 0, 0, $thumb_white_color);
                            imagecolortransparent($thumb, $thumb_white_color);

                            imagealphablending($source, TRUE);
                            imagealphablending($thumb, TRUE);
                        } else if($image->getExt() == "png") {
                            $source = imagecreatefrompng($path);

                            $thumb_white_color = imagecolorallocate($thumb, 255, 255, 255);
                            imagefill($thumb, 0, 0, $thumb_white_color);
                            imagecolortransparent($thumb, $thumb_white_color);

                            imagealphablending($source, TRUE);
                            imagealphablending($thumb, TRUE);
                        } else {
                            $source = imagecreatefromjpeg($path);
                            $thumb_white_color = imagecolorallocate($thumb, 255, 255, 255);
                            imagefill($thumb, 0, 0, $thumb_white_color);
                            imagecolortransparent($thumb, $thumb_white_color);

                            imagealphablending($source, TRUE);
                            imagealphablending($thumb, TRUE);
                        }

                        //определяем координаты по середине полотна
                        $dstY = 0;
                        $dstX = 0;
                        if($fixHeight){
                            $dstX = round(($width - $real_width)/2);//для макс контура
                            $dstY = round(($height - $real_height)/2);
                        }

                        imagecopyresampled($thumb, $source, $dstX, $dstY, 0, 0, $real_width, $real_height, $width_src, $height_src);

                        if($image->getExt() == "png") {
                            imagepng($thumb, $path_new);
                        } else if($image->getExt() == "gif") {
                            imagegif($thumb, $path_new);
                        } else {
                            imagejpeg($thumb, $path_new, 75);
                        }
                    }
                    }

                    //Parsing
                    $value = new umiImageFile($path_new);

                    $arr = Array();
                    $arr['size'] = $value->getSize();
                    $arr['filename'] = $value->getFileName();
                    $arr['filepath'] = $value->getFilePath();
                    $arr['src'] = $value->getFilePath(true);
                    $arr['ext'] = $value->getExt();

                    $arr['width'] = $value->getWidth();
                    $arr['height'] = $value->getHeight();

                    $arr['template'] = $template;

                    $arr['alt_text'] = $alt_text;

                    if(cmsController::getInstance()->getCurrentMode() == "admin") {
                        $arr['src'] = str_replace("&", "&amp;", $arr['src']);
                    }

                    if(true == $returnArrayOnly) {
                        return $arr;
                    } else {
                        list($tpl) = def_module::loadTemplates("thumbs/{$template}.tpl", "image");
                        return def_module::parseTemplate($tpl, $arr);
                    }
		}

                
	}