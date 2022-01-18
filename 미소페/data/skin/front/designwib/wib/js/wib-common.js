$(function(){
	var popular = $("#w-header-top .popular-rank");
	popular.each(function(){
		var popularIdx = $(this).index();
		$(this).attr("data-index", popularIdx + 1);
	});

	var searchRank = $("#w-header-top .rank-area ul").bxSlider({
		auto: true,
		pause: 5000,
		speed:800,
		controls:true,
		pager:false,
		mode:'vertical',
		//preventDefaultSwipeY:true,
	});
	$(".search-area-open").click(function(){
		$(".rank-area-view").addClass("on");
	});
	$(".search-area-close").click(function(){
		$(".rank-area-view").removeClass("on");
	});

	$("html").click(function(e) {
		if(!$(".rank-area").has(e.target).length && !$(".rank-area-view").has(e.target).length){
			$(".rank-area-view").removeClass("on");
		}
	});

	if ($(".best-product .swiper-slide").length)
	{
		var bestpSlider = new Swiper(".best-product .swiper-container", {
			slidesPerView: "3",
			speed: 700,
			spaceBetween: 15,
			observer : true,
			observeParents: true,
			// autoplay : { 
			// 	loop : true,
			// 	delay : 8000, 
			// },
			navigation: {
				prevEl: ".best-product .prev.arrow",
				nextEl: ".best-product .next.arrow",
			},
		});
	}
	

	$("li.my-page").mouseenter(function(){
		$(this).find(" > ul").addClass("on");
	});
	$("li.my-page").mouseleave(function(){
		$(this).find(" > ul").removeClass("on");
	});

	$("#w-footer .middle .tab a").click(function(){
		var tabIdx = $(this).index();
		$(this).addClass("on").siblings("a").removeClass("on");
		$("#w-footer .middle .content").eq(tabIdx).stop().fadeIn().siblings(".content").stop().hide();
	});

	var benefitSlider = new Swiper("#footer_wrap #w-footer .top .right .swiper-container", {
		slidesPerView: "1",
		speed: 700,
		observer : true,
		observeParents: true,
		// autoplay : { 
		// 	loop : true,
		// 	delay : 8000, 
		// },
		pagination: {
			el: "#w-footer .right .swiper-pagination",
			type: "fraction",
		  },
	});
	
	$("#w-header .category-area > ul > li > a").mouseenter(function(){
		if ($(this).next("ul").is(":visible"))
		{
			// $("#w-header .category-area > ul > li > ul").stop().slideUp();
			// $("#w-header .category-area > ul > li").removeClass("on");
		} else{
			$("#w-header .category-area > ul > li > ul").stop().slideUp();
			$("#w-header .category-area > ul > li").removeClass("on");
			$(this).next("ul").stop().slideDown();
			$(this).closest("li").addClass("on");
		}
		
	});

});


$(window).load(function(){
	if ($("#w-header .bottom .today-area .swiper-container .swiper-wrapper .swiper-slide").length)
	{
		var recentSlider = new Swiper(".today-area .swiper-container", {
			slidesPerView: "3",
			speed: 700,
			spaceBetween: 6,
			observer : true,
			// observeParents: true,
			autoplay : { 
				loop : true,
				delay : 8000, 
			},
			navigation: {
				prevEl: ".today-area .prev.arrow",
				nextEl: ".today-area .next.arrow",
			},
		});
	}
	
	if ($(".today-area .swiper-container .swiper-wrapper .swiper-slide").length >= 1)
	{
		$(".no-product").hide();
	}

});