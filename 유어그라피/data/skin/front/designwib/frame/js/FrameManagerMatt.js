var FrameManagerMatt = {
    
    contentPage : ['print', 'print_paper', 'artsize', 'frame_select', 'matt_board', 'cover', 'detail'],
    maxSize : 5120000,  // 파일 최대 사이즈
    imgInfo : [],       // 이미지 배열
    maxState : 1,       // 전체 진행도 상태
    cropper : {},       // 크로퍼 객체
    artSize: '100_100', // 프린트 이미지 사이즈
    frameBaseMount : 'single',
    mattTop : 50,       // 매트보드 상단 여백
    mattLeft : 50,      // 매트보드 왼쪽 여백
    mattRight : 50,     // 매트보드 오른족 여백
    mattBottom : 50,    // 매트보드 하단 여백
    contentMove : true, // 컨텐츠 화면 이동체크
    passWidth : true,   // 프레임 사이즈 가로 체크
    passHeight : true,  // 프레임 사이즈 가로 체크
    
    frameType : '',
    frameFolder : basicGoodsNo,
    frameBaseType : '원목',
    frameBaseColor : basicColor,
    goodsViewJson : goodsViewJson,
    
    // 매트가격
    mattPrice : 0,
    mattTopColor : '245_245_245',
    mattBotColor : '245_245_245',
    
    // 종이 가격
    artInfo : {},
    artPrice : 0,
    
    // 프레임 가격
    framePrice : framePriceInfo,
    // 인건비 배열
    manPrice : manPrice,
    // 마진율 구간
    maPer : [1200, 1600, 2400, 2870, 3516, 4060, 9999 ],
    alpha : ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
    
    // 커버 가격
    coverInfo : {},
    coverPrice : 0,
    coverSelect : false,
    
    init : function(){
        this.clickEvent();

        $('#matt_board .mount_select ul li:eq(0)').addClass('hidden');
        $('#matt_board .mount_select ul li:eq(1)').addClass('on');
        
        $('#matt_board .mount_select ul li:eq(0)').hide();
        $('#matt_board .mount_select ul li:eq(1)').css({
            'margin-right' : '16px' 
        });
        $('#matt_board .mount_select ul li:eq(2)').css({
            'margin-right' : '0' ,
            'margin-top' : '0'
        });
        
        //setTimeout(console.log.bind(console,"%cDESIGN & PUBLISHING BY %cDESIGNWIB \r\n%c주소 : 서울시 구로구 대륭포스트5차\r\n%c전화 : 070-1234-1234\r\n%c홈페이지 : https://www.designwib.co.kr/", 'color:#000;', 'color:#4ab7fe;', '', '', ''),0);
        setTimeout(console.log.bind(console,"%c                        \r\n%cDESIGN & CODING BY %cDESIGNWIB \r\n%chttps://www.designwib.co.kr", 'background: #000 url("https://www.designwib.co.kr/web/img/logo/logo-white.png") no-repeat;background-size: contain;', 'color:#000;', 'color:#4ab7fe;', ''),0);
        
//        setTimeout(console.table.bind(console,[
//            {
//                first: 'René'
//            },
//            {
//                first: 'Chaim'
//            },s
//            {
//                first: 'Henri'
//            }
//        ]),0);
    },
    
    clickEvent : function(){
        var _this = this;
        
        // 기본형 클릭 이벤트 등록 eventBtn
        $(document).on('click', '.eventBtn', function(){
            //console.log('check');
            var _thisEvent = $(this).data('event');
            _this[_thisEvent]($(this));
        });
        
        // 특수형 개별 클릭 이벤트 등록
        // 프레임 색상 선택
        $(document).on('click', '#frame_select .coreColor ul li', function(){
            var corecolor = $(this).data('corecolor');
            _this.frameBaseColor = corecolor;
            _this.setFrameList();
            $('.coreColor .select_box > p').text(corecolor);
            $('.coreColor .select_box').removeClass('on');
        });
        
        // 프레임 재질 선택
        $(document).on('click', '#frame_select .coreType ul li', function(){
            var coretype = $(this).data('coretype');
            _this.frameBaseType = coretype;
            _this.setFrameList();
            $('.coreType .select_box > p').text(coretype);
            $('.coreType .select_box').removeClass('on');
        });
        
        // 마운트 색상 종류 TOP
        $('.mount_select .color_select ul.topul li div').on('click', function(){
            _this.mattTopColor = $(this).data('color');
            $('.mount_select .color_select ul.topul li div').removeClass('on');
            $(this).addClass('on');
            _this.setFrameList();
        });
        
        // 마운트 색상 종류 ACCENT
        $('.mount_select .color_select ul.accul li div').on('click', function(){
            _this.mattBotColor = $(this).data('color');
            $('.mount_select .color_select ul.accul li div').removeClass('on');
            $(this).addClass('on');
            _this.setFrameList();
        });
        
        // 매트 패딩 적용
        $('.border_save_btn').on('click', function(){
            
            var matOldTop = parseInt($('.border_wrap .top input').val());
            var matOldLeft = parseInt($('.border_wrap .left input').val());
            var matOldRight = parseInt($('.border_wrap .right input').val());
            var matOldBottom = parseInt($('.border_wrap .bottom input').val());
            
            if(matOldTop < 15 || matOldLeft < 15 || matOldRight < 15 || matOldBottom < 15){
                return false;
            }
            
            _this.mattTop = parseInt($('.border_wrap .top input').val());
            _this.mattLeft = parseInt($('.border_wrap .left input').val());
            _this.mattRight = parseInt($('.border_wrap .right input').val());
            _this.mattBottom = parseInt($('.border_wrap .bottom input').val());
            _this.setFrameList();
        });
        
        // 커버 리스트 클릭
        $('#cover .cover_select ul li').on('click', function(){
            // 커버 정보 셋팅
            $('#cover .cover_select ul li').removeClass('on');
            $(this).addClass('on');
            _this.setCoverInfo($(this));
            _this.coverSelect = true;
            _this.setCoverPrice();
        });
        
        $('.f_navi ul li').on('click', function(e){

            if($(this).index() == 6){
                _this.setDetail();
            }
            
            if(_this.nowState == 7 && $(this).index() < 6){
                $('#content' ).removeClass('hidden');
                $('#frame_cart_wrap').addClass('hidden');
                $('#frame_wrap').addClass('left');
                $('#frame_footer').addClass('left');
                $('.sub_content').css('height', '100%');
            }
            
            if(_this.maxState > 1){
                _this.nowState = $(this).index() + 1;
                _this.setAction();
            }

        });
        
        // 이미지 재크롭
        $('.btn_box .img_crop_btn').unbind('click').on('click', function(){
            _this.nowState = 2;
            _this.setAction();
            $('#print_paper').addClass('hidden');
            $('#crop_image').removeClass('hidden');
            $('.cropBox').removeClass('hidden');
            $('.resultBox').addClass('hidden');
            $('.frameshadow').addClass('hidden');
            //_this.cropper.setAspectRatio(1,1);
        });
        
        $('.img_rotate_btn').on('click', function(){
            var ReWidth = $('.customHeight').val();
            var ReHeight = $('.customWidth').val();
            $('.customHeight').val(ReHeight);
            $('.customWidth').val(ReWidth);
            $('.size_save_btn').trigger('click');
        });
        
        // 종이규격 선택
        $('.standard ul li').on('click', function(){
            _this.artSize = $(this).data('size');
            _this.setStandartSize($(this));
        });
        
        // 추천 사이즈 선택
        $(document).on('click', '#artsize .recomm .select_box ul li', function(){
            _this.artSize = $(this).data('size');
            _this.setStandartSize($(this));
        });
        
        $(document).on('click', '.photo_del_btn, .photo_reset_btn', function(){
            var imgPa = $(this).parents('.dz-clickable');
            console.log(imgPa);
            imgPa.find('.needsclick').removeClass('hidden');
            imgPa.find('.dz-reault').addClass('hidden');
            // needsclick dz-reault
        });
        
        // 최종 확인 페이지 셋팅
        $('#cover .next_btn').unbind('click').on('click', function(){
            _this.setDetail();
            _this.nowState = 7;
            _this.setAction();
        });
        
        $('.cart_save_btn').on('click', function(){

            var packGoodsNo = _this.frameFolder;
            var data = {
                mode : 'cartIn',
                goodsNo : packGoodsNo,
                goodsCnt : 1,
                findOptionSno : 1,
                price : _this.framePrice.settlePrice + _this.artPrice + _this.mattPrice + _this.coverPrice,
                img : _this.imgPath
            };
            console.log(data);
            $.ajax({
                url : '/frame/cart_ps.php',
                method : 'post',
                data : data,
                dataType: "json",
                success : function(e){
                    //if(e.error == 0){
                        _this.cartNo = e;
                        console.log('ch');
                        $('#frameStart, #content' ).addClass('hidden');
                        $('#frame_cart_wrap').removeClass('hidden');
                        $('#frame_wrap').removeClass('left');
                        $('#frame_footer').removeClass('left');
                        $('.sub_content').css('height', 'auto');
                        $('.order_now_btn').unbind('click').on('click', function(){
                           location.href = '/order/order.php?cartIdx=['+_this.cartNo+']'; 
                        });
                    //}
                }
            });
        });
       
    },
    
    // 최초 윈도우 갯수 선택
    windowSelect : function(obj){
        var _this = this;
        
        console.log('윈도우 갯수 선택');
        
        // 윈도우 갯수 등록
        _this.windowType = obj.data('window');
        
        $('.imgContBox img').attr('src', '/data/skin/front/designwib/wib/img/imgbox/matt_1_'+_this.windowType+'.png');
        
        // 진행도 등록
        _this.nowState = 1;
        // 프레임 스튜디오 컨텐츠 화면 전환
        _this.setContent();
        
        // 드랍이미지 플러그인 실행
        _this.setDropUploader();
    },
    
    // 아트 프린팅 선택
    setArtPrint : function(obj){
        var _this = this;
        var _artType = obj.data('type');

        console.log('아트 프린팅 선택');
        console.log(_artType);

        // 선택영역 활성화
        $('#print .choice_type1 .type1 li').removeClass('on');
        obj.addClass('on');
        
        // 아트 프린팅 타입 설정
        _this.artPrintType = _artType;
        
        // 아트 프린팅 선택에 따른 버튼 활성화 변경
        $('.next_step_upload button').removeClass('hidden');
        var next_step_upload = (_artType == 'frame')?'next_upload2-2':'next_upload2-1';
        $('.'+next_step_upload).addClass('hidden');
                
    },
    
    // ?
    printSelect : function(obj){
        var _this = this;
        
        $('#print_paper ul li:eq(0)').trigger('click');
        
        _this.nowState = 2;
        _this.setAction();
    },
    
    printSelectNo : function(){
        var _this = this;
        _this.contentMove = true;
        _this.artPrintType = 'frame';
        console.log(1);
        _this.imgInfo = [];
        _this.cropper = {};
        
        $('.cropBox').empty();
        $('.btn_box').addClass('hidden');
        
        $('#print_paper ul li:eq(0)').trigger('click');
        
        
        for(var i =0; i < _this.windowType; i++){
            console.log(2);
            _this.imgInfo.push({
                imgPath : '/data/wibUpload/frame/blank.jpg',
                widthSize : 100,
                heightSize : 100,
                filename : 'blank.jpg',
                thumnail : ''
            });
            _this.cropper['crop'+(i+1)] = [];
            console.log(_this.imgInfo);
            _this.setCropBox('image/jpg', i+1);
        }
        $('.cropBox').addClass('hidden');
        $('.imgContBox').addClass('hidden');
        console.log(_this.artPrintType);
    },
    
    // 종이 재질 선택
    paperSelect : function(obj){
        var _this = this;
        
        console.log('종이 재질 선택');
        //console.log(obj);
        
        $('#print_paper .choice_select li').removeClass('on');
        obj.addClass('on');
        // 종이 정보 셋팅
        _this.setArtInfo(obj);
    },
    
    // 이미지 업로드 팝업
    uploadMultiImg : function(){
        var _this = this;
        
        //$('.photo_pop_all').empty();
        //_this.setDropUploader();
        
        $('.photo_pop').addClass('on');
        $('.dark_bg').addClass('on');
    },
    
    // 크롭 이미지 변경
    changeImg : function(obj){
        var _thisNum = obj.data('num');
        $('.crImgBox').addClass('hidden');
        $('.crImg'+_thisNum).removeClass('hidden');
        
        $('.select_img_list ul li').removeClass('on');
        obj.addClass('on');
    },
    
    // 사이즈 변경으로 이동
    gotoSetSize : function(){
        
        var _this = this;
        
        _this.nowState = 3;
        _this.setAction();
    },
    
    // 사이즈 변경 
    setSize : function(obj){
        var _this = this;
        
        if(!$('.customWidth').val() || !$('.customHeight').val()){
            alert('가로/세로 사이즈를 모두 입력해 주세요.');
            return false;
        }

        if(!_this.passWidth || !_this.passHeight){
            alert('프레임 최소/최대 크기를 확인해 주세요.');
            return false;
        }
        
        _this.artSize = $('.customWidth').val()+'_'+$('.customHeight').val();
        
        if(_this.artPrintType != 'frame'){
            _this.nowState = 2;
            _this.setAction();

            $('.frameshadow').addClass('hidden');
            $('#print_paper').addClass('hidden');
            $('#crop_image').removeClass('hidden');
            $('.cropBox').removeClass('hidden');
            $('.resultBox').addClass('hidden');
        }
        // 직업 입력한 가로 세로 사이즈 종횡비 입력
        
        for(var i =1; i < _this.windowType+1; i++){
            if(_this.cropper['crop'+i]){
                _this.cropper['crop'+i].setAspectRatio($('.customWidth').val()/$('.customHeight').val());
            }
        }
    },
    
    // 4.프레임으로 이동
    gotoFrame : function(){
        var _this = this;
        
        _this.nowState = 4;
        _this.setAction();
    },
    
    // 프레임 선택
    selectFrame : function(obj){
        var _this = this;
        
        var _frameThis = obj;
        _this.frameFolder = obj.data('folder');
        _this.contentMove = false;
        $.ajax({
            url : '/frame/goods_ps.php',
            method : 'post',
            data : { mode : 'get_frame', goodsNo : _this.frameFolder},
            success : function(e){
                if(e.code == 200){
                    _this.goodsViewJson = e.data;
                    $('#crop_image .save_btn').trigger('click');

                    $('#frame_select .on_frame ul li').removeClass('on');
                    _frameThis.addClass('on');
                }
            }
        });
    },
    
    // 5. 매트보드로 이동
    gotoMattBoard : function(){
        var _this = this;
        
        $('#matt_board .mount_select ul li:eq(0)').hide();
        $('#matt_board .mount_select ul li:eq(1)').css({
            'margin-right' : '16px' 
        });
        $('#matt_board .mount_select ul li:eq(2)').css({
            'margin-right' : '0' ,
            'margin-top' : '0'
        });
        
        _this.nowState = 5;
        _this.setAction();
    },
    
    // 마운트 선택
    setMount : function(obj){
        var _this = this;
        
        $('.mount_select .choice_select ul li').removeClass('on');
        obj.addClass('on');
        var mounts = obj.data('mount');
        
        _this.mattTop = parseInt($('.border_wrap .top input').val());
        _this.mattLeft = parseInt($('.border_wrap .left input').val());
        _this.mattRight = parseInt($('.border_wrap .right input').val());
        _this.mattBottom = parseInt($('.border_wrap .bottom input').val());
        
        _this.frameBaseMount = mounts;
        _this.setFrameList();

        if(mounts == 'double'){
            $('.accentBox').removeClass('hidden');
        }else{
            $('.accentBox').addClass('hidden');
        }
    },
    
    // 6. 투명커버 이동
    gotoCover : function(){
        var _this = this;
        
        _this.nowState = 6;
        _this.setAction();
    }
 
};

var FrameMatt = new Object();
FrameMatt = $.extend({}, FrameManagerMatt, FrameComponentMatt);
FrameMatt.init();


