$(function(){
    
    // 상품정렬
    $('.sort_type').click(function(){
        $('.sort_inner').toggle(); 
        $(this).toggleClass('on');
    });
    
    
    // 필터
    $('.side_title.f_title').click(function(){
        $(this).toggleClass('on');
        $(this).siblings('.inner').toggle();
    });
    
    
});
