<div class="table-title gd-help-manual">
    연관상품 그룹 수정
</div>

<table class="table table-cols">
    <colgroup>
        <col class="width-sm" />
        <col />
    </colgroup>
    <tr>
        <th>그룹명</th>
        <td><?=$groupInfo['groupName']?></td>
    </tr>
    <tr>
        <th>연결된 상품</th>
        <td>
            <table class="table table-rows">
                <tr>
                    <th class="center"><input type="checkbox" /></th>
                    <th class="center">이미지</th>
                    <th class="center">상품명</th>
                    <th class="center">판매가</th>
                </tr>
                <?php
                foreach($groupInfo['list'] as $key => $val) {
                ?>
                <tr>
                    <td class="center"><input type="checkbox"></td>
                    <td class="center"><?php echo gd_html_goods_image($val['goodsNo'], $val['imageName'], $val['imagePath'], $val['imageStorage'], 30, $val['goodsNm'], '_blank'); ?></td>
                    <td class="hand"><a class="text-blue hand js-goods-popup" data-goodsno="<?=$val['goodsNo']; ?>"><?=$val['goodsNm']?></a></td>
                    <td class="center"><?=gd_currency_display($val['goodsPrice'])?></td>
                </tr>
                <?php
                }
                ?>
            </table>
    
            <div class="table-action">  
                <div class="pull-left">
                    <button type="button" class="btn btn-white">선택 삭제</button>
                </div>
            </div>
        </td>
    </tr>
</table>
<script type="text/javascript">
$(function() {
    $( ".js-goods-popup" ).click(function() {
        goods_register_popup($(this).data('goodsno'));
    });
});
</script>