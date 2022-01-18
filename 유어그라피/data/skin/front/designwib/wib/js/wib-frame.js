$(function(){

	//이미지 슬라이드
	var imgNav = $('#frame_left .img_box .img_nav');
	var imgCont = $('#frame_left .img_box .img_cont ul');

	imgCont.slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true,
		asNavFor: imgNav,
		swipe: false
	});

	imgNav.slick({
		infinite: false,
		slidesToShow: 5,
		slidesToScroll: 1,
		vertical: true,
		verticalSwiping: true,
		arrows: false,
		asNavFor: imgCont,
		dots: false,
		focusOnSelect: true
	});

	//이미지 업로드 팝업
	$('.upload_btn').click(function(){
		$('.dark_bg, .upload_pop').addClass('on');
	});

	//팝업닫기
	$('.pop .pop_close').click(function(){
		$('.dark_bg, .pop').removeClass('on');
		$("body").css("overflow","visible");
	});
        
        $('.dark_bg').click(function(){
            $('.dark_bg, .pop').removeClass('on');
            $("body").css("overflow","visible");
	});
	
	//사이즈 탭
	tabs('#frame_right .size_tab .tab_tit > li','#frame_right .size_tab .tab_cont > .cont');

	//셀렉트 창
	$('#frame_right .select_box > p').click(function(){
		var target = $(this).parent();
		target.toggleClass('on');
	});


	

	//매트보드
	$('#frame_right .mount_select .cont_wrap .cont_tit').click(function(){
		var titTarget = $(this);
		if(titTarget.hasClass('close')){
			titTarget.removeClass('close');
			titTarget.next().stop().slideDown(300);
		}else{
			titTarget.addClass('close');
			titTarget.next().stop().slideUp(300);
		}
	});

	//프레임 슬라이드
	var frameNav = $('.frame_info_pop .list ul.img_list_nav');
	var frameCont = $('.frame_info_pop .list ul.img_list_cont');

	frameCont.slick({
		slidesToShow: 3,
		slidesToScroll: 1,
		arrows: true,
		asNavFor: frameNav,
		responsive: [
			{
				breakpoint:960,
				settings: {
					slidesToShow: 1,
					arrows:false
				}
			}	
		]
	});

	frameNav.slick({
		infinite: true,
		slidesToShow: 4,
		slidesToScroll: 1,
		arrows: false,
		asNavFor: frameCont,
		dots: false,
		focusOnSelect: true
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