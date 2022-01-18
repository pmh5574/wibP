$(function(){

	$('#wrap').css("opacity","1");

	$(window).on('resize', function(){
		$('#wrap').css("opacity","1");
	});	

	//상단 카테고리
	$('#header .header_all .h_bottom .cate li').addClass('swiper-slide');
	var hCate = new Swiper('#header .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
    });

	//스크롤시
    $(window).scroll(function(){
        var currentTop = parseInt($(this).scrollTop());
		var contTop = $('#header').height();
		if(currentTop > contTop){
			$('#header').addClass('fix');
			$('.f_fix, .scroll_btn').addClass('on');
		}else{
			$('#header').removeClass('fix');
			$('.f_fix, .scroll_btn').removeClass('on');
		}

	});

	//사이드 열기
	$('#header .header_all .h_bottom .side_btn').click(function(){
		$('#side').addClass('on');
		$("body").css({"height":"100%", "overflow":"hidden"});
	});

	//사이드 닫기
	$('#side .side_all .side_close').click(function(){
		$('#side').removeClass('on');
		$("body").css({"height":"auto", "overflow":"visible"});
	});

	//상단이동
	$('.top_btn').click(function(){
		$('html,body').animate({
			scrollTop: $('html,body').offset().top
		});		
	});

	//인기검색어 순위
	$('#header .header_all .rank_area li').each(function(){
		var popularIdx = $(this).index();
		$(this).attr("data-index", popularIdx + 1);
	});

	//검색창 열기
	$('#header .header_all .h_top .search_btn, .f_fix ul li .f_search_btn').click(function(){
		$('#header .header_all .h_top').addClass('on');
		$("body").css({"height":"100%", "overflow":"hidden"});
		$('._header_search .layer_search_box dl dd .keyword_box input').attr("readonly", false);
		$('html,body').animate({
			scrollTop: $('html,body').offset().top
		}, 300);	

	});

	//검색창 닫기
	$('#header .header_all .h_top .back_btn').click(function(){
		$('#header .header_all .h_top').removeClass('on');
		$("body").css({"height":"auto", "overflow":"visible"});
		$('._header_search .layer_search_box dl dd .keyword_box input').attr("readonly", true);
	});


});

// 탭 카테고리 동작 모듈
function tabs(cate,cont) {
	$(cate).click(function() {
		var $thisIndex = $(this).index();
		$(cate).removeClass('on');
		$(this).addClass('on');
		$(cont).removeClass('on');
		$(cont).eq($thisIndex).addClass('on');
	});
}
