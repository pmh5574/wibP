<?php
namespace Controller\Front\Main;

use Framework\StaticProxy\Proxy\FileHandler;
use Request;
use Component\Storage\Storage;
use Vendor\Spreadsheet\Excel\Reader as SpreadsheetExcelReader;
use UserFilePath;

class GoGoExcelController extends \Controller\Front\Controller
{
    protected $storage;  
    public $Header;
    public $Footer;

    public function index()
    {
        // $no_matches = '2019. 12. 21';
        // preg_match_all("/^([0-9]{4}).\s([0-9]{2}).\s([0-9]{2})$/", $no_matches, $_find_date);
        // print_r($_find_date);
        //테스트해봄
        //-----------------------------------
        $postValue = Request::post()->toArray();
        $this->setLayouts();
        $this->db = \App::load('DB');
        $data[] = '<tr><th>No</th><th>내용</th><th>속성</th><th>댓글</th><th>평가</th><th>작성자</th><th>작성일</th><th>추천</th><th>승인</th></tr>';
        
        $data[] = "<tr><td>1</td><td>엑셀 테스트</td><td>포토</td><td>0</td><td>4</td><td>qwer1234</td><td>2020.10.08</td><td>0</td><td>승인완료</td></tr>";
        
        $fileConfig['location'] = 'evendgoods';
        $fileConfig['menu'] = 'evendgoods';
        
        $inputData = $this->Header;
        $inputData .= "<table border='1'>";
        $inputData .= implode(\chr(13) . \chr(10), $data);
        $inputData .= '</table>';
        $inputData .= $this->Footer;
        
        
        $fileName = $fileConfig['location'] . array_sum(explode(' ', microtime()));
        $xlsFilePath = UserFilePath::data('excel', $fileConfig['menu'], $fileName . '.xls')->getRealPath();
        FileHandler::write($xlsFilePath, $inputData, 0707);
        
        $this->download($xlsFilePath, $fileName . '.xls');
        exit;
        
        
        
        
        
        //$_excel = Request::files()->toArray();
        
        $_excel = Request::files()->get('filesupload');
        if(!$_excel['name']){
            echo '<script>alert("데이터 없다~"); history.back(); </script>';exit;
        }
        $file = $_excel['name'];
        $tmp_file = $_excel['tmp_name'];
        $upload_directory = 'testexcel/'.$file;

        $this->storage = Storage::disk(3,'local');

        $this->storage->upload($tmp_file, $upload_directory);

        $data = new SpreadsheetExcelReader();
        $data->setOutputEncoding('CP949');
        $chk = $data->read($tmp_file);

        // print_r($chk);
        // print_r($data);
        // require_once '../PHPExcel-1.8/Classes/PHPExcel.php';

        // $objPHPExcel = new PHPExcel();
        // require_once '../PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';
        // $filename = '../../data/goods/testexcel/test1.xlsx';

        //$data = new Spreadsheet_Excel_Reader();
        // $data->setOutputEncoding('utf-8');
        // $data->read($tmp_file);

        // $tmp = explode(".", $tmp_file);
        print_r($tmp);
        print_r($_excel);
        exit;
        //move_uploaded_file();
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