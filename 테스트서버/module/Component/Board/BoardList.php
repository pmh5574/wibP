<?php
namespace Component\Board;

use App;
use Request;

class BoardList extends \Bundle\Component\Board\BoardList
{
    public function getList($isPaging = true, $listCount = 0, $subjectCut = 0, $arrWhere = [], $arrInclude = null, $displayNotice = true)
    {
        $result = parent::getList($isPaging, $listCount, $subjectCut, $arrWhere, $arrInclude, $displayNotice);

        $server = Request::server()->toArray();
        $obj = App::load('\Component\Board\BannedWords');
        if ($result['data']) {
            $chkFields = [
                'subject',
                'contents',
                'goodsNm',
                'workedContents',
                'viewContents',
                'listContents',
            ];
            $obj->load();
            foreach ($result['data'] as $key => $value) {
                foreach ($value as $k => $v) {
                    if (in_array($k, $chkFields)) {
                        $obj->convert($v, $value['bdId']);
                        $result['data'][$key][$k] = $v;
                    }
                } // endforeach
            } // endforeach
        } // endif

        return $result;
    }
}
