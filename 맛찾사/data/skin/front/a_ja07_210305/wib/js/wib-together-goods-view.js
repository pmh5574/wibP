$(document).ready(function () { 
    changeCnt();
    changePrice();

    // 함께하면 좋은상품 장바구니담기
    $('.rel_bg').click(function () {
        $('.rel_bg, .before_cart_wrap').hide();
        $('.before_cart_wrap .shop_cart_cont').html('');
    });
    
    $('.goods_list .rel_checkbox label').on('click', function(){
        
       $(this).toggleClass('check');
       
       changeCnt();
       changePrice();
//       addGoodsNo($(this));
       
    });
    
    $('.goods_list .r_cart_btn').on('click', function(){
        selectWibCart();
    });
    
    
    
    
    
});

function changeCnt()
{
    var cnt = 0;
    
    $('.goods_list .rel_checkbox label').each(function(){
        
       if($(this).hasClass('check')){
           cnt++;
       }
       
    });
    
    $('.goods_list .r_bottom_left strong').html(cnt);
}

function changePrice()
{
    var price = 0;
    

    
    $('.goods_list .rel_checkbox label').each(function(){
        
       if($(this).hasClass('check')){
           var orgPrice = $(this).closest('li').children('.item_cont').find('.item_money_box .item_price span').html();
           price += numOrgPrice(orgPrice);
           
       }
       
    });
    var commaPrice = addComma(price);
    
    $('.goods_list .r_bottom_right strong').html(commaPrice);
}

function numOrgPrice(orgPrice){
    orgPrice = orgPrice.replace("원","");
    orgPrice = orgPrice.replace(/,/gi,"");
    orgPrice = orgPrice.replace("-","");
    return Number(orgPrice);
}

function addComma(price){
    var strPrice = String(price);
    strPrice = strPrice.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return strPrice; 
}

function togetherCartIn(data)
{
    $.ajax({
        url : '/order/cart_ps.php',
        methos : 'post',
        data : data,
        async: false,
        cache : false,
        dataType: "json",
        success : function(e){
            if(e.error == 0){
                getCartList();
            }
        }
    });
}
                    
function selectWibCart(){
    
    var goodsNo = [];
    
    var chkCnt = $('.goods_list .rel_checkbox label.check').length;
    if (chkCnt == 0) {
        alert('선택된 상품이 없습니다.');
        return;
    }
    
    $('.goods_list .rel_checkbox label').each(function(){
        if($(this).hasClass("check")){
            
            goodsNo.push($(this).data('goodsno'));
            
        }
    });
    
    getList(goodsNo);
}

function getList(goodsNo){
    $.ajax({
        url : '../goods/goods_together_list.php',
        type : 'post',
        data : {
            type: 'goods',
            goodsNo: goodsNo
        },
        success : function(data){
           
           $('.rel_bg, .before_cart_wrap').show();
           $('.before_cart_wrap .shop_cart_cont').html(data);
        }
    });
}



