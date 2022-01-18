$(function(){
	$('p').filter(function() {
		var innter_text = $(this).html().trim();
		if ( innter_text == '&nbsp;' || innter_text == '&nbsp' ) {
			return this;
		}
	}).remove();

	$(".item_goods_sec .openblock .openblock-header").click(function(){
		if ($(this).next("div").is(":visible"))
		{
			$(this).removeClass("on");
			$(this).next("div").stop().slideUp();
		} else{
			$(this).addClass("on");
			$(this).next("div").stop().slideDown();
		}
	});

	$(".write-box-view").click(function(){
		$(".plus_review_write").stop().fadeIn();
		$(".chosen-container-single-nosearch").css("width", "894px");
	});

	$("a.go-review").click(function(){
		var reviewOffset = $(".plus_review_cont").offset().top;
		$("html, body").animate({
			scrollTop : reviewOffset - 100
		});

	});

	// $(".plus_review_photo_cont > div").slick({
	// 	slidesPerRow: 9,
	// 	// rows: 2,
	// 	dots: true,
	// 	arrows: true,
	// 	infinite: true,
	// 	speed: 1000,
	// 	cssEase: 'cubic-bezier(0.645, 0.045, 0.355, 1)'
	// });
	
});