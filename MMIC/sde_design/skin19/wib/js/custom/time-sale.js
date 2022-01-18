(function () {
    $(function () {

        var deadLine = {};
        var trimBox = [];
        var monthList = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        
        // 상품리스트 반복
        $.each($('.discountPeriod'),function(i,value) {
            
            // 마감기간 시간 추출
            var time = $(this).find('.layerDiscountPeriod p:last-child').text().substr(19,16);
            console.log(time);
            // 추출한 시간 객체 담기
            deadLine["item" + i] = time; 
            
            // 1초마다 남은시간 실시간 노출 동작
            setInterval(function() {
                timeSale(trimBox[i],$(value));
            },1000);       
                      
        });

        // 년,월,일,시간,분 초단위로 환산하여 새로 담기
        for(key in deadLine) {
            
            var month = monthList[deadLine[key].substr(5,2)-1];
            var day = deadLine[key].substr(8,2);
            var year = deadLine[key].substr(0,4);
            var hour = deadLine[key].substr(11,2);
            var minute = deadLine[key].substr(14,2);                    
            var sortTime = month + ' ' + day + ', ' + year + ' ' + hour + ':' + minute;

            sortTime = new Date(sortTime).getTime();
            trimBox.push(sortTime);

        }

    });

	
    
    function timeSale(end,target) {

        var now = new Date().getTime();
        var distance = end - now;

        // 일,시간,분,초 환산
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // 원하는 영역에 노출
        if(!(hours>9)){
            hours = "0"+hours;
        }
        if(!(minutes>9)){
            minutes = "0"+minutes;
        }
        if(!(seconds>9)){
            seconds = "0"+seconds;
        }
        
        target.parents('.description').find('.time').find("#count").text(days + "D " + hours + ":" + minutes + ":" + seconds);

        // 남은시간 0일 때 동작 (작동해제)
        if (distance < 0) {
            clearInterval(timeSale);
            target.parents('.description').find('.time').find("#count").text('EXPIRED');
        }          
    }
    
    // 타임세일상품만 보이기
    $('.ec-base-product .spec li:nth-of-type(2)').addClass('timeOn');
    $('.timeOn').parents('.spec').siblings('.time').show();
    
    // 취소선
    $('.discount_price').parents('.spec').find('.xans-record-:nth-of-type(1)').css({'text-decoration':'line-through','color':'#000'});
    
})(jQuery);
