<?php

/**
 * Description of image
 *
 * @author pangyiguang
 */
class Image {

    /**
     * 裁剪图片
     * @param string $fromurl   处理原图路径
     * @param string $tourl     生成裁剪目标图路径
     * @param int $tw           裁剪宽度
     * @param int $th           裁剪高度
     * @param int $x            裁剪x源点
     * @param int $y            裁剪y源点
     * @param int $w            裁剪宽度
     * @param int $h            裁剪高度
     * @return bool
     */
    public static function resizeImg($fromurl, $tourl, $tw = 100, $th = 100, $x = 0, $y = 0, $w = 100, $h = 100) {
        $info = self::get_imageinfo($fromurl, 2);
        if ($info) {
            switch ($info) {
                case 1:$img = imagecreatefromgif($fromurl);
                    break;
                case 2:$img = imagecreatefromjpeg($fromurl);
                    break;
                case 3:$img = imagecreatefrompng($fromurl);
                    break;
                default :return false;
            }
        } else {
            return false;
        }
        if (is_string($img)) {
            return false;
        }
        $tx = $x + $w;
        $ty = $y + $h;
        $wm = imagesx($img);
        $hm = imagesy($img);
        if ($wm >= $tx && $hm >= $ty) {
            $newimg = imagecreatetruecolor($tw, $th);
            imagecopyresized($newimg, $img, 0, 0, $x, $y, ceil($tw * ($wm / $w)), ceil($th * ($hm / $h)), $wm, $hm);
            if ($info == 1) {
                $tourl = imagegif($newimg, $tourl);
            }
            if ($info == 2) {
                $tourl = imagejpeg($newimg, $tourl);
            }
            if ($info == 3) {
                $tourl = imagepng($newimg, $tourl);
            }
            imagedestroy($newimg);
            if ($tourl) {
                return true;
            }
        } else {
            return false;
        }
        return false;
    }

    /**
     * 生成缩略图
     * @param string $fromurl   等待处理缩略的图片路径
     * @param int $maxX         最大宽度
     * @param int $maxY         最大高度
     * @param int $x1           x坐标目标点
     * @param int $y1           y坐标目标点
     * @param int $x2           x坐标源点
     * @param int $y2           y坐标源点
     * @return mix              【bool or array】
     */
    public static function thumb($fromurl, $maxX = 300, $maxY = 300, $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0) {
        $info = self::get_imageinfo($fromurl, 2);
        if ($info) {
            switch ($info) {
                case 1:$img = imagecreatefromgif($fromurl);
                    break;
                case 2:$img = imagecreatefromjpeg($fromurl);
                    break;
                case 3:$img = imagecreatefrompng($fromurl);
                    break;
                default :return false;
            }
        } else {
            return false;
        }
        if (is_string($img)) {
            return false;
        }
        $x = imagesx($img);
        $y = imagesy($img);
        if (($maxX && $x > $maxX) || ($maxY && $y > $maxY)) {
            if ($maxX && $x > $maxX) {
                $rax = $maxX / $x;
                $rx = true;
            }
            if ($maxY && $y > $maxY) {
                $ray = $maxY / $y;
                $ry = true;
            }
            if ($rx && $ry) {
                $r = ($rax > $ray) ? $ray : $rax;
            } elseif ($rax) {
                $r = $rax;
            } elseif ($ry) {
                $r = $ray;
            }
            $newX = $x * $r;
            $newY = $y * $r;
            $newimg = imagecreatetruecolor($newX, $newY);
            imagecopyresampled($newimg, $img, $x1, $y1, $x2, $y2, $newX, $newY, $x, $y);
            if ($info == 1) {
                $tourl = imagegif($newimg, $fromurl);
            }
            if ($info == 2) {
                $tourl = imagejpeg($newimg, $fromurl);
            }
            if ($info == 3) {
                $tourl = imagepng($newimg, $fromurl);
            }
            imagedestroy($newimg);
            if ($tourl) {
                return array('w' => $newX, 'h' => $newY);
            }
        } else {
            return array('w' => $x, 'h' => $y);
        }
        return false;
    }

