<?php 
    if($result){
        foreach($result as $key => $val) {
?>
    <form name="frmWrite" id="frmWrite" action="../goods/test_athlete_ps.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="mode" value="modify">
        <input type="hidden" name="sno" value="<?= $sno ?>">
        <input type="hidden" name="" value="">
        <div class="page-header js-affix">
            <h3>선수 등록</h3>
            <input type="submit" value="저장하기" class="btn btn-red"/>
        </div>
        <div class="table-title gd-help-manual">선수 등록</div>
        

        <table class="table table-cols">
            <colgroup>
                <col class="width-md"/>
                <col/>
            </colgroup>

            <tr>
                <th>브랜드</th>
                <td>
                    
                    <select class="chosen-select" name="brandCd">
                        <option value="001" <?php if($val['brandCd'] == "001") echo "selected"; ?>>NIKE</option>
                        <option value="003" <?php if($val['brandCd'] == "003") echo "selected"; ?>>ADIDAS</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>선수 이름</th>
                <td>
                    <input type="text" name="athlete" value="<?= $val['athlete'] ?>" class="form-control width-md js-maxlength" maxlength="30"/>
                </td>
            </tr>
            <tr>
                <th>선수 사진</th>
                <td>
                    <ul class="pdl0" id="uploadBox">
                        <li class="form-inline mgb5">
                            <input type="file" name="athleteUploadFileNm"> <br>
                           <?php if($val['athleteUploadFileNm']){ ?> 
                            저장된 이미지 <img src="../data/plus_review/1000000009/<?= $val['athleteUploadFileNm']; ?>" width="100" class="middle">
                           <?php } ?>
                        </li>
                    </ul>
                    <div class="notice-info">
                        이미지를 교체하고 싶으면 이미지를 다시 등록해주세요.
                    </div>
                </td>
            </tr>
        </table>
    </form>
<?php 
        } 
    }else {
?>
        <form name="frmWrite" id="frmWrite" action="../goods/test_athlete_ps.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="mode" value="save">
            <div class="page-header js-affix">
                <h3>선수 등록</h3>
                <input type="submit" value="저장하기" class="btn btn-red"/>
            </div>
            <div class="table-title gd-help-manual">선수 등록</div>
            

            <table class="table table-cols">
                <colgroup>
                    <col class="width-md"/>
                    <col/>
                </colgroup>
                
                
                <tr>
                    <th>브랜드</th>
                    <td>
                        
                        <select class="chosen-select" name="brandCd">
                            <option value="001">NIKE</option>
                            <option value="003">ADIDAS</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>선수 이름</th>
                    <td>
                        <input type="text" name="athlete" value="<?= $val['athlete'] ?>" class="form-control width-md js-maxlength" maxlength="30"/>
                    </td>
                </tr>
                <tr>
                    <th>선수 사진</th>
                    <td>
                        <ul class="pdl0" id="uploadBox">
                            <li class="form-inline mgb5">
                                <input type="file" name="athleteUploadFileNm"> <br>
                            <?php if($val['athleteUploadFileNm']){ ?> 
                                저장된 이미지 <img src="../data/plus_review/1000000009/<?= $val['athleteUploadFileNm']; ?>" width="100" class="middle">
                            <?php } ?>
                            </li>
                        </ul>
                        <div class="notice-info">
                            이미지를 등록해주세요.
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    <?php } ?>
