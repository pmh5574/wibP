var _forms = '';
    
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// 슬라이드 가격 텍스트 출력
function setTextPrice(){
    var fpStart = $( ".grape" ).slider( "values", 0 );
    var fpEnd = $( ".grape" ).slider( "values", 1 );
    if(fpStart == '150000' && fpEnd == '150000'){
        fpEnd = '99999999';
    }
    $('.sprice, .fpStartBox').text(numberWithCommas($( ".grape" ).slider( "values", 0 )));
    $('.eprice, .fpEndBox').text(numberWithCommas($( ".grape" ).slider( "values", 1 )));
    $('.fpStart').val($( ".grape" ).slider( "values", 0 ));
    $('.fpEnd').val(fpEnd);
}

// 가격 체크박스 해제
function removeAllCheck(){
    _targetLabel.removeClass('on');
    _targetBox.prop('checked', false);
    $('.checker').empty();
}

 // 하단선택 리스트중 가장 마지막 리스트에 after 제거
function totalBoxCheck(){
    
}

// 필터하단 삭제버튼
function boxClose(target){
    var _box = '';
    var _hide = '';
    var _input = '';
    
    switch(target){
        // 컬러 초기화
        case 'sel_color':
            _box = 'clBox';
            _hide = 'sel_color';
            _input ='filterColor';
            $('.color_box .color').removeClass('sel');
        break;
        
        // 가격 초기화
        case 'sel_price':
            _input ='filterColor';
            $('.priceBox .priceInsert').prop('checked', false);
            $('.priceBox label').removeClass('on');
            $('.checker').empty();
            wibSlider.slider( "values", [0, 150000] );
            setTextPrice();
        break;
        
        // 패턴 초기화
        case 'sel_pat':
            _box = 'ptBox';
            _hide = 'sel_pat';
            _input ='filterPattern';
            $('.pattern_box .filterpattern').prop('checked', false);
            $('.pattern_box label').removeClass('on');
        break;
        
        // 시즌 초기화
        case 'sel_sea':
            _box = 'seBox';
            _hide = 'sel_sea';
            _input ='filterSeason';
            $('.season_box .filterseason').prop('checked', false);
            $('.season_box label').removeClass('on');
        break;
        
        // 카테고리 초기화
        case 'sel_cate':
            $('.cateB').removeClass('on');
            $('.sel_cate').hide();
            $('.caBox').html('');
            $('#cateGoodsB').val('').prop('selected', true).trigger('change');
        break;
    }
    
    if(target != 'sel_price' && target != 'sel_cate'){
        $('.'+_box).empty();
        $('.'+_hide).hide();
        $('.'+_input).empty();
    }
    
}

// 필터 초기화
function allReset(){
    
}

// 검색 및 하단리스트용 엘리먼트 추가
function formSet(){
    if($('.goods_search_box form').length > 0){
        _forms = 'frmSearch';
    }else if($('.goods_pick_list form').length > 0){
        _forms = 'goods_pick_list';
    }
    
    var cateForm = $('.cateForm');
    
    var _addHtml = '';
    _addHtml += '<div class="filterColor" style="display:none"></div>';
    _addHtml += '<div class="filterPrice" style="display:none"><input type="hidden" name="fpStart" class="fpStart" value="0"><input type="hidden" name="fpEnd" class="fpEnd" value="200000"><div class="checker"></div></div>';
    _addHtml += '<div class="filterPattern" style="display:none"></div>';
    _addHtml += '<div class="filterSeason" style="display:none"></div>';
    $('#'+_forms).append(_addHtml).append(cateForm);
}
$(document).ready(function(){
    formSet();
});




var _targetLabel = $('.priceBox label');
var _targetBox = $('.priceBox .priceInsert');

// price slide
var wibSlider = $( ".grape" ).slider({
    range: true,
    min: 0,
    max: 150000,
    values: [ fpStart, fpEnd ],
    slide: function( event, ui ) {
        $('.sprice, .fpStartBox').text(numberWithCommas(ui.values[0]));
        $('.eprice, .fpEndBox').text(numberWithCommas(ui.values[1]));
        $('.fpStart').val(ui.values[0]);
        $('.fpEnd').val(ui.values[1]);
        
        var _findRanger = false;
        $('.priceInsert').each(function(index){
            var _thisprice = $(this).val().split('||');
            var _sPrice = parseInt(_thisprice[0]);
            var _ePrice = parseInt(_thisprice[1]);
            
            // 가격조건 있을시 체크
            if(ui.values[0] >= _sPrice && ui.values[1] <= _ePrice ){
                removeAllCheck();
                $(this).next('label').addClass('on');
                $(this).prop('checked', false);
                $('.checker').empty().append('<input type="hidden" name="priceCheck"  value="'+index+'">');
                _findRanger = true;
            }
        });
        // 가격조건 없을시 체크 초기화
        if(!_findRanger){
            removeAllCheck();
        }
    }
});
setTextPrice();

// price check
$('.priceInsert').on('click', function(){
    var _thisprice = $(this).val().split('||');
    var _sPrice = _thisprice[0];
    var _ePrice = _thisprice[1];

    removeAllCheck();
    $(this).prop('checked', true);
    $('.checker').empty().append('<input type="hidden" name="priceCheck"  value="'+($(this).parent().index())+'">');
    
    wibSlider.slider( "values", [_sPrice, _ePrice] );
    setTextPrice();
    if(_ePrice == '99999999'){
        $('.eprice').html('&nbsp;');
    }
});

