$(function(){
	$(window).load(function(){
		$(".item_photo_slide .slider_goods_nav li").eq(0).addClass("on");
	});
	$(".item_photo_slide .slider_goods_nav li").click(function(){
		$(this).addClass("on").siblings("li").removeClass("on");
	});
	
	$("#lySns .ly_close").click(function(){
		$(".item_detail_tit .btn-area .btn_qa_share_box").removeClass("on");
	});
	
	$(".benefit-btn-area ul li a").click(function(){
        $(this).closest("li").toggleClass("on");
        
        gd_benefit_calculation();
	});

	$(".benefit-open").click(function(){
		$(".benefit-layer").stop().fadeIn();
	});
	$(".benefit-close").click(function(){
		$(".benefit-layer").hide();
	});

	
	$(".location_select:last-child .location_tit a span").clone().appendTo(".item_goods_sec .detail_explain_box h3 span b");

	$(".detail_explain_box .item_gallery_type").addClass("swiper-container");
	$(".detail_explain_box .item_gallery_type > ul").addClass("swiper-wrapper");
	$(".detail_explain_box .item_gallery_type > ul > li").addClass("swiper-slide");

	var explainSlider = new Swiper(".detail_explain_box .swiper-container", {
		slidesPerView: "5",
		autoplay : { 
			loop : true,
			delay : 6000, 
		},
		spaceBetween: 32,
		speed: 1000,
		navigation: {
			prevEl: ".detail_explain_box .swiper-button-prev",
			nextEl: ".detail_explain_box .swiper-button-next",
		},
		scrollbar: {
			el: ".detail_explain_box .swiper-scrollbar",
			//hide: true,
		},
	});
	
	$(window).on("load scroll", function(){
		var goodsTabOffset = $(".item_goods_tab").offset().top - 92;
		if ( $( document ).scrollTop() > goodsTabOffset ) {
			$(".item_goods_tab").addClass("fixed");
		}
		else {
			$(".item_goods_tab").removeClass("fixed");
		}
	});

	$(".item_goods_tab li a").click(function(){
		var naviPosition = $(this).attr("data-class");
		var move = $(naviPosition).offset().top - 149;
		$("html,body").stop().animate({"scrollTop":move});
	});

	$(window).on("load scroll", function(){	
		var goods_sec = $(".item_goods_sec").offset().top - 149;
		
		var go01off = $(".go01").offset().top - 150;
		var go02off = $(".go02").offset().top - 150;
		var go03off = $(".go03").offset().top - 150;
		var go04off = $(".go04").offset().top - 150;
		var go05off = $(".go05").offset().top - 150;

		var footer_sec = $("#footer_wrap").offset().top - 510;
		
		var $position = $(window).scrollTop();
		if ($position > go01off)
		{	
			$(".item_goods_tab li").eq(0).addClass("on").siblings("li").removeClass("on");
			$(".item_goods_tab > ul").removeAttr("class");
		}
		if ($position > go02off)
		{	
			$(".item_goods_tab li").eq(1).addClass("on").siblings("li").removeClass("on");
			$(".item_goods_tab > ul").removeAttr("class");
			$(".item_goods_tab > ul").addClass("left20");
		}
		if ($position > go03off)
		{	
			$(".item_goods_tab li").eq(2).addClass("on").siblings("li").removeClass("on");
			$(".item_goods_tab > ul").removeAttr("class");
			$(".item_goods_tab > ul").addClass("left40");
		}
		if ($position > go04off)
		{	
			$(".item_goods_tab li").eq(3).addClass("on").siblings("li").removeClass("on");
			$(".item_goods_tab > ul").removeAttr("class");
			$(".item_goods_tab > ul").addClass("left60");
		}
		if ($position > go05off)
		{	
			$(".item_goods_tab li").eq(4).addClass("on").siblings("li").removeClass("on");
			$(".item_goods_tab > ul").removeAttr("class");
			$(".item_goods_tab > ul").addClass("left80");
		}		
	});
});