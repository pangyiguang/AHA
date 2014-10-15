<?php

defined('AHA_ROOT') OR die('Unauthorized access!');

/**
 * Description of VerifyCode
 * 验证码生成类
 * 用法：
 * $code=new VerifyCode(200, 50, 5,5);//初始化参数
 * $code->outPut();//输出
 * 验证码值：$_SESSION['code']
 *
 * @author pangyiguang
 */
class VerifyCode {

    private $width;
    private $height;
    private $wordNum;
    private $Code = '';
    private $fontPath;
    private $distrube;
    private $im;
    private $codeString = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ';

    /**
     * 初始化
     * @param integer $width    验证码宽度
     * @param integer $height   验证码长度
     * @param integer $wordNum  字符个数
     * @param integer $distrube 干扰掩码（1-线；2-像素；4-字体；3-线+像素；5-线+字体；6-像素+字体；7-线+像素+字体；4-没有干扰）
     * @param string $fontPath  字体路径
     */
    function __construct($width = 120, $height = 30, $wordNum = 5, $distrube = 3, $fontPath = '') {
        $this->width = $width;
        $this->height = $height;
        $this->wordNum = $wordNum;
        $this->distrube = (int) $distrube;
        if ($fontPath && file_exists($fontPath)) {
            $this->fontPath = $fontPath;
        } else {
            $this->fontPath = AHA_ROOT . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'CreativZoo.ttf';
        }
        $this->_getCode();
    }

    public function outPut() {
        $this->_imageCreate();
        $this->_setBackgroundColor();
        $this->_setCode();
        $this->_setDistrube();
        ob_start();
        header('Content-type: image/gif');
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        header('Expires: 0');
        header('Pragma: no-cache');
        imagegif($this->im);
        imagedestroy($this->im);
    }

    private function _imageCreate() {
        $this->im = imagecreate($this->width, $this->height); //新建一个基于调色板的图像
    }

    private function _setBackgroundColor() {
        $bgcolor = ImageColorAllocate($this->im, rand(200, 250), rand(200, 250), rand(200, 250)); //为调色板图像分配颜色
        imagefill($this->im, 0, 0, $bgcolor); //颜色填充
    }

    private function _setDistrube() {
        if ($this->distrube & 1) {
            $this->_setDistrubeLine();
        }
        if ($this->distrube & 2) {
            $this->_setDistrubePixel();
        }
        if ($this->distrube & 4) {
            $this->_setDistrubeChar();
        }
    }

    private function _setDistrubeLine() {
        for ($i = 0; $i < 2; $i++) {//绘背景干扰线
            $color = imagecolorallocate($this->im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)); //干扰线颜色
            imagearc($this->im, mt_rand(-5, $this->width), mt_rand(-5, $this->height), mt_rand(20, 300), mt_rand(20, 200), 55, 44, $color); //干扰线
        }
    }

    private function _setDistrubePixel() {
        for ($i = 0; $i < $this->wordNum * 40; $i++) {//绘背景干扰点
            $color = imagecolorallocate($this->im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)); //干扰点颜色
            imagesetpixel($this->im, mt_rand(0, $this->width), mt_rand(0, $this->height), $color); //干扰点
        }
    }

    private function _setDistrubeChar() {
        $loor = floor($this->height * 2);
        $codeNum = strlen($this->codeString);
        for ($i = 0; $i < $loor; $i++) {
            $x = rand(0, $this->width);
            $y = rand(0, $this->height);
            $jiaodu = rand(0, 360);
            $fontsize = rand(8, 15);
            $char = $this->codeString[rand(0, $codeNum - 1)];
            $color = ImageColorAllocate($this->im, rand(40, 140), rand(40, 140), rand(40, 140));
            imagettftext($this->im, $fontsize, $jiaodu, $x, $y, $color, $this->fontPath, $char);
        }
    }

    private function _setCode() {
        $y = floor($this->height / 2) + floor($this->height / 4);
        $fontsize = rand(30, 35);
        for ($i = 0; $i < $this->wordNum; $i++) {
            $char = $this->Code[$i];
            $x = floor($this->width / $this->wordNum) * $i + 8;
            $jiaodu = rand(-20, 30);
            $color = ImageColorAllocate($this->im, rand(0, 50), rand(50, 100), rand(100, 140)); //随机分配字符颜色
            imagettftext($this->im, $fontsize, $jiaodu, $x, $y, $color, $this->fontPath, $char);
        }
    }

    private function _getCode() {
        $codeNum = strlen($this->codeString);
        for ($j = 0; $j < $this->wordNum; $j++) {
            $this->Code .= $this->codeString[rand(0, $codeNum - 1)];
        }
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['code'] = $this->Code;
    }

}
