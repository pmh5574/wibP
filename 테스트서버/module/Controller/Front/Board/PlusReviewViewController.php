<?php
namespace Controller\Front\Board;

use App;

class PlusReviewViewController extends \Bundle\Controller\Front\Board\PlusReviewViewController {
    public function index()
    {
        parent::index();
        if ($data = $this->getData("data")) {
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

            foreach ($data as $k => $v) {
                if (in_array($k, $chkFields)) {
                    $obj->convert($v, "plus_review");
                    $data[$k] = $v;
                }
            }
            $this->setData("data", $data);

        }


    }
}
