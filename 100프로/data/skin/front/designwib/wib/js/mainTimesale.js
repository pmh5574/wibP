/**
 * 메인 노출용 타임세일
 * 
 * # target => 타임세일 출력용 타겟 아이디이며 필요에 따라 수정해서 사용
 * # sno => 타임세일 고유넘버이며 target에 데이터 태그로 지정 ( data-sno )
 * ex) <div id="HTML출력용 아이디" data-sno="타임세일 고유넘버">
 */
var mainTimesale = {
    target: 'timesaleSno',
    sno: '',
    init: function () {
        var snoDiv = $('#timesaleSno');
        if (snoDiv.length > 0 && parseInt(snoDiv.data('sno')) > 0) {
            this.sno = snoDiv.data('sno');
            this.getAjax();
        } else {
            alert('타임세일 넘버를 확인하세요.');
        }
    },
    getAjax: function () {
        $.ajax({
            url: '/event/time_sale.php?sno=' + this.sno,
            success: function (result) {
                var list = mainTimesale.getList(result);
                var Time = mainTimesale.getTime(result);

                if (Time > 0) {
                    $('#' + mainTimesale.target).append(list);
                    mainTimesale.setTimer(Time);
                }


                // 슬라이드
                $(".time_sale #timesaleSno .goods_list_cont > div").addClass("time_prd swiper-container");
                $(".time_sale .goods_list_cont .time_prd > ul").addClass("swiper-wrapper");
                $(".time_sale .goods_list_cont .time_prd > ul > li").addClass("swiper-slide");
                $(".time_sale #timesaleSno .goods_list_cont > div").append("<div class='swiper-button-prev'></div>");
                $(".time_sale #timesaleSno .goods_list_cont > div").append("<div class='swiper-button-next'></div>");
                var Timeswiper = new Swiper('.time_sale .swiper-container', {
                    slidesPerView: 1.5,
                    spaceBetween: 127,
                    speed: 400,
                    loop: true,
                    centeredSlides: true,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                });

                var timeSaleBox = $('.time_box').html();
                $(".time_sale .goods_list_cont .time_prd ul li").each(function () {
                    $(this).find(".item_info_cont").append("<div class='time_box_new'>" + timeSaleBox + "</div>");
                });

            }
        });
    },
    getList: function (list) {
        var result = '';
        var data = list.split('<div class="goods_list_cont">');
        data = data[1].split('</ul>');
        result = '<div class="goods_list "><div class="goods_list_cont">' + data[0] + '</ul></div></div></div>';
        //result += '<div class="btn_goods_down_more"><button class="btn_goods_view_down_more" data-page="2">갓딜 상품 더보기</button></div>';

        return result;
    },
    getTime: function (data) {
        var Time = data.split("gd_dailyMissionTimer('");
        Time = Time[1].split("')");

        return Time[0];
    },
    setTimer: function (time) {

        var timeHtml = '<div class="time_box">';
        timeHtml += '<span class="timeDays" class="flip">00</span><span class="days">일</span>';
        timeHtml += '<span class="timeHour" class="flip">00</span><span class="style"></span>';
        timeHtml += '<span class="timeMin" class="flip">00</span><span class="style"></span>';
        timeHtml += '<span class="timeSec" class="flip">00</span>';
        timeHtml += '</div>';
        
        $('#' + mainTimesale.target).prepend(timeHtml);
        

        var timer = time;
        var days, hours, minutes, seconds;

        var interval = setInterval(function () {
            days = parseInt(timer / 86400, 10);
            hours = parseInt(((timer % 86400) / 3600), 10);
            minutes = parseInt(((timer % 3600) / 60), 10);
            seconds = parseInt(timer % 60, 10);

            $('.timeDays').text(days);
            $('.timeHour').text(hours);
            $('.timeMin').text(minutes);
            $('.timeSec').text(seconds);

            if (--timer < 0) {
                timer = 0;
                clearInterval(interval);
            }
        }, 1000);
    }
};

$(document).ready(function () {
    mainTimesale.init();
});


