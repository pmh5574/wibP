<form id="companyNameForm" name="companyNameForm" action="./company_ps.php" method="post">
    <div class="page-header js-affix">
        <h3><?=end($naviMenu->location);?> </h3>
        <div class="btn-group">
            <input type="submit" value="저장" class="btn btn-red" />
        </div>
    </div>
    
    <div class="table-title">회사명 리스트</div>
    <div class="search-detail-box">
        <div class="table-action mgb0 mgt0">
            <div class="pull-left">
                <div class="btn-group">
                    <button type="button" class="btn btn-white btn-icon-plus" onclick="addCompany()">항목추가</button>
                </div>
            </div>
        </div>
    
        <table class="table table-rows" id="companyList" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th class="width-lg">번호</th>
                    <th>회사명</th>
                    <th class="width-lg">삭제</th>
                </tr>
            </thead>
            <tbody>
                
                    <?php 
                    if($data){
                        $p = 1;
                        foreach($data as $key => $value){
                    ?>
                            <tr>
                                <td class="center number">
                                    <?= $p++; ?>
                                </td>
                                <td class="center companyInput">
                                    <input type="text" name="companyName[]" value="<?= $value; ?>" class="form-control" style="height:30px;">
                                </td>
                                <td class="center">
                                    <input type="button" class="btn btn-white btn-icon-minus" onclick="delCompany(this)" value="삭제">
                                </td>
                            </tr>
                    <?php
                        }
                    }else{
                    ?>
                        <tr>
                            <td class="center number">
                                1
                            </td>
                            <td class="center">
                                <input type="text" name="companyName[]" value="" class="form-control" style="height:30px;">
                            </td>
                            <td class="center">
                                <input type="button" class="btn btn-white btn-icon-minus" onclick="delCompany(1)" value="삭제">
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
            </tbody>
        </table>
        
        <div class="table-action mgb0 mgt0">
            <div class="pull-left">
                <div class="btn-group">
                    <button type="button" class="btn btn-white btn-icon-plus" onclick="addCompany()">항목추가</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    
    //컬럼 추가
    function addCompany()
    {
        var length = $('#companyList tbody tr').length;
        
        var addLine = '';
        addLine += '<tr>';
        addLine += '<td class="center number">'+(length+1)+'</td>';
        addLine += '<td class="center"><input type="text" name="companyName[]" value="" class="form-control" style="height:30px;"></td>';
        addLine += '<td class="center"><input type="button" class="btn btn-white btn-icon-minus" onclick="delCompany(this)" value="삭제"></td>';
        addLine += '</tr>';
        
        $('#companyList tbody').append(addLine);
        
    }
    
    //컬럼 삭제
    function delCompany(obj)
    {
        var length = $('#companyList tbody tr').length;
        
        if(length == 1){
            alert('첫 번째 항목은 삭제가 안됩니다.');
            return false;
        }
        
        $(obj).closest('tr').remove();
        
        setSno();
    }
    
    //순서 재설정
    function setSno()
    {
        $('.number').each(function(i){
            $(this).html(i+1);
        });
    }
  
</script>