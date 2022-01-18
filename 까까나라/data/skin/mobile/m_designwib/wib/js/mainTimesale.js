/**
 * 메인 노출용 타임세일
 * 
 * # target => 타임세일 출력용 타겟 아이디이며 필요에 따라 수정해서 사용
 * # sno => 타임세일 고유넘버이며 target에 데이터 태그로 지정 ( data-sno )
 * ex) <div id="HTML출력용 아이디" data-sno="타임세일 고유넘버">
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
                //var list = mainTimesale.getList(result);
                var Time = mainTimesale.getTime(result);
                
                if(Time > 0){
                    //$('#'+mainTimesale.target).append(list);
					$.post('/event/time_sale_ps.php', {'mode' : 'get_time_sale_list', 'displayType' : 11, 'sno' :mainTimesale.sno}, function (data) {
                        $('#'+mainTimesale.target).append(data);

                        // 타임세일 swiper
                        $('#'+mainTimesale.target+' .goods_list').addClass('swiper-container');
                        $('#'+mainTimesale.target+' .goods_list .goods_list_all').addClass('swiper-wrapper');
                        $('#'+mainTimesale.target+' .goods_list .goods_list_all > li').addClass('swiper-slide');
                        var timeSaleSwiper = new Swiper('#'+mainTimesale.target+' .swiper-container', {
                            slidesPerView: 'auto',
                            freeMode: true,
                            observer : true,
                            observeParents: true,
                        });

                        //이미지에 타임세일 시간 하나 더
                        $('#'+mainTimesale.target+' .img_box a').append('<div class="m_wib_timesale"><span></span>시간 남음</div>');
					});
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
        timeHtml += '<span id="timeHour">00</span>시';
        timeHtml += '<span id="timeMin">00</span>분';
        timeHtml += '<span id="timeSec">00</span>초';
        timeHtml += '</div>';
        
        $('#'+mainTimesale.target).prepend(timeHtml);
        
        var timer = time;
        var days,hours, minutes, seconds;

        var interval = setInterval(function(){
            days    = parseInt(timer / 86400, 10);
            hours   = parseInt(((timer % 86400 ) / 3600), 10) + (days * 24);
            minutes = parseInt(((timer % 3600 ) / 60), 10);
            seconds = parseInt(timer % 60, 10);
            
			$('#timeHour').text(hours);
            $('#timeMin').text(" : " + minutes);
            $('#timeSec').text(" : " + seconds);
         
            //추가
            $('.m_wib_timesale span').text(hours);

            if (--timer < 0) {
                timer = 0;
                clearInterval(interval);
            }
        }, 1000);

    },
    setEvent : function(){
        $('#'+mainTimesale.target).on('click','.js_option_layer', function(){
            var params = {
                type : $(this).data('type'),
                sno: $(this).data('sno'),
                goodsNo: $(this).data('goodsno'),
                mainSno : '44',
            };
        
            $('#popupOption').modal({
                remote: '../goods/layer_option.php',
                cache: false,
                params: params,
                type : 'POST',
                show: true
            });
        });
    }
};

$(document).ready(function(){
    mainTimesale.init();  
});




