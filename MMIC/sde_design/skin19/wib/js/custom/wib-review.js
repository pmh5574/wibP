(function ($) {
    var jQuery = $;
    $(function () {
        
        // 리뷰페이지 페이지인식
        var _href = location.href;
		var _docDomain = document.domain;
		var _catdID = _href.split(_docDomain)[1];
		var _catdIDTree = _href.split('board/')[1];
        
		// 추천이후 달고 아는 url파라미터 값이 있어 추가 체크
		var addCheck = _catdIDTree.indexOf('4/');
		
		if (_catdIDTree == 'product-review/4/' || addCheck != -1){
            $('.review-board').show()
            $('.ori-board').hide()
		}else{
			$('.review-board').hide()
            $('#wrap').css('background','#fff');
            $('#wrap #contents').css('width','1200px');
		}
        
        
        // 이미지 리뷰만 노출
        $('.review-board .review-boardImg ul li a').each(function(){
            var _this = $(this);
            var _length = _this.children().length;
            if(_length == '0'){
                _this.parent().hide();
            }
        });
        
        // 리뷰 팝업 ★
        $('.review-board .review-boardImg ul.inner li').click(function(){
			var popstarts = $(this).data('count');
			var reContent = $(this).children('.pop-data').html();
			var likeFunc = $(this).data('nums');
			var starHtml = '';
			
			// 별점 체크
			for( i=0;i<5;i++){
				var starClass = (i<popstarts)?'starOn':'starOff';
				starHtml += '<span class="'+starClass+'">★</span>';
			}
			
			// 리뷰 넘버값
			var exNum = likeFunc.split('&');
			var intNum = exNum[0].split('?no=');

			$('.pop-inner').html(reContent);
			$('.pop-inner .pop-starts').html(starHtml);
			
			$('html, body').css('position', 'fixed');
            $('.review-pop').show();
			
			// 리뷰상세에서 게시글 및 사이트 고유 세션값이 존재해서 ajax로 해당값 찾아옴
			$.ajax({
				url : '/article/review/4/'+intNum[1]+'/?no='+intNum[1]+'&board_no=4',
				success : function(msg){
					
					var enData = msg.split("article_vote('");
					var splitData = enData[1].split("');");
					var finalKey = splitData[0].split('%2F&'); // 최종값
					
					// 필요한 주소값으로 재구성
					var urls = '/exec/front/Board/vote/4?no='+intNum[1]+'&return_url=%2Fboard%2Fproduct%2Flist.html&'+finalKey[0];
					$('.pop-inner .likeBtn').attr('onclick', "likeAdd('"+urls+"');");
                    
				}
			});
        });
        
        $('.review-pop .pap-back').click(function(){
            $('.review-pop').hide();
			$('html, body').css('position', 'relative');
        });
        
        // 리사이즈
        var reWt = $('.review-board .review-boardImg ul.inner > li').width();
        $('.review-board .review-boardImg ul.inner > li').css('height',reWt);
        $(window).resize(function(){
            var reWt = $('.review-board .review-boardImg ul.inner > li').width();
            $('.review-board .review-boardImg ul.inner > li').css('height',reWt);
        });
    
        
    });
    
    
})(addjQuery);

function popClose(){
	$('.review-pop').hide();
	$('html, body').css('position', 'relative');
}


function likeAdd(url){
	location.href = url;
}