<script type="text/javascript">
function goods_search_popup() {
    window.open('../share/popup_goods.php', 'popup_goods_search', 'width=1255, height=790, scrollbars=no');
}

function setAddGoods(resultJson) {
    var addGoodsCnt = resultJson.info.length;
    var addHtml = "";
    $.each(resultJson.info, function(key, val) {

        var stockText = "";
        // 상품 재고
        if (val.stockFl == 'n') {
            totalStock    = '∞';
        } else {
            totalStock    = val.totalStock;
        }

        if(val.soldOutFl =='y' || totalStock== 0) stockText = "품절";
        else stockText = "정상";

        addHtml += '<tr id="tbl_add_goods_'+val.goodsNo+'">';
        addHtml += '<input type="hidden" name="addGoodsNo[]" value="' + val.goodsNo + '" />';
        addHtml += '<td class="center">';
        addHtml += '<input type="checkbox" name="itemGoodsNo[]" id="layer_goods_'+val.goodsNo+'"  value="'+val.goodsNo+'"/></td>';
        addHtml += '<td class="center number addGoodsNumber_'+val.goodsNo+'">'+(addGoodsCnt)+'</td>';
        addHtml += '<td class="center">'+decodeURIComponent(val.image)+'</td>';
        addHtml += '<td>'+val.goodsNm;
        addHtml += '<div>'+decodeURIComponent(val.goodsIcon) +'</div></td>';
        addHtml += '<td class="center">'+val.goodsPrice+'</td>';
        addHtml += '<td class="center">' + val.scmNm + '</td>';
        addHtml += '<td class="center">'+totalStock+'</td>';
        addHtml += '<td class="center js-goodschoice-hide">' + val.regDt + '</td>';
        addHtml += '<td class="center">'+stockText+'</td>';
        addHtml += '</tr>';
        addGoodsCnt--;
    });

    $("#addGoodsList").html(addHtml);

    $("#addGoodsCnt").html(resultJson.info.length);
}

function resetAddGoodsList() {
    $("#addGoodsList").html('');
    $("#addGoodsCnt").html('0');
}

function frmAddGroupFormCheck() {

    if($("#groupName").val() == '') {
        alert("그룹명을 입력해주세요");
        return false;
    }

    if($("#addGoodsList tr").length < 1) {
        alert("그룹에 추가할 상품을 1개 이상 선택해주세요");
        return false;
    }
}
/*
function groupListUpdate() {

    $.ajax({
        url: './wb_goods_ps.php',
        data: {mode: 'ajaxGetGroupList'},
        dataType: 'json',
        type: 'post',
        success: function(response) {
            if(response && response.length > 0) {
                var html = '';
                for(var idx in response) {
                    html += '<tr>';
                    html += '    <td class="center"><input type="checkbox" name="groupNo[]" value="<?=$val['groupNo']?>"/></td>';
                    html += '    <td class="center"><?=$val['groupNo']?></td>';
                    html += '    <td class="hand"><?=$val['groupName']; ?></td>';
                    html += '    <td class="center"><?=$val['count']?>개</td>';
                    html += '</tr>';
                }
                $("#groupList").html('');
                $("#groupList").html(html);
            }
        }, 
        error: function(error) {
            alert(error.getMessage());
        }
    });
}
*/

function popupWindow(groupNo) {
    window.open('./wb_relation_group_view.php?groupNo='+groupNo, 'popup_relation_group', 'width=1000, height=790, scrollbars=no');
}
</script>

<div class="page-header js-affix">
    <h3><?=end($naviMenu->location); ?> </h3>
</div>

<div class="table-title gd-help-manual">
    연관상품 그룹 생성
</div>

<form id="frmAddGroup" name="frmAddGroup" action="./wb_goods_ps.php" method="post" class="js-form-enter-submit" onSubmit="return frmAddGroupFormCheck()" target="ifrmProcess">
<input type="hidden" name="mode" value="registerRelationGroup" />
    <div class="search-detail-box">
        <table class="table table-cols">
            <colgroup>
                <col class="width-sm"/>
                <col/>
            </colgroup>
            <tr>
                <th>그룹명</th>
                <td>
                    <input type="text" name="groupName" id="groupName" class="form-control"/>
                </td>
            </tr>
            <tr>
                <th>상품선택</th>
                <td>
                    <button type="button" class="btn btn-white" onclick="goods_search_popup();">상품추가</button>
                    <button type="button" class="btn btn-white" onclick="resetAddGoodsList();">초기화</button>
                    <div>
                        연관상품 개수 : <span id="addGoodsCnt">0</span>개
                        <table class="table table-rows">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="allCheck1" value="y" onClick="all_checkbox(this.id, 'itemGoodsNo');"></th>
                                    <th>번호</th>
                                    <th>이미지</th>
                                    <th>상품명</th>
                                    <th>판매가</th>
                                    <th>공급사</th>
                                    <th>재고</th>
                                    <th>등록일</th>
                                    <th>품절상태</th>
                                </tr>
                            </thead>
                            <tbody id="addGoodsList">
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>      
        </table>
    </div>

    <div class="table-btn">
        <input type="submit" value="등록" class="btn btn-lg btn-black">
    </div>

</form>


<div>
    <form id="frmList" action="" method="get" target="ifrmProcess" >
        <input type="hidden" name="mode" value="">
        <table class="table table-rows">
            <thead>
            <tr>
                <th class="width5p"><input type="checkbox"  class="js-checkall" data-target-name="sno"/></th>
                <th class="width5p">그룹번호</th>
                <th>그룹명</th>
                <th class="width500p">연관상품 이미지</th>
                <th class="width10p">연관상품</th>
            </tr>
            </thead>
            <tbody id="groupList">
            <?php
            if (gd_isset($groupList)) {

                foreach ($groupList as $key => $val) {

                    ?>

                    <tr>
                        <td class="center"><input type="checkbox" name="groupNo[]" value="<?=$val['groupNo']; ?>"/></td>
                        <td class="center"><?=$val['groupNo']?></td>
                        <td class="hand"><a href="javascript:popupWindow('<?=$val['groupNo']?>')"><?=$val['groupName']; ?></a></td>
                        <td class="center">
                            <?php
                                foreach($val['imageData'] as $idKey => $idVal) {
                            ?>
                                <?=gd_html_goods_image($idVal['goodsNo'], $idVal['imageName'], $idVal['imagePath'], $idVal['imageStorage'], 30, $idVal['goodsNm'], '_blank');?>
                            <?php
                                }
                            ?>
                        </td>
                        <td class="center"><?=$val['count']?>개</td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr>
                    <td class="center" colspan="4">검색된 정보가 없습니다.</td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>

        <div class="table-action">
            <div class="pull-left">
                <button type="button" class="btn btn-white checkDelete">선택 삭제</button>
            </div>
        </div>

    </form>


    <div class="center"></div>
</div>