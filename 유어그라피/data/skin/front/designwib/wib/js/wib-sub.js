/* =============================
   ���������� ������ ���ҽ��Դϴ�.
   ����/������ ����Ʈ�� Ʋ������ �ֽ��ϴ�.
   ============================= */



/*  PC �ػ� */
$(function() {

	// �Խ��� ���������� ������ó�� 
	$('.body-write .board_write_table td.write_editor').attr('colspan','2');
});



/* MOBILE �ػ� */
(function () {
	$(function () {

		// ��ǰ�ı� Ÿ�� �Խ��� �ν� 
		var checkReview = location.href.split('/');
		checkReview = checkReview[4];
		if(checkReview == 'list.php?bdId=goodsreview' || checkReview == 'mypage_goods_review.php') {
		   $('.board_list_gallery').addClass('list_review');
		}

		// ��ǰ���� �亯 ���� ǥ�� 
		$('.board_list_qa .board_list_table tr').each(function() {
			var qaStatus = $(this).find('td').last().text();
			if(qaStatus.indexOf('����') != -1) {
			   $(this).addClass('ing');
			}
			if(qaStatus.indexOf('�亯�Ϸ�') != -1) {
			   $(this).addClass('end');
			}
		})

		// �Խ��� �Խñ۾����� ����ó�� 
		$('.board_list_table tbody tr').each(function() {
			var boardTextNon = $(this).find('td').text();
			if(boardTextNon.indexOf('���������ʽ��ϴ�')) {
				$(this).find('td').addClass('notdata');
			}
		});
		
		// �Խ��Ǿ��������� ������ó�� 
		$('.write_editor,.wirte_editor').prevAll('th').hide();
		$('.wirte_editor').attr('colspan',2).parent('tr').addClass('wirte_editor_wrap');
	});

	// �� ī�װ� ���� 
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



/* ���� �˾� ���������� */
window.onload = function() {
	$('.sys_pop').parent('div').addClass('popup_wrap');
	$('.sys_pop').each(function() {
		var popInnerHeight = $(this).height() + 25;
		$('.sys_pop').closest('.popup_wrap').css('height',popInnerHeight);
	});
}
