<form name="frmWrite" id="frmWrite" action="../goods/test_athlete_register.php" method="get">
    <div class="page-header js-affix">
        <h3>선수 리스트</h3>
        <input type="hidden" name="mode" value="save">
        <input type="submit" value="선수 등록" class="btn btn-red"/>
    </div>  
</form>

<form id="frmSearchGoods" name="frmSearchGoods" method="get" class="js-form-enter-submit">
	<div class="table-title gd-help-manual">선수 이름 검색</div>
	<div class="search-detail-box">
		<table class="table table-cols">
			<tr>
				<th>검색어</th>
				<td colspan="3">
					<div class="form-inline">
						<select class="form-control" id="key" name="key">
                            <option value="athlete" <?php if($key == "athlete") echo 'selected'; ?>>선수명</option>
                            <option value="brandNm" <?php if($key == "brandNm") echo 'selected'; ?>>브랜드</option>
                            <option value="brandCd" <?php if($key == "brandCd") echo 'selected'; ?>>브랜드 코드</option>
						</select>
						<input type="text" name="keyword" value="" class="form-control">
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="table-btn">
		<input type="submit" value="검색" class="btn btn-lg btn-black">
	</div>
</form>

<div class="table-responsive">
	<table class="table table-rows">
		<thead>
			<tr>				
				<th>번호</th>
                <th>브랜드 코드</th>
                <th>브랜드</th>
				<th>이미지</th>
				<th>선수 이름</th>
                <th>수정</th>
                <th>삭제</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($result as $key => $val) {	?>
				<tr>
					<td class="center number">
                        <?= $pcnt-- ?>
					</td>
                    <td class="center"><?= $val['brandCd']; ?></td>
                    <td class="center"><?= $val['brandNm'] ?></td>
					<td class="center">
                        <img src="../data/plus_review/1000000009/<?= $val['athleteUploadFileNm']; ?>" width="40" class="middle">
					</td>
					<td>
                        <a class="center"><?= $val['athlete']; ?></a>
                    </td>
					<td class="center padlr10">
						<a href="./test_athlete_register.php?mode=modify&sno=<?= $val['sno'] ?>" class="btn btn-white btn-sm">수정</a>
                    </td>
                    <td class="center padlr10">
						<a href="./test_athlete_register.php?mode=delete&sno=<?= $val['sno'] ?>" class="btn btn-white btn-sm">삭제</a>
					</td>
				</tr>
           <?php	}	?>
           
           

		</tbody>
    </table>
</div>
<div class="text-center">
    <?= $testpage ?>
</div>
<script type="text/javascript">
    // $(function(){
    //     function athleteModify(mode, sno){
    //         location.href='./test_athlete_register.php?mode=modify&sno=<?= $val['sno'] ?>';
    //     }

    //     function athleteDelete(mode, sno){
    //         location.href='./test_athlete_register.php?mode=modify&sno=<?= $val['sno'] ?>';
    //     }
    // });
</script>