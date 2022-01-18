$(function(){
	var wheader = $("#wheader");
	var wsearch = $("#wsearch");
	var popular = $("#wsearch .popular-rank");
	$(window).load(function(){
		wheader.addClass("load");
		// wsearch.addClass("load");
	});
	
	popular.each(function(){
		var popularIdx = $(this).index();
		$(this).attr("data-index", popularIdx + 1);
	});

	var searchRank = $("#wsearch .rank-area ul").bxSlider({
		auto: true,
		pause: 5000,
		speed:800,
		controls:true,
		pager:false,
		mode:'vertical',
		//preventDefaultSwipeY:true,
	});
    

    /*
    // sns
    $('.sns_wrap ul li').find('img').hover(function() {
        $(this).attr("src",$(this).attr("src").replace(/off\.png$/, 'on.png')); 
    }, function() { 
        $(this).attr("src",$(this).attr("src").replace(/on\.png$/, 'off.png')); 
    });
    */
	$(window).load(function(){
		$(".circle-menu-toggle").click(function(){
			if ($(this).hasClass("on"))
			{
				$(this).removeClass("on");
				$(".recently-area").removeClass("on");
			} else{
				$(this).addClass("on");
				$(".recently-area").addClass("on");
			}
		});
	});
	$(".go-top").click(function(){
		$("html,body").animate({
			scrollTop: 0
		});		
	});

	$(window).on("load scroll", function(){
		if ( $( document ).scrollTop() > 150 ) {
			$(".right-quick" ).addClass("on" );
		}else {
			$(".right-quick" ).removeClass("on" );
		}
	});

	$("ul.depth1").closest("li").addClass("no-href");
	$(".no-href > a").attr("href", "javascript:;");
	$(window).load(function(){
		$(".no-href > a").click(function(){
            /*
			if ($(this).closest("li.no-href").hasClass("on"))
			{
				$(".no-href").removeClass("on");
			} else{
				$(".no-href").addClass("on");
			}
            */
            $(this).parent('.no-href').toggleClass("on");
            $(this).parent('.no-href').siblings('.no-href').removeClass('on');
		});
	});
	

	// 메뉴 아이콘
	$("#wheader .inner .category-area > ul > li").each(function(){
		var menuTxt = $(this).find(" > a").text();
		if ( menuTxt == "오늘의 특가" )
		{
			$(this).find(" > a").addClass("icon01");
		}
		if ( menuTxt == "신선과일" )
		{
			$(this).find(" > a").addClass("icon02");
		}
		if ( menuTxt == "새벽배송" )
		{
			$(this).find(" > a").addClass("icon03");
		}
	});

	
	
	
});