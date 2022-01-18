<script type="text/javascript">
   $(document).ready(function () {
        var bdId = '<?=$req['bdId']?>';
        var bdSecretFl = '<?=$bdWrite['cfg']['bdSecretFl']?>';
        var mode = '<?=$mode?>';
        var flag = true;

        $('#frmWrite').find('[name=queryString]').val(getUrlVars());
        $('select[name=bdId]').bind('change', function () {
            var flag = true;
            $('#board-table').find('input[type=text]').each(function (index, item) {
                if ($(item).val() != '') {
                    flag = false;
                    return false;
                }
            })
            if (flag === false) {
                dialog_confirm('게시판 변경 시 입력된 정보가 초기화됩니다. <br> 변경하시겠습니까?', function (result) {
                    if (result) {
                        location.href = 'article_write.php?bdId=' + $('select[name=bdId]').val();
                    }
                    else {
                        $('select[name=bdId]').val(bdId);
                    }
                });
            }
            else {
                location.href = 'article_write.php?bdId=' + $('select[name=bdId]').val();
            }
        })
$("#frmWrite").validate({
            submitHandler: function (form) {
				
                form.target = 'ifrmProcess';
                form.submit();
            },
            // onclick: false, // <-- add this option
            rules: {
                bdId: 'required',
                storeTitle: 'required',

                storeSiDo: {selectcheck1: true},

				

				storeType: {selectcheck3: true},
				storePhoneNo2: {required : true,
								digits : true, },
				storePhoneNo3: {required : true,
								digits : true, },
				address: 'required'
				
                },
             
            messages: {
                bdId: {
                    required: '게시판 아이디를 선택해주세요.'
                },
                storeTitle: {
                    required: '제목을 입력해주세요.'
                },
				storePhoneNo1: {phonecheck: true
                },
				storePhoneNo2: {
                    required: '매장 전화번호를 입력해주세요',
					digits: '숫자만 입력해주세요'

                },
				storePhoneNo3: {
                required: '매장 전화번호를 입력해주세요',
				digits: '숫자만 입력해주세요'

                },
				address: {
                    required: '주소를 입력해주세요'
                }

            }

        });
		jQuery.validator.addMethod('selectcheck1', function (value) {
			return (value != '시/도');
		}, "시/도를 선택해주세요.");
		jQuery.validator.addMethod('selectcheck3', function (value) {
			return (value != '==매장선택==');
		}, "매장을 선택해주세요.");
		jQuery.validator.addMethod('phonecheck', function (value) {
			return (value != '0');
		}, "전화번호를 선택해주세요.");
	});

