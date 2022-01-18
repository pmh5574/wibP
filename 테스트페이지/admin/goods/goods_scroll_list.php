<style>
    .wib-table{display: none;}
    .wib-table.on{display: block;}
</style>

<div class="page-header js-affix">
    <h3>상세 컬러, 이미지 그룹 관리</h3>
    <button class="btn btn-red-line" onclick="addList();" style="float: right; margin-top: -40px"/>등록</button>
</div>
    
<div class="table-title">컬러, 이미지 리스트</div>

<form name="frmWrite" id="frmWrite" action="./goods_scroll_ps.php" method="post" enctype="multipart/form-data" target="ifrmColorPorcess">
    <input type="hidden" name="mode" class="mode" value="save">

    <div class="table-responsive wib-table">

        <table class="table table-rows">
            <thead>
                <tr>
                    <th>번호</th>
                    <th>그룹명</th>
                    <th>컬러</th>
                    <th>이미지</th>
                    <th>저장</th>
                    <th>취소</th>
                </tr>
            </thead>

            <tbody>
                <tr class="set-add-list"></tr>
            </tbody>
        </table>
    </div>
</form>

<form name="frmList" id="frmList" action="./goods_scroll_ps.php" method="post" enctype="multipart/form-data" target="ifrmColorPorcess">
    <input type="hidden" name="mode" class="mode" value="delete">
    
    <div class="search-detail-box">
        <div class="table-action mgb0 mgt0">
            <div class="pull-left">
                <div class="btn-group">
                    <button type="button" class="btn btn-white" onclick="delScroll()">선택 삭제</button>
                </div>
            </div>
        </div>

        <table class="table table-rows">
            <thead>
                <tr>
                    <th class="width-2xs"><input type="checkbox" value="y" class="js-checkall" data-target-name="sno"></th>
                    <th class="width-2xs">번호</th>
                    <th>그룹명</th>
                    <th>컬러</th>
                    <th>이미지</th>
                    <th class="width-lg">수정</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if($data){

                    foreach($data as $key => $value){
                ?>
                        <tr>
                            <td class="center">
                                <input type="checkbox" name="sno[<?= $value['sno']; ?>]" data-sno="<?= $value['sno']; ?>" value="<?= $value['scrollImg']; ?>">
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
                            <td class="center">
                                <input type="button" class="btn btn-white " onclick="modScroll('<?= $value['sno']; ?>', '<?= $value['scrollName']; ?>', '<?= $value['scrollColor']; ?>', '<?= $value['scrollImg']; ?>', '<?= ($pcnt+1) ?>')" value="수정">
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

        <div class="table-action mgt0">
            <div class="pull-left">
                <div class="btn-group">
                    <button type="button" class="btn btn-white" onclick="delScroll()">선택 삭제</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="center">
        <?= $paging; ?>
    </div>
</form>
<iframe name="ifrmColorPorcess" src="/blank.php" class="display-none"></iframe>
<script>
    $(document).on('click', '.saveScrollBtn', function(e){
        e.preventDefault();
        
        dialog_confirm('선택한 컬러와 이미지를 저장 하시겠습니까?', function (result) {
            if (result) {
                $('#frmWrite input[name=\'mode\']').val('save');
                $('#frmWrite').submit();
            }
        });
    });
    
    $(document).on('click', '.modifyScrollBtn', function(e){
        e.preventDefault();
       
        dialog_confirm('선택한 컬러와 이미지를 수정 하시겠습니까?', function (result) {
            if (result) {
                $('#frmWrite input[name=\'mode\']').val('modify');
                $('#frmWrite').submit();
            }
        });
    });
    
    var i = 0;
    
    function addList()
    {
        if($('.set-add-list').text() != ''){
            alert('등록 또는 취소 후 다시 눌러주세요.');
            return false;
        }
        
        $('.wib-table').addClass('on');
        
        var addList = '';
        addList += '<td class="center number">1</td>';
        addList += '<td class="center"><input type="text" name="scrollName" value="" class="form-control" style="height:30px;"></td>';
        addList += '<td class="center"><input type="text" name="scrollColor" value="" class="form-control" style="height:30px;"></td>';
        addList += '<td><div id="uploadBox" class="form-inline"><input type="file" name="scrollImg"></div><div class="notice-info">이미지파일의 용량은 모두 합해서5MB까지만 등록할 수 있습니다.</div></td>';
        addList += '<td class="center"><input type="submit" value="등록" class="btn btn-red saveScrollBtn"></td>';
        addList += '<td class="center"><input type="submit" value="취소" class="btn btn-white" onclick="delLine(this)"></td>';
        
        $('.set-add-list').html(addList);
        
        init_file_style();

    }
    
    //취소
    function delLine(obj)
    {
        
        $(obj).closest('.set-add-list').html('');
        $('.wib-table').removeClass('on');

    }
    
    function delScroll()
    {
        var chkCnt = $('input[name*="sno"]:checked').length;
        
        if (chkCnt == 0) {
            alert('선택된 리스트가 없습니다.');
            return false;
        }
        
        var checkSno = [];
        $('input[name*="sno"]:checked').each(function(e){
            checkSno[e] = $(this).data('sno');
        });

        var inSno = [];
        <?php 
        
            $i = 0; 
            
            foreach($delCheck as $value){ 
        ?>
                inSno[<?=$i;?>] = <?= $value; ?>;
                <?php $i++; ?>
        <?php } ?>

        for(var q = 0; q < inSno.length; q++){

            for(var j = 0; j < checkSno.length; j++){

                if(inSno[q] == checkSno[j]){
                    alert('상품에 등록된 컬러, 이미지 그룹이 있습니다.<br />등록된 상품에 컬러, 이미지 그룹을 삭제 후 다시 삭제해주세요.');
                    return false;
                }
            }    
        }
        
        
        dialog_confirm('선택한 ' + chkCnt + '개의 컬러와 이미지 정말로 삭제하시겠습니까?', function (result) {
            if (result) {
                $('#frmList').submit();
            }
        });

    }
    
    function modScroll(sno, name, color, img, number)
    {
        var _html = '';
        _html += '<input type="hidden" name="sno" value="'+sno+'">';
        _html += '<input type="hidden" name="orgImg[]" value="'+img+'">';
        _html += '<td class="center">'+number+'</td>';
        _html += '<td class="center"><input type="text" name="scrollName" value="'+name+'" class="form-control" style="height:30px;"></td>';
        _html += '<td class="center"><input type="text" name="scrollColor" class="form-control" value="'+color+'" style="height:30px;" /></td>';
        _html += '<td><div id="uploadBox" class="form-inline"><input type="file" name="scrollImg" value="'+img+'"> 저장된 이미지 <img src="/data/goods/'+img+'" width="100;"></div><div class="notice-info">이미지파일의 용량은 모두 합해서5MB까지만 등록할 수 있습니다.</div><div class="notice-info">이미지를 교체하실 경우 이미지를 다시 등록해주세요.</div></td>';
        _html += '<td class="center"><input type="submit" value="저장" class="btn btn-red modifyScrollBtn"></td>';
        _html += '<td class="center"><input type="submit" value="취소" class="btn btn-white" onclick="delLine(this)"></td>';
        
        $('.wib-table').addClass('on');
        
        $('.set-add-list').html('');
        $('.set-add-list').html(_html);
        
        init_file_style();
        
        return false;
    }
    
    
    
    
  
</script>