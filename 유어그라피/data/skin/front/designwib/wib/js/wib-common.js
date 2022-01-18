$(function(){

	$('#wrap').css("opacity","1");

	//검색창
	$('.search_btn').click(function(e){
		e.preventDefault();
		$(this).toggleClass('open');
		$('#header .top_search').toggleClass('open');	
	});

	//모바일 사이드 카테고리
	$('#side .s_cate .arrow').click(function(){
		$(this).toggleClass('on');
		$(this).next('.depth-two-wrap').slideToggle();
	});

	//사이드 열기
	var main = $('.sub_content > div').hasClass('main');
//	console.log(main);
	$('.side_btn').click(function(){
		$('#side').addClass('open');
		if(main){
			fullpage_api.setMouseWheelScrolling(false);
			fullpage_api.setAllowScrolling(false);
		}
	});

	//사이드 닫기
	$('#side .side_all .logo .close_btn').click(function(){
		$('#side').removeClass('open');
		if(main){
			fullpage_api.setMouseWheelScrolling(true);
			fullpage_api.setAllowScrolling(true);
		}
	});

	//스크롤시
    $(window).scroll(function(){
        var currentTop = parseInt($(this).scrollTop());
		if(currentTop > 0){
			$("#header").addClass("fix");
		}else{
			$("#header").removeClass("fix");	
		}
	});

	//리사이징
	function mobileSize() {
		var screenWidth = $(window).width();
		if(screenWidth > 769){
			$('#side').removeClass('open');
		}
	}

	mobileSize();

	$(window).on('resize', function(){
		mobileSize();
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
