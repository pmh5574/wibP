(function () {
    $(function () {

        var deadLine = {};
        var trimBox = [];
        var monthList = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        
        // ��ǰ����Ʈ �ݺ�
        $.each($('.discountPeriod'),function(i,value) {
            
            // �����Ⱓ �ð� ����
            var time = $(this).find('.layerDiscountPeriod p:last-child').text().substr(19,16);
            console.log(time);
            // ������ �ð� ��ü ���
            deadLine["item" + i] = time; 
            
            // 1�ʸ��� �����ð� �ǽð� ���� ����
            setInterval(function() {
                timeSale(trimBox[i],$(value));
            },1000);       
                      
        });

        // ��,��,��,�ð�,�� �ʴ����� ȯ���Ͽ� ���� ���
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

        // ��,�ð�,��,�� ȯ��
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // ���ϴ� ������ ����
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

        // �����ð� 0�� �� ���� (�۵�����)
        if (distance < 0) {
            clearInterval(timeSale);
            target.parents('.description').find('.time').find("#count").text('EXPIRED');
        }          
    }
    
    // Ÿ�Ӽ��ϻ�ǰ�� ���̱�
    $('.ec-base-product .spec li:nth-of-type(2)').addClass('timeOn');
    $('.timeOn').parents('.spec').siblings('.time').show();
    
    // ��Ҽ�
    $('.discount_price').parents('.spec').find('.xans-record-:nth-of-type(1)').css({'text-decoration':'line-through','color':'#000'});
    
})(jQuery);
