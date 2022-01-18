<?php

/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */

namespace Controller\Mobile\Board;

use Component\Board\Board;
use Component\Board\BoardConfig;
use Component\Board\BoardBuildQuery;
use Component\Board\BoardList;
use Component\Page\Page;
use Framework\Debug\Exception\AlertBackException;
use Framework\Debug\Exception\AlertRedirectException;
use Framework\Debug\Exception\RedirectLoginException;
use Framework\Debug\Exception\RequiredLoginException;
use Request;
use View\Template;
use Framework\Utility\Strings;
use Session;

class ListController extends \Bundle\Controller\Mobile\Board\ListController {

    public function index() {
        try {
            if (Request::get()->get('mypageFl') == 'y') {
                if (gd_is_login() === false) {
                    throw new RequiredLoginException();
                }
            }

            $locale = \Globals::get('gGlobal.locale');
            $this->addCss([
                'plugins/bootstrap-datetimepicker.min.css',
                'plugins/bootstrap-datetimepicker-standalone.css',
            ]);
            $this->addScript([
                'gd_board_common.js',
                'moment/moment.js',
                'moment/locale/' . $locale . '.js',
                'jquery/datetimepicker/bootstrap-datetimepicker.js',
            ]);
            $req = Request::get()->toArray();

            if ($req['mypageFl'] == 'y') {
                $req['memNo'] = Session::get('member.memNo');
            }

            $boardList = new BoardList($req);
            $boardList->checkUseMobile();
            $getData = $boardList->getList(true, $boardList->cfg['bdListCnt']);
            $bdList['selectboxAddress'] = $boardList->getStoreAddress();
            $bdList['selectboxType'] = $boardList->getStoreType();
            $bdList['cfg'] = $boardList->cfg;

            $bdList['cnt'] = $getData['cnt'];
            $bdList['list'] = $getData['data'];

            //추가


            
            if ($req['bdId'] == 'event') {

                $boardConfig = new BoardConfig($req['bdId']);

                $cfg = $boardConfig->cfg;    //게시판 설정 세팅

                if (!$cfg) {
                    throw new \Exception(__('게시판 설정이 되지않았습니다.'));
                }

                if ($cfg['bdKind'] == Board::KIND_GALLERY) {

                    $listCount = $cfg['bdListColsCount'] * $cfg['bdListRowsCount'];
                    $cfg['bdListCount'] = $listCount;
                } else {
                    $listCount = $cfg['bdListCount'];
                }

                gd_isset($req['page'], 1); //값 없으면 1

                $offset = ($req['page'] - 1) * $listCount;

                //포함시킬 필드 b.는 보드
                $eventDisplayFl = array('b.eventDisplayFl');

                $buildQuery = BoardBuildQuery::init($req['bdId'], $boardConfig);

                //selectList(search,addWhereQuery,offset,limit,arrInclude(포함시킬 필드))
                $currentEvent = $buildQuery->selectList([" isDelete = 'n' "], ["  now() BETWEEN eventStart AND eventEnd "], $offset, $listCount, $eventDisplayFl);
                $endEvent = $buildQuery->selectList([" isDelete = 'n' "], ["  now() >  eventEnd "], $offset, $listCount, $eventDisplayFl);
            }
            
            foreach ($bdList['list'] as $key => $val) {

                $bdList['list'][$key]['address'] = str_replace("^|^", " ", $val['address']);

                if ($val['storeDisplayFl'] == 'y') {
                    unset($bdList['list'][$key]);
                }
                
                //추가
                
                if ($req['bdId'] == 'event') {

                    $bdList['list'][$key]['eventStart'] = substr($val['eventStart'], 0, 10);
                    $bdList['list'][$key]['eventEnd'] = substr($val['eventEnd'], 0, 10);

                    //진행중인 이벤트는 current
                    foreach ($currentEvent as $k => $v) {

                        if ($val['sno'] == $v['sno']) {

                            $bdList['list'][$key]['period'] = 'current';
                            $bdList['list'][$key]['eventDisplayFl'] = $v['eventDisplayFl'];
                        }
                    }

                    //끝난이벤트는 end
                    foreach ($endEvent as $k => $v) {

                        if ($val['sno'] == $v['sno']) {

                            $bdList['list'][$key]['period'] = 'end';
                            $bdList['list'][$key]['eventDisplayFl'] = $v['eventDisplayFl'];
                        }
                    }
                }
                
            }


            $bdList['noticeList'] = $getData['noticeData'];
            $bdList['categoryBox'] = $boardList->getCategoryBox($req['category'], ' onChange="this.form.submit();" ');
            $getData['pagination']->setBlockCount(Board::PAGINATION_MOBILE_BLOCK_COUNT);
            //$getData['pagination']->setBlockCount(Board::PAGINATION_MOBILE_COUNT);
            $getData['pagination']->setPage();
            $bdList['pagination'] = $getData['pagination']->getPage();
        } catch (RequiredLoginException $e) {
            throw new RedirectLoginException($e->getMessage());
        } catch (\Exception $e) {
            if ($req['gboard'] == 'y') {
                throw new AlertCloseException($e->getMessage());
            }
            throw new AlertBackException($e->getMessage());
        }

        if (gd_isset($req['noheader'], 'n') != 'n') {
            $this->getView()->setDefine('header', 'outline/_share_header.html');
            $this->getView()->setDefine('footer', 'outline/_share_footer.html');
        }
        $this->setData('bdList', $bdList);
        $this->setData('req', gd_htmlspecialchars($boardList->req));
        $this->setData('gPageName', __($bdList['cfg']['bdNm']));

        if ($req['bdId'] == 'store') {

            $path = 'board/skin/store/list_store.html';
        } else {
            $path = 'board/skin/' . $bdList['cfg']['themeId'] . '/list.html';
        }


        $this->getView()->setDefine('list', $path);
        $this->getView()->setDefine('tpl', 'board/list.html');
    }

}
