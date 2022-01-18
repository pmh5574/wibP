// 우편번호 검색 레이어팝업 변경

function gd_postcode_search_layer(zone, addr, zip) {

    clearTimeout(frameTime);

    var zip_width = ($(window).width() < 700)?'100%':'500px'; 
    var zip_style = '<style>.layers_on { display: block !important; position: fixed;width: '+zip_width+';height: 630px;top: 50%;left: 50%;transform: translate(-50%, -50%);z-index: 99999;border: 1px solid #000;}</style>';
    $('body').append(zip_style);

    var frame = $('iframe[name=ifrmProcess]');
    frame.attr('src', '../share/postcode_search.php?zoneCodeID='+zone+'&addrID='+addr+'&zipCodeID='+zip);

    frame.addClass('layers_on');
    $('#layerDim').removeClass('dn');

    var notDim = '';
    if(zone == 'shippingZonecode'){
        notDim = 'y';
    }

    frame.load(function(){
        frame.contents().find('head').append('<link type="text/css" rel="stylesheet" href="/data/skin/front/pure/css/reset.css?ts=1577750766">')
        frame.contents().find('.post-search h1').html('<div class="title">우편번호 찾기</div><div id="close" class="title-close" onclick="window.parent.frmclose(\''+notDim+'\');">X</div>');
        frame.contents().on('click', '.address', function(){
            frmclose(notDim);
        });
    });

}

function frmclose(notDim){
    console.log(notDim);
    var frame = $('iframe[name=ifrmProcess]');
    frame.removeClass('layers_on');
    if(notDim == ''){
        $('#layerDim').addClass('dn');
    }

    frameTime = setTimeout(function(){
        frame.attr('src', '/blank.php');
        clearTimeout(frameTime);
    }, 2000);
}

var frameTime;
var layerSearchArea = [];

$(document).ready(function(){
    
    // 마이페이지 회원정보 변경 -> 우편번호 변경
    if($('#btnPostcode').length > 0){
        $("#btnPostcode").attr('id', '#btnPostcodeNew').click(function(){
            gd_postcode_search_layer('zonecode', 'address', 'zipcode');
        });
    }
    
    // 마이페이지 배송지 관리 -> 새 배송지 추가
    if($('.btn_add_shipping').length > 0){
        $('.btn_add_shipping').removeClass('btn_open_layer').click(function(){
            var target = $(this).attr('href');
            if (target == '#lyDeliveryAdd') {
                // 배송지 등록/수정 모드에 따른 파라미터 설정
                var param = !_.isUndefined($(this).data('sno')) && $(this).data('sno') > 0 ? '?sno=' + $(this).data('sno') : '';

                // AJAX 호출
                $(target).empty();
                $.get('../order/layer_shipping_address_regist.php' + param, function(data){
                    $(target).append(data);
                    $(target).find('>div').center();
                    $(target).find('.layer_wrap_cont').css('top', (parseInt($(target).find('.layer_wrap_cont').css('top').slice(0,-2))) / 2 );
                    $(target).find('.btn_post_search').attr('onclick', "gd_postcode_search_layer('shippingZonecode', 'shippingAddress', 'shippingZipcode');");
                    $(target+', #layerDim').removeClass('dn');
                    $('body').css('overflow', 'hidden');
                });
            }
        });
    }

    // 주문서작성 우편번호 조회
    if($('.address_postcode .btn_post_search').length > 0){
        $(".address_postcode .btn_post_search").attr('onclick', "gd_postcode_search_layer('receiverZonecode', 'receiverAddress', 'receiverZipcode');");
    }

    // 주문서작성 배송지관리
    $('.js_shipping').removeClass('btn_open_layer').click(function(e){
        var shippingNo = '';
        $('#myShippingListLayer').empty();
        if (typeof $(this).data('no') != 'undefined') {
            shippingNo = $('.btn_open_layer.js_shipping').index(this);
        }
console.log('dddd');
        var target = $(this).attr('href');
        $.get('../order/layer_shipping_address.php?shippingNo=' + shippingNo, function(data){
            $('#myShippingListLayer').append(data);
            $('#myShippingListLayer').find('>div').center();
            $(target).find('.layer_wrap_cont').css('top', (parseInt($(target).find('.layer_wrap_cont').css('top').slice(0,-2))) / 2 );
            $(target).find('.btn_ly_add_shipping').removeClass('btn_open_layer');
            //$(target).find('.btn_post_search').attr('onclick', "gd_postcode_search_layerssss('shippingZonecode', 'shippingAddress', 'shippingZipcode');");
            $(target+', #layerDim').removeClass('dn');
            $('body').css('overflow', 'hidden');
        });
    });
    
});

