<form id="frmBundleType" name="frmBundleType" action="../policy/last_bundle.php" method="post">
    <input type="hidden" name="mode" value="<?php if($data) echo 'modify'; else echo 'save';?>">
    <input type="hidden" name="sno" value="<?= $sno ?>">
    <table class="table table-cols">
        <tbody>
            <tr>
                <th class="width-sm">묶음그룹명</th>
                <td>
                    <input type="text" name="method" class="form-control width-lg" value="<?=$data['method']?>">
                </td>
            </tr>
            <tr>
                <th class="width-sm">계산 방식</th>
                <td>
                    <div class="wib-bundle"><input type="radio" name="deliveryType" value="ROW" <?php if($data['deliveryType'] == 'ROW') echo 'checked'; ?>> 묶음 그룹에서 가장 작은 배송비로 부과</div>
                    <div><input type="radio" name="deliveryType" value="HIGH" <?php if($data['deliveryType'] == 'HIGH') echo 'checked'; ?>> 묶음 그룹에서 가장 큰 배송비로 부과</div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="goods-grie-bottom-area center">
        <input type="button" value="저장" class="btn btn-gray js-save">
        <input type="button" value="취소" class="btn btn-white js-close">
    </div>

    
</form>

<script>
$(function(){
    //값없으면 못넘기게 하려고 대충만들어둠
    $('.checks').on('click',function(){
        $('input[name="deliveryType"]').val();
    });

    $('.js-close').click(function(){
        $(document).off("keydown");

        layer_close();
    });

    $(".js-save").click(function(e){
        $("#frmBundleType").submit();
    });
});
    
    
</script>
<style>
    .wib-bundle{padding-bottom:5px;}
</style>