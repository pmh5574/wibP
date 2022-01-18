/* =============================
   서브페이지 반응형 모듈소스입니다.
   수정/삭제시 사이트가 틀어질수 있습니다.
   ============================= */



/*  PC 해상도 */
$(function() {

	// 게시판 쓰기페이지 에디터처리 
	$('.body-write .board_write_table td.write_editor').attr('colspan','2');
});



/* MOBILE 해상도 */
(function () {
	$(function () {

		// 상품후기 타입 게시판 인식 
		var checkReview = location.href.split('/');
		checkReview = checkReview[4];
		if(checkReview == 'list.php?bdId=goodsreview' || checkReview == 'mypage_goods_review.php') {
		   $('.board_list_gallery').addClass('list_review');
		}

		// 상품문의 답변 상태 표시 
		$('.board_list_qa .board_list_table tr').each(function() {
			var qaStatus = $(this).find('td').last().text();
			if(qaStatus.indexOf('접수') != -1) {
			   $(this).addClass('ing');
			}
			if(qaStatus.indexOf('답변완료') != -1) {
			   $(this).addClass('end');
			}
		})

		// 게시판 게시글없을시 구분처리 
		$('.board_list_table tbody tr').each(function() {
			var boardTextNon = $(this).find('td').text();
			if(boardTextNon.indexOf('존재하지않습니다')) {
				$(this).find('td').addClass('notdata');
			}
		});
		
		// 게시판쓰기페이지 에디터처리 
		$('.write_editor,.wirte_editor').prevAll('th').hide();
		$('.wirte_editor').attr('colspan',2).parent('tr').addClass('wirte_editor_wrap');
	});

	// 탭 카테고리 동작 
	$.tabs = function (cate, cont) {
		$(cate).click(function () {
			var $thisIndex = $(this).index();
			$(cate).removeClass('on');
			$(this).addClass('on');
			$(cont).removeClass('on');
			$(cont).eq($thisIndex).addClass('on');
		});
	}
})(jQuery);



/* 공통 팝업 사이즈조절 */
window.onload = function() {
	$('.sys_pop').parent('div').addClass('popup_wrap');
	$('.sys_pop').each(function() {
		var popInnerHeight = $(this).height() + 25;
		$('.sys_pop').closest('.popup_wrap').css('height',popInnerHeight);
	});
}
