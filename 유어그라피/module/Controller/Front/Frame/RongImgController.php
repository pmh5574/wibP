<?php
namespace Controller\Front\Frame;

use Request;


class CreateImgController extends \Controller\Front\Controller
{
    public $prefix;
    
    public $frameSize;
    public $frameImg;
    public $framePadding;
    
    public $mattPadding;
    public $mattShadow;
    public $topColor;
    public $botColor;
    
    public $finSize;

    public $folder = '1000000000';
    
    
    public function index()
    {
        echo '1'; exit;

    }
    
    // 크롭 이미지 비율계산 후 출력될 프레임 이미지 가로X세로 값 리턴
    public function basicRatio($imgData)
    {
        $frameSize = [
            'width' => 0,
            'height' => 0
        ];
        if($imgData['width'] > $imgData['height']){
            $frameSize['width'] = 1000+$this->finSize;
            $frameSize['height'] = (1000 + $this->finSize) * $imgData['height'] / $imgData['width'];
            
        }else if($imgData['width'] < $imgData['height']){
            $frameSize['width'] = (1000 + $this->finSize) * $imgData['width'] / $imgData['height'];
            $frameSize['height'] = 1000+$this->finSize;
            
        }else{
            $frameSize['width'] = $frameSize['height'] = (1000+$this->finSize) * $imgData['width'] / $imgData['height'];
            
        }
        
        return $frameSize;
    }
    
    // 프레임에 외부에 사용될 이미지 정보
    public function getFrameImg($framesize)
    {
        
        if($framesize['width'] > $framesize['height']){
            $this->prefix = 'w_';
        }else if($framesize['width'] < $framesize['height']){
            $this->prefix = 'h_';
        }else{
            $this->prefix = 'w_';
        }
        
        return [
            'top' => './data/wibUpload/'.$this->folder.'/'.$this->prefix.'top.png',
            'bottom' => './data/wibUpload/'.$this->folder.'/'.$this->prefix.'bottom.png',
            'left' => './data/wibUpload/'.$this->folder.'/'.$this->prefix.'left.png',
            'right' => './data/wibUpload/'.$this->folder.'/'.$this->prefix.'right.png'
        ];
    }
    
    // 프레임 이미지 합성
    public function setFrameImg($basicBox)
    {
        $left = 0;
        $leftTop = 0;
        if($this->prefix == 'w_'){
            $leftTop = $this->framePadding;
        }
        
        if($this->prefix == 'h_'){
            $left = $this->framePadding;
        }
        
        $allLength = (1000+$this->finSize)/70;
        $frameTopPattern = imagecreatefrompng('./data/wibUpload/'.$this->folder.'/pattern_top.png'); 
        for($i=0;$i<$allLength;$i++){
            imagecopymerge($basicBox, $frameTopPattern, $i*70+70, 0, 0, 0, 70, $this->framePadding, 100);
        }
        
        $frameleftPattern = imagecreatefrompng('./data/wibUpload/'.$this->folder.'/pattern_left.png'); 
        for($i=0;$i<$allLength;$i++){
            imagecopymerge($basicBox, $frameleftPattern, 0, $i*70+70, 0, 0, $this->framePadding, 70, 100);
        }
        
        $framerightPattern = imagecreatefrompng('./data/wibUpload/'.$this->folder.'/pattern_right.png'); 
        for($i=0;$i<$allLength;$i++){
            imagecopymerge($basicBox, $framerightPattern, $this->frameSize['width'] - $this->framePadding, $i*70+70, 0, 0, $this->framePadding, 70, 100);
        }
        
        $framebottomPattern = imagecreatefrompng('./data/wibUpload/'.$this->folder.'/pattern_bottom.png'); 
        for($i=0;$i<$allLength;$i++){
            imagecopymerge($basicBox, $framebottomPattern, $i*70+70, $this->frameSize['height'] - $this->framePadding, 0, 0, 70, $this->framePadding, 100);
        }
        
        $frameTopLeftPattern = imagecreatefrompng('./data/wibUpload/'.$this->folder.'/pattern_Topleft.png'); 
        imagecopymerge($basicBox, $frameTopLeftPattern, 0, 0, 0, 0, 70, $this->framePadding, 100);
        $frameTopRightPattern = imagecreatefrompng('./data/wibUpload/'.$this->folder.'/pattern_Topright.png'); 
        imagecopymerge($basicBox, $frameTopRightPattern, $this->frameSize['width'] - $this->framePadding, 0, 0, 0, 70, $this->framePadding, 100);
        
        $frameBotLeftPattern = imagecreatefrompng('./data/wibUpload/'.$this->folder.'/pattern_Botleft.png'); 
        imagecopymerge($basicBox, $frameBotLeftPattern, 0, $this->frameSize['height'] - $this->framePadding, 0, 0, 70, $this->framePadding, 100);
        $frameBotRightPattern = imagecreatefrompng('./data/wibUpload/'.$this->folder.'/pattern_Botright.png'); 
        imagecopymerge($basicBox, $frameBotRightPattern, $this->frameSize['width'] - $this->framePadding, $this->frameSize['height'] - $this->framePadding, 0, 0, 70, $this->framePadding, 100);
    }
    
