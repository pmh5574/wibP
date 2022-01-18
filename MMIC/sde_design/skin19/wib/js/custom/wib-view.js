(function ($) {
    var jQuery = $;
        $(document).ready(function(){
          /*
        // 우측 고정
        $.fn.isInViewport = function() {
          var elementTop = $(this).offset().top;
          var elementBottom = elementTop + $(this).outerHeight();

          var viewportTop = $(window).scrollTop();
          var viewportBottom = viewportTop + $(window).height();

          return elementBottom > viewportTop && elementTop < viewportBottom;
        };

        $(window).on('resize scroll', function() {

           var top = $(window).height();
           var tab = $('.tab-menu').height();

           if($('#footer-wrap .ft-logo').isInViewport()){
                   //$('.detailArea').css({'position':'absolute','bottom':tab+20,'top':'auto'});
                   $('.detailArea').fadeOut('fast');
               } else {
                   //$('.detailArea').css({'position':'fixed','top':top/10});
                   $('.detailArea').fadeIn('fast');
               };

        });
           */
            
        // 좌측고정
        $('#fix-info ul li').on('click',function(){
            $(this).toggleClass('on');
            $(this).children('div').slideToggle('fast');
            //$(this).children('div').parent('li').siblings('li').children('div').hide();
        });
        

        // 회원등급혜택
        $('.xans-product-detail .infoArea td ul.discountMember li a img').attr('src','/wib/img/icon/member-img.png');

        // 기간할인 박스 숨김
        $('.xans-product-detail .infoArea td > span > ul.discountMember > li').each(function(){
            var memImg = $(this).find('img').attr('benefit');

            if(memImg.indexOf('DP') != -1){
                $(this).hide();
            }
        });

        // 옵션
        $('#product_option_id1 > option').text('SIZE');

        // 적립금 텍스트 노출
        $('#span_mileage_text').parents('.xans-record-').find('th').addClass('add-m');
        $('.add-m').siblings('td').addClass('add-m2');



        // 탭메뉴
        $('.tab-info').click(function(){
            $(this).addClass('on');
            $(this).siblings().removeClass('on');
            $('#prdReview, #prdQnA').hide();
            $('#prdInfo').show();
        });

        $('.tab-review').click(function(){
            $(this).addClass('on');
            $(this).siblings().removeClass('on');
            $('#prdInfo, #prdQnA').hide();
            $('#prdReview').show();
        });

        $('.tab-qa').click(function(){
            $(this).addClass('on');
            $(this).siblings().removeClass('on');
            $('#prdInfo, #prdReview').hide();
            $('#prdQnA').show();
        });


        // 총 상품금액
        $('#product_option_id1, .infoArea .xans-product-option .ec-product-button li').parents('.infoArea').find('#totalPrice').hide();
        $('#product_option_id1, .infoArea .xans-product-option .ec-product-button li').on('click',function(){
            $('#totalPrice').show();
        });

        $('#contents #totalProducts .option_products .option_product td .delete').hide();


        // 타임세일
        var deadLine = {};
        var trimBox = [];
        var monthList = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        // 상세 타임세일 AJAX
        var WibBenefitType = $('.ec-front-product-show-benefit-icon').attr('benefit');
        var WibiBenefitProductNo = $('.ec-front-product-show-benefit-icon').attr('product-no');

		String.prototype.replaceAll = function(org, dest) {
			return this.split(org).join(dest);
		}

        $.post('/exec/front/Product/Benefitinfo', 'benefit_type='+WibBenefitType+'&product_no=' + WibiBenefitProductNo, function(WibHtml) {

            var WibTimeSplit = WibHtml.split('</p><p>20');

			// 익스오류 문자열 변경 - => /
			var replaceDate = WibTimeSplit[1].substr(17,16).replaceAll("-","/");
            var endDate = new Date(replaceDate).getTime();

            setInterval(function() {
                timeSaless(endDate);
            },1000);

        });

        // 기간할인시 판매가 숨김
        $('#span_product_price_sale').parents('.infoarea-inner').find('#span_product_price_text').parents('tr').css('text-decoration','line-through');


        // 회원혜택 텍스트 삭제
        $('ul.discountMember li').html('');
        $('ul.discountMember li').append('<a href="#"><img class="newbenefit" product-no="986" benefit="MG" src="/wib/img/icon/member-img.png" alt="회원등급 할인혜택"></a>');
        $('ul.discountMember img.newbenefit').click(function() {

          $('ul.discountMember li > div.discount_layer').hide();

          if ($(this).parent().parent().has('div.discount_layer').length == 0) {
                var sBenefitType = $(this).attr('benefit');
                var oObj = $(this);
                var oHtml = $('<div class="member_sale" style="display:none;">');
                var iBenefitProductNo = $(this).attr('product-no');
                oHtml.addClass('ec-base-tooltip discount_layer');

                //회원등급관리의 등급할인인 경우 class추가
                if (sBenefitType == 'MG') {
                    oHtml.addClass('member_rating');
                }

                $(this).parent().parent().append(oHtml);
                $.post('/exec/front/Product/Benefitinfo', 'benefit_type='+sBenefitType+'&product_no=' + iBenefitProductNo, function(sHtml) {
                    oHtml.html(sHtml);

                    var _rightSplit = sHtml.split('right">');
                    var WibSplit = _rightSplit[1].split('<br />');

                    WibSplit[0] = WibSplit[0].replaceAll('&#36;','$');
                    $('ul.discountMember li .right').text(function(i,t){
                        return t.replace(WibSplit[0],'');
                    });
                    $('.member_sale').show();
                });

            } else {
                $(this).parent().parent().find('div.discount_layer').show();
            }
            return false;
        });



    });

    // 타임세일2
    function timeSaless(end) {

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

        $('#count').text(days + "D " + hours + ":" + minutes + ":" + seconds).parent('.time').addClass('on');

        // 남은시간 0일 때 동작 (작동해제)
        if (distance < 0) {
            clearInterval(timeSale);
            $("#count").text('EXPIRED');
        }
    }



})(addjQuery);
