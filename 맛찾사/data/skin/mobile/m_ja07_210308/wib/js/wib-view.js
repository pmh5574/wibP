$(function(){
	if ($(".top-thumb .swiper-slide").length == 1)
	{
		$(".top-thumb .swiper-pagination").hide();
	}
	var mvSlider = new Swiper(".top-thumb .swiper-container", {
		// autoplay : { 
		// 	delay : 3000, 
		// 	loop : true,
		// },
		speed : 800,
		lazy : {
			loadPrevNext : true 
		},
		pagination: {
			el: ".top-thumb .swiper-pagination",
			type: "progressbar",
		  },
		// scrollbar: {
		// 	el: ".top-thumb .swiper-scrollbar",
		// },
		// on : {
		// 	slideChangeTransitionEnd : function(){
		// 		if (this.activeIndex == 1 || this.activeIndex == 2 || this.activeIndex == 3 || this.activeIndex == 4 || this.activeIndex == 5 || this.activeIndex == 6){
		// 			$(".mcon01 .swiper-scrollbar").addClass("black");
		// 		} else{
		// 			$(".mcon01 .swiper-scrollbar").removeClass("black");
		// 		}
		// 	}
		// }
	});
    
    
    // 탭메뉴
    $('.goods_view .detail_btm_tab_menu_box .detail_btm_tab_menu ul li').click(function(){
        $(this).addClass('selected');
        $(this).siblings().removeClass('selected');
    })
    
    // 탭 스크롤 픽스
	var tabBox = $('.goods_view .detail_btm_tab_menu_box').offset().top - 77;
    
	$(window).scroll(function(event){ 

		// 상단 고정
		var Top = $(this).scrollTop();
        var tab01 = $('#tab01').offset().top;
        var tab02 = $('#tab02').offset().top;
        var tab03 = $('#tab03').offset().top;
        
		if(Top > tabBox){
			$('.goods_view .detail_btm_tab_menu_box .detail_btm_tab_menu').addClass('fixed');
		}else{
			$('.goods_view .detail_btm_tab_menu_box .detail_btm_tab_menu').removeClass('fixed');
		}
        
        if(tab01 < Top + 10){
            $('#goodsDescription').addClass('selected');
            $('#goodsDescription').siblings().removeClass('selected');
        }
        
        if(tab02 < Top + 10){
            $('#detailReview').addClass('selected');
            $('#detailReview').siblings().removeClass('selected');
        }
        if(tab03 < Top + 10){
            $('#detailQna').addClass('selected');
            $('#detailQna').siblings().removeClass('selected');
        }
        
        
	}); 
    
    // 구매버튼
    $('.naver_btn .st_btn_buy').click(function(){
        $('.ly_buy_dn.detail_buy_btn').hide();
        $('#divAddPay').show();
    });
    
    $('.buy_btn .st_btn_buy , .cart_btn .st_btn_buy').click(function(){
        $('.ly_buy_dn.detail_buy_btn').show();
        $('#divAddPay, .st_btn_wrap').hide();
    });
    
    $('.st_buy_close').click(function(){
        $('.st_btn_wrap').show();
    });
    
    function fnMove(seq){
        var offset = $("#tab" + seq).offset();
        $('html, body').animate({scrollTop : offset.top}, 400);
    }
    
    
    // 리뷰 내용 클릭
    $('.review_detail').click(function(){
        $(this).parent('li').removeClass('selected');
        $(this).html('',);
    });
    
    
    
    
});