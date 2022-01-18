$(function(){

	//메인배너 페이징 작업
	function pagingCount(target, event, slick, currentSlide, nextSlide){
	  var i = (currentSlide ? currentSlide : 0) + 1;
	  if(event.type == 'beforeChange'){
		i = nextSlide + 1;
	  }
	  target.html("<span>" + i + "</span>" + slick.slideCount);		
	}

	// 메인 슬라이드 배너
	var $status = $('.main_ban_wrap .main_ban_paging');
	var $slickElement = $('.main_ban');

	$slickElement.on('init reInit beforeChange', function (event, slick, currentSlide, nextSlide) {
		pagingCount($status, event, slick, currentSlide, nextSlide);
	});

	$slickElement.slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 5000,
		fade:true,
		speed : 800,
		arrows:false,
	});

	//메인배너 자동 재생
    $('.main_ban_wrap .main_ban_btn .play_btn').click(function() {
        var target = $(this);
        target.toggleClass('stop');
        if($(this).hasClass('stop')) {
            $slickElement.slick('slickPause');
        } else {
			$slickElement.slick('slickPlay');
        }
    });

	//기획특가
	$('.main_plan .pd .goods_list').addClass('swiper-container');
	$('.main_plan .pd .goods_list .goods_list_all').addClass('swiper-wrapper');
	$('.main_plan .pd .goods_list .goods_list_all > li').addClass('swiper-slide');
	var planSwiper = new Swiper('.main_plan .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
    });

	//카테고리 베스트
    var bestSwiper = new Swiper('.best_pd .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
		navigation: {
			nextEl: '.best_pd .tab_box .next_btn',
			prevEl: '.best_pd .tab_box .prev_btn',
		}
    });

	//브랜드별 인기상품
    var bestSwiper = new Swiper('.brand_best_pd .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
		navigation: {
			nextEl: '.brand_best_pd .tab_box .next_btn',
			prevEl: '.brand_best_pd .tab_box .prev_btn',
		},
    });

	//버튼 클릭시 동작
	$(".next_btn, .prev_btn").on("click", function(e) {
		e.preventDefault();
		var data = $(this).siblings().find('li');
		var divTag = $(this).parents(".tab_pd").find('.tab_cont > ul');
		var moreBtn = $(this).parents(".tab_pd").find('.more_btn_box a');
		var count = data.length - 1;
		var next = $(this).hasClass('next_btn');
		data.each(function(){
		  if($(this).hasClass('on')){
			var indexNum = $(this).index();
			 if((next == true && indexNum != count) || (next != true && indexNum != 0)){
				indexNum = next ? indexNum + 1 : indexNum - 1;
				data.removeClass('on');
				divTag.css('display','none');
				moreBtn.removeClass('on');
				data.eq(indexNum).addClass('on');
				divTag.eq(indexNum).css('display','block');
				moreBtn.eq(indexNum).addClass('on');
				return false;
			 }
		  }
		});   
		return false;
	});

	//리얼리뷰
	/*
	var reviewSwiper = new Swiper('.main_review .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
    });
	*/

	//인스타그램
	$('.main_insta .inso_widget_area_1').addClass('swiper-container');
	$('.main_insta .inso_widget_data_1').addClass('swiper-wrapper');
	var instaSwiper = new Swiper('.main_insta .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
    });

	// 게시판 탭작업
	tabs('.main_board .tab > li','.main_board .tab_cont .cont');

});