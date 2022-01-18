if( $('[rel="jourboo"]').size() == 0 ){
    var $cate = '';
    $.ajax({
        url: '/exec/front/Product/SubCategory',
        dataType : 'json',
        success: function(data) {
            // name,param,cate_no,parent_cate_no,design_page_url
            $.each(data, function(m,key) {
                // 1차 카테고리
                if(key.parent_cate_no=='1'){
                    $cate += '<li class="cate-grade-one"><a href="/product/list.html' + key.param +'">'+ key.name +'</a><span class="c-arrow"></span>';
                    $cate += '  <ul class="cate-grade-two-wrap">';
                    $.each(data, function(n,sub) {
                        // 2차 카테고리
                        if( key.cate_no == sub.parent_cate_no ){
                            $cate += '<li class="cate-grade-two hover-line"><a href="/product/list.html' + sub.param +'">'+ sub.name +'</a>';
                            $cate += '  <ul class="cate-grade-three-wrap">';
                            $.each(data, function(o,child) {
                                // 3차 카테고리
                                if( sub.cate_no == child.parent_cate_no ){
                                    $cate += '<li class="cate-grade-three"><a href="/product/list.html' + child.param +'">'+ child.name +'</a>';
                                    $cate += '  <ul class="cate-grade-four-wrap">';
                                    $.each(data, function(p,last) {
                                        // 4차 카테고리
                                        if( child.cate_no == last.parent_cate_no ){
                                            $cate += '<li class="cate-grade-four"><a href="/product/list.html' + last.param +'">'+ last.name +'</a></li>';                                    
                                        }
                                });
                                $cate += '  </ul>';
                                $cate += '</li>';   
                                };
                            });    
                            $cate += '  </ul>';
                            $cate += '</li>';
                        };
                    });
                    $cate += '  </ul>';
                    $cate += '</li>';
                };
            });

            $('.product.nav-layer .category-nav>ul').prepend($cate);
            
            // 하위 카테고리
            $('.cate-grade-one').find('.cate-grade-two-wrap .cate-grade-two').parent('.cate-grade-two-wrap').siblings('.c-arrow').css('opacity','1');
            $('.c-arrow').click(function(){
                $(this).toggleClass('on');
                $(this).siblings('.cate-grade-two-wrap').slideToggle(300);
            });
            
        }
    });
};