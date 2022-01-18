(function ($) {
    var jQuery = $;
        $(document).ready(function(){
        
    
        // 상단 띠배너 닫기
        if ($.cookie('banToggle') == undefined) {
            var topBanH = $('.topbanner').height();
            var winHeight = $(window).height();
            $(".topbanner").show();
        } else if ($.cookie('banToggle') == '1') {
            $(".topbanner").hide();
            $('#wrap').css('top','0');
            $('#header-top').css('top','37px');
        } else if ($.cookie('banToggle') == '0') {
            $(".topbanner").show();
            $('#wrap').css('top','78px');
            $('#header-top').css('top','115px');
        }
        $(".topbanner .top-close").click(function () {
            $.cookie('banToggle', '1', {
                expires: 1,
                path: '/'
            });
            $(".topbanner").slideUp();
            $('#wrap').css('top','0');
            $('#header-top').css('top','37px');
            return false;
        });
        
        // 전체메뉴
        var burger = $('.all-menu');

        burger.each(function(index){
            var $this = $(this);

            $this.on('click', function(e){
                e.preventDefault();
                $(this).toggleClass('active');
                $('.product.nav-layer').toggleClass('on');
                $('#searchBarForm,.top-cart-wrap').hide();
            })
        });
        
        // 검색창
        $('#keyword').attr('placeholder','SEARCH');
        
        $('#header-top .h-right ul li.search').click(function(){
            $('#searchBarForm').slideToggle('fast');
            $('.top-cart-wrap').hide();
            $('.all-menu').removeClass('active');
            $('.product.nav-layer').removeClass('on');
        });
        
        // 장바구니
        $('#header-top .h-right ul li.cart').click(function(){
            $('.top-cart-wrap').slideToggle(200);
            $('#searchBarForm').hide();
            $('.all-menu').removeClass('active');
            $('.product.nav-layer').removeClass('on');
        });
        
        // 상단 장바구니 위치이동
        $('.top-cart').prependTo($('.top-cart-wrap'));
        
        // 상단 스크롤 이동
        $('a.btn-top').click(function (e) {
            $('html,body').animate({
                scrollTop: $('html, body').offset().top
            });
            e.preventDefault();
        });
        
        // today view
        $('.today-btn').click(function(){
            $('.recent-wrap').slideDown();
            $('.close-arrow').fadeIn();
            $(this).addClass('on');
            $(this).siblings().removeClass('on');
            $('.bottom-cart').hide();
        });
        
        // cart
        $('.cart-btn').click(function(){
            $('.bottom-cart').slideDown();
            $('.close-arrow').fadeIn();
            $(this).addClass('on');
            $(this).siblings().removeClass('on');
            $('.recent-wrap').hide();
        });
            
        // 하위메뉴 닫기
        $('.close-arrow').click(function(){
            $('.recent-wrap , .bottom-cart').slideUp();
            $('.close-arrow').fadeOut();
            $('.log-wrap li').removeClass('on');
        });
        
        // language
        $('.language').click(function(){
            $('.lang-wrap , .back-bg').fadeIn(200);
        });
        
        $('.back-bg').click(function(){
            $('.lang-wrap , .back-bg').fadeOut(200);
        });
            
        // member-info
        $('.member-close').click(function(){
            $('.member-info').hide();
        });
            
        // 주문서 날짜조회 이미지
        $('#order_search_btn').attr('src','/wib/img/icon/date_icon.png');
            

    });
})(addjQuery);
