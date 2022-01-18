<?php
namespace Component\Board;

use Request;
use App;

class BoardView extends \Bundle\Component\Board\BoardView
{
    public function getView()
    {
        $result = parent::getView();
        $server = Request::server()->toArray();
        if ($result) {
            $obj = App::load('\Component\Board\BannedWords');
            $chkFields = [
                'subject',
                'contents',
                'goodsNm',
                'workedContents',
                'viewContents',
                'listContents',
            ];

            $obj->load();
            $bdId = $this->cfg['bdId'];
            foreach ($result as $k => $v) {

                if (is_array($v)) {
                    foreach ($v as $_k => $_v) {
                        if (in_array($_k, $chkFields)) {
                            $obj->convert($_v, $bdId);
                            $result[$k][$_k] = $_v;
                        }
                    }
                }
                else {
                    if (in_array($k, $chkFields)) {
                        $obj->convert($v, $bdId);
                        $result[$k] = $v;
                    }
                }
            }
        }

        return $result;
        
    }
}
