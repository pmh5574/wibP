$(function(){

	$('#wrap').css("opacity","1");

	//�˻�â
	$('.search_btn').click(function(e){
		e.preventDefault();
		$(this).toggleClass('open');
		$('#header .top_search').toggleClass('open');	
	});

	//����� ���̵� ī�װ�
	$('#side .s_cate .arrow').click(function(){
		$(this).toggleClass('on');
		$(this).next('.depth-two-wrap').slideToggle();
	});

	//���̵� ����
	var main = $('.sub_content > div').hasClass('main');
//	console.log(main);
	$('.side_btn').click(function(){
		$('#side').addClass('open');
		if(main){
			fullpage_api.setMouseWheelScrolling(false);
			fullpage_api.setAllowScrolling(false);
		}
	});

	//���̵� �ݱ�
	$('#side .side_all .logo .close_btn').click(function(){
		$('#side').removeClass('open');
		if(main){
			fullpage_api.setMouseWheelScrolling(true);
			fullpage_api.setAllowScrolling(true);
		}
	});

	//��ũ�ѽ�
    $(window).scroll(function(){
        var currentTop = parseInt($(this).scrollTop());
		if(currentTop > 0){
			$("#header").addClass("fix");
		}else{
			$("#header").removeClass("fix");	
		}
	});

	//������¡
	function mobileSize() {
		var screenWidth = $(window).width();
		if(screenWidth > 769){
			$('#side').removeClass('open');
		}
	}

	mobileSize();

	$(window).on('resize', function(){
		mobileSize();
	});	

});

// �� ī�װ� ���� ���
function tabs(cate,cont) {
	$(cate).click(function() {
		var $thisIndex = $(this).index();
		$(cate).removeClass('on');
		$(this).addClass('on');
		$(cont).removeClass('on');
		$(cont).eq($thisIndex).addClass('on');
	});
}
