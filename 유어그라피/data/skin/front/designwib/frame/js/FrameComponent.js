var FrameComponent = {
    
    // setAction을 위한 컨텐트 페이지 전환
    setContent : function(){
      
        // 진행도 셋팅
        this.setState();
        
        // 푸터 레이아웃 변경
        if(this.nowState && this.actioStart){
            $('#frame_footer, #frame_wrap').addClass('left');
            $('.total_price').removeClass('hidden');
            $('#frame_footer .right_txt').removeClass('hidden');
            this.actioStart = false;
        }
        
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

        if(this.artPrint == 'frame'){
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
    
    // 프레임 상세 타입 셋팅
    setFrameDetail : function(_this, type){
        $('.choice_type1 .'+type+' li').removeClass('on');
        _this.addClass('on');
        
        // 액자 나 액자/인쇄 선택 구간일시 다음단계 버튼변경
        if(type == 'type1'){
            var next_step_upload = '';
            $('.next_step_upload button').removeClass('hidden');
            
            next_step_upload = (_this.data('type') == 'frame')?'next_upload2-2':'next_upload2-1';
            $('.'+next_step_upload).addClass('hidden');
        }
    },
    
    setSlickRefresh : function(){
        var imgNav = $('#frame_left .img_box .img_nav');
	var imgCont = $('#frame_left .img_box .img_cont ul');
        imgNav.slick('refresh');
        imgCont.slick('refresh');
    },
    
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
    
    setImgUpload : function(){
        
        var _this = this;
        var fileType = '';
        
        $(".dropzone").dropzone({ 
            paramName: "file",
            maxFilesize: 10,
            
            accept: function(file, done) {
              console.log(file);
              
              fileType = file.type;
              if(_this.maxSize < file.size){
                  alert('5MB 이하로 이미지를 올려주세요.');
                  _this.maxSizeCh = false;
              }else{
                    this.maxSizeCh = true;
                    $('.file_up').hide();
                    $('.file_down').show();
                    $('.file_down .loading strong').text('0');
                    $('.resultBox').empty().addClass('hidden');
                    if(_this.imgreUpload){
                        $('.btn_box .img_crop_btn').trigger('click');
                    }
                    done();
              }
              
            },
            
            addedfile : function(file){
                if(this.maxSizeCh){
                    $('.loading_bar span').css('width', '0');
                    $('.file_down .txt').text('이미지 체크중...');
                }
            },

            sending : function(){
                // 1
                $('.loading_bar span').css('width', '20%');
                maxNum = 20;
                _this.textHookfunc(0);
                $('.file_down .txt').text('이미지 전송 대기...');
            },

            uploadprogress : function(file, progress, bytesSent) {
                // 2
                maxNum = maxNum+10;
                _this.textHookfunc(maxNum-10);
                $('.loading_bar span').css('width', maxNum+'%');
                $('.file_down .txt').text('이미지 전송중...');
            },

            success : function(file, result){
                // 3
                $('.loading_bar span').css('width', '80%');
                maxNum = 80;
                _this.textHookfunc(60);
                $('.file_down .txt').text('이미지 완료 처리중...');
                _this.imgPath = result.img;

                // dropzone 파일 이미지 가로X세로
                _this.widthSize = file.width;
                _this.heightSize = file.height;
                _this.filename = file.name;
            },

            complete : function(result){
                // 4
                console.log(fileType);
                _this.setCropBox(fileType);
                _this.artPrint = 'print';
            },
            
            error : function(result){
                console.log(result);
            }
            
        });
        
    },
    
    // 합성된 이미지 출력
    setFrameImg : function(imgsrc, cropData){
        
        var _this = this;
        var ranPageNum = Math.random(100000);
        
        $('.frameshadow').addClass('hidden');
        _this.frameShadow = 'resultBox';
        
        
        var printFrameInfo = _this.setPrintFrameInfo(cropData);
        var artSizeSp = _this.artSize.split('_');
        
        $.ajax({
            url : '/frame/create_img.php?v='+ranPageNum,
            method: 'POST',
            cache : false,
            data : { 
                cropImg : imgsrc, width : cropData.width, height : cropData.height, 
                finSize : artSizeSp[0], folder : _this.frameFolder, mount : _this.frameBaseMount, 
                mattTopColor : _this.mattTopColor, mattBotColor : _this.mattBotColor,
                mattTop : _this.mattTop, mattLeft : _this.mattLeft, mattRight : _this.mattRight,
                mattBottom : _this.mattBottom
            },
            success : function(e){

                var addStyle = (cropData.height > cropData.width)?'width: auto;':'width: 100%;height: auto;';
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
                
                // 프린트 정보 출력 print_w
                $('.print_w').text(printFrameInfo[0].width);    // 프린트물 가로 사이즈
                $('.print_h').text(printFrameInfo[0].height);   // 프린트물 세로 사이즈
                $('.upload_name span').text(_this.filename);    // 업로드 이미지 이름
                
                
                // 윈도우(프레임) 여백은 현재 20으로 고정 차후 변경시 수정
                _this.frameWidth = printFrameInfo[0].width - 10 + _this.mattLeft + _this.mattRight + (20*2); 
                _this.frameHeight = printFrameInfo[0].height - 10 + _this.mattTop + _this.mattBottom + (20*2); 
                $('.frame_w').text(_this.frameWidth);
                $('.frame_h').text(_this.frameHeight);
                
                if(_this.artPrint == 'frame' && _this.contentMove){
                    $('.selectSizeBtn').trigger('click');
                    $('#artsize .tab_tit').addClass('half');
                }
                
                
                _this.setFramePrice();
                
                //$('.frameshadow').removeClass('hidden');
                clearTimeout(textHook);
                
                textHook = setTimeout(function(){
                    _this.setFrameShadow();
                }, 100);
                
                _this.contentMove = true;
            }
        });
        
    },
    
    // 프린트 이미지가 있을경우 크기 및 DPI 측정
    setPrintFrameInfo : function(cropData){
        
        var _this = this;
        var frameinfo = [];
        
        var artSizeSp = _this.artSize.split('_');
        console.log(artSizeSp);
        // 최소값 100mm를 가로에 적용해서 비율값 산정
        var ratio = [];
        ratio.width = parseInt(artSizeSp[0]);
        ratio.height = Math.round(parseInt(artSizeSp[0]) * cropData.height / cropData.width);
        ratio.dpi = Math.round(cropData.width * 2.54 / 10);
        console.log('width : '+(ratio.width/10)+'cm, height : '+(ratio.height/10)+'cm');
        console.log('DPI : '+ratio.dpi);
        
        //if(ratio.width >= 100 && ratio.height >= 100){
            frameinfo.push({
                'width' : _this.toFixedReturn(ratio.width.toFixed(1)),
                'height' : _this.toFixedReturn(ratio.height.toFixed(1)),
                'dpi' : ratio.dpi
            });
        //}
        
        // 최소 dpi가 140이상일경우 아래로 떨어질때까지 cm * 2
        if(ratio.dpi > 50 && _this.artPrint != 'frame'){
            frameinfo = []; // 프레임 배열 초기화
            for(var rti=1;rti<11;rti++){
                var forwidth = (ratio.width/10) * rti;
                var forheight = (ratio.height/10) * rti;
                var fordpi = cropData.width * 2.54 / forwidth;
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
            paliHtml += '<li data-artsize="'+frameinfo[pali]['width']+'_'+frameinfo[pali]['height']+'"><p>'+(frameinfo[pali]['width']/10)+'X'+(frameinfo[pali]['height']/10)+'cm</p></li>';
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
    
    // 프레임 최소/최대 이미지 체크
    limitSizeCheck : function(obj){
        var _this = this;
        var msgWidth = '';
        var msgHeight = '';
        
        //customWidth customHeight
        if(obj.hasClass('customWidth')){
            if(obj.val() < _this.limitminW){
                msgWidth = '프레임 최소 가로 길이는 '+_this.limitminW+'mm입니다.';
            }else if(obj.val() > _this.limitmaxW){
                msgWidth = '프레임 최대 가로 길이는 '+_this.limitmaxW+'mm입니다.';
            }else{
                msgWidth = '';
            }

            if(msgWidth != ''){
                obj.parents('li').addClass('focus');
                $('.customWidthInfo').removeClass('hidden').text(msgWidth);
                _this.passWidth = false;
            }else{
                obj.parents('li').removeClass('focus');
                $('.customWidthInfo').addClass('hidden').empty();
                _this.passWidth = true;
            }
            
        }else{
            if(obj.val() < _this.limitminH){
                msgHeight = '프레임 최소 가로 길이는 '+_this.limitminH+'mm입니다.';
            }else if(obj.val() > _this.limitmaxH){
                msgHeight = '프레임 최대 가로 길이는 '+_this.limitmaxH+'mm입니다.';
            }else{
                msgHeight = '';
            }

            if(msgHeight != ''){
                obj.parents('li').addClass('focus');
                $('.customHeightInfo').removeClass('hidden').text(msgHeight);
                _this.passHeight = false;
            }else{
                obj.parents('li').removeClass('focus');
                $('.customHeightInfo').addClass('hidden').empty();
                _this.passHeight = true;
            }
        }
        
    },
    
    setStandartSize : function(obj){  
        var _this = this;
        
        _this.setStandartText();
        _this.artSize = obj.data('size');
        obj.parent().prev().text(obj.find('p').text());
        
        var artAspect = _this.artSize.split('_');
        _this.cropper.setAspectRatio(artAspect[0]/artAspect[1]);
        $('#crop_image .save_btn').trigger('click');
    },
    
    setNoframeSize : function(){
        
    },
    
    setStandartText : function(){
        var _this = this;
        
        for( var stVal in _this.standart){
            $('.'+stVal+' > p').text(_this.standart[stVal]);
            $('.'+stVal).removeClass('on');
        }
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
                        framelist += '<li class="'+onClass+'" data-folder="'+fmList[fi].goodsNo+'">';
                        framelist += '<div class="chk_box">';
                        framelist += '<img src="/data/goods/'+fmList[fi].imagePath+fmList[fi].imageName+'" alt="'+fmList[fi].goodsNm+'" >';
                        framelist += '<div class="btn_box inline">';
                        framelist += '<button type="button" class="frame_info_btn" data-goodsno="'+fmList[fi].goodsNo+'"></button>';
                        var frameVideoCl = (fmList[fi].externalVideoUrl)?'onV':'offV';
                        framelist += '<button type="button" class="frame_video_btn '+frameVideoCl+'" data-goodsno="'+fmList[fi].goodsNo+'"></button>';
                        framelist += '</div>';
                        framelist += '<div class="txt">';
                        var frameDescrip = (fmList[fi].shortDescription != '')?fmList[fi].shortDescription:fmList[fi].goodsNm;
                        framelist += '<h3>'+frameDescrip+'</h3>';
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
    
    setCropBox : function(fileType){
        var _this = this;
        $('.frameshadow').addClass('hidden');
        $('.loading_bar span').css('width', '100%');
        maxNum = 100;
        _this.textHookfunc(80);

        $('.imgContBox').addClass('hidden');
        
        $('.file_down .txt').text('이미지 업로드 완료.');

        var newWidht = newHeight = 463;
        if(_this.widthSize > _this.heightSize){
            // 가로 이미지 기반으로 이미지 세로 비율값
            newHeight = 463 * _this.heightSize / _this.widthSize;
        }else{
            // 세로 이미지 기반으로 이미지 가로 비율값
            newWidht = 463 * _this.widthSize / _this.heightSize;
        }


        //console.log(newWidht);
        $('.cropBox').empty().append('<img src="'+_this.imgPath+'">').removeClass('hidden').css({
            'width' : newWidht+'px',
            'height' : newHeight+'px'
        });

        var images = $('.cropBox img');
        _this.cropper = new Cropper(images[0], {
            viewMode : 1,
            zoomOnWheel : false,
            //aspectRatio: 244 / 325,
            ready: function (event) {

                var containerData = _this.cropper.getContainerData();

                var cropPrintImg = $('.cropper-hide');

                _this.cropper.zoomTo({
                  x: cropPrintImg[0].clientWidth ,
                  y: cropPrintImg[0].clientHeight
                });

            },

            crop: function (event) {
                
                if(_this.artPrint == 'frame'){
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
        
        if(_this.artPrint != 'frame'){
            $('#print_paper').addClass('hidden');
            $('#crop_image').removeClass('hidden');
        }

        // 크롭 이미지 저장
        $('#crop_image .save_btn').unbind('click').click(function (){
            $('.frameshadow').addClass('hidden');
            var mimeType = fileType.split('image/');
            var cropData = _this.cropper.getData();
            //console.log(cropData);
            _this.cropper.getCroppedCanvas().toBlob(function(blob){

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
                        _this.setFrameImg(e, cropData);
                    }
                });
            }, 'image/jpeg');
        });
        
        if(_this.artPrint != 'frame'){
            $('.btn_box').removeClass('hidden');
        }
    },
    
    setComma : function(price){
        var commPrice = (price)?price:1000
        //return commPrice;
        return commPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    
    setArtInfo : function(art){
        var _this = this;
console.log('종이선택');
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
    
    setFrameShadow : function(){

        var _this = this;

        var imgBoxLeft = $('.img_box').offset().left;
        var imgContBoxL = $('.'+_this.frameShadow+' img').offset().left;
        var imgContBoxT = $('.'+_this.frameShadow+' img').offset().top;
        var imgFrameWidth = $('.'+_this.frameShadow+' img').width();
        var imgFrameHeight = $('.'+_this.frameShadow+' img').height();
        
        //console.log(imgContBoxL);
        //console.log(imgFrameWidth);
        //console.log(imgFrameHeight);
        $('.frameshadow').css({
            'position' : 'fixed',
            'left' : (imgContBoxL+imgFrameWidth - 2)+'px',
            'top' : (imgContBoxT + 1 )+ 'px',
            'height' : imgFrameHeight + 'px'
        });
        
        var _slickIndex = $('.img_nav .slick-current').data('slick-index');
        if(_slickIndex == '0'){
            $('.frameshadow').removeClass('hidden');
        }else{
            $('.frameshadow').addClass('hidden');
        }
        
    }
    
};


