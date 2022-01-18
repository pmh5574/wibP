$(function(){

	//중분류
	var goodsList = $('.goods_list_category_all').height();
	$('.goods_list_category').css("padding-top",goodsList + "px");
	console.log(goodsList);
	if(goodsList == '0'){
		$('.goods_list .goods_top_box').addClass("zero");
	}

	//스크롤시
    $(window).scroll(function(){
        var currentTop = parseInt($(this).scrollTop());
		var contTop = $('.goods_list .goods_list_box').offset().top;
		if(currentTop > contTop){
			$('.sub_top, .goods_list').addClass('fix');
		}else{
			$('.sub_top, .goods_list').removeClass('fix');
		}
	});

	//전체보기
	$('.goods_list_category .all_cate').click(function(){
		$('.goods_list_category .all_cate, .goods_list .goods_list_category ul').toggleClass('on');
	});








});