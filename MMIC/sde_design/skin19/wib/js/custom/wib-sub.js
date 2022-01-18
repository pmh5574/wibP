(function ($) {
    var jQuery = $;
    $(function () {
        // 로그인폼 대체문구
        $('.security .check label').text('Save ID');
        $('input#member_id').attr('placeholder','ID');
        $('input#member_passwd').attr('placeholder','Password');
        
        // 주문폼 인풋 대체문구
        $('input#order_name').attr('placeholder','Order Name');
        $('input#order_id1').attr('placeholder','Order Number');
        $('input#order_password').attr('placeholder','Order Password');
        $('input#name').attr('placeholder','Name');
        
        $('input#verification_code').attr('placeholder','verification_code');
        $('input#new_passwd').attr('placeholder','New Password');
        $('input#new_passwd_confirm').attr('placeholder','New Password confirm');
        
        // 아이디찾기 폼
        $('input#name').attr('placeholder','NAME');
        $('input#email').attr('placeholder','EMAIL');

		var _href = location.href;
		var _docDomain = document.domain;
		var _catdID = _href.split(_docDomain)[1];
		var _catdIDTree = _href.split('member/')[1];
        

		if (_catdIDTree == 'login.html?noMemberOrder&returnUrl=%2Fmyshop%2Forder%2Flist.html'){
			$('.member-form .tab-menu li').removeClass('on');
			$('.member-form .tab-menu li').eq(1).addClass('on');
            $('.member-form .tab-menu li').eq(3).addClass('on');
			$('.Login_cont').hide();
			$('.NoLogin_cont').show();
		}else{
			$('.member-form .tab-menu li').removeClass('on');
			$('.member-form .tab-menu li').eq(0).addClass('on');
			$('.NoLogin_cont').hide();
			$('.Login_cont').show();
		}

        
		if (_catdIDTree == 'login.html?noMemberOrder&returnUrl=%2Fmyshop%2Findex.html'){
			$('.member-form .tab-menu li').removeClass('on');
			$('.member-form .tab-menu li').eq(0).addClass('on');
			$('.NoLogin_cont').hide();
			$('.Login_cont').show();
			$(".nomember_login").hide();
		}
        
        if(_catdIDTree.indexOf('noMember=1') != -1){
            $('.member-form .tab-menu li').removeClass('on');
			$('.member-form .tab-menu li').eq(1).addClass('on');
			$('.Login_cont').hide();
            $('.NoLogin_cont').hide();
            $('.link-box').show();
        }
        
        

        
        
    });
    
    // 탭동작
    $.tabs = function (cate, cont) {
        $(cate).click(function () {
            var $thisIndex = $(this).index();
            $(cate).removeClass('on');
            $(this).addClass('on');
            $(cont).removeClass('on');
            $(cont).eq($thisIndex).addClass('on');
        });
    }  
    
    
    
    
})(addjQuery);