</script>
<form name="frmWrite" id="frmWrite" action="article_ps.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="queryString" value=""/>
    <input type="hidden" name="isAdmin" value="true"/>
    <div class="page-header js-affix">
        <h3><?php echo end($naviMenu->location); ?></h3>
        <div class="btn-group">
            
            <input type="button" value="목록" class="btn btn-white btn-icon-list" onclick="goList('<?= $adminList; ?>');"/>
           
            <input type="submit" value="저장하기" class="btn btn-red"/>
        </div>
    </div>
   
    <div class="table-title ">매장 <?= $mode ?></div>
    <input type="hidden" name="sno" value="<?= $req['sno'] ?>">
    <input type="hidden" name="mode" value="<?= $req['mode'] ?>">

    <table class="table table-cols" id="board-table">
        <colgroup>
            <col class="width10p"/>
            <col/>
        <tr>
            <th>게시판</th>
            <td>
        </colgroup>
        <?php
        if($req['mode'] == 'write' && gd_is_provider() === false) {?>
                <select name="bdId" id="bdId">
                    <?php foreach ($boardList as $data) { ?>
                        <option value="<?= $data['bdId'] ?>" <?php if ($req['bdId'] == $data['bdId']) echo 'selected' ?>><?= $data['bdNm'] ?>(<?= $data['bdId'] ?>)</option>
                    <?php } ?>
                </select>
        <?php  }
        else {?>
            <?= $bdWrite['cfg']['bdNm'].'('.$bdWrite['cfg']['bdId'].')' ?>
            <input type="hidden" name="bdId" value="<?= $bdWrite['cfg']['bdId'] ?>">
        <?php }?>
            </td>
        </tr>
        <tr>
            <th class="require">매장명</th>
            <td>
                <input type="text" name="storeTitle" id="storeTitle" class="form-control"
                       value="<?= gd_isset($bdWrite['data']['storeTitle']) ?>"
                       class="input_text width80p">
            </td>
        </tr>
        <tr>
            <th class="require">지역 설정</th>
            <td class="form-inline">
			<?php if($address) { ?> 
			<select class="form-control" id="storeSiDo" name="storeSiDo"  >
				<option>시/도 </option>
				<?php foreach($address as $key => $val){
						if($val['addrDepth']==1){
				?>
					<option value=<?=$val['addrName'] ?> <?php if ($bdWrite['data']['storeSiDo'] == $val['addrName']) echo 'selected' ?> > <?=$val['addrName'] ?> </option>		

			<?php  
						}
					}
				} ?>
			</select>
            </td>
        </tr>
        <tr>
            <th class="require">매장 선택</th>
            <td class="form-inline">
                <select class="form-control" id="storeType" name="storeType">
				<option>매장 선택</option>
				<option value="아울렛" <?php if ($bdWrite['data']['storeType'] == '아울렛') echo 'selected' ?> >아울렛</option>
				<option value="백화점" <?php if ($bdWrite['data']['storeType'] == '백화점') echo 'selected' ?> >백화점</option>
			</select>
               
            </td>
        </tr>
            <tr>
                <th class="require">전화번호</th>
                <td class="form-inline">
					<select name="storePhoneNo1">
						<option>전화번호 선택</option>
						<option value="02" <?php if ($bdWrite['data']['storePhoneNo1'] == '02') echo 'selected' ?> > 02</option>
						<option value="031" <?php if ($bdWrite['data']['storePhoneNo1'] == '031') echo 'selected' ?> >031</option>
						<option value="032" <?php if ($bdWrite['data']['storePhoneNo1'] == '033') echo 'selected' ?> >032</option>
						<option value="033" <?php if ($bdWrite['data']['storePhoneNo1'] == '033') echo 'selected' ?> >033</option>
						<option value="041" <?php if ($bdWrite['data']['storePhoneNo1'] == '041') echo 'selected' ?> >041</option>
						<option value="042" <?php if ($bdWrite['data']['storePhoneNo1'] == '042') echo 'selected' ?> >042</option>
						<option value="043" <?php if ($bdWrite['data']['storePhoneNo1'] == '043') echo 'selected' ?> >043</option>
						<option value="044" <?php if ($bdWrite['data']['storePhoneNo1'] == '044') echo 'selected' ?> >044</option>
						<option value="051" <?php if ($bdWrite['data']['storePhoneNo1'] == '051') echo 'selected' ?> >051</option>
						<option value="052" <?php if ($bdWrite['data']['storePhoneNo1'] == '052') echo 'selected' ?> >052</option>
						<option value="053" <?php if ($bdWrite['data']['storePhoneNo1'] == '053') echo 'selected' ?> >053</option>
						<option value="054" <?php if ($bdWrite['data']['storePhoneNo1'] == '054') echo 'selected' ?> >054</option>
						<option value="055" <?php if ($bdWrite['data']['storePhoneNo1'] == '055') echo 'selected' ?> >055</option>
						<option value="061" <?php if ($bdWrite['data']['storePhoneNo1'] == '061') echo 'selected' ?> >061</option>
						<option value="062" <?php if ($bdWrite['data']['storePhoneNo1'] == '062') echo 'selected' ?> >062</option>
						<option value="063" <?php if ($bdWrite['data']['storePhoneNo1'] == '063') echo 'selected' ?> >063</option>
						<option value="064" <?php if ($bdWrite['data']['storePhoneNo1'] == '064') echo 'selected' ?> >064</option>
					</select>
					<input type="text" name="storePhoneNo2" value="<?= gd_isset($bdWrite['data']['storePhoneNo2']) ?>" class="form-control" size=4 maxlength="4" />
					<input type="text" name="storePhoneNo3" value="<?= gd_isset($bdWrite['data']['storePhoneNo3']) ?>" class="form-control" size=4 maxlength="4" />
				</td>
            </tr>
           <tr>
                <th class="require">폐점여부</th>
                 <td>
					<label class="radio-inline">
						<input type="radio" name="storeDisplayFl" value="n" <?php if ($bdWrite['data']['storeDisplayFl']=='n' || empty($bdWrite['data']['storeDisplayFl'])) echo 'checked'; ?> />오픈중
					</label>
					<label class="radio-inline">
						<input type="radio" name="storeDisplayFl" value="y" <?php if ($bdWrite['data']['storeDisplayFl']=='y') echo 'checked'; ?>/>폐점
					</label>
				 </td>
            </tr>
            <tr>
                <th class="require">주소</th>
                 <td>
					<div class="form-inline">
						<input type="text" name="address" value="<?= gd_isset($bdWrite['data']['address']) ?>" class="form-control"/>
						<input type="text" name="addressSub" value="<?= gd_isset($bdWrite['data']['addressSub']) ?>" class="form-control"/> 
						<input type="button" onclick="postcode_search('zonecode', 'address', 'zipcode');" value="주소찾기" class="btn btn-gray btn-sm"/>
					</div>
				 </td>
            </tr>
    </table>
  
    <div class="text-center">
        <button class="btn btn-white" type="button" onclick="btnList('<?= $req['bdId'] ?>')">목록가기</button>
    </div>
</form>