    /**
     * 图片水印处理，支持格式gif、jpeg、png、wbmp
     * @param string $orgUri    待处理水印的图片（必填）
     * @param string $targetUri 水印成功后图片的存放路径（必填）
     * @param int $waterPos     水印位置，10种【0-9】（必填）
     *                                      0为随机位置；
     *                                      1为顶端居左，2为顶端居中，3为顶端居右；
     *                                      4为中部居左，5为中部居中，6为中部居右；
     *                                      7为底端居左，8为底端居中，9为底端居右；
     * @param string $waterUri  水印图片路径，用到图片水印才用
     * @param array $waterText  文字水印，属性text为必填，当存在图片水印时，文字水印失效    array('text'=>'','angle'=>0,'font'=>'./arial.ttf','size'=>15,'color'=>'#FF0000')
     *                                      text为水印文字，angle为倾斜角度，font字体文件路径,size为字体大小，color为文字颜色
     * @return bool
     * @example
     * echo makeImageWater('120.jpg','1555.jpg',0,'./logo.jpg');
     * echo makeImageWater('120.jpg','15556.jpg',0,'',array('text'=>'tett','font'=>'./arial.ttf','size'=>15,'color'=>'#000000'));
     */
    public static function makeImageWater($orgUri, $targetUri, $waterPos, $waterUri = '', $waterText = array()) {
        $isWater = false;
        if (!$orgUri || !$targetUri) {//两个路径要求必须
            return $isWater;
        }
        if (!file_exists($orgUri)) {//处理原图必须存在
            return $isWater;
        }

        if ($targetUri !== $orgUri && file_exists($targetUri)) {//存在水印目标图片时
            return true;
        }
        $image_info = getimagesize($orgUri);
        $org_width = $image_info[0];
        $org_height = $image_info[1];
        $org_mine = $image_info['mime'];
        switch ($org_mine) {
            case 'image/gif':
                if (imagetypes() & IMG_GIF) {
                    $org_im = imagecreatefromgif($orgUri);
                }
                break;
            case 'image/jpeg':
                if (imagetypes() & IMG_JPG) {
                    $org_im = imagecreatefromjpeg($orgUri);
                }
                break;
            case 'image/png':
                if (imagetypes() & IMG_PNG) {
                    $org_im = imagecreatefrompng($orgUri);
                }
                break;
            case 'image/wbmp':
                if (imagetypes() & IMG_WBMP) {
                    $org_im = imagecreatefromwbmp($orgUri);
                }
                break;
            default:
                $org_im = NULL;
                break;
        }

        if (!$org_im) {
            return false;
        }
        
        if ($waterUri && file_exists($waterUri)) {
            $image_info = getimagesize($waterUri);
            $water_width = $image_info[0];
            $water_height = $image_info[1];
            switch ($image_info['mime']) {
                case 'image/gif':
                    (imagetypes() & IMG_GIF) && ($water_im = imagecreatefromgif($waterUri));
                    break;
                case 'image/jpeg':
                    (imagetypes() & IMG_JPG) && ($water_im = imagecreatefromjpeg($waterUri));
                    break;
                case 'image/png':
                    (imagetypes() & IMG_PNG) && ($water_im = imagecreatefrompng($waterUri));
                    break;
                case 'image/wbmp':
                    (imagetypes() & IMG_WBMP) && ($water_im = imagecreatefromwbmp($waterUri));
                    break;
                default:
                    $water_im = NULL;
                    break;
            }
        }
        $waterText['size'] = $waterText['size'] ? $waterText['size'] : 15;
        $waterText['text'] = $waterText['text'] ? $waterText['text'] : 'www.pangyiguang.com';
        $waterText['color'] = $waterText['color'] ? $waterText['color'] : '#FFFFFF';
        $waterText['font'] = $waterText['font'] ? $waterText['font'] : './arial.ttf';
        if (!file_exists($waterText['font'])) {
            $waterText['font'] = AHA_ROOT.'/static/fonts/arial.ttf';
        }
        if (strlen($waterText['color']) !== 7) {
            $waterText['color'] = '#FFFFFF';
        }
        if (!$water_im && $waterText && is_array($waterText) && $waterText['text']) {
            $image_info = imagettfbbox(ceil($waterText['size']), 0, $waterText['font'], $waterText['text']); //取得使用 TrueType 字体的文本的范围
            $water_width = $image_info[2] - $image_info[6];
            $water_height = $image_info[3] - $image_info[7];
        }
        unset($image_info);
        if ($water_width > 0 && $water_height > 0 && $org_width > $water_width && $org_height > $water_height) {//具备生成水印的条件时
            switch ($waterPos) {
                case 0://随机
                    $posX = rand(0, ($org_width - $water_width));
                    $posY = rand(0, ($org_height - $water_height));
                    break;
                case 1://1为顶端居左
                    $posX = 0;
                    $posY = 0;
                    break;
                case 2://2为顶端居中
                    $posX = ($org_width - $water_width) / 2;
                    $posY = 0;
                    break;
                case 3://3为顶端居右
                    $posX = $org_width - $water_width;
                    $posY = 0;
                    break;
                case 4://4为中部居左
                    $posX = 0;
                    $posY = ($org_height - $water_height) / 2;
                    break;
                case 5://5为中部居中
                    $posX = ($org_width - $water_width) / 2;
                    $posY = ($org_height - $water_height) / 2;
                    break;
                case 6://6为中部居右
                    $posX = $org_width - $water_width;
                    $posY = ($org_height - $water_height) / 2;
                    break;
                case 7://7为底端居左
                    $posX = 0;
                    $posY = $org_height - $water_height;
                    break;
                case 8://8为底端居中
                    $posX = ($org_width - $water_width) / 2;
                    $posY = $org_height - $water_height;
                    break;
                case 9://9为底端居右
                    $posX = $org_width - $water_width;
                    $posY = $org_height - $water_height;
                    break;
                default://随机
                    $posX = rand(0, ($org_width - $water_width));
                    $posY = rand(0, ($org_height - $water_height));
                    break;
            }
            imagealphablending($org_im, true); //设定图像的混色模式

            if ($water_im) {//图片水印
                imagecopy($org_im, $water_im, $posX, $posY, 0, 0, $water_width, $water_height); //拷贝水印到目标
                imagedestroy($water_im);
            } else {//文字水印
                imagestring($org_im, $waterText['size'], $posX, $posY, $waterText['text'], imagecolorallocate($org_im, hexdec(substr($waterText['color'], 1, 2)), hexdec(substr($waterText['color'], 3, 2)), hexdec(substr($waterText['color'], 5))));
            }
            //生成水印后的图片
            switch ($org_mine) {//取得背景图片的格式
                case 'image/gif':
                    imagegif($org_im, $targetUri);
                    $isWater = true;
                    break;
                case 'image/jpeg':
                    imagejpeg($org_im, $targetUri);
                    $isWater = true;
                    break;
                case 'image/png':
                    imagepng($org_im, $targetUri);
                    $isWater = true;
                    break;
                case 'image/wbmp':
                    imagewbmp($org_im, $targetUri);
                    $isWater = true;
                    break;
            }
        }
        imagedestroy($org_im);
        return $isWater;
    }
    
    /**
     * 获取图片信息
     * @param string $file  文件路径
     * @param int $int      获取类型
     * @return mix
     */
    public static function get_imageinfo($file, $int = 2) {
        if ($file && file_exists($file)) {
            $info = getimagesize($file);
            if ($info) {
                return $info[$int];
            }
        }
        return false;
    }

    /**
     * 判断文件是否为图片
     * @param string $file  文件路径
     * @return boolren
     */
    public static function is_photo($file) {
        $ext = self::get_imageinfo($file, 2);
        if ($ext) {
            $types = '.gif|.jpeg|.png|.jpg';
            $ext = image_type_to_extension($ext);
            return stripos($types, $ext) !== false ? true : false;
        } else {
            return false;
        }
    }

}
