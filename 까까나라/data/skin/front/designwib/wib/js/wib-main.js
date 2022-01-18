$(function(){

	//메인배너 페이징 작업
	function pagingCount(target, event, slick, currentSlide, nextSlide){
	  var i = (currentSlide ? currentSlide : 0) + 1;
	  if(event.type == 'beforeChange'){
		i = nextSlide + 1;
	  }
	  target.html("<span>" + i + "</span>" + slick.slideCount);		
	}

	//메인배너 컬러 매칭
	function mainColor(target, type, indexNum){
		var selectIndex = target.find('.slick-active').data('slick-index');
		$('.main_bg li').each(function(target){
		var index = $(this).data('index'); // $(this)는 59번줄 참고!
                
                // beforeChange에서만 특정 컬러를 찾는거면 변수를 여러번 지정하지말고
                // var color = $(this).data('color'); 여기서 지정하고
                // if문에서만  if (type == 'beforeChange') {  color = $('.main_bg li').eq(indexNum).data('color'); 
                // 해주면됨 ㅇ_ㅇb
                
		if(selectIndex == index){
			if (type == 'beforeChange') {
				var color = $('.main_bg li').eq(indexNum).data('color');
			} else {
				var color = $(this).data('color');   
			}
                        
			$('.main_ban_wrap .main_ban').css('background',color);
		}
		});
	}

	// 메인 슬라이드 배너
	var $status = $('.main_ban .main_ban_paging');
	var $slickElement = $('.main_ban ul');

	$slickElement.on('init reInit beforeChange', function (event, slick, currentSlide, nextSlide) {
		pagingCount($status, event, slick, currentSlide, nextSlide);
		mainColor($(this),event.type, nextSlide);
		//대상이 되는 놈 nextSlide
	});

	$slickElement.slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 5000,
		fade:true,
		speed : 800,
		prevArrow: $('.main_ban_prev'),
		nextArrow: $('.main_ban_next'),
	});

	//메인배너 자동 재생
    $('.main_ban_wrap .main_ban_btn .play_btn').click(function() {
        // var target = $(this);
        // target.toggleClass('stop');
        // if(target.hasClass('stop')) {
        // 개체는 한번만 구해서 사용하는게 좋음 ㅇ_ㅇb
        $(this).toggleClass('stop');
        if($(this).hasClass('stop')) {
            $slickElement.slick('slickPause');
        } else {
			$slickElement.slick('slickPlay');
        }
    });
	
	//기획 특가 더보기
	if($('.main_plan .cont .pd div').hasClass('btn_goods_more')){
		$(".main_plan").append($('.main_plan .cont .pd .btn_goods_more'));
	}

	//카테고리 베스트
    var bestSwiper = new Swiper('.best_pd .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
		navigation: {
			nextEl: '.best_pd .goods_tab_tit .next_btn',
			prevEl: '.best_pd .goods_tab_tit .prev_btn',
		}
    });

	//브랜드별 인기상품
    var bestSwiper = new Swiper('.brand_best_pd .swiper-container', {
		slidesPerView: 'auto',
		freeMode: true,
		observer : true,
		observeParents: true,
		navigation: {
			nextEl: '.brand_best_pd .goods_tab_tit .next_btn',
			prevEl: '.brand_best_pd .goods_tab_tit .prev_btn',
		},
    });

	// 탭 가운데 정렬
	$('.best_pd, .brand_best_pd').each(function(){
		var bestW = 0;
		var bWrap = $(this).find(".item_hl_tab_type .goods_tab_tit ul");
		var bWidth = $(this).find(".item_hl_tab_type .goods_tab_tit li");
		bWidth.each(function(){
			bestW += Number($(this).width());
		});	
		if (bestW <= bWrap.width()){
			bWrap.css("justify-content", "center");
		} else{
			bWrap.css("justify-content", "left");
		}
	});
	
	//버튼 클릭시 동작
	$(".next_btn, .prev_btn").on("click", function(e) {
		e.preventDefault();
		var data = $(this).siblings().find('li');
		var divTag = $(this).parents(".goods_list_cont").find('.goods_tab_box');
		var moreBtn = $(this).parents(".main_pd").find('.btn_goods_more a');
		//var swiperDiv = $(this).parents('.swiper-container');
		var count = data.length - 1;
		var next = $(this).hasClass('next_btn');
		data.each(function(){
		  if($(this).hasClass('on')){
			var indexNum = $(this).index();
			 if((next == true && indexNum != count) || (next != true && indexNum != 0)){
				indexNum = next ? indexNum + 1 : indexNum - 1;
				//next ? bestSwiper.slideNext() : bestSwiper.slidePrev();
				data.removeClass('on');
				divTag.removeClass('on');
				moreBtn.removeClass('on');
				data.eq(indexNum).addClass('on');
				divTag.eq(indexNum).addClass('on');
				moreBtn.eq(indexNum).addClass('on');
				return false;
			 }
		  }
		});   
		return false;
	});
	
	//스크롤시
    $(window).scroll(function(){
        var currentTop = parseInt($(this).scrollTop());
		var contTop = $('.main_ban_wrap').height();
		if(currentTop > contTop){
			$('#scroll_left, #scroll_right').addClass('on');
		}else{
			$('#scroll_left, #scroll_right').removeClass('on');
		}

		if(currentTop > 0){
			$('#header_warp').addClass('on');
		}else{
			$('#header_warp').removeClass('on');
		}
	});

});