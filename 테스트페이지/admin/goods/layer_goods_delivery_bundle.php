<table class="table table-rows" id="deliveryBundle">
    <thead>
    <tr>
        <th>묶음그룹번호</th>
        <th>그룹명</th>
        <th>배송비 계산방식</th>
        <th>선택</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($result) {
        
        foreach ($result as $key => $value) {
            
    ?>
            <tr class="text-center">
                <td class="wib-delivery-sno"><?=$value['sno']?></td>
                <td class="wib-delivery-method"><input type="hidden" class="deliveryNm_<?=$value['sno']?>" value="<?=$value['method']?>"><?=$value['method']?></td>
                <td class="wib-delivery-deliveryType"><?=$value['deliveryType']?></td>
                <td><a class="btn btn-sm btn-white js-close" data-sno="<?=$value['sno']?>">선택</a></td>
            </tr>
    <?php 
        } 
    } else { 
    ?>
        <tr>
            <td colspan="14" class="no-data">
                배송비 조건이 없습니다.
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

<script>
    $(function(){
        $('.js-close').on('click',function(){
            var _sno = $(this).data('sno');
            var _deliveryNm = $('#deliveryBundle .deliveryNm_'+_sno).val();
            
            var resultJson = {
                'snoId' : _sno,
                'deliveryNm' : _deliveryNm
            };
            setDeliveryBundle(resultJson);
            $('div.bootstrap-dialog-close-button').click();
        });
    });

</script>
