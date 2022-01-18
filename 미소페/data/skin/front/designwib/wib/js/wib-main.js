$(function(){

	var mvSlider = new Swiper(".mcon01 .swiper-container", {
		slidesPerView: "1",
		autoplay : { 
			loop : true,
			delay : 6000, 
		},
		speed: 1000,
		navigation: {
			prevEl: ".mcon01 .swiper-button-prev",
			nextEl: ".mcon01 .swiper-button-next",
		},
		pagination: {
			el: ".mcon01 .swiper-pagination",
			type: "fraction",
		},
		scrollbar: {
			el: ".mcon01 .swiper-scrollbar",
			//hide: true,
		},
	});
	$(".mcon01 .swiper-container .pager").click(function(){
		$(this).toggleClass("on");
	});
	$(".mcon01 .pager a.mcon01-stop").click(function(){
		mvSlider.autoplay.stop();
	});
	$(".mcon01 .pager a.mcon01-play").click(function(){
		mvSlider.autoplay.start();
	});

	var mcon04Slider = new Swiper(".mcon04 .swiper-container", {
		slidesPerView: "4",
		// autoplay : { 
		// 	loop : true,
		// 	delay : 6000, 
		// },
		spaceBetween: 41,
		speed: 1000,
		navigation: {
			prevEl: ".mcon04 .swiper-button-prev",
			nextEl: ".mcon04 .swiper-button-next",
		},
		scrollbar: {
			el: ".mcon04 .swiper-scrollbar",
			//hide: true,
		},
	});

	var mcon05Slider = new Swiper(".mcon05 .right .swiper-container", {
		slidesPerView: "2.2",
		loop: true,
		autoplay : { 
			loop : true,
		    delay : 6000, 
		},
		spaceBetween: 40,
		speed: 500,
		navigation: {
			prevEl: ".mcon05 .swiper-button-prev",
			nextEl: ".mcon05 .swiper-button-next",
		},
		on : {
			slideChangeTransitionStart : function(){
				var rightAttr = $(".mcon05 .right .swiper-slide-active").attr("data-swiper-slide-index");
				$(".mcon05 .inner .left .swiper-slide").removeClass("on");
				
				$(".mcon05 .inner .left .swiper-slide").each(function(){
					if ($(this).attr("data-swiper-slide-index") == rightAttr)
					{
						$(this).addClass("on");
					}
				});
				if (rightAttr == "0")
				{
					$(".mcon05 .inner .left li").eq(0).addClass("on").siblings("li").removeClass("on");
				}
			}
		}
	});
	$(".mcon05 .inner .right .item_list_type ul li.swiper-slide").clone().appendTo(".mcon05 .inner .left");
	$(window).load(function(){
		$(".mcon05 .inner .left .swiper-slide").eq(0).addClass("on");
	});


	$(".mcon06 .tab ul li").click(function(){
		var mcon06Offset = $(".mcon06").offset().top - 92;
		var tabIdx = $(this).index();
		$("html, body").animate({
			scrollTop : mcon06Offset
		});
		$(this).addClass("on").siblings("li").removeClass("on");
		$(".mcon06 .contents-wrap .content").eq(tabIdx).stop().fadeIn().siblings(".content").stop().hide();
	});

	
});