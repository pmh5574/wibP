<?php
namespace Component\Board;

use App;
use Component\Member\Manager;

class BannedWords
{
    private $db;
    private $list;

    public function __construct()
    {
        $this->db = App::load('DB');
    }

    public function getList($isAll = true)
    {
        $list = [];
        $conds = "";
        if (!$isAll)
            $conds = " WHERE is_use='1'";

        if ($tmp = $this->db->query_fetch("SELECT * FROM wm_banned_words{$conds} ORDER BY idx")) {
            $tmp2 = [];
            foreach ($tmp as $t) {
                $tmp2[strlen($t['word'])][] = $t;
            }
            krsort($tmp2);
            $tmp3 = [];
            foreach ($tmp2 as $list) {
                foreach ($list as $t) {
                    $tmp3[] = $t;
                }
            }

            foreach ($tmp3 as $t) {
                $t['word'] = trim($t['word']);
                $t['converted_char'] = trim($t['converted_char']);
                $converted = "";
                $len = mb_strlen($t['word']);
                $start = 0;
                if ($t['notConvertCnt'] > 0 && $len > $t['notConvertCnt']) {
                    $converted = mb_strcut($t['word'], 0, $t['notConvertCnt'] * 3);
                    $start = $t['notConvertCnt'];
                }
                for ($i = $start; $i < $len; $i++) {
                    $converted .= $t['converted_char'];
                }

                $t['converted'] = $converted;
                $list[] = $t;
            }
        }
        $list2 = [];
        foreach ($list as $li) {
            $list2[$li['idx']] = $li;
        }
        
        $list = array_values($list2);
        return $list;
    }

    /* 전체 리스트 불러오기 */
    public function load()
    {
        $this->list = $this->getList(false);
    }

    /* 변환 */
    public function convert(&$word, $bdId)
    {
        if ($this->list && $word && $bdId) {
            $list = $tmp2 = [];
            $tmp = $this->list;
            foreach ($tmp as $t) {
                $tmp2[strlen($t['word'])][] = $t;
            }
            krsort($tmp2);

            foreach ($tmp2 as $k => $_list) {
                foreach ($_list as $li) {
                    $list[] = $li;
                }
            }

            foreach ($list as $li) {
                $li['board_id'] = trim($li['board_id']);
                if ($li['board_id']) {
                    $bdIds = explode(",", $li['board_id']);
                    if (!in_array($bdId, $bdIds))
                        return $word;
                }

                if (!($li['is_manager_show'] && Manager::isAdmin())) {
                    $word = str_replace($li['word'], $li['converted'], $word);
                }

            }
        }
    }

    /* 게시판 ID 추출 */
    public function getBoardList()
    {
        $list = [];
        if ($tmp = $this->db->query_fetch("SELECT bdId, bdNm FROM " . DB_BOARD . " ORDER BY sno desc"))
            $list = $tmp;


        return $list;
    }
}
