<style>
    #header, #footer, #menu, .gnbAnchor_wrap{display:none;}
    #container-wrap #content.row {margin:0}
    body, #container-wrap {min-width:100%;}
</style>

<div class="page-header js-affix affix-top">
    <h3>쿠폰 리스트</h3>
</div>

<table class="table table-rows promotion-coupon-list">
    <thead>
        <tr>
            <th>쿠폰코드</th><th>상태</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $value) { ?>
        <tr>
            <td><?=$value['code']?></td><td><?=$value['type']?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