    // 매트보드 합성
    public function setMattColor($basicBox)
    {
        $width = $this->frameSize['width'] - ($this->framePadding*2);
        $height = $this->frameSize['height'] - ($this->framePadding*2);
        
        // TOP
        $matt_top = imagecreatetruecolor($width, $this->mattPaddingTop);
        $ivory_top = imagecolorallocate($matt_top, $this->topColor[0], $this->topColor[1], $this->topColor[2]);
        imagefill($matt_top, 0, 0, $ivory_top);
        
        imagecopymerge($basicBox, $matt_top, $this->framePadding, $this->framePadding, 0, 0, $width, $this->mattPaddingTop, 100);
        
        // LEFT
        $matt_left = imagecreatetruecolor($this->mattPaddingTop, $height);
        $ivory_left = imagecolorallocate($matt_left, $this->topColor[0], $this->topColor[1], $this->topColor[2]);
        imagefill($matt_left, 0, 0, $ivory_left);
        imagecopymerge($basicBox, $matt_left, $this->framePadding, $this->framePadding, 0, 0, $this->mattPaddingLeft, $height, 100);
        
        // RIGHT
        $matt_right = imagecreatetruecolor($this->mattPaddingRight, $height);
        $ivory_right = imagecolorallocate($matt_right, $this->topColor[0], $this->topColor[1], $this->topColor[2]);
        imagefill($matt_right, 0, 0, $ivory_right);
        imagecopymerge($basicBox, $matt_right, $this->frameSize['width'] - $this->framePadding - $this->mattPaddingRight , $this->framePadding, 0, 0, $this->mattPaddingRight, $height, 100);
        
        // BOTTOM
        $matt_bot = imagecreatetruecolor($width, $this->mattPaddingBottom);
        $ivory_bot = imagecolorallocate($matt_bot, $this->topColor[0], $this->topColor[1], $this->topColor[2]);
        imagefill($matt_bot, 0, 0, $ivory_bot);
        imagecopymerge($basicBox, $matt_bot, $this->framePadding, $this->frameSize['height'] - $this->framePadding - $this->mattPaddingBottom, 0, 0, $width, $this->mattPaddingBottom, 100);
    }
    
