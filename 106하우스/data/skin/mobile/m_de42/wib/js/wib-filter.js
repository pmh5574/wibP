var wibFilter = {
    
    ajaxUrl : '',
    cateCd : '',
    page : '',
    sort : '',
    filterSize : [],
    filterPrice : [],
    
    init : function(option){
        var _this = this;
        this.ajaxUrl = option.url;
        this.cateCd = option.cateCd;
        this.page = option.page;
        this.sort = option.sort;
        
        $('.f_size span.check_box_img').on('click', function(e){
            
            e.preventDefault();
            _this.setSize($(this));
        });
        
        $('.f_price span.check_box_img').on('click', function(e){
            
            e.preventDefault();
            _this.setPrice($(this));
        });
        
        $('.btn_box button.more_btn').on('click', function(e){
            
            e.preventDefault();
            _this.setPage($(this));
            
        });
        
        $('select[name="goods_sort"]').on('change', function(e){
            
            e.preventDefault();
            
            _this.page = 1;
            _this.getList();
            
        });
        
        $('.fil_reset, .filter_reset').on('click', function(e){
           
            e.preventDefault();
            _this.filterReset();
        });
        
        
        
    },
    
    getList : function(){
        var _this = this;
        var sort =  $('select[name="goods_sort"]').val();

        $.ajax({
            'url' : this.ajaxUrl+'?cateCd='+this.cateCd,
            'method' : 'post',
            data : {
                'cateCd' : _this.cateCd,
                'filterSize' : _this.filterSize,
                'filterPrice' : _this.filterPrice,
                'page' : _this.page,
                'sort' : sort
            },
            
            success : function(data){
                
                var viewCnt = $('#wibPaging').val();

                if($(data).find("li.no_bx").length && _this.page != 1) {

                    alert("더이상 상품이 없습니다");
                    $('.more_btn').hide();
                    

                }else if($(data).find("li.no_bx").length && _this.page == 1){
                    
                    $('.cont_list .filter_goods_list .goods_product_list').html(data);
                    $('.more_btn').hide();
                    
                }else {
                    
                    if(_this.page == 1){
                        $('.cont_list .filter_goods_list .goods_product_list').html(data);
                        $('.more_btn').show();

                    }else{
                        $('.cont_list .filter_goods_list .goods_product_list').append(data);

                    }

                    
                    $('.btn_box button.more_btn').data('page',parseInt(_this.page)+1);
                    

                    if ($(data).find('.goods_list_info').length < viewCnt) {
                        $('.more_btn').hide();
                    }
                }

                
                _this.checkClass();
            }
        });
    },
    
    filterReset : function(){
        var _this = this;
        _this.page = 1;
        
        $('.filter_inner ul li').each(function(){
            if($(this).hasClass('on')){
                $(this).children('span.check_box_img').trigger('click');
            }
        });
        
        _this.getList();
    },
    
    numberWithCommas : function(x){
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    
    checkClass : function(){
        
        var _check = 0;
        
        $('.filter_result .filterVal').each(function(){
            if(!($(this).find('p').html() == '' || $(this).find('p').html() == undefined)){
                _check++;
            }
        });
        
        if(_check > 0){
            if(!$('.filter_result').hasClass('filterOn')){
                $('.filter_result').addClass('filterOn');
            }
        }else{
            if($('.filter_result').hasClass('filterOn')){
                $('.filter_result').removeClass('filterOn');
            }
        }
    },
    
    delPrice : function(code){
        var _this = this;
        _this.page = 1;
        
        $('.pr_'+code).remove();
        $('.prpa_'+code).removeClass('on');
        $('.prpa_'+code+' .check_box_img').removeClass('on');
        
        _this.filterPrice = [];
        
        $('.hiddenPrice > input').each(function(){
            var eThisClass = $(this).attr('class').replace('pr_','');
            
            _this.filterPrice.push(eThisClass);
        });
        
        _this.getList();
    },
    
    setPrice : function(obj){
        var _this = this;
        _this.page = 1;

        var priceCode = obj.data('price');
        var priceHtml = obj.text();
        
        $('.hiddenPrice').append("<input type='hidden' class='pr_"+priceCode+"' value='"+priceHtml+"'>");
        
        if(obj.parent().hasClass('on')){
            _this.delPrice(priceCode);
            return false;
        }
        
        obj.addClass('on');
        obj.parent().addClass('on prpa_'+priceCode);
        
        var appendPrice = '';
        _this.filterPrice = [];
        
        $('.hiddenPrice > input').each(function(){
            var eThis = $(this).val();
            var eThisClass = $(this).attr('class').replace('pr_','');

            appendPrice += '<div class="pr_'+eThisClass+'">';
            appendPrice += '<p>'+eThis+'</p>';
            appendPrice += '<button type="button" class="filter_del" onclick="wibFilter.delPrice(\''+eThisClass+'\')"></button>';
            appendPrice += '</div>';
            _this.filterPrice.push(eThisClass);
        });
        
        $('.filter_result .fPrice').empty().append(appendPrice).css('display', 'inline-block');
        $('.filter_result .fPrice').show();
        
        _this.getList();
        
    },
    
    delSize : function(code){
        var _this = this;
        _this.page = 1;
        
        $('.sz_'+code).remove();
        $('.szpa_'+code).removeClass('on');
        $('.szpa_'+code+' .check_box_img').removeClass('on');
        _this.filterSize = [];
        $('.hiddenSize > input').each(function(){
            var eThis = $(this).val();
            _this.filterSize.push(eThis);
        });
        _this.getList();
    },
    
    setSize : function(obj){
        var _this = this;
        _this.page = 1;

        var sizeClass = obj.data('size');
        var sizeCode = obj.text();
        
        

        $('.hiddenSize').append("<input type='hidden' class='sz_"+sizeClass+"' value='"+sizeCode+"'>");
        
        if(obj.parent().hasClass('on')){
            _this.delSize(sizeClass);
            return false;
        }
        
        obj.addClass('on');
        obj.parent().addClass('on szpa_'+sizeClass);
        
        var appendSize = '';
        _this.filterSize = [];
        $('.hiddenSize > input').each(function(){
            var eThis = $(this).val();

            var eSizeClass = $(this).attr('class').split('sz_');

            appendSize += '<div class="sz_'+eSizeClass[1]+'">';
            appendSize += '<p>'+eThis+'</p>';
            appendSize += '<button type="button" class="filter_del" onclick="wibFilter.delSize(\''+eSizeClass[1]+'\')"></button>';
            appendSize += '</div>';
            _this.filterSize.push(eThis);
        });
        
        
        $('.filter_result .fSize').empty().append(appendSize).css('display', 'inline-block');
        $('.filter_result .fSize').show();
        
        _this.getList();
    },
    
    setPage : function(obj){
        
        var _this = this;
        var filterPage = obj.data('page');

        _this.page = filterPage;
        
        _this.getList();
    }
    
};

$(document).ready(function(){
    
    var _url = location.search;
    
    var cateCd = _url.split('?cateCd=')[1];
    
    wibFilter.init({
        'url' : '/goods/filter_goods.php',
        'cateCd' : cateCd,
        'page' : 1
    });
});
