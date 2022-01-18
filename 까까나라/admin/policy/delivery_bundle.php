<div class="page-header js-affix">
    <h3><?php echo end($naviMenu->location);?></h3>
    <div class="btn-group">
        <a class="btn btn-red-line js-bundle-delivery">배송비묶음조건 등록</a>
    </div>
</div>

<form id="frmDeliveryList" action="../policy/layer_delivery_bundle.php" method="post">
    <input type="hidden" name="mode" value="delete"/>
    <table class="table table-rows">
        <thead>
        <tr>
            <th><input type="checkbox" class="js-checkall" data-target-name="deliverChk[]" /></th>
            <th>묶음그룹번호</th>
            <th>그룹명</th>
            <th>배송비 계산방식</th>
            
            <th>수정</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (empty($result) === false && is_array($result)) {
            
            foreach ($result as $value) {
                
        ?>
        <tr class="text-center">
            
            <td><input type="checkbox" name="deliverChk[]" value="<?=$value['sno']?>" data-sno="<?=$value['sno']?>"/></td>
            <td><?=$value['sno']?></td>
            <td><?=$value['method']?></td>
            <td><?=$value['deliveryType']?></td>
            
            <td><a class="btn btn-sm btn-white js-bundle-delivery-modify" data-sno="<?=$value['sno']?>">수정</a></td>
        </tr>
        <?php $sortNo++; } } else { ?>
        <tr>
            <td colspan="14" class="no-data">
                배송비 조건이 없습니다.
            </td>
        </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="table-action">
        <div class="pull-left">
            <button type="button" class="btn btn-white js-delete-delivery">선택삭제</button>
        </div>
    </div>
</form>


<div class="layer-delivery-bundle" style="display:none;">
    
</div>
<script type="text/javascript">
    <!--
    $(document).ready(function () {

        //묶음배송설정
        $('.js-bundle-delivery').click(function(){

            $.ajax({
                url:'./layer_delivery_bundle.php',
                type: 'post',
                data: {'mode' : 'save',

                },
                success : function(data){
                    var layerForm = '<div id="chargeDelivery">' + data + '</div>';
                    BootstrapDialog.show({
                        title: '배송비묶음그룹',
                        message: $(layerForm),
                        size: 'wide',
                        closable: true,
                       
                    });
                }   

            });

        });

       

        //묶음배송수정
        $('.js-bundle-delivery-modify').click(function(){
            
            var sno = $(this).data('sno');
            
            $.ajax({
                url:'./layer_delivery_bundle.php',
                type: 'post',
                data: {'mode' : 'modify',
                       'sno' : sno
                },
                success : function(data){
                    var layerForm = '<div id="chargeDelivery">' + data + '</div>';
                    BootstrapDialog.show({
                        title: '배송비묶음그룹',
                        message: $(layerForm),
                        closable: true,
                        onhidden: function (dialog) {

                        }
                    });
                }   

            });

        });

        //묶음배송삭제
        $('.js-delete-delivery').click(function(e){
            $('#frmDeliveryList').submit();
        });

        // 배송리스트 폼 체크
        $('#frmDeliveryList').validate({
            dialog: false,
            submitHandler: function (form) {
                BootstrapDialog.confirm({
                    title: "배송비조건 삭제 확인",
                    message: '선택한 ' + $('input[name="deliverChk[]"]:checked').length + '개 배송비조건을 정말로 삭제하시겠습니까?<br>삭제 시 정보는 복구 되지 않습니다.',
                    btnOKLabel: "삭제",
                    btnCancelLabel: "취소",
                    callback: function (result) {
                        if (result) {
                            form.target = 'ifrmProcess';
                            form.submit();
                        }
                    }
                });
            },
            rules: {
                'deliverChk[]': 'required'
            },
            messages: {
                'deliverChk[]': {
                    required: "하나 이상 체크하세요.",
                }
            }
        });
        
    });
    //-->
</script>