    // 매트보드 그림자 합성 => 최종 2단계로 수정예정
    public function setMattShadow($basicBox)
    {
        $width = $this->frameSize['width'] - ($this->framePadding*2) + 1;
        $height = $this->frameSize['height'] - ($this->framePadding*2);
        
        // TOP
        $matt_top = imagecreatetruecolor($width, $this->mattShadow);
        $ivory_top = imagecolorallocate($matt_top, 0, 0, 0);
        imagefill($matt_top, 0, 0, $ivory_top);
        for($top=0;$top<5;$top++){
            imagecopymerge($basicBox, $matt_top, $this->framePadding, $this->framePadding, 0, 0, $width, $this->mattShadow - ($top+2), 3);
        }
        
        // LEFT
        $matt_left = imagecreatetruecolor($this->mattShadow, $height);
        $ivory_left = imagecolorallocate($matt_left, 0, 0, 0);
        imagefill($matt_left, 0, 0, $ivory_left);
        //imagecopymerge($basicBox, $matt_left, $this->framePadding, $this->framePadding+$this->mattShadow, 0, 0, $this->mattShadow, $height, 3);
        for($top=0;$top<5;$top++){
            imagecopymerge($basicBox, $matt_left, $this->framePadding, $this->framePadding, 0, 0, $this->mattShadow - ($top+2), $height, 3);
        }
        
        // RIGHT
        $matt_right = imagecreatetruecolor($this->mattShadow, $height);
        $ivory_right = imagecolorallocate($matt_right, 0, 0, 0);
        imagefill($matt_right, 0, 0, $ivory_right);
        //imagecopymerge($basicBox, $matt_right, $this->frameSize['width'] - $this->framePadding - $this->mattShadow , $this->framePadding+$this->mattShadow, 0, 0, $this->mattShadow, $height, 3);
        for($top=0;$top<5;$top++){
            imagecopymerge($basicBox, $matt_right, $this->frameSize['width'] - $this->framePadding - $this->mattShadow + ($top+2) , $this->framePadding, 0, 0, $this->mattShadow - ($top+2), $height, 3);
        }
        
        // BOTTOM
        $matt_bot = imagecreatetruecolor($width, $this->mattShadow);
        $ivory_bot = imagecolorallocate($matt_bot, 0, 0, 0);
        imagefill($matt_bot, 0, 0, $ivory_bot);
        //imagecopymerge($basicBox, $matt_bot, $this->framePadding, $this->frameSize['height'] - $this->framePadding - $this->mattShadow, 0, 0, $width, $this->mattShadow, 3);
        for($top=0;$top<5;$top++){
            imagecopymerge($basicBox, $matt_bot, $this->framePadding, $this->frameSize['height'] - $this->framePadding - $this->mattShadow + ($top+2) , 0, 0, $width, $this->mattShadow - ($top+2), 3);
        }
        
    }
    
    public function setMattShadowInner($basicBox)
    {
        $width = $this->frameSize['width'] - ($this->framePadding*2) - ( $this->mattPaddingLeft + $this->mattPaddingRight );
        $height = $this->frameSize['height'] - ($this->framePadding*2) - ( $this->mattPaddingTop + $this->mattPaddingBottom );
        
        // TOP
        $matt_top = imagecreatetruecolor($width, $this->mattShadow);
        $ivory_top = imagecolorallocate($matt_top, 0, 0, 0);
        imagefill($matt_top, 0, 0, $ivory_top);
        //imagecopymerge($basicBox, $matt_top, $this->framePadding+$this->mattPaddingLeft , $this->framePadding+$this->mattPaddingTop, 0, 0, $width, $this->mattShadow, 7);
        for($top=0;$top<5;$top++){
            imagecopymerge($basicBox, $matt_top, $this->framePadding+$this->mattPaddingLeft, $this->framePadding+$this->mattPaddingTop , 0, 0, $width, $this->mattShadow- ($top+2), 4);
        }
        
        
        // LEFT
        $matt_left = imagecreatetruecolor($this->mattShadow, $height);
        $ivory_left = imagecolorallocate($matt_left, 0, 0, 0);
        imagefill($matt_left, 0, 0, $ivory_left);
        for($top=0;$top<5;$top++){
            imagecopymerge($basicBox, $matt_left, $this->framePadding+$this->mattPaddingLeft, $this->framePadding+$this->mattPaddingTop, 0, 0, $this->mattShadow - ($top+2), $height, 4);
        }
        // RIGHT
        $matt_right = imagecreatetruecolor($this->mattShadow, $height);
        $ivory_right = imagecolorallocate($matt_right, 0, 0, 0);
        imagefill($matt_right, 0, 0, $ivory_right);
        for($top=0;$top<5;$top++){
            imagecopymerge($basicBox, $matt_right, $this->frameSize['width'] - $this->framePadding - $this->mattPaddingRight - $this->mattShadow + ($top+2) , $this->framePadding+$this->mattPaddingTop, 0, 0, $this->mattShadow - ($top+2), $height, 4);
        }
        
        // BOTTOM
        $matt_bot = imagecreatetruecolor($width, $this->mattShadow);
        $ivory_bot = imagecolorallocate($matt_bot, 0, 0, 0);
        imagefill($matt_bot, 0, 0, $ivory_bot);
        for($top=0;$top<5;$top++){
            imagecopymerge($basicBox, $matt_bot, $this->framePadding+$this->mattPaddingLeft, $this->frameSize['height'] - $this->framePadding - $this->mattPaddingBottom - $this->mattShadow + ($top+2), 0, 0, $width, $this->mattShadow - ($top+2), 4);
        }
    }
    
