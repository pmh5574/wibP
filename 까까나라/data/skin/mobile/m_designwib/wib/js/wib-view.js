$(function(){

	//관련상품
	$('.related_goods ul.list .goods_list').addClass('swiper-container');
	$('.related_goods ul.list .goods_list_all').addClass('swiper-wrapper');
	$('.related_goods ul.list .goods_prd_item11').addClass('swiper-slide');
	var relatedSwiper = new Swiper('.related_goods .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
    });

	//배송정보
	$('.info_box > p').click(function(){
		var target = $(this);
		if(target.hasClass('on')){
			target.removeClass('on');
			target.next().stop().slideUp(300);
		}else{
			target.addClass('on');
			target.next().stop().slideDown(300);
		}
	});

	//탭
	$('.tab_box li').click(function(){
		$(this).addClass("selected").siblings().removeClass("selected");
	});

	//구매버튼
	$('.detail_order_btn').click(function(){
		$("#layerDim").removeClass('dn');
		$('.detail_buy').css('z-index','350');
	});

	$('.detail_buy .detail_buy_top_btn .detail_open_close').click(function(){
		$("#layerDim").addClass('dn');
		$('.detail_buy').css('z-index','200');
	});

	

});

$(window).load(function(){
	//스크롤시
	var contTop = $('.detail_btm_tab_menu').offset().top;

    $(window).scroll(function(){
        var currentTop = parseInt($(this).scrollTop());
		if(currentTop > contTop){
			$('.detail_btm_tab_menu_box').addClass('fix');
		}else{
			$('.detail_btm_tab_menu_box').removeClass('fix');
		}

		//상세 top 위치
		$('.detail_cont').each(function(){
			var contTop = $(this).offset().top - 1;
			if(currentTop >= contTop){
				var idChk = $(this).attr('id');
				$('.detail_btm_tab_menu_box ul li').each(function(){
					var id = $(this).find('a').attr('href');
					id = id.split("#")[1];
					if(idChk == id){
						$(this).addClass("selected").siblings().removeClass("selected");
					}
				});
			}
		});
	});
});

