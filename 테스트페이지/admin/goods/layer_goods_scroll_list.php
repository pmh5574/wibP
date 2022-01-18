<form name="frmList" id="frmList" action="./goods_scroll_ps.php" method="post" enctype="multipart/form-data" target="ifrmColorPorcess">
    <input type="hidden" name="mode" class="mode" value="select">
    
    <div class="search-detail-box">

        <table class="table table-rows">
            <thead>
                <tr>
                    <th class="width5p"><input type="checkbox" value="y" class="js-checkall" data-target-name="sno"></th>
                    <th class="width5p">번호</th>
                    <th>그룹명</th>
                    <th>컬러</th>
                    <th>이미지</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($data){

                    foreach($data as $key => $value){
                ?>
                        <tr>
                            <td class="center">
                                <input type="checkbox" name="sno[]" value="<?= $value['sno']; ?>^|^<?= $value['scrollName']; ?>^|^<?= $value['scrollImg']; ?>^|^<?= $value['scrollColor']; ?>">
                            </td>
                            <td class="center number">
                                <?= $pcnt--; ?>
                            </td>
                            <td class="center">
                                <?= $value['scrollName']; ?>
                            </td>
                            <td class="center">
                                <span style="display: inline-block; width: 30px; height: 30px; vertical-align: middle; border: 1px solid <?=$value['scrollColor']; ?>; background: <?=$value['scrollColor']; ?>; border-radius: 400px;"></span>
                            </td>
                            <td class="center">
                                <img src="/data/goods/<?= $value['scrollImg']; ?>" width="40;">
                            </td>
                        </tr>
                <?php

                    }
                }else{
                ?>
                    <tr>
                        <td class="center" colspan="5">정보가 없습니다.</td>
                    </tr>

                <?php
                }
                ?>
            </tbody>
        </table>


    </div>
    
    <div class="center">
        <?= $paging; ?>
    </div>
    <div class="text-center">
        <input type="button" value="확인" class="btn btn-lg btn-black" onclick="select_scroll_list();">
    </div>
</form>
<iframe name="ifrmColorPorcess" src="/blank.php" class="display-none"></iframe>
<script type="text/javascript">
    function layer_goods_scroll_list(page)
    {
        var parameters = {
            'page' : page
        };
        
        $.get('.layer_goods_scroll_list.php', parameters, function(data){
           $('#layerGoodsScrollList').html(data); 
        });
    }
    
    function select_scroll_list()
    {
        var chkCnt = $('input[name*="sno"]:checked').length;
        var orgCnt = $('#wibGoodsScrollList tbody tr').length;

        if(chkCnt == 0){
            alert('선택된 리스트가 없습니다.');
            return false;
        }else if(chkCnt > 6){
            alert('최대 선택 가능한 개수는 6개 입니다.');
            return false;
        }else if(chkCnt+orgCnt > 6){
            alert('최대 등록 개수는 6개 입니다. 기존의 그룹을 삭제 후 등록해 주세요.');
            return false;
        }
        
        var resultJson = [];
        var listCnt = 0;
        
        $('input[name*="sno"]:checked').each(function(){
            var firstVal = $(this).val().split('^|^');
            var sno = firstVal[0];
            var scrollName = firstVal[1];
            var scrollImg = firstVal[2];
            var scrollColor = firstVal[3];
            
            if($('#wibGoodsScrollList' + sno).length == 0){
                resultJson.push({"sno" : sno, "scrollName" : scrollName, "scrollImg" : scrollImg, "scrollColor" : scrollColor});
                listCnt++;
            }
            
        });
        
        if(listCnt > 0){
            set_scroll_list(resultJson);
            
            $('div.bootstrap-dialog-close-button').click();
        }else{
            alert('동일한 컬러, 이미지 그룹이 이미 존재합니다.');
        }
        
    }
    
    function set_scroll_list(data)
    {
        $('#wibGoodsScrollList').show();
        
        var addHtml = '';
        $.each(data, function(key, val){
            var complied = _.template($('#goodsScrollTemplate').html());
            addHtml += complied({
                sno: val.sno,
                scrollName: val.scrollName,
                scrollImg: val.scrollImg,
                scrollColor: val.scrollColor
            });
        });
        
        $('#wibGoodsScrollList tbody').append(addHtml);
    }
</script>
<script type="text/html" id="goodsScrollTemplate">
    <tr id="wibGoodsScrollList<%= sno %>">
        <td class="center">
            <%= scrollName %>
            <input type="hidden" name="scrollSno[]" value="<%= sno %>" class="form-control" readonly="readonly">
        </td>
        <td class="center">
            <span style="display: inline-block; width: 30px; height: 30px; vertical-align: middle; border: 1px solid <%= scrollColor %>; background: <%= scrollColor %>; border-radius: 400px;"></span>
        </td>
        <td class="center">
            <img src="/data/goods/<%= scrollImg %>" width="40;">
        </td>
        <td class="center">
            <input type="button" value="삭제" class="btn btn-white btn-icon-minus del-groupSno">
        </td>
    </tr>
</script>