    // 싱글마운트 마감처리
    public function setMattSingle($basicBox)
    {
        $width = $this->frameSize['width'] - ($this->framePadding) - ( $this->mattPaddingRight );
        $height = $this->frameSize['height'] - ($this->framePadding) - ( $this->mattPaddingBottom);
        
        
        $startPointTop = $this->framePadding + $this->mattPaddingTop - 5;
        $startPointLeft = $this->framePadding + $this->mattPaddingLeft - 5;
        
        // top
        $pointTop = [
            $startPointLeft,  $startPointTop, // Point 1 (x, y) 
            $startPointLeft+5, $startPointTop+5,  // Point 2 (x, y) 
            $width,  $startPointTop+5, // Point 3 (x, y) 
            $width+5,  $startPointTop, // Point 3 (x, y) 
        ];
        $imageTop = imagecreatetruecolor(1000, 1000);
        $blueTop = imagecolorallocate($imageTop, 232, 232, 232);
        
        imagefilledpolygon($basicBox, $pointTop, 4, $blueTop);
        
        // left
        $pointLeft = [
            $startPointLeft,  $startPointTop, // Point 1 (x, y) 
            $startPointLeft+5, $startPointTop+5,  // Point 2 (x, y) 
            $startPointLeft+5,  $height, // Point 3 (x, y) 
            $startPointLeft,  $height+5, // Point 3 (x, y) 
        ];
        $imageLeft = imagecreatetruecolor(1000, 1000);
        $blueLeft = imagecolorallocate($imageLeft, 218, 218, 218);
        
        imagefilledpolygon($basicBox, $pointLeft, 4, $blueLeft);
        
        // right
        $pointRight = [
            $width+5,  $startPointTop, // Point 1 (x, y) 
            $width, $startPointTop+5,  // Point 2 (x, y) 
            $width,  $height, // Point 3 (x, y) 
            $width+5,  $height+5, // Point 3 (x, y) 
        ];
        $imageRight = imagecreatetruecolor(1000, 1000);
        $blueRight = imagecolorallocate($imageRight, 255, 255, 255);
        
        imagefilledpolygon($basicBox, $pointRight, 4, $blueRight);
        
        // bottom
        $pointBot = [
            $startPointLeft+5,  $height, // Point 1 (x, y) 
            $startPointLeft, $height+5,  // Point 2 (x, y) 
            $width+5,  $height+5, // Point 3 (x, y) 
            $width,  $height, // Point 3 (x, y) 
        ];
        $imageBot = imagecreatetruecolor(1000, 1000);
        $blueBot = imagecolorallocate($imageBot, 240, 239, 243);
        
        imagefilledpolygon($basicBox, $pointBot, 4, $blueBot);
    }
    
