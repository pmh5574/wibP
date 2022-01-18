var FrameManager = {
    
    state : 7,          // 총 진행페이지
    nowState : 1,       // 현재 진행중 상태
    maxState : 1,       // 전체 진행도 상태
    pageState : 1,      // 우측 컨텐츠 진행도 상태
    maxSize : 5120000,  // 파일 최대 사이즈
    maxSizeCh : true,   // 파일 사이즈 체크
    widthSize : 0,      // 이미지 넒이
    heightSize : 0,     // 이미지 높이
    filename : '',      // 이미지 파일이름
    mattTop : 0,       // 매트보드 상단 여백
    mattLeft : 0,      // 매트보드 왼쪽 여백
    mattRight : 0,     // 매트보드 오른족 여백
    mattBottom : 0,    // 매트보드 하단 여백
    actioStart : false, // 액자선택 이후 액션 체크
    artSize: '100_100', // 프린트 이미지 사이즈
    artPrint : 'frame', // 프린트 이미지 타입
    limitminW : 100,    // 프레임 min 가로
    limitmaxW : 1800,   // 프레임 max 가로
    limitminH : 100,    // 프레임 min 세로
    limitmaxH : 1800,   // 프레임 max 세로
    passWidth : true,   // 프레임 사이즈 가로 체크
    passHeight : true,  // 프레임 사이즈 가로 체크
    contentMove : true, // 컨텐츠 화면 이동체크
    imgreUpload : false,
    frameWidth : 0,
    frameHeight : 0,
    statePage : ['start', 'upload', 'artsize', 'frames', 'mount', 'glazing', 'review'],
    contentPage : ['print', 'print_paper', 'artsize', 'frame_select', 'matt_board', 'cover', 'detail'],
    standart : {
        'stPaper' : '종이규격 (예 : A4)',
        'stInch' : '영국식 (인치)',
        'stMeter' : '미터법 (cm)'
    },
    
    /**
     * 액자 타입
     * 기본 = basic, 매트 : matt, 트레이 : tray, 박스 : box, 띄움 : space
     * @type String
     */
    frameType : '',
    frameFolder : basicGoodsNo,
    frameBaseType : '원목',
    frameBaseColor : basicColor,
    frameBaseMount : 'nomatt',
    goodsViewJson : goodsViewJson,
    
    /**
     * 프레임 액자 및 인쇄 타입
     * 액자 = frame, 액자&인쇄 : print
     * @type String
     */
    frameDetailType : '',
    
    // 프레임 인쇄물 종이 타입
    framePrintType : '',
    
    // 이미지 PATH
    imgPath : '',
    
    resultImg : '',
    
    /**
     * 프린팅 프레임 정보
     * @type Array
     */
    printFrame : [],
    
    // 크로퍼 객체
    cropper : [],
    
    // 프레임 가격
    framePrice : framePriceInfo,
    // 인건비 배열
    manPrice : manPrice,
    // 마진율 구간
    maPer : [1200, 1600, 2400, 2870, 3516, 4060, 9999 ],
    alpha : ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
    
    // 종이 가격
    artInfo : {},
    artPrice : 0,
    
    // 매트가격
    mattPrice : 0,
    mattTopColor : '245_245_245',
    mattBotColor : '245_245_245',
    
    // 커버 가격
    coverInfo : {},
    coverPrice : 0,
    coverSelect : false,
    
    // 프레임 그림자 모드
    frameShadow : 'imgContBox',
    
    cartNo : 0,
    
    init : function(){
        this.clickEvent();
    },
    
    clickEvent : function(){
        var _this = this;
        
        // 최초 액자 선택 : STEP1
        $('.frameSelect').unbind('click').on('click', function(){
            _this.frameType = $(this).data('type');
            if(_this.frameType == 'matt'){
                //alert('준비중입니다.');
                location.href="/frame/frame_matt.php";
                return false;
            }
            _this.nowState = 1;
            _this.actioStart = true;
            _this.setContent();
            _this.setFrameShadow();
        });
        
        // 액자 & 액자/인쇄 선택 : STEP2-1
        $('#print .choice_type1 .type1 li').unbind('click').on('click', function(){
            _this.frameDetailType = $(this).data('type'); // 삭제예정
            _this.artPrint = _this.frameDetailType;
            _this.setFrameDetail($(this), 'type1');
        });
        
        // 액자/인쇄 선택 : STEP2-2
        $('#print .choice_type1 .type2 li').unbind('click').on('click', function(){
            var artLi = $(this);
            _this.framePrintType = artLi.data('type');
            _this.artPrint = _this.frameDetailType;
            _this.setFrameDetail(artLi, 'type2');
        });
        
        // 액자만 필요 선택
        $('.next_upload2-1').unbind('click').on('click', function(){
            //$('.cropBox').append('<img src="/data/wibUpload/frame/blank.jpg">');
            _this.contentMove = true;
            _this.frameBaseMount = 'nomatt';
            _this.widthSize = _this.heightSize = 1000;
            _this.imgPath = '/data/wibUpload/frame/blank.jpg';
            _this.setCropBox('image/jpg');
        });
        
        // 액자&인쇄 모두 필요 선택
        $('.next_upload2-2').unbind('click').on('click', function(){
            _this.nowState = 2;
            _this.setAction();
        });
        
        
        //이미지 업로드 팝업
	$('.upload_btn').click(function(){
            $('.dark_bg, .upload_pop').addClass('on');
	});
        
        _this.setImgUpload();
        
        $('#print_size .next_btn').unbind('click').on('click', function(){
            _this.nowState = 4;
            _this.setAction();
        });
        $('#artsize .next_btn').unbind('click').on('click', function(){
            _this.nowState = 4;
            _this.setAction();
        });
        
        $('#frame_select .next_btn').unbind('click').on('click', function(){
            _this.nowState = 5;
            _this.setAction();
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
        
        // 프린트 사이즈 => 사이즈변경
        $('.selectSizeBtn').unbind('click').on('click', function(){
            _this.nowState = 3;
            _this.setAction();
        });
        
        $(document).on('keyup', '.customWidth, .customHeight', function(){
            _this.limitSizeCheck($(this));
        });
        
        $('.size_save_btn').unbind('click').on('click', function(){
            
            if(!$('.customWidth').val() || !$('.customHeight').val()){
                alert('가로/세로 사이즈를 모두 입력해 주세요.');
                return false;
            }
            
            if(!_this.passWidth || !_this.passHeight){
                alert('프레임 최소/최대 크기를 확인해 주세요.');
                return false;
            }
            
            //_this.artSize
            _this.artSize = $('.customWidth').val()+'_'+$('.customHeight').val();
            //console.log(_this.artSize);
            //console.log(_this.artPrint);
            
            if(_this.artPrint != 'frame'){
                _this.nowState = 2;
                _this.setAction();
            
                $('.frameshadow').addClass('hidden');
                $('#print_paper').addClass('hidden');
                $('#crop_image').removeClass('hidden');
                $('.cropBox').removeClass('hidden');
                $('.resultBox').addClass('hidden');
            }
            // 직업 입력한 가로 세로 사이즈 종횡비 입력
            _this.cropper.setAspectRatio($('.customWidth').val()/$('.customHeight').val());

            
        });
        
        $('.img_rotate_btn').on('click', function(){
            var ReWidth = $('.customHeight').val();
            var ReHeight = $('.customWidth').val();
            $('.customHeight').val(ReHeight);
            $('.customWidth').val(ReWidth);
            $('.size_save_btn').trigger('click');
        });
        
        $(document).on('click', '#artsize .recomm .select_box ul li', function(){
            //console.log('d');
            _this.artSize = $(this).data('artsize');
            $('#crop_image .save_btn').trigger('click');
            //_this.cropper.setAspectRatio($('.customWidth').val()/$('.customHeight').val());
        });
        
        $('#print_paper .choice_type1 ul li').on('click', function(){
            $('#print_paper .choice_select li').removeClass('on');
            $(this).addClass('on');
            // 종이 정보 셋팅
            _this.setArtInfo($(this));
        });
            
        
        // 프레임 선택
        $(document).on('click', '#frame_select .on_frame ul li', function(e){
            if($(e.target).attr("type") == 'button'){
                return false;
            }
            var _frameThis = $(this);
            _this.frameFolder = $(this).data('folder');
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
            
        });
        
        //프레임정보 팝업
	$(document).on('click', '.frame_info_btn', function(){
            var _thisGoodsNo = $(this).data('goodsno');
            $.ajax({
                url : '/frame/popup.php',
                method : 'post',
                data : { goodsNo : _thisGoodsNo, page : 'popup' },
                success : function(result){
                    $('.frame_info_pop').empty().append(result);
                    $('.dark_bg, .frame_info_pop').addClass('on');
                    $("body").css("overflow","hidden");
                }
            });
		
	});
        
        //프레임비디오 팝업
	$(document).on('click', '.frame_video_btn', function(){
            var _thisGoodsNo = $(this).data('goodsno');
            
            if($(this).hasClass('offV')){
                alert('동영상이 없습니다.');
                return false;
            }
            
            $.ajax({
                url : '/frame/popup.php',
                method : 'post',
                data : { goodsNo : _thisGoodsNo, page : 'video' },
                success : function(result){
                    $('.frame_video_pop').empty().append(result);
                    $('.dark_bg, .frame_video_pop').addClass('on');
                    $("body").css("overflow","hidden");
                }
            });
	});
        
        // 종이규격 선택
        $('.standard ul li').on('click', function(){
            _this.setStandartSize($(this));
        });
        
        // 락 잠금/해제
        $('#lock').unbind('click').on('click', function(){
            if($(this).prop('checked') == true){
                _this.cropper.setAspectRatio(0);
                
                console.log('1');
            }else{
                //_this.cropper.setAspectRatio(1);
                _this.cropper.setAspectRatio($('.customWidth').val()/$('.customHeight').val());
                console.log('2');
            }
        });
        
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
        
        // 마운트 종류 선택
        $('.mount_select .choice_select ul li').on('click', function(){
            $('.mount_select .choice_select ul li').removeClass('on');
            $(this).addClass('on');
            var mounts = $(this).data('mount');

            if(mounts == 'nomatt'){
                _this.mattTop = 0;
                _this.mattLeft = 0;
                _this.mattRight = 0;
                _this.mattBottom = 0;
            }else{
                _this.mattTop = parseInt($('.border_wrap .top input').val());
                _this.mattLeft = parseInt($('.border_wrap .left input').val());
                _this.mattRight = parseInt($('.border_wrap .right input').val());
                _this.mattBottom = parseInt($('.border_wrap .bottom input').val());
            }
            console.log('topMatt : '+_this.mattTop);
            
            _this.frameBaseMount = mounts;
            _this.setFrameList();
            
            if(mounts == 'double'){
                $('.accentBox').removeClass('hidden');
            }else{
                $('.accentBox').addClass('hidden');
            }
            
            
            // 매트보드 계산식 적용
            _this.setMattPrice();
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

        // 커버 페이지 셋팅
        $('#matt_board .next_btn').on('click', function(){
            _this.nowState = 6;
            _this.setAction();
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
        
        $('.f_navi ul li').on('click', function(e){
            console.log(_this.nowState);
            
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
        
        $('.re_load_btn').on('click', function(e){
            _this.imgreUpload = true;
            $('.file_up').show();
            $('.file_down').hide();
            $('.dark_bg, .upload_pop').addClass('on');
        });
        
        $('.back_btn').on('click', function(e){
            if(_this.nowState > 1){
                _this.nowState = _this.nowState - 1;
                _this.setAction();
            }
        });
        
//        $('.delivery_txt').click(function(){
//            $('#frameStart, #content' ).addClass('hidden');
//            $('#frame_cart_wrap').removeClass('hidden');
//            $('#frame_wrap').removeClass('left');
//            $('#frame_footer').removeClass('left');
//            $('.sub_content').css('height', 'auto');
//        });

        $(document).on('click', '.img_nav .slick-slide', function(){
            var navIndex = $(this).data('slick-index');
            console.log(navIndex);
            if(navIndex == '0'){
                $('.frameshadow').removeClass('hidden');
            }else{
                $('.frameshadow').addClass('hidden');
            }
        });

        $(window).resize(function(){
            _this.setFrameShadow();
        });
    }
 
};


