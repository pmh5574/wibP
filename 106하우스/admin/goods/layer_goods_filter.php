<style>
    .goodsCateNm{width: 30px;height: 30px;}
</style>
    
<form id="frmFilter" name="frmFilter" action="goods_filter_ps.php" method="post" target="ifrmProcess">
    <input type="hidden" name="mode" value="save">
    <?php foreach($goodsNo as $goodsNoValue){ ?>
    <input type="hidden" name="goodsNo[]" value="<?= $goodsNoValue; ?>">
    <?php } ?>
    <div id="option">
        <table class='table table-cols'>
            <thead>
                <tr>
                    <th class='left width-md'>
                        3차 카테고리명
                    </th>
                    <th class='left' style="width:280px;">
                        필터명
                    </th>
                    <th class='left' style="width: 380px;">
                        필터값
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    if($category == '001002001' || $category == '001002002' || $category == '001002'){
                ?>
                    <tr>
                        <td rowspan='9'>
                            <?= $categoryName; ?>
                        </td>
                        <td rowspan='9'>
                            링 필터 값
                        </td>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='44'>44
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='46'>46
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='48'>48
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='50'>50
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='52'>52
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='54'>54
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='56'>56
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='58'>58
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='60'>60
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='62'>62
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value='64'>64
                        </td>
                    </tr>
                <?php
                    }else if($category == '001004001' || $category == '001004002' || $category == '001004003' || $category == '001004'){
                ?>
                    <tr>
                        <td rowspan='15'>
                            <?= $categoryName; ?>
                        </td>
                        <td rowspan='15'>
                            브레이슬릿 필터 값
                        </td>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="One Size">One Size
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="15cm">15cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="16cm">16cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="17cm">17cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="17.5cm">17.5cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="18cm">18cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="19cm">19cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="20cm">20cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="20.5cm">20.5cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="21cm">21cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="23cm">23cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="25cm">25cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="35cm">35cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="38cm">38cm
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" name="filter_size[]" value="41cm">41cm
                        </td>
                    </tr>
                    
                <?php 
                    }
                ?>
            </tbody>
        </table>
    </div>
    
    <div class="goods-grie-bottom-area center">
        <input type="submit" value="저장" class="btn btn-gray js-save">
        <input type="button" value="취소" class="btn btn-white js-close">
    </div>
</form>
<iframe name='ifrmProcess' src="/blank.php" width="100%" height="200" class='display-none'></iframe>

<script type="text/javascript">
    $(function(){
        $('.js-close').click(function(){
            layer_close();
        });
    });
    
</script>