$(document).ready(function () {
    
    // 탭 스크롤 픽스
    var header = $('#header_warp').height();        // 헤더 세로값
    var tabH = $('.item_goods_tab').height();       // 탭세로값
    var tabBox = $('.item_goods_tab').offset().top - header;       //헤더에서 탭세로 뺀값
    tabH = header + tabH;   // 헤더 + 탭 값
    
    $(window).scroll(function (event) {

        // 상단 고정
        var Top = $(this).scrollTop();

        if (Top > tabBox) {
            $('.item_goods_tab ul').addClass('fixed');
        } else {
            $('.item_goods_tab ul').removeClass('fixed');
        }

        $('.item_goods_sec > div').each(function () {
            var contTop = $(this).offset().top - tabH;
            if (Top >= contTop) {
                var idChk = $(this).attr('id');
                $('.item_goods_tab ul li').each(function () {
                    var id = $(this).find('a').attr('href');
                    id = id.split("#")[1];
                    if (idChk == id) {
                        $(this).addClass("on").siblings().removeClass("on");
                    }
                });
            }
        });

    });
    
    // 함께하면 좋은상품
    $('.goods_list .item_gallery_type ul').not('.slick-initialized').slick({
        slidesToShow: 5,
        slidesToScroll: 1,
        dots: false,
        arrows: true,
        infinite: false,
        speed: 500,
        prevArrow: $('#aro1_prev'),
        nextArrow: $('#aro1_next')
        //autoplay: false,
        //autoplaySpeed: 3000
    });


    // 리뷰 내용 클릭
    $('.js_detail').click(function () {
        $(this).prev('.js_data_row').removeClass('on');
        $(this).hide();
    });
    
});



