$(function(){

	//풀페이지
	new fullpage('#fullpage', {
		anchors: ['page1', 'page2', 'f_section'],
		verticalCentered: true,
		afterLoad: function(origin,index){
			var indexNum = index.index;
			var result = $('#fullpage .section').eq(indexNum).find('.txt').hasClass('white');
			//텍스트 흰색일 경우
			if(result == true){
				$('#header .header_all, .scroll_motion').addClass('white');
			}else{
				$('#header .header_all, .scroll_motion').removeClass('white');
			}
			//하단으로 갈 경우
			if(index.anchor == "f_section"){
				$('.scroll_motion').hide();
			}else{
				$('.scroll_motion').show();
			}
        }
	});

	//하단 위치이동
	$("#fullpage .f_section").prepend( $("#footer_wrap") ); 

	//리사이징
	function mobileSize() {
		var screenWidth = $(window).width();
		if(screenWidth < 769){
			$('#fullpage').addClass('mo');
		}else{
			$('#fullpage').removeClass('mo');
		}

		//배너 적용
		if($("#fullpage").hasClass('mo')){
			var eachTarget = $('.m_main_banner a');
			
		}else{
			var eachTarget = $('.main_banner a');
		}

		eachTarget.each(function(){
			
			var target = $(this);
			var targetIndex = target.index();
			var targetImgSrc = target.find('img').attr('src');
			//var targetImgHref = $(this).attr('href');
			$('#fullpage .section').each(function(){
				var section = $(this);
				var sectionIndex = section.index();
				if(targetIndex == sectionIndex){
					$(this).css("background-image","url(" + targetImgSrc + ")");
				}
			});
		});
	}

	mobileSize();


	$(window).on('resize', function(){
		mobileSize();
	});	
    


});