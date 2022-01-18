$(function(){
	if ($(".best_item_view").length)
	{
		$(".best_item_view .item_basket_type ul li").each(function(){
			var bestProductIdx = $(this).index() + 1;
			$(this).attr("data-index", bestProductIdx);
		});
	}

	$(window).load(function(){
		sortTxt = $(".pick_list_box .pick_list label.on").text();
		$(".sort-title").text(sortTxt);
		$(".sort-title").click(function(){
			if ($(this).hasClass("on"))
			{
				$(this).removeClass("on");
				$(this).next("ul").stop().fadeOut("fast");
			} else{
				$(this).addClass("on");
				$(this).next("ul").stop().fadeIn("fast");
			}
		});
		$("li.depth1li").each(function(){
			if ($(this).hasClass("on"))
			{
				$(this).find("> ul").stop().slideDown();
			}
		});
	});
});







$(function(){

	// 해당 레프트값 찾아서 오픈
	var thisUrl = location.href;
	var thisDomain = document.domain;
	var compareUrl = thisUrl.split(thisDomain)[1];
	
	var compareUrl2 = ".." + compareUrl
	var compareUrl3 = compareUrl2.split("&")[0];

	console.log("compareUrl★★★" + compareUrl);
	console.log("compareUrl3★★★" + compareUrl3)

	// 2뎁스 비교	
	// $("#w-header .category-area > ul > li > ul > li").each(function(){
	// 	var depth2attr = $(this).find("a").attr("href");
	// 	if (compareUrl3 == depth2attr)
	// 	{
	// 		$(this).closest("li.depth1li").addClass("on");
	// 		$(this).addClass("depth2-on");
	// 	}
	// });
	// $(".list-left-menu > ul > li").has(".depth2-on").addClass("first-open");
	// 3뎁스 비교
	// $(".list-left-menu > ul > li > ul > li > ul > li").each(function(){
	// 	var depth3attr = $(this).find("a").attr("href");
	// 	if (compareUrl3 == depth3attr)
	// 	{
	// 		$(this).addClass("depth3-on");
	// 	}
	// });
	// $(".list-left-menu > ul > li").has(".depth3-on").addClass("first-open");
});