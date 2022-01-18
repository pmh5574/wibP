<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Controller\Front\Main;


class GoUploadExcelController extends \Controller\Front\Controller
{
    public $Header;
    public $Footer;
    
    public function index()
    {
        $this->setLayouts();
        $request = \App::getInstance('request');
        $filesValue = $request->files()->toArray();
        $tmp = $filesValue['filesupload']['tmp_name'];

        $xls = new \PhpOffice\PhpSpreadsheet\Reader\Xls();

        $chk = $xls->canRead($tmp);
        
        $xls->setReadDataOnly(true);
        $sheet = $xls->setReadEmptyCells(false)->load($tmp)->getActiveSheet();
        $rows = $sheet->getRowIterator();
//        print_r($rows);
        foreach ($rows as $row) { // 모든 행에 대해서

            $cellIterator = $row->getCellIterator();

//               $cellIterator->setIterateOnlyExistingCells(false);

        }
//        print_r($cellIterator);
        $maxRow = $sheet->getHighestRow();
//        print_r($maxRow);
        for ($i = 1 ; $i <= $maxRow ; $i++) {

            $_a = $sheet->getCell('A' . $i)->getValue(); // A열
            $_b = $sheet->getCell('B' . $i)->getValue(); // B열
            $_c = $sheet->getCell('C' . $i)->getValue(); // C열
            $_d = $sheet->getCell('D' . $i)->getValue(); // D열
            $_e = $sheet->getCell('E' . $i)->getValue(); // E열
            $_f = $sheet->getCell('F' . $i)->getValue(); // F열
            $_g = $sheet->getCell('G' . $i)->getValue(); // G열
            $_h = $sheet->getCell('H' . $i)->getValue(); // H열
            $_i = $sheet->getCell('I' . $i)->getValue(); // I열

            echo $_a.$_b.$_c.$_d.$_e.$_f.$_g.$_h.$_i."<br/>";

        }
        






//        print_r($sheet);
//        
//        echo $this->excelHeader;
//
//        echo '<table border="1">' . chr(10);
//        echo '<tr>' . chr(10);
//        echo '<td>' . __('번호') . '</td><td>' . __('상품번호') . '</td><td>' . __('등록/수정') . '</td><td>' . __('이미지저장소') . '</td>' . chr(10);
//        echo '</tr>' . chr(10);
//
//        $this->processExcel();
//        echo $this->excelFooter;
        exit;
    }
    
    public function setLayouts()
    {
        
        // 엑셀 상하단
        $this->Header = '<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">' . chr(10);
        $this->Header .= '<head>' . chr(10);
        $this->Header .= '<title>Excel Down</title>' . chr(10);
        $this->Header .= '<meta http-equiv="Content-Type" content="text/html; charset=' . SET_CHARSET . '" />' . chr(10);
        $this->Header .= '<style>' . chr(10);
        $this->Header .= 'br{mso-data-placement:same-cell;}' . chr(10);
        //        $this->excelHeader .= 'td{mso-number-format:"\@";} ' . chr(10);
        $this->Header .= '.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}' . chr(10);
        $this->Header .= '.xl24{mso-number-format:"\@";} ' . chr(10);
        $this->Header .= '.title{font-weight:bold; background-color:#F6F6F6; text-align:center;} ' . chr(10);
        $this->Header .= '</style>' . chr(10);
        $this->Header .= '</head>' . chr(10);
        $this->Header .= '<body>' . chr(10);

        $this->Footer = '</body>' . chr(10);
        $this->Footer .= '</html>' . chr(10);   

        
    }
   
}
