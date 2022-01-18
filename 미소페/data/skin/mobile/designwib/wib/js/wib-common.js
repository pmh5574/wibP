$(function(){

	// 상단 검색창
	$("._header_search .layer_search_box dl dd .keyword_box input").attr("placeholder","검색어를 입력해주세요.");


	// 탑버튼
	$(".fix_btn .top_btn").click(function(){
		$("body,html").animate({
			scrollTop : 0
		});
	});


	// 인기검색어 순위
	$(".latest_search_list li.popular a").prepend("<span class='num'></span>");
	var rankNum = 1;
	$(".latest_search_list li.popular").each(function() {
		if(rankNum < 10) {
			$(this).find(".num").html(rankNum);
		} else{
			$(this).find(".num").html(rankNum);
		}
		rankNum++;
	});


	// 인기검색어 > 미소페 베스트 상품
	$(".sch_best_prd .item_wrap").addClass("swiper-container");
	$(".sch_best_prd .item_wrap .item_list").addClass("swiper-wrapper");
	$(".sch_best_prd .item_wrap .item_list > li").addClass("swiper-slide");
	$(".sch_best_prd .item_wrap .item_list").after("<div class='swiper-pagination'></div>");
	var swiper4 = new Swiper(".sch_best_prd .swiper-container", {
		slidesPerView: 2.8,
		spaceBetween: 10,
		freeMode: true,
		observer: true,
		observeParents: true,
		slidesOffsetAfter: 15,
		pagination: {
			el: '.swiper-pagination',
			type: 'progressbar',
		},
	});	

	
	// 하단 공지사항
	$(".notice_board ul").slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		vertical: true,
		autoplay: true,
		autoplaySpeed: 1000,
		speed: 1000,
	});


	// 상단 띠배너
	$('.top_ban .close').click(function() {
		$('.top_ban').slideUp();
		$.cookie('rQuick', 'rQuick',{expires : 1, path : '/'}); 
	});
	if($.cookie('rQuick') == 'rQuick'){
		$('.top_ban').hide();
	} else {
		$('.top_ban').show();
	}

});