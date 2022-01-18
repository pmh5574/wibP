// 우편번호 검색 레이어팝업 변경

function gd_postcode_search_layer(zone, addr, zip) {

    clearTimeout(frameTime);

    var zip_width = ($(window).width() < 1024)?'100%':'500px'; 
	var zip_height = ($(window).width() < 1024)?'100%':'630px';
	var zip_top = ($(window).width() < 1024)?'0':'50%';
	var zip_transform = ($(window).width() < 1024)?'translateX(-50%)':'translate(-50%, -50%)';
    var zip_style = '<style>.layers_on { display: block !important; position: fixed;width: '+zip_width+';height: '+zip_height+';top: '+zip_top+';left: 50%;transform: '+zip_transform+';z-index: 99999;border: 1px solid #000;}</style>';
    $('body').append(zip_style);

    var frame = $('iframe[name=ifrmProcess]');
    frame.attr('src', '../share/postcode_search.php?zoneCodeID='+zone+'&addrID='+addr+'&zipCodeID='+zip);
    
    frameOnTime = setTimeout(function(){
        frame.addClass('layers_on');
        clearTimeout(frameOnTime);
    }, 500);
    
    $('#layerDim').removeClass('dn');

    var notDim = '';
    if(zone == 'shippingZonecode'){
        notDim = 'y';
        $('#myShippingListLayer').hide();
    }

    frame.load(function(){
        frame.contents().find('head').append('<link type="text/css" rel="stylesheet" href="/data/skin/front/my10/wib/css/wib-addr.css">');
        frame.contents().find('.post-search h1').html('<div class="title">우편번호 검색</div><div id="close" class="title-close" onclick="window.parent.frmclose(\''+notDim+'\');">X</div>');
        frame.contents().on('click', '.address', function(){
            frmclose(notDim);
        });
    });

}

function gd_postcode_search_layer_ex(zone, addr, zip, exname) {

    var zip_width = ($(window).width() < 1024)?'100%':'500px'; 
	var zip_height = ($(window).width() < 1024)?'100%':'630px';
	var zip_top = ($(window).width() < 1024)?'0':'50%';
	var zip_transform = ($(window).width() < 1024)?'translateX(-50%)':'translate(-50%, -50%)';
    var zip_style = '<style>.layers_on { display: block !important; position: fixed;width: '+zip_width+';height: '+zip_height+';top: '+zip_top+';left: 50%;transform: '+zip_transform+';z-index: 99999;border: 1px solid #000;}</style>';
    $('body').append(zip_style);

    var frame = $('iframe[name=ifrmProcess]');
    frame.attr('src', '../share/postcode_search.php?zoneCodeID='+zone+'&addrID='+addr+'&zipCodeID='+zip);

    var notDim = '';
    if(zone == 'shippingZonecode'){
        notDim = 'y';
    }
    
    var origin = 'myShippingListLayer';
    if(exname){
        origin = exname;
    }

    frame.load(function(){
        frame.contents().find('head').append('<link type="text/css" rel="stylesheet" href="/data/skin/front/designwib/wib/css/wib-addr.css">');
        frame.contents().find('.post-search h1').html('<div class="title">우편번호 검색</div><div id="close" class="title-close" onclick="window.parent.frmclose(\''+notDim+'\', \''+origin+'\');">X</div>');
        frame.contents().on('click', '.address', function(){
            frmclose(notDim,origin);
        });
    });

}

function gd_postcode_search_layer_open(hidename){
    var frame = $('iframe[name=ifrmProcess]');
    frame.addClass('layers_on');
    $('#'+hidename).hide();
}

function frmclose(notDim, exname){

    var frame = $('iframe[name=ifrmProcess]');
    frame.removeClass('layers_on');
    var origin = 'myShippingListLayer';
    if(exname){
        origin = exname;
    }

    $('#'+origin).show();
    if(notDim == ''){
        $('#layerDim').addClass('dn');
    }

    frameTime = setTimeout(function(){
        frame.attr('src', '/blank.php');
        clearTimeout(frameTime);
    }, 2000);
}

var frameTime;
var frameOnTime;
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

                gd_postcode_search_layer_ex('shippingZonecode', 'shippingAddress', 'shippingZipcode', 'lyDeliveryAdd');
                
                // AJAX 호출
                $(target).empty();
                $.get('../order/layer_shipping_address_regist.php' + param, function(data){
                    $(target).append(data);
                    $(target).find('>div').center();
                    $(target).find('.layer_wrap_cont').css('top', (parseInt($(target).find('.layer_wrap_cont').css('top').slice(0,-2))) / 2 );
                    $(target).find('.btn_post_search').attr('onclick', "gd_postcode_search_layer_open('lyDeliveryAdd');");
                    $(target+', #layerDim').removeClass('dn');
                    $('body').css('overflow', 'hidden');
                });
            }
        });
    }

    // 주문서작성 우편번호 조회
    if($('.order_wrap .address_postcode .btn_post_search').length > 0){
        $(".order_wrap .address_postcode .btn_post_search").attr('onclick', "gd_postcode_search_layer('receiverZonecode', 'receiverAddress', 'receiverZipcode');");
    }

    // 주문서작성 배송지관리
    $('.js_shipping').removeClass('btn_open_layer').click(function(e){
        var shippingNo = '';
        $('#myShippingListLayer').empty();
        if (typeof $(this).data('no') != 'undefined') {
            shippingNo = $('.btn_open_layer.js_shipping').index(this);
        }

        var target = $(this).attr('href');
        $.get('../order/layer_shipping_address.php?shippingNo=' + shippingNo, function(data){
            $('#myShippingListLayer').append(data);
            $('#myShippingListLayer').find('>div').center();
            $(target).find('.layer_wrap_cont').css('top', (parseInt($(target).find('.layer_wrap_cont').css('top').slice(0,-2))) / 2 );
            $(target).find('.btn_ly_add_shipping').removeClass('btn_open_layer').unbind('click').on('click', function(){
                // 등록/수정 레이어 바인딩
                var target = $(this).attr('href');
                if (target == '#deliveryAddLayer') {
                    // 배송지 등록/수정 모드에 따른 파라미터 설정
                    var param = '?';
                    param += !_.isUndefined($(this).data('sno')) && $(this).data('sno') > 0 ? 'sno=' + $(this).data('sno') : '';
                    param += '&shippingNo=';
                    
                    gd_postcode_search_layer_ex('shippingZonecode', 'shippingAddress', 'shippingZipcode');
                    
                    // AJAX 호출
                    $.get('../order/layer_shipping_address_regist.php' + param, function(data){
                        $('#myShippingListLayer').empty().append(data).find('>div').center();
                        $('#myShippingListLayer').find('.btn_post_search').attr('onclick', "gd_postcode_search_layer_open('myShippingListLayer');");
                    });
                }
            });
            //$(target).find('.btn_post_search').attr('onclick', "gd_postcode_search_layerssss('shippingZonecode', 'shippingAddress', 'shippingZipcode');");
            $(target+', #layerDim').removeClass('dn');
            $('body').css('overflow', 'hidden');
        });
    });
    
});

