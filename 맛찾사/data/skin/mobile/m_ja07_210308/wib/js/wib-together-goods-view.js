$(document).ready(function () { 
    changeCnt();
    changePrice();
    
    // 함께사면 좋은상품 팝업
    

    $('.r_pop_close').click(function () {
        $('.rel_cart_pop').hide();
        $('.rel_cart_pop').html('');
    });
    
    $('.list .rel_checkbox label').on('click', function(){
        
       $(this).toggleClass('check');
       
       changeCnt();
       changePrice();
//       addGoodsNo($(this));
       
    });
    
    $('.r_cart_btn').on('click', function(){
        selectWibCart();
    });
    
    
    
    
    
});

function changeCnt()
{
    var cnt = 0;
    
    $('.list .rel_checkbox label').each(function(){
        
       if($(this).hasClass('check')){
           cnt++;
       }
       
    });

    $('.rel_price .rel_cnt').html(cnt);
}

function changePrice()
{
    var price = 0;

    $('.list .rel_checkbox label').each(function(){
        
       if($(this).hasClass('check')){

           var orgPrice = $(this).closest('li').find('.c_price').text();
           
           price += numOrgPrice(orgPrice);
           
       }
       
    });

    var commaPrice = addComma(price);
    
    $('.rel_price strong').html(commaPrice);
}

function numOrgPrice(orgPrice){
    orgPrice = orgPrice.replace("원","");
    orgPrice = orgPrice.replace(/,/gi,"");
    return Number(orgPrice);
}

function addComma(price){
    var strPrice = String(price);
    strPrice = strPrice.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return strPrice;
}

                    
function selectWibCart(){
    
    var goodsNo = [];
    
    var chkCnt = $('.list .rel_checkbox label.check').length;
    if (chkCnt == 0) {
        alert('선택된 상품이 없습니다.');
        return;
    }
    
    $('.list .rel_checkbox label').each(function(){
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
            
           $('.rel_cart_pop').html(data);
           $('.rel_cart_pop').show();
           
        }
    });
}



