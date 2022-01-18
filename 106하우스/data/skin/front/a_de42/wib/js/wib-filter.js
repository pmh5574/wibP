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
        
        
        $(document).on('click', '.pagination ul li a', function(e){
            e.preventDefault();
            _this.setPage($(this));
        });
        
        $('.filter_reset').on('click', function(e){
            e.preventDefault();
            _this.filterReset();
        });
        
        
    },
    
    getList : function(){
        var _this = this;
        var sort =  $('input[name="sort"]:checked').val();
        var pageNum = $('select[name="pageNum"]').val();
        console.log(sort);
        console.log(pageNum);
        
        $.ajax({
            'url' : this.ajaxUrl+'?cateCd='+this.cateCd,
            'method' : 'post',
            data : {
                'cateCd' : _this.cateCd,
                'filterSize' : _this.filterSize,
                'filterPrice' : _this.filterPrice,
                'page' : _this.page,
                'sort' : sort,
                'pageNum' : pageNum
            },
            success : function(e){
                


                
                $('.cont_list .filter_goods_list').empty().append(e);
                
                _this.checkClass();
            }
        });
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
    
    filterReset : function(){
        var _this = this;
        _this.page = 1;
        
        $('.filter_result .filterVal .filter_del').each(function(){
            
            $(this).trigger('click');
            
        });
        
        _this.getList();
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
        console.log('qwwe');
        var _this = this;
        var _href = obj.attr('href');
        var _split = _href.split('?page=');
        var filterPage = _split[1].split('&');            
        console.log(filterPage[0]);
        _this.page = filterPage[0];
        
        _this.getList();
    }
    
};

$(document).ready(function(){
    
    var _url = location.search;
    
    var cateCcd = _url.split('cateCd=')[1];
    var cateCd = cateCcd.split('&')[0];
    
    wibFilter.init({
        'url' : '/goods/filter_goods.php',
        'cateCd' : cateCd,
        'page' : 1
    });
});