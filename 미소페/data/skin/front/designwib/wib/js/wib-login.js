$(function(){
	$('.login_tab h3:eq(0)').click(function(){
		$('.login_tab').removeClass('move');
		$("#formLogin").stop().fadeIn("fast");
		$("#formOrderLogin").hide();
		$("#nonM").hide();
		$(this).addClass("on").siblings("h3").removeClass("on");
	});

	$('.login_tab h3:eq(1)').click(function(){
		$('.login_tab').addClass('move');
		$("#formLogin").hide();
		$("#formOrderLogin").stop().fadeIn("fast");
		$("#nonM").show();
		$(this).addClass("on").siblings("h3").removeClass("on");
	});
});