// color check
$('.color_box .color').on('click', function(){
    var _colorThis = $(this);
    if(_colorThis.hasClass('sel')){
        _colorThis.removeClass('sel');
    }else{
        _colorThis.addClass('sel');
    }
    
    // 검색용 인풋 추가
    $('.filterColor').empty();
    $('.color_box .color').each(function(){
        var _nameThis = $(this);
        if(_nameThis.hasClass('sel') == true){ 
            $('.filterColor').append('<input type="hidden" name="filterColor[]" value="'+_nameThis.data('name')+'" data-code="'+_nameThis.css('background')+'" data-backs="'+_nameThis.data('backs')+'">');
        }
    });
    
    // 하단리스트 추가
    var colorInput = $('.filterColor input');
    var colorBox = $('.clBox');
    if(colorInput.length > 0){
        colorBox.empty();
        colorInput.each(function(){
            var _inputThis = $(this); 
            colorBox.append('<span class="clList"><span class="color '+_inputThis.data('backs')+'" style="background:'+_inputThis.data('code')+'"></span><span class="name">'+_inputThis.val()+'</span></span>');
        });
        $('.sel_color').css('display', 'inline-block');
    }else{
        $('.sel_color').hide();
        colorBox.empty();
    }
    // 마지막 after 제거
    totalBoxCheck();
});

// pattern check
$('.filterpattern').on('click', function(){
    $('.filterPattern').empty();
    $('.filterpattern').each(function(){
        var _patternThis = $(this);
        if(_patternThis.prop('checked') == true){
            $('.filterPattern').append('<input type="hidden" name="filterPattern[]" value="'+_patternThis.val()+'">');
        }
    });
    addFootList('pattern_box');
});

// season check
$('.filterseason').on('click', function(){
    $('.filterSeason').empty();
    $('.filterseason').each(function(){
        var _seasonThis = $(this);
        if(_seasonThis.prop('checked') == true){
            $('.filterSeason').append('<input type="hidden" name="filterSeason[]" value="'+_seasonThis.val()+'">');
        }
    });
    addFootList('season_box');
});

$('.filter_box .ok').on('click', function(){
    $('#'+_forms).submit();
});

$('.select_box .del').on('click', function(){
    var _targetData = $(this).data('del');
    boxClose(_targetData);
});

// 패턴 및 시즌 클릭 트리거
function otherTriggers(target, children, nums){
    $('.'+target+' .input_box').children().eq(nums).find('.'+children).trigger('click');
    $('.'+target+' .input_box').children().eq(nums).children('label').addClass('on');
    addFootList(target);
}

function cateTrigger(dataA, dataB){
    if(dataA != ''){
        $('#cateGoodsA').val(dataA).prop('selected', true).trigger('change');
        $('.cateA').each(function(){
            var _this = $(this);
            if(_this.data('cateval') == dataA){
                _this.trigger('click');
            }
        });
        if(dataB != ''){
            $('#cateGoodsB').val(dataB).prop('selected', true).trigger('change');
            $('.cateB').each(function(){
                var _this = $(this);
                if(_this.data('cateval') == dataB){
                    _this.trigger('click');
                }
            });
        }
    }
}

// 필터하단 선택한 필터 텍스트 추가
function addFootList(target){
    var text = '';
    var addText = '';
    var eachTarget = '';
    var eachBox = '';
    var eachShowBox = '';
    if(target == 'pattern_box'){
        eachTarget = 'filterPattern';
        eachBox = 'ptBox';
        eachShowBox = 'sel_pat';
    }else{
        eachTarget = 'filterSeason';
        eachBox = 'seBox';
        eachShowBox = 'sel_sea';
    }
    
    var targetInput = $('.'+eachTarget+' input');
    if(targetInput.length > 0){
        $('.'+eachShowBox).css('display', 'inline-block');
        $('.'+eachBox).empty();
        targetInput.each(function(){
            var _inputThis = $(this);
            text += _inputThis.val()+', ';
        });
        addText = text.slice('-'+text.length, -2);
        $('.'+eachBox).append(addText);
    }else{
        $('.'+eachShowBox).hide();
        $('.'+eachBox).empty();
    }
    
}

$('.reset').on('click', function(e){
    e.preventDefault;
    filterReset();
});

// 필터 초기화
function filterReset(){
    $('.select_box .del').each(function(){
        $(this).trigger('click');
    });
    $('.fpEnd').val('99999999');
    $('#cateGoodsA').val('').prop('selected', true).trigger('change');
    $('#cateGoodsB').val('').prop('selected', true).trigger('change');
    $('.total_box .ok').trigger('click');
}

/**
 * 카테고리 셀렉트 추가
 */
$('.cateA').click(function(){
    var _this = $(this);
    var _child = _this.next('ul').children().eq(0).data('cateval');
    var _selectData = _this.data('cateval');
    
    $('.cateBox ul').hide();
    _this.next('ul').show();
    
    
    if(_this.hasClass('on')){
        _selectData = '';
        _this.removeClass('on');
    }else{
        $('.cateA').removeClass('on');
        _this.addClass('on');
    }
    $('.cateB').removeClass('on');
    
    $('#cateGoodsA').val(_selectData).prop('selected', true).trigger('change');
    $('#cateGoodsB').val('').prop('selected', true).trigger('change');
});

$('.cateB').click(function(){
    var _this = $(this);
    var _prev = _this.parent('ul').prev('.cateA');

    if(!_prev.hasClass('on')){
        _prev.trigger('click');
    }
    
    if(_this.hasClass('on')){
        _this.removeClass('on');
        $('.sel_cate').hide();
        $('.caBox').html('');
        return false;
    }
    
    $('.cateB').removeClass('on');
    _this.addClass('on');
    
    $('#cateGoodsB').val(_this.data('cateval')).prop('selected', true).trigger('change');
    $('.sel_cate').css('display', 'inline-block');
    $('.caBox').html(_this.text());
});
