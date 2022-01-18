/**
 * 메인 노출용 타임세일
 * 
 * # target => 타임세일 출력용 타겟 아이디이며 필요에 따라 수정해서 사용
 * # sno => 타임세일 고유넘버이며 target에 데이터 태그로 지정 ( data-sno )
 * ex) <div id="HTML출력용 아이디" data-sno="타임세일 고유넘버"></div>
 */
var mainTimesale = {
    target : 'timesaleSno',
    sno : '',
    init : function(){
        var snoDiv = $('#timesaleSno');
        if(snoDiv.length > 0 && parseInt(snoDiv.data('sno')) > 0){
            this.sno = snoDiv.data('sno');
            this.getAjax();
        }else{
            alert('타임세일 넘버를 확인하세요.');
        }
    },
    getAjax : function(){
        $.ajax({
            url : '/event/time_sale.php?sno='+this.sno,
            success : function(result){
                var list = mainTimesale.getList(result);
                var Time = mainTimesale.getTime(result);
                
                if(Time > 0){
                    $('#'+mainTimesale.target).append(list);
                    mainTimesale.setTimer(Time);
                    mainTimesale.setEvent();
                }
                
            }
        });
    },
    getList : function(list){
        var result = '';
        var data = list.split('<div class="goods_list_cont">');
        data = data[1].split('</ul>');
        result = '<div class="goods_list "><div class="goods_list_cont">'+data[0]+'</ul></div></div></div>';
        result += '<div class="btn_goods_down_more"><button class="btn_goods_view_down_more" data-page="2">갓딜 상품 더보기</button></div>';
        
        return result;
    },
    getTime : function(data){
        var Time = data.split("gd_dailyMissionTimer('");
        Time = Time[1].split("')");

        return Time[0];
    },
    setTimer : function(time){
        
        var timeHtml = '<div class="timeNum">';
        timeHtml += '<span id="timeDay">00</span>일';
        timeHtml += '<span id="timeHour">00</span>시';
        timeHtml += '<span id="timeMin">00</span>분';
        timeHtml += '<span id="timeSec">00</span>초';
        timeHtml += '</div>';
        
        $('#'+mainTimesale.target).prepend(timeHtml);
        
        var timer = time;
        var days,hours, minutes, seconds;

        var interval = setInterval(function(){
            days    = parseInt(timer / 86400, 10);
            hours   = parseInt(((timer % 86400 ) / 3600), 10);
            minutes = parseInt(((timer % 3600 ) / 60), 10);
            seconds = parseInt(timer % 60, 10);
            
            $('#timeDay').text(days);
            $('#timeHour').text(" : " + hours);
            $('#timeMin').text(" : " + minutes);
            $('#timeSec').text(" : " + seconds);
         
            if (--timer < 0) {
                timer = 0;
                clearInterval(interval);
            }
        }, 1000);
    },
    setEvent : function(){
        $('#'+mainTimesale.target+' .item_photo_box').on('click', '.btn_add_wish_', function(){
            
            var login = false;
            $('.login_menu .inline li a').each(function(){
                var text = $(this).attr('href')+'';
                if(text.indexOf('logout') > 0){
                    login = true;
                } 
            });
            if(!login){
                alert("로그인하셔야 본 서비스를 이용하실 수 있습니다.");
                document.location.href = "../member/login.php";
                return false;
            }
            
            
            var minOrderCnt = 1;
            if ($(this).data('min-order-cnt')) {
                minOrderCnt = parseInt($(this).data('min-order-cnt'));
                if (minOrderCnt > 1) {
                    alert(__('최소 %s개 이상 구매가능합니다', minOrderCnt));
                }
            }
            $('#frmWishTemplateView input[name=\'cartMode\']').val($(this).data('goods-no'));
            $('#frmWishTemplateView input[name=\'optionFl\']').val($(this).data('optionfl'));
            $('#frmWishTemplateView input[name=\'mode\']').val('wishIn');

            var params = $('#frmWishTemplateView').serialize();

            $.ajax({
                method: "POST",
                cache: false,
                url: '../mypage/wish_list_ps.php',
                data: params,
                success: function (data) {
                    // error 메시지 예외 처리용
                    if (!_.isUndefined(data.error) && data.error == 1) {
                        alert(data.message);
                        return false;
                    }


                    $("#addWishLayer").removeClass('dn');
                    $('#layerDim').removeClass('dn');
                    $("#addWishLayer").find('> div').center();
                },
                error: function (data) {
                    alert(data.message);
                }
            });

            return false;
            
        });
        
        $('#'+mainTimesale.target+' .item_photo_box').on('click', '.btn_add_cart_', function(){
            if($(this).data('mode') == 'cartIn') {
                var params = {
                    page: 'goods',
                    type: 'goods',
                    goodsNo: $(this).data('goods-no'),
                    mainSno : '',
                };

                $.ajax({
                    method: "POST",
                    cache: false,
                    url: "../goods/layer_option.php",
                    data: params,
                    success: function (data) {
                        
                        $('#optionViewLayer').empty().append(data);
                        $('#optionViewLayer').find('>div').center();
                    },
                    error: function (data) {
                        alert(data.message);
                    }
                });
            }
        });
    }
};

$(document).ready(function(){
    mainTimesale.init();
});


