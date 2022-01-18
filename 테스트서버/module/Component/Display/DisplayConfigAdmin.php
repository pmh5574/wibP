<?php

/**
 * 상품노출형태 관리
 * @author atomyang
 * @version 1.0
 * @since 1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */
namespace Component\Display;


class DisplayConfigAdmin extends \Bundle\Component\Display\DisplayConfigAdmin
{
    

    public function __construct()
    {
        parent::__construct();
        // PDF다운로드 항목 추가
        $this->goodsDisplayField['pdfDownload'] = "PDF다운로드";
    }

    public function getDateGoodsDisplay()
    {
        return parent::getDateGoodsDisplay();
    }
}