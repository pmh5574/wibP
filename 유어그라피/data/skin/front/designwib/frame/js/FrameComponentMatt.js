var FrameComponentMatt = {
    
    // 프레임 스튜디오 컨텐츠 화면 전환
    setContent : function(){
      
        // 진행도 셋팅
        this.setState();
        
        // 프레임 화면 클래스 추가
        $('#frame_wrap').addClass('left');
        
        // 푸터 레이아웃 변경
        $('#frame_footer, #frame_wrap').addClass('left');
        $('.total_price').removeClass('hidden');
        $('#frame_footer .right_txt').removeClass('hidden');
        
        // 화면 변경
        $('#frameStart').addClass('hidden');
        $('#content').removeClass('hidden');
        
        // 페이지 변경으로 인한 슬릭 재활성처리
        this.setSlickRefresh();
    },
    
    // 우측 정보페이지 전환
    setAction : function(){
        
        // 진행도 셋팅
        this.setState();
        
        // 진행도 체크에 따른 화면 변경
        var page = this.contentPage[parseInt(this.nowState) - 1];

        $('.right_all section').addClass('hidden');
        $('#'+page).removeClass('hidden');

        if(this.artPrintType == 'frame'){
            $('.artCommd').addClass('hidden');
        }else{
            $('.artCommd').removeClass('hidden');
        }

    },

    
    // 진행도 셋팅
    setState : function(){
        if(this.nowState > this.maxState){
            this.maxState = this.nowState;
        }
        
        // 헤더 네비게이션 진행도 셋팅
        this.setHeader();
    },
    
    // 헤더 네비게이션 진행도 셋팅
    setHeader : function(){
        var setState = this.nowState - 1;
        var headNavTarget = $('.f_navi > ul li');
        
        headNavTarget.removeClass('on');
        headNavTarget.each(function(e){
            if(e <= setState){
                $(this).addClass('on');
            }
        });
    },
    
    // 슬릭 재활성
    setSlickRefresh : function(){
        var imgNav = $('#frame_left .img_box .img_nav');
	var imgCont = $('#frame_left .img_box .img_cont ul');
        imgNav.slick('refresh');
        imgCont.slick('refresh');
    },
    
    // 종이재질 선택시 데이터 입력
    setArtInfo : function(art){
        var _this = this;
        _this.artInfo = {}; // 종지재질 정보
        
        _this.artInfo.artName = art.data('name');
        _this.artInfo.artCd = art.data('artCd');
        _this.artInfo.priceA = art.data('rollprice-a');
        _this.artInfo.lengthA = art.data('length-a');
        _this.artInfo.priceB = art.data('rollprice-b');
        _this.artInfo.lengthB = art.data('length-b');
        _this.artInfo.itemPrice = art.data('itemprice');
        _this.artInfo.ppA = art.data('ppa');
        _this.artInfo.ppB = art.data('ppb');
        _this.artInfo.ppC = art.data('ppc');
        _this.artInfo.ppD = art.data('ppd');
        _this.artInfo.ppE = art.data('ppe');
        _this.artInfo.ppF = art.data('ppf');
        _this.artInfo.ppG = art.data('ppg');

    },
    
    // 드랍 이미지 업로더 
    setDropUploader : function(){
        
        var _this = this;
        var _totalWin = _this.windowType;
        
        $.ajax({
            url : '/frame/multi_drop_zone.php',
            method : 'post',
            data : { multiNum :  _totalWin },
            success : function(e){
                $('.photo_pop_all').empty().append(e);
                $('.cropBox').empty();
                for(var i =0; i < _totalWin; i++){
                    _this.setDropzone(i+1);
                    _this.imgInfo.push({
                        imgPath : '',
                        widthSize : 0,
                        heightSize : 0,
                        filename : '',
                        thumnail : ''
                    });
                    _this.cropper['crop'+i] = [];
                }
            }
        });
        console.log(_this.imgInfo);
        console.log(_this.cropper);
    },
    
    // 이미지 업로드 표시
    textHookfunc : function(numval){
        
        var _this = this;
        clearTimeout(textHook);      
        
        $('.file_down .loading strong').text(numval);
        num = numval;
        num ++;
        if(num <= maxNum){
            textHook = setTimeout(function(){
                _this.textHookfunc(num);
            }, 10);
        }
        
        if(num >= 100){
            textHook = setTimeout(function(){
                $('.pop_close').trigger('click');
            }, 1000);
        }
    },
    
    setDropzone : function(num){
        var _this = this;
        var fileType = '';
        
        $(".dropzone"+num).dropzone({
            maxFilesize: 10,
            autoProcessQueue: false,
            
            init : function(){
                var myDropzone = this; 

                $('#photo_up_btn').on("click", function () { 
                    $('.resultBox').addClass('hidden');
                    $('.cropBox').empty();
                    myDropzone.processQueue(); 
                });
                
            },
            
            // addedfile전 조건체크
            accept: function(file, done) {
                var reader = new FileReader();
                fileType = file.type;
                
                reader.onload = function(event) {
                    // event.target.result contains base64 encoded image
                    var base64String = event.target.result;
                    $('.dropzoneLi'+num).addClass('on');
                    $('.dropzoneLi'+num+' .dz-message').addClass('hidden');
                    $('.dropzoneLi'+num+' .dz-reault').removeClass('hidden');
                    $('.dropzoneLi'+num+' .dz-reault .img img').attr('src', base64String);
                    $('.dropzoneLi'+num+' .dz-reault .name p').text(file.name);
                };
                reader.readAsDataURL(file);
                
                if(file){
                    done();
                }
            },
            
            addedfile : function(file){
                
            },
            sending : function(){
                console.log(2);
            },
            uploadprogress : function(file, progress, bytesSent) {
                console.log(4);
            },
            success : function(file, result){
                console.log(5);
                
                // 업로드된 이미지 위치정보
                _this.imgInfo[num-1].imgPath = result.img;
                
                // dropzone 파일 이미지 가로X세로
                _this.imgInfo[num-1].widthSize = file.width;
                _this.imgInfo[num-1].heightSize = file.height;
                // 이미지 파일명
                _this.imgInfo[num-1].filename = file.name;
                
                console.log(_this.imgInfo);
            },
            complete : function(result){
                console.log('이미지 업로드 완료');
                _this.setCropBox(fileType, num);
                $('.imgContBox').addClass('hidden');
                $('.photo_pop').removeClass('on');
                $('.dark_bg').removeClass('on');
                _this.artPrintType = 'print';
            },
            error : function(result){
                
            }
        });
        
    },
    
    // 각 이미지별 크로퍼 플러그인 셋팅
    setCropBox : function(fileType, num){
        var _this = this;
        
        var newWidht = newHeight = 463;
        if(_this.imgInfo[num-1].widthSize > _this.imgInfo[num-1].heightSize){
            // 가로 이미지 기반으로 이미지 세로 비율값
            newHeight = 463 * _this.imgInfo[num-1].heightSize / _this.imgInfo[num-1].widthSize;
        }else{
            // 세로 이미지 기반으로 이미지 가로 비율값
            newWidht = 463 * _this.imgInfo[num-1].widthSize / _this.imgInfo[num-1].heightSize;
        }
        
        var hiddenClass = (num == 1)?'':'hidden';
        
        $('.cropBox').append('<div class="crImg'+num+' '+hiddenClass+' crImgBox"><img src="'+_this.imgInfo[num-1].imgPath+'"></div>').removeClass('hidden');
        $('.cropBox .crImg'+num+'').css({
            'width' : newWidht+'px',
            'height' : newHeight+'px'
        });
        
        _this.imgInfo[num-1].thumnail = '<li class="eventBtn" data-event="changeImg" data-num="'+num+'"><img src="'+_this.imgInfo[num-1].imgPath+'"></li>';
        
        if(_this.windowType == $('.crImgBox').length){
            $('.cropBox').append('<div class="img_num inline"><div>이미지</div><p><span>1</span> / '+_this.windowType+'</p></div>');
            
            var thumList = '<div class="select_img_list">';
            thumList += '<ul class="inline">';
            for(var ti=0;ti<_this.imgInfo.length;ti++){
                thumList += _this.imgInfo[ti].thumnail;
            }
            thumList += '</ul></div>';
            $('.cropBox').append(thumList).css('height', 'auto');
            $('.cropBox').find('.select_img_list ul li:eq(0)').addClass('on');
        }
        
        var images = $('.cropBox .crImg'+num+' img');
        
        _this.cropper['crop'+num] = new Cropper(images[0], {
            viewMode : 1,
            zoomOnWheel : false,
            aspectRatio: 500 / 500,
            ready: function (event) {

                var cropPrintImg = $('.crImg'+num+' .cropper-hide');

                _this.cropper['crop'+num].zoomTo({
                  x: cropPrintImg[0].clientWidth ,
                  y: cropPrintImg[0].clientHeight
                });

            },

            crop: function (event) {
                
                if(_this.artPrintType == 'frame'){
                    $('.cropBox').addClass('hidden');
                    $('#crop_image').addClass('hidden');
                    $('.imgContBox').addClass('hidden');
                    $('#crop_image .save_btn').trigger('click');
                }
            },

            zoom: function (event) {
              // Keep the image in its natural size
              if (event.detail.oldRatio === 1) {
                event.preventDefault();
              }
            }
        });
        
        if(_this.artPrintType != 'frame'){
            $('#print_paper').addClass('hidden');
            $('#crop_image').removeClass('hidden');
        }
        
        // 크롭 이미지 저장
        if(_this.windowType == $('.crImgBox').length){
            $('#crop_image .save_btn').unbind('click').click(function (){
                var frameImgInfo = [];
                var sucNum = 1;
                for(var ci=1;ci<_this.windowType+1;ci++){
                    console.log(ci);
                    var mimeType = fileType.split('image/');
                    var cropData = _this.cropper['crop'+ci].getData();

                    _this.cropper['crop'+ci].getCroppedCanvas().toBlob(function(blob){

                        var cropFormdata = new FormData();
                        cropFormdata.append('file', blob, 'example.'+mimeType[1]);
                        $.ajax({
                            url : '/frame/upload.php',
                            method: 'POST',
                            data: cropFormdata,
                            processData: false,
                            contentType: false,
                            cache : false,
                            success : function(e){
                                console.log(e);
                                frameImgInfo.push({
                                    imgsrc : e,
                                    cropdata : cropData
                                });
                                
                                if(_this.windowType == sucNum){
                                    _this.setFrameImg(frameImgInfo);
                                    console.log('ch');
                                }
                                console.log(_this.windowType+ '//' + sucNum);
                                sucNum++;
                                //_this.setFrameImg(e, cropData);
                            }
                        });
                    }, 'image/jpeg');
                }

            });
        }
        
        if(_this.artPrintType != 'frame'){
            $('.btn_box').removeClass('hidden');
        }
    },
    
    
    // 합성된 이미지 출력
    setFrameImg : function(frameImgInfo){
        
        var _this = this;
        var ranPageNum = Math.random(100000);
        var imgSrc = [];
        var cropWidth = [];
        var cropHeight = [];
        
        
        for(var sf=0;sf<frameImgInfo.length;sf++){
            imgSrc.push(frameImgInfo[sf].imgsrc);
            cropWidth.push(frameImgInfo[sf].cropdata.width);
            cropHeight.push(frameImgInfo[sf].cropdata.height);
        }
        
        
        var printFrameInfo = _this.setPrintFrameInfo(frameImgInfo);
        var artSizeSp = _this.artSize.split('_');
        
        $('input[name=artWidth]').val(artSizeSp[0]);
        $('input[name=artHeight]').val(artSizeSp[1]);
        
        $.ajax({
            url : '/frame/create_img_matt.php?v='+ranPageNum,
            method: 'POST',
            cache : false,
            data : { 
                cropImg : imgSrc, width : cropWidth, height : cropHeight, 
                finSize : artSizeSp[0], folder : _this.frameFolder, mount : _this.frameBaseMount, 
                mattTopColor : _this.mattTopColor, mattBotColor : _this.mattBotColor,
                mattTop : _this.mattTop, mattLeft : _this.mattLeft, mattRight : _this.mattRight,
                mattBottom : _this.mattBottom, finSizeW : artSizeSp[0], finSizeH : artSizeSp[1],
                window : _this.windowType
            },
            success : function(e){
                console.log('규격변경');                
                
                var addStyle = (cropHeight[0] > (cropWidth[0] * _this.windowType ) )?'width: auto;':'width: 100%;height: auto;';
                $('.cropBox').addClass('hidden');
                $('.resultBox').empty().append('<img src="'+e+'" style="'+addStyle+'">').removeClass('hidden');

                _this.resultImg = e;
                
                if(_this.contentMove){
                    console.log(_this.contentMove);
                    // 진행도 업데이트
                    _this.nowState = 3;
                    _this.setAction();

                    // 우측 컨텐츠화면 프린트 사이즈로 변경
                    $('#crop_image').addClass('hidden');
                    $('#artsize').addClass('hidden');
                    $('#print_size').removeClass('hidden');
                }
                console.log(_this.imgInfo);
                // 프린트 정보 출력 print_w
                $('.print_w').text(artSizeSp[0]);    // 프린트물 가로 사이즈
                $('.print_h').text(artSizeSp[1]);   // 프린트물 세로 사이즈
                
                if(_this.artPrintType == 'frame'){
                    $('.upload_name span').empty();
                    $('.upload_name').hide();
                }else{
                    $('.upload_name span').empty();
                    for(var fNum in _this.imgInfo){
                        var fComma = '';
                        if(fNum > 0){
                            fComma = ', ';
                        }
                        $('.upload_name span').append(fComma+_this.imgInfo[fNum].filename);
                    }
                }
                
                //$('.upload_name span').text(_this.filename);    // 업로드 이미지 이름
                
                
                // 윈도우(프레임) 여백은 현재 20으로 고정 차후 변경시 수정
                _this.frameWidth = ( artSizeSp[0] * _this.windowType ) - 10 + _this.mattLeft + _this.mattRight + (20*2); 
                _this.frameHeight = ( artSizeSp[1] * _this.windowType ) - 10 + _this.mattTop + _this.mattBottom + (20*2); 
                $('.frame_w').text(_this.frameWidth);
                $('.frame_h').text(_this.frameHeight);
                
                if(_this.artPrintType == 'frame' && _this.contentMove){
                    //$('.selectSizeBtn').trigger('click');
                    $('#artsize .tab_tit').addClass('half');
                }
                
                
                _this.setFramePrice();
                
                //$('.frameshadow').removeClass('hidden');
                //clearTimeout(textHook);
                
                //textHook = setTimeout(function(){
                //    _this.setFrameShadow();
                //}, 100);
                
                _this.contentMove = true;
            }
        });
        
    },
    
    setFrameList : function(){
        var _this = this;
        $.ajax({
            url : '/frame/goods_ps.php',
            method : 'post',
            data : { mode : 'get_frame_list', color : _this.frameBaseColor, frameBaseType : _this.frameBaseType },
            success : function(e){
                
                if(e.code == 200){
                    var framelist = '';
                    var fmList = e.list;
                    var onClass = '';
                    for(var fi =0;fi<e.total;fi++){
                        onClass = (fi==0)?'on':'';
                        framelist += '<li class="'+onClass+' eventBtn" data-folder="'+fmList[fi].goodsNo+'" data-event="selectFrame">';
                        framelist += '<div class="chk_box">';
                        framelist += '<img src="/data/goods/'+fmList[fi].imagePath+fmList[fi].imageName+'" alt="'+fmList[fi].goodsNm+'" >';
                        framelist += '<div class="btn_box inline">';
                        framelist += '<button type="button" class="frame_info_btn" data-goodsno="'+fmList[fi].goodsNo+'"></button>';
                        var frameVideoCl = (fmList[fi].externalVideoUrl)?'onV':'offV';
                        framelist += '<button type="button" class="frame_video_btn '+frameVideoCl+'" data-goodsno="'+fmList[fi].goodsNo+'"></button>';
                        framelist += '</div>';
                        framelist += '<div class="txt">';
                        framelist += '<h3>'+fmList[fi].shortDescription+'</h3>';
                        framelist += '</div>';
                        framelist += '</div></li>';
                    }
                    $('.on_frame ul').empty().append(framelist);
                    $('.on_frame ul li:nth-child(1)').trigger('click');
                    $('#frame_select .on_frame').addClass('show');
                    $('#frame_select .no_frame').removeClass('show');
                }else{
                    $('#frame_select .no_frame').addClass('show');
                    $('#frame_select .on_frame').removeClass('show');
                }
            }
        });
    },
    
    setFramePrice : function(){

        
        var _this = this;
        
        // 네변의 합
        _this.framePrice.borderSum = (_this.frameWidth * 2) + (_this.frameHeight * 2);
        //console.log('프레임 가로 : '+_this.frameWidth);
        //console.log('프레임 세로 : '+_this.frameHeight);
        
        // mm당 단가
        var mmPrice = _this.framePrice.oriLength / _this.framePrice.oriPrice;
        //console.log('mm당 단가 : '+mmPrice);
        
        // 원가
        var oriPrice = ( _this.framePrice.borderSum + _this.framePrice.ross ) * mmPrice;
        //console.log('원가 : '+oriPrice);
        
        // 인건비
        _this.framePrice.manPrice = _this.getManPrice(_this.frameWidth, _this.frameHeight);
       //console.log('인건비 : '+_this.framePrice.manPrice);
        
        // 프레임 마진율
        var maper = 0;
        //console.log(_this.goodsViewJson);
        for(maval in _this.maPer){
            if(_this.framePrice.borderSum >= maper && _this.framePrice.borderSum < _this.maPer[maval]){
                maper = _this.goodsViewJson['pp'+_this.alpha[maval]] / 100;
                //console.log(_this.goodsViewJson['pp'+_this.alpha[maval]]);
                break;
            }
            maper = _this.maPer[maval];
            //console.log('마진체크 : '+maper);
            //console.log('프레임체크 : '+_this.framePrice.borderSum);
        }
        //console.log('마진율 : '+maper);
        
        // 합계
        var settlePrice = ( oriPrice + _this.framePrice.manPrice ) * maper;
        //console.log('합계 : '+settlePrice);
        
        var tofix = ( Math.ceil((settlePrice / 100).toFixed(1)) ) * 100;
        //console.log('10원단위 올림 : '+tofix);
        
        _this.framePrice.settlePrice = tofix;
        $('.total_price p strong').text(_this.setComma(tofix));
        
        // 용지 가격 산출
        _this.setArtPrice();
        
        // 매트 가격 산출
        _this.setMattPrice();
        
        // 커버 가격 산출
        _this.setCoverPrice();
        
    },
    
    setCoverInfo : function(cover){
        var _this = this;
        console.log('커버선택');
        _this.coverInfo.coverName = cover.data('name');
        _this.coverInfo.coverCd = cover.data('covercd');
        _this.coverInfo.price = cover.data('price');
        _this.coverInfo.coverw = cover.data('coverw');
        _this.coverInfo.coverh = cover.data('coverh');
        _this.coverInfo.ppA = cover.data('ppa');
        _this.coverInfo.ppB = cover.data('ppb');
        _this.coverInfo.ppC = cover.data('ppc');
        _this.coverInfo.ppD = cover.data('ppd');
        _this.coverInfo.ppE = cover.data('ppe');
        _this.coverInfo.ppF = cover.data('ppf');
        _this.coverInfo.ppG = cover.data('ppg');
        console.log(_this.coverInfo);
    },
    
    getManPrice : function(width, height){
        var _this = this;
        
        var total = _this.manPrice.length;
        var manPrice = 0;
        var manPriceW = 0;
        var manPriceH = 0;
        
        for(var mi=0;mi<total;mi++){
            if(_this.manPrice[mi].start_num <= width && _this.manPrice[mi].end_num >= width){
                manPriceW = _this.manPrice[mi].percent / 100;
                break;
            }
        }
        
        for(var mi=0;mi<total;mi++){
            if(_this.manPrice[mi].start_num <= height && _this.manPrice[mi].end_num >= height){
                manPriceH = _this.manPrice[mi].percent / 100;
                break;
            }
        }
        
        manPrice = ((manPriceW+manPriceH)*2 + 1) * _this.manPrice[0].manprice;
        
        return manPrice;
    },
    
    setComma : function(price){
        var commPrice = (price)?price:1000
        //return commPrice;
        return commPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    
    setArtPrice : function(){
        var _this = this;
        console.log(_this.artInfo);
        // 용지선택일때
        if(_this.artPrint == 'frame'){
            return false;
        }
        // 네변의 합
        // _this.framePrice.borderSum
        var artSplit = _this.artSize.split('_');
        var artBorderSum = ( parseInt(artSplit[0])*2 ) + ( parseInt(artSplit[1])*2 );
        
        // 용지 인건비
        var artManPrice = _this.getManPrice(parseInt(artSplit[0]),parseInt(artSplit[1]));
        console.log('용지 인건비  : '+artManPrice);
        
        // 용지 인치
        var artInch = (_this.frameWidth > 600)?'B':'A';
        // 롤지 가격
        var rollPrice = _this.artInfo['price'+artInch];
        // 롤지 단가
        var rollLength = _this.artInfo['length'+artInch];
        
        // mm당 단가
        var mmPrice = ((rollPrice / rollLength)/1000).toFixed(2);
        console.log('단가식  : '+rollPrice+'/'+rollLength+'/1000');
        console.log('mm당 단가 : '+mmPrice);
        
        // 용지 원가
        var paperPrice = mmPrice * parseInt(artSplit[1]);
        console.log('용지 원가 : '+paperPrice);
        
        // 잉크값
        var artInk = ( parseInt(artSplit[0]) * parseInt(artSplit[1]) )/80;
        
        //종이 마진율
        var maper = 0;
        for(maval in _this.maPer){
            if(artBorderSum >= maper && artBorderSum < _this.maPer[maval]){
                maper = _this.artInfo['pp'+_this.alpha[maval]] / 100;
                console.log(_this.artInfo['pp'+_this.alpha[maval]]);
                break;
            }
            maper = _this.maPer[maval];
        }
        
        console.log('네변 : '+artBorderSum);
        
        // 판매가
        var artSettlePrice = ( paperPrice + _this.artInfo.itemPrice + artInk + artManPrice ) * maper;
        var artfix = ( Math.ceil((artSettlePrice / 100).toFixed(1)) ) * 100;
        console.log('판매가 : '+artfix);
        
        _this.artPrice = artfix;
        $('.total_price p strong').text(_this.setComma(_this.framePrice.settlePrice + _this.artPrice));
        
    },
    
    setMattPrice : function(){
        var _this = this;
        var mattManInfo = [120, 125, 130, 135, 140, 145, 150];
        var mattPer = [1200, 1600, 2400, 2870, 3516, 4060, 9999];
        
        // 매트보드 마진율
        var maper = 0;
        for(maval in mattPer){
            if(_this.framePrice.borderSum >= maper && _this.framePrice.borderSum < _this.maPer[maval]){
                maper = mattManInfo[maval] / 100;
                console.log('매트마진 : '+mattManInfo[maval]);
                break;
            }
            maper = mattPer[maval];
        }
        
        // _this.frameWidth * 2) + (_this.frameHeight
        var mattPrice = 2374 * ( _this.frameWidth * _this.frameHeight / 1000000 );
        var mattSettlePrice = ( mattPrice + _this.framePrice.manPrice ) * maper;
        var mattfix = ( Math.ceil((mattSettlePrice / 100).toFixed(1)) ) * 100;
        
        if(_this.frameBaseMount == 'double'){
            mattfix = mattfix * 2;
        }else if(_this.frameBaseMount == 'nomatt'){
            mattfix = 0;
        }
        
        _this.mattPrice = mattfix;
        
        $('.total_price p strong').text(_this.setComma(_this.framePrice.settlePrice + _this.artPrice + _this.mattPrice));
    },
    
    // 커버 가격 산출
    setCoverPrice : function(){
        var _this = this;
        console.log(_this.coverInfo);
        // 초기 선택일 경우
        if(!_this.coverSelect){
            //console.log($('.cover_select ul li:nth-child(1)'));
            $('.cover_select ul li:nth-child(1)').trigger('click');
        }
        
        // 원재료 회배
        var coverBase = _this.coverInfo.coverw * _this.coverInfo.coverw / 1000000 ;
        // 1회배 단가 
        var basePrice = _this.coverInfo.price / coverBase;
        // 원가
        var price = basePrice * ( _this.frameWidth * _this.frameHeight / 1000000 );
        
        // 커버 마진율
        var maper = 0;
        for(maval in _this.maPer){
            if(_this.framePrice.borderSum >= maper && _this.framePrice.borderSum < _this.maPer[maval]){
                maper = _this.coverInfo['pp'+_this.alpha[maval]] / 100;
                break;
            }
            maper = _this.maPer[maval];
        }
        
        // 판매가
        var coverPrice = ( price + _this.framePrice.manPrice ) * maper;
        var coverfix = ( Math.ceil((coverPrice / 100).toFixed(1)) ) * 100;
        _this.coverPrice = coverfix;
        
        console.log('커버가격 : '+coverfix);
        $('.total_price p strong').text(_this.setComma(_this.framePrice.settlePrice + _this.artPrice + _this.mattPrice + coverfix));
        $('.total_price .shPrice').text(_this.setComma(_this.framePrice.settlePrice + _this.artPrice + _this.mattPrice + coverfix));

    },
    
    setDetail : function(){
        var _this = this;
        // 프레임 네임 
        $('.detailFrameName').text(_this.goodsViewJson.goodsNm);
        // 프레임 컬러
        $('.detailFrameColor').text(_this.goodsViewJson.coreColor);
        // 프레임 소재
        $('.detailFrameType').text(_this.goodsViewJson.coreType);
        // 전면 두께
        $('.detailFrameFront').text();
        // 측면 두께
        $('.detailFrameSide').text();
        // 가로/세로 사이즈
        $('.detailFrameWidth').text( (_this.frameWidth/10-2) +'cm');
        $('.detailFrameHeight').text( (_this.frameHeight/10-2) +'cm');
        // 외형 사이즈
        $('.detailFrameOutWidth').text( (_this.frameWidth/10) +'cm');
        $('.detailFrameOutHeight').text( (_this.frameHeight/10) +'cm');
        
        // 아트 프린트 정보 _this.artInfo
        var artSplit = _this.artSize.split('_');
        var artMsg = '선택안함';
        // 최종 업로드 이미지 이름
        if(_this.filename != ''){
            $('.detailArtFile').text(_this.filename);  
            $('.detailArtPaper').text(_this.artInfo.artName);  
            $('.detailArtSizeW').text( (artSplit[0]/10)+'cm');  
            $('.detailArtSizeH').text( (artSplit[1]/10)+'cm');  
        }else{
            $('.detailArtFile').text(artMsg);  
            $('.detailArtPaper').text(artMsg);  
            $('.detailArtSizeW').text(artMsg);  
            $('.detailArtSizeH').text(artMsg);  
        }
        
        // 커버 정보 _this.coverInfo
        if(_this.frameBaseMount != 'nomatt'){
            $('.detailMattSizeW').text( (( artSplit[0] - 10 )/10) + 'cm' );
            $('.detailMattSizeH').text( (( artSplit[1] - 10 )/10) + 'cm' );
            $('.detailMattOutsideW').text( (( artSplit[0] - 10 + 100 )/10) +'cm');
            $('.detailMattOutsideH').text( (( artSplit[1] - 10 + 100 )/10) +'cm');
        }else{
            $('.detailMattSizeW').text( artMsg );
            $('.detailMattSizeH').text( artMsg );
            $('.detailMattOutsideW').text( artMsg );
            $('.detailMattOutsideH').text( artMsg );
        }
        
        console.log(_this.goodsViewJson);
        
        var direct = '정방형';
        if(_this.frameWidth > _this.frameHeight){
            direct = '가로';
        }else if(_this.frameWidth < _this.frameHeight){
            direct = '세로';
        }
        $('.detailBackDirevt').text(direct);
        
        $('.pd_info .img img').attr('src', _this.resultImg);
    },
    
    setStandartSize : function(obj){
        var _this = this;
        
        _this.setStandartText();
        _this.artSize = obj.data('size');
        obj.parent().prev().text(obj.find('p').text());
        
        _this.nowState = 2;
        _this.setAction();
        $('#print_paper').addClass('hidden');
        $('#crop_image').removeClass('hidden');
        $('.cropBox').removeClass('hidden');
        $('.resultBox').addClass('hidden');
        $('.frameshadow').addClass('hidden');
        
        var artAspect = _this.artSize.split('_');
        for(var i =1; i < _this.windowType+1; i++){
            if(_this.cropper['crop'+i]){
                _this.cropper['crop'+i].setAspectRatio(artAspect[0]/artAspect[1]);
            }
        }
        $('.select_box').removeClass('on');
        
    },
    
    setStandartText : function(){
        var _this = this;
        
        for( var stVal in _this.standart){
            $('.'+stVal+' > p').text(_this.standart[stVal]);
            $('.'+stVal).removeClass('on');
        }
    },
    
    setPrintFrameInfo : function(frameImgInfo){
        var _this = this;
        
        var frameinfo = [];
        var ratioWidth = [];
        var ratioHeight = [];
        var ratioDpi = [];
        var artSizeSp = _this.artSize.split('_');
        
        for(var sf=0;sf<frameImgInfo.length;sf++){
            ratioWidth.push(parseInt(artSizeSp[0]));
            ratioHeight.push(Math.round(parseInt(artSizeSp[0]) * frameImgInfo[sf].cropdata.height / frameImgInfo[sf].cropdata.width));
            ratioDpi.push(Math.round(frameImgInfo[sf].cropdata.width * 2.54 / 10));
        }
        console.log(frameImgInfo);
        console.log(ratioHeight);
        
        var minHeight = Math.min.apply(null, ratioHeight);
        console.log('minHeight : '+minHeight);
        
        var minNum = 0;
        for(var mi=0;mi<ratioHeight.length;mi++){
            if(minHeight == ratioHeight[mi]){
                minNum = mi;
            }
        }
        
        console.log('minNum : '+minNum);
        
        console.log('width : '+(ratioWidth/10)+'cm, height : '+(ratioHeight/10)+'cm');
        console.log('DPI : '+ratioDpi);
        
        frameinfo.push({
            'width' : _this.toFixedReturn(ratioWidth[minNum].toFixed(1)),
            'height' : _this.toFixedReturn(ratioHeight[minNum].toFixed(1)),
            'dpi' : ratioDpi[minNum]
        });
        
        // 최소 dpi가 140이상일경우 아래로 떨어질때까지 cm * 2
        if(ratioDpi[minNum] > 50 && _this.artPrint != 'frame'){
            frameinfo = []; // 프레임 배열 초기화
            for(var rti=1;rti<11;rti++){
                var forwidth = (ratioWidth[minNum]/10) * rti;
                var forheight = (ratioHeight[minNum]/10) * rti;
                var fordpi = frameImgInfo[minNum].cropdata.width * 2.54 / forwidth;
                if(fordpi > 50){
                    frameinfo.push({
                        'width' : _this.toFixedReturn(forwidth.toFixed(1))*10,
                        'height' : _this.toFixedReturn(forheight.toFixed(1))*10,
                        'dpi' : fordpi
                    });
                }
            }
            
        } // end if
        
        // 프린트 사이즈 => 추천 사이즈 설정 / 직접입력 값 설정
        var paliHtml = '';
        for(var pali=0;pali<frameinfo.length;pali++){
            if(pali == 0){
                _this.artSize = frameinfo[pali]['width']+'_'+frameinfo[pali]['height'];
                $('#artsize .recomm .select_box > p').text((frameinfo[pali]['width']/10)+'X'+(frameinfo[pali]['height']/10)+'cm');
                $('input[name=artWidth]').val(frameinfo[pali]['width']);
                $('input[name=artHeight]').val(frameinfo[pali]['height']);
            }
            paliHtml += '<li data-size="'+frameinfo[pali]['width']+'_'+frameinfo[pali]['height']+'"><p>'+(frameinfo[pali]['width']/10)+'X'+(frameinfo[pali]['height']/10)+'cm</p></li>';
        }
        $('#artsize .recomm .select_box ul').empty().append(paliHtml);
        
        console.log(_this.artSize);
        console.log(frameinfo);
        return frameinfo;
    },
    
    toFixedReturn : function(val){
        var returnVal = val.split('.');

        return (returnVal[1] > 0)?val:parseInt(returnVal[0]);
    },
    
};


