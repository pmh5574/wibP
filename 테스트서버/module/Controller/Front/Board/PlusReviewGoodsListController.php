<?php
namespace Controller\Front\Board;

use App;

class PlusReviewGoodsListController extends \Bundle\Controller\Front\Board\PlusReviewGoodsListController
{
    public function index()
    {
        parent::index();
        if ($data = $this->getData('data')) {
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
            
            foreach ($data['list'] as $key => $value) {
                foreach ($value as $k => $v) {

                    if (in_array($k, $chkFields)) {
                        $obj->convert($v, "plus_review");

                        $data['list'][$key][$k] = $v;
                    }
                    else {
                        if (is_array($v)) {
                            foreach ($v as $_k => $_v) {
                                foreach ($_v as $__k => $__v) {
                                    if (in_array($__k, $chkFields)) {
                                        $obj->convert($__v, "plus_review");

                                        $data['list'][$key][$k][$_k][$__k] = $__v;
                                    }
                                }
                            }
                        }
                    }
                } // endforeach
            } // endforeach
            $this->setData("data", $data);
        }
    }

}
