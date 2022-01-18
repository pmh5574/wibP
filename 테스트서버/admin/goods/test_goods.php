<div class="page-header js-affix">
    <h3>test 상품관리</h3>   
</div>

<form id="frmSearchGoods" name="frmSearchGoods" method="get" class="js-form-enter-submit">
	<div class="table-title gd-help-manual">상품 검색</div>
	<div class="search-detail-box">
		<table class="table table-cols">
			<tr>
				<th>검색어</th>
				<td colspan="3">
					<div class="form-inline">
						<select class="form-control" id="key" name="key">
							<option value="goodsNm">상품명</option>
							<option value="goodsNo">상품코드</option>
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
				<th>
					<input type="checkbox" value="y" class="js-checkall" data-target-name="goodsNo">
				</th>
				<th>번호</th>
				<th>상품코드</th>
				<th>이미지</th>
				<th style="min-width: 300px !important;">상품명</th>
				<th>판매가</th>
				<th>공급사</th>
				<th style="min-width: 120px !important;">노출상태</th>
				<th style="min-width: 120px !important;">판매상태</th>
				<th>재고</th>
				<th>등록일/수정일</th>
                <th>수정</th>
                <th>할인율 보여주기</th>
			</tr>
		</thead>
		<tbody>
			<?php	foreach($aa as $key => $val) {	?>
				<tr>
					<td class="center"><input type="checkbox" name="" value="" /></td>
					<td class="center number">
						<?= $pcnt-- ?>
					</td>
					<td class="center number"><?= $val['goodsNo']; ?></td>
					<td class="width-2xs center"><img src="../data/goods/<?= $val['imagePath']; ?>t50_<?= $val['imageName']; ?>" width="40" alt="<?= $val['goodsNm']; ?>" title="<?= $val['goodsNm']; ?>" class="middle">
					</td>
					<td><a class="text-blue hand" onclick="#"><?= $val['goodsNm']; ?></a></td>
					<td class="center number text-nowrap">
						<div>
							<span class="font-num"><?= $val['goodsPrice']; ?>원</span>
						</div>
					</td>
						<td class="center number text-nowrap"></td>
					
					<td class="center">
						
						<br>
						
					</td>
					<td class="center">
						
						<br>
						
					</td>
					<td class="center"><?= $val['totalStock']; ?></td>
					<td class="center date">
						<?= $val['regDt']; ?>
						<br>
						<?= $val['modDt']; ?>
					</td>
					<td class="center padlr10">
						<a href="./goods_register.php?goodsNo=<?= $val['goodsNo'] ?>&page=1" class="btn btn-white btn-sm">수정</a>
                    </td>
                    <?php 
                            $this->db = \App::load('DB');
							$strSQL = "SELECT * FROM es_goods WHERE goodsNo = ".$val['goodsNo'];
                            $result=$this->db->query_fetch($strSQL,null); 
                            // print_r($result);
                    ?>
                    <td class="center">
                        <button class="btn btn-white btn-sm sale-on<?=$pcnt?>" onclick="saleon(<?=$pcnt?>,<?=$val['goodsNo']?>)"
                        <?php if($result[0]['goodsDiscountSee'] == 'y') echo 'style=display:none;' ?>
                        <?php if($result[0]['goodsDiscountFl'] == 'n') echo 'disabled' ?>>
                            OFF
                        </button>
                        <button class="btn btn-white btn-sm sale-off<?=$pcnt?>" onclick="saleoff(<?=$pcnt?>,<?=$val['goodsNo']?>)"
                        <?php if($result[0]['goodsDiscountSee'] == 'n' || $result[0]['goodsDiscountSee'] == null) echo 'style=display:none;' ?>
                        <?php if($result[0]['goodsDiscountFl'] == 'n') echo 'disabled' ?>>
                            ON
                        </button>
                    </td>   
				</tr>
           <?php	}	?>
           
           

		</tbody>
	</table>
</div>

<div class="text-center">
    <nav>
		<?= $testpage ?>    
    </nav>
</div>
<script>
    $(function(){
        $('.btn-lg').click(function(){
            location.href="http://www.naver.com";
        });
    });
    function saleon(pcnt,goodsNo){

        $.ajax({
            url : '/goods/test_goods.php',
            method : 'post',
            data : {'saleon' : 'on',
                    'goodsNo' : goodsNo 
                    },
            success : function(data){
                alert('할인율이 보여주기가 설정 됐습니다.');
                $('.sale-off'+pcnt).show();
                $('.sale-on'+pcnt).hide();
            }
        });
    }
    function saleoff(pcnt,goodsNo){
        $.ajax({
            url : '/goods/test_goods.php',
            method : 'post',
            data : {'saleon' : 'off',
                    'goodsNo' : goodsNo
                    },
            success : function(data){
                alert('할인율 보여주기가 취소 됐습니다.');
                $('.sale-off'+pcnt).hide();
                $('.sale-on'+pcnt).show();
            }
        });
    }
</script>