    // 매트보드 더블마운트 매트처리
    public function setMattDoubleColor($basicBox)
    {
        $width = $this->frameSize['width'] - ($this->framePadding*2) - ( $this->mattPaddingLeft + $this->mattPaddingRight ) + 60;
        $height = $this->frameSize['height'] - ($this->framePadding*2) - ( $this->mattPaddingTop + $this->mattPaddingBottom )+ 60;
        
        $startPointX = $this->framePadding + $this->mattPaddingLeft - 25;
        $startPointY = $this->framePadding + $this->mattPaddingTop - 25;
        
        $startPointRightX = $this->frameSize['width'] - ($this->framePadding) - (  $this->mattPaddingRight ) + 6;
        
        $startPointBottomY = $this->frameSize['height'] - ($this->framePadding) - (  $this->mattPaddingBottom ) + 6;
        
        // TOP
        $matt_top = imagecreatetruecolor($width, $this->mattPaddingTop);
        $ivory_top = imagecolorallocate($matt_top, $this->botColor[0], $this->botColor[1], $this->botColor[2]);
        imagefill($matt_top, 0, 0, $ivory_top);
        
        imagecopymerge($basicBox, $matt_top, $startPointX, $startPointY, 0, 0, $width, 20, 100);
        
        // LEFT
        $matt_left = imagecreatetruecolor($this->mattPaddingTop, $height);
        $ivory_left = imagecolorallocate($matt_left, $this->botColor[0], $this->botColor[1], $this->botColor[2]);
        imagefill($matt_left, 0, 0, $ivory_left);
        imagecopymerge($basicBox, $matt_left, $startPointX, $startPointY, 0, 0, 20, $height, 100);
        
        // RIGHT
        $matt_right = imagecreatetruecolor($this->mattPaddingRight, $height);
        $ivory_right = imagecolorallocate($matt_right, $this->botColor[0], $this->botColor[1], $this->botColor[2]);
        imagefill($matt_right, 0, 0, $ivory_right);
        imagecopymerge($basicBox, $matt_right, $startPointRightX , $startPointY, 0, 0, 20, $height, 100);
        
        // BOTTOM
        $matt_bot = imagecreatetruecolor($width, $this->mattPaddingBottom);
        $ivory_bot = imagecolorallocate($matt_bot, $this->botColor[0], $this->botColor[1], $this->botColor[2]);
        imagefill($matt_bot, 0, 0, $ivory_bot);
        imagecopymerge($basicBox, $matt_bot, $startPointX, $startPointBottomY, 0, 0, $width, 20, 100);
    }
    
    // 더블마운트 마감처리
    public function setMattDouble($basicBox)
    {
        $width = $this->frameSize['width'] - ($this->framePadding) - ( $this->mattPaddingRight ) + 26;
        $height = $this->frameSize['height'] - ($this->framePadding) - ( $this->mattPaddingBottom) + 26;
        
        
        $startPointTop = $this->framePadding + $this->mattPaddingTop - 5 - 28;
        $startPointLeft = $this->framePadding + $this->mattPaddingLeft - 5 - 28;
        
        // top
        $pointTop = [
            $startPointLeft,  $startPointTop, // Point 1 (x, y) 
            $startPointLeft+8, $startPointTop+8,  // Point 2 (x, y) 
            $width,  $startPointTop+8, // Point 3 (x, y) 
            $width+8,  $startPointTop, // Point 3 (x, y) 
        ];
        $imageTop = imagecreatetruecolor(1000, 1000);
        $blueTop = imagecolorallocate($imageTop, 232, 232, 232);
        
        imagefilledpolygon($basicBox, $pointTop, 4, $blueTop);
        
        // left
        $pointLeft = [
            $startPointLeft,  $startPointTop, // Point 1 (x, y) 
            $startPointLeft+8, $startPointTop+8,  // Point 2 (x, y) 
            $startPointLeft+8,  $height, // Point 3 (x, y) 
            $startPointLeft,  $height+8, // Point 3 (x, y) 
        ];
        $imageLeft = imagecreatetruecolor(1000, 1000);
        $blueLeft = imagecolorallocate($imageLeft, 218, 218, 218);
        
        imagefilledpolygon($basicBox, $pointLeft, 4, $blueLeft);
        
        // right
        $pointRight = [
            $width+8,  $startPointTop, // Point 1 (x, y) 
            $width, $startPointTop+8,  // Point 2 (x, y) 
            $width,  $height, // Point 3 (x, y) 
            $width+8,  $height+8, // Point 3 (x, y) 
        ];
        $imageRight = imagecreatetruecolor(1000, 1000);
        $blueRight = imagecolorallocate($imageRight, 245, 245, 245);
        
        imagefilledpolygon($basicBox, $pointRight, 4, $blueRight);
        
        // bottom
        $pointBot = [
            $startPointLeft+8,  $height, // Point 1 (x, y) 
            $startPointLeft, $height+8,  // Point 2 (x, y) 
            $width+8,  $height+8, // Point 3 (x, y) 
            $width,  $height, // Point 3 (x, y) 
        ];
        $imageBot = imagecreatetruecolor(1000, 1000);
        $blueBot = imagecolorallocate($imageBot, 240, 239, 243);
        
        imagefilledpolygon($basicBox, $pointBot, 4, $blueBot);
    }
    
}
