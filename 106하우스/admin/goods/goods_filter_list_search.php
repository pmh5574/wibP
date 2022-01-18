<?php if($goodsColorList) {?>
    <script>
        <!--
        function selectColor(val,target,name) {
            var color = $(val).data('color');
            var title = $(val).data('content');

            if($(target+" #"+name+color).length == '0') {
                var addHtml = "<div id='"+name+ color + "' class='btn-group btn-group-xs'>";
                addHtml += "<input type='hidden' name='goodsColor[]' value='" + color + "'>";
                addHtml += "<button type='button' class='btn btn-default js-popover' data-html='true' data-content='"+title+"' data-placement='bottom' style='background:#" + color + ";'>&nbsp;&nbsp;&nbsp;</button>";
                addHtml += "<button type='button' class='btn btn-icon-delete' data-toggle='delete' data-target='#"+name+ color + "'>삭제</button></div>";
            }
            $(target+" #selectColorLayer").append(addHtml);

            $('.js-popover').popover({trigger: 'hover',container: '#content',});
        }
        //-->
        
    </script>
<?php } ?>


<form id="frmSearchGoods" name="frmSearchGoods" method="get" class="js-form-enter-submit">
    <div class="table-title gd-help-manual">
        <?php if($search['delFl'] =='y') { echo "삭제 "; } ?>상품 검색
        <?php if(empty($searchConfigButton) && $searchConfigButton != 'hide'){?>
        
        <?php }?>
    </div>

    <div class="search-detail-box">
        <input type="hidden" name="detailSearch" value="<?=$search['detailSearch']; ?>"/>
        <input type="hidden" name="delFl" value="<?=$search['delFl']; ?>"/>
        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col>
                <col class="width-md"/>
                <col/>
            </colgroup>
            <tbody>
            <?php if(gd_use_provider() === true) { ?>
            <?php if(gd_is_provider() === false) { ?>
            <tr>
                <th>공급사 구분</th>
                <td colspan="3">
                    <?php if($mode['page']!='delivery') { ?>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="all" <?=gd_isset($checked['scmFl']['all']); ?> onclick="$('#scmLayer').html('');"/>전체
                    </label>
                    <?php } ?>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="n" <?=gd_isset($checked['scmFl']['n']); ?> onclick="$('#scmLayer').html('')" ;/>본사
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="scmFl" value="y" <?=gd_isset($checked['scmFl']['y']); ?> onclick="layer_register('scm', 'checkbox')"/>공급사
                    </label>
                    <label>
                        <button type="button" class="btn btn-sm btn-gray" onclick="layer_register('scm','checkbox')">공급사 선택</button>
                    </label>

                    <div id="scmLayer" class="selected-btn-group <?=$search['scmFl'] == 'y' && !empty($search['scmNo']) ? 'active' : ''?>">
                        <h5>선택된 공급사 : </h5>
                        <?php if ($search['scmFl'] == 'y') {
                            foreach ($search['scmNo'] as $k => $v) { ?>
                                <span id="info_scm_<?= $v ?>" class="btn-group btn-group-xs">
                                <input type="hidden" name="scmNo[]" value="<?= $v ?>"/>
                                <input type="hidden" name="scmNoNm[]" value="<?= $search['scmNoNm'][$k] ?>"/>
                                <span class="btn"><?= $search['scmNoNm'][$k] ?></span>
                                <button type="button" class="btn btn-icon-delete" data-toggle="delete" data-target="#info_scm_<?= $v ?>">삭제</button>
                                </span>
                            <?php }
                        } ?>
                    </div>
                </td>
            </tr>
            <?php } ?>
            <?php } ?>
            <tr>
                <th>검색어</th>
                <td colspan="3">
                    <div class="form-inline">
                        <select class="form-control " id="key" name="key">
                            <option value="goodsNm" selected="selected">상품명</option>
                            <option value="goodsNo">상품코드</option>
                        </select>
                        
                        <input type="text" name="keyword" value="<?=$search['keyword']; ?>" class="form-control"/>
                    </div>
                </td>
            </tr>
            <tr>
                <th>기간검색</th>
                <td colspan="3">
                    <div class="form-inline">
                        <select name="searchDateFl" class="form-control">
                            <?php if($search['delFl'] =='y') { ?>
                                <option value="delDt" <?=gd_isset($selected['searchDateFl']['delDt']); ?>>삭제일</option>
                            <?php } ?>
                            <option value="regDt" <?=gd_isset($selected['searchDateFl']['regDt']); ?>>등록일</option>
                            <option value="modDt" <?=gd_isset($selected['searchDateFl']['modDt']); ?>>수정일</option>
                        </select>

                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][0]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        ~
                        <div class="input-group js-datepicker">
                            <input type="text" class="form-control width-xs" name="searchDate[]" value="<?=$search['searchDate'][1]; ?>" />
                            <span class="input-group-addon"><span class="btn-icon-calendar"></span></span>
                        </div>
                        <?= gd_search_date($search['searchPeriod']) ?>
                    </div>
                </td>
            </tr>
            </tbody>
            <tbody class="filter-js-search-detail" style="display: table-row-group !important;">
                    <tr>
                <th>카테고리</th>
                <td class="contents" colspan="3">
                    <div class="form-inline">
                        <?=$cate->getMultiCategoryBox(null, $search['cateGoods']); ?>
                    </div>
                </td>
            </tr>
            
            </tbody>
        </table>
        
    </div>

    <div class="table-btn">
        <input type="submit" value="검색" class="btn btn-lg btn-black">
    </div>


    <div class="table-header">
        <div class="pull-left">
            검색 <strong><?=number_format($page->recode['total']);?></strong>개 /
            전체 <strong><?=number_format($page->recode['amount']);?></strong>개
            <?php
            if($stateCount) {
            ?>
            | 품절 <?=number_format($stateCount['soldOutCnt']);?>개
            | 노출 : PC <?=number_format($stateCount['pcDisplayCnt']);?>개 / 모바일 <?=number_format($stateCount['mobileDisplayCnt']);?>개
            | 미노출 : PC <?=number_format($stateCount['pcNoDisplayCnt']);?>개 / 모바일 <?=number_format($stateCount['mobileNoDisplayCnt']);?>개
            <?php
            }
            ?>
        </div>
        <div class="pull-right form-inline">
            <?=gd_select_box('sort', 'sort', $search['sortList'], null, $search['sort'], null); ?>
            <?=gd_select_box('pageNum', 'pageNum', gd_array_change_key_value([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 200, 300, 500]), '개 보기', Request::get()->get('pageNum'), null); ?>
            <?php
            if($goodsBatchStockAdminGridMode == 'goods_batch_stock_list'){
                ?><button type="button" class="js-layer-register btn btn-sm btn-black" style="height: 27px !important;" data-type="goods_batch_stock_grid_config" data-goods-batch-stock-grid-mode="<?=$goodsBatchStockAdminGridMode?>" data-title="조회항목 설정">조회항목설정</button><?php
            }
            ?>
        </div>
    </div>
    <input type="hidden" name="searchFl" value="y">
    <input type="hidden" name="applyPath" value="<?=gd_php_self()?>">
</form>
<script>
function brand_del(){
    $('input[name=brandCdNm]').val('');
}
</script>

