<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH ."controllers/board".EXT);
class goods extends board {

	function __construct() {
		parent::__construct();

		/**
		** @ board start
		**/
		$this->mygdqnatbl				= 'goods_qna';//상품문의
		$this->mygdreviewtbl			= 'goods_review';//상품후기dd

		$this->mygdqna->boardurl->resets	= '/goods/qna_catalog?goods_seq='.$_GET['goods_seq'];
		$this->mygdqna->boardurl->lists		= '/goods/qna_catalog?goods_seq='.$_GET['goods_seq'];
		$this->mygdqna->boardurl->view		= '/goods/qna_view?goods_seq='.$_GET['goods_seq'].'&seq=';
		$this->mygdqna->boardurl->write		= '/goods/qna_write?goods_seq='.$_GET['goods_seq'];
		$this->mygdqna->boardurl->modify	= $this->mygdqna->boardurl->write.'&seq=';
		$this->mygdqna->boardurl->reply		= $this->mygdqna->boardurl->write.'&reply=y&seq=';
		$this->mygdqna->boardurl->goodsview		= '/goods/view?no=';						//상품접근

		$this->mygdreview->boardurl->resets	= '/goods/review_catalog?goods_seq='.$_GET['goods_seq'];
		$this->mygdreview->boardurl->lists		= '/goods/review_catalog?goods_seq='.$_GET['goods_seq'];
		$this->mygdreview->boardurl->view		= '/goods/review_view?goods_seq='.$_GET['goods_seq'].'&seq=';
		$this->mygdreview->boardurl->write		= '/goods/review_write?goods_seq='.$_GET['goods_seq'];
		$this->mygdreview->boardurl->modify	= $this->mygdreview->boardurl->write.'&seq=';
		$this->mygdreview->boardurl->reply		= $this->mygdreview->boardurl->write.'&reply=y&seq=';
		$this->mygdreview->boardurl->goodsview		= '/goods/view?no=';						//상품접근

		/**
		** @ board end
		**/
		$this->load->library('snssocial');
		$this->load->helper('goods');
		$this->load->helper('order');
		$this->load->model('goodsmodel');
	}

	public function main_index()
	{
		redirect("/goods/index");
	}

	public function index()
	{
		redirect("/goods/catalog");
	}

	//카테고리별
	public function catalog()
	{
		$this->load->model('categorymodel');
		$this->load->model('goodsdisplay');
		$this->load->model('categorymodel');
		$this->load->model('countmodel');

		/*######################## 18.07.13 gcs yun jy : 더보기 오류 패치 s */
		$aGetParams	= $this->input->get();
		$code = isset($aGetParams['code']) ? $aGetParams['code'] : '';
		$sort = isset($aGetParams['sort']) ? $aGetParams['sort'] : '';
		$get_display_style = $aGetParams['display_style'];
		
		$sc_top = $aGetParams['sc_top'];
		if($aGetParams['page'] == 1 || !$aGetParams['page']) $sc_top = '';				
		
		if($aGetParams['designMode'] && !$code){
			$tmp	= $this->categorymodel->get_default_category()->row_array();
			$code	= $aGetParams['code'] = $tmp['category_code'];
		}
		/*######################## 18.07.13 gcs yun jy : 더보기 오류 패치 e */


/*######################## 19.03.28 gcs yun jy : #1816 신제품 페이지 s */
		if($this->input->get('pageType') =='new') {

			$categoryData['level'] = "2";
			$categoryData['title'] = "신상품";
			$categoryData['type'] = "folder";
			$categoryData['catalog_allow'] = "show";
			$categoryData['plan_main_display'] = "n";
			$categoryData['hide'] = "0";
			$categoryData['hide_in_navigation'] = "0";
			$categoryData['hide_in_gnb'] = "0";
			$categoryData['hide_in_brand'] = "0";
			$categoryData['navigation_use'] = "n";
			$categoryData['brand_navigation_use'] = "n";
			$categoryData['list_use'] = "y";
			$categoryData['list_default_sort'] = "newly";
			$categoryData['list_style'] = "lattice_a";
			$categoryData['list_count_w'] = "2";
			$categoryData['list_count_w_lattice_b'] = "2";
			$categoryData['list_count_h'] = "10";
			$categoryData['list_paging_use'] = "y";
			$categoryData['list_image_size'] = "large";
			$categoryData['list_text_align'] = "center";
			$categoryData['search_use'] = "y";
			$categoryData['m_list_use'] = "m";
			$categoryData['m_list_default_sort'] = "newly";
			$categoryData['m_list_style'] = "lattice_a";
			$categoryData['m_list_count_w'] = "2";
			$categoryData['m_list_count_h'] = "20";
			$categoryData['m_list_count_r'] = "4";
			$categoryData['m_list_paging_use'] = "y";
			$categoryData['m_list_image_size'] = "list2";
			$categoryData['m_list_mobile_h'] = "100";
			$categoryData['m_list_text_align'] = "center";
			$categoryData['list_count_h_lattice_b'] = "1";
			$categoryData['list_count_h_list'] = "20";
			$categoryData['list_image_size_lattice_b'] = "view";
			$categoryData['list_image_size_list'] = "list2";
			$categoryData['img_opt_lattice_a'] = "1";
			$categoryData['img_padding_lattice_a'] = "0";

			$categoryData['list_info_settings'] = '[{"kind":"provider_name", "font_decoration":{"color":"#9b9b9b", "bold":"bold", "underline":"none"}},{"kind":"goods_name", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}},{"kind":"count", "buy_count":"count"},{"kind":"sale_price", "font_decoration":{"color":"#000000", "bold":"bold", "underline":"none"}, "compare":{"kind":"compare_sale_price", "font_decoration":{"color":"#000000", "bold":"bold", "underline":"none"}, "currency_symbols":{}}},{"kind":"icon"},{"kind":"status_icon", "status_icon_runout":"status_icon", "status_icon_purchasing":"status_icon", "status_icon_unsold":"status_icon"},{"kind":"score"},{"kind":"pageview"}]';

			$categoryData['m_list_info_settings'] = '[{"kind":"brand_title", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}},{"kind":"goods_name", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}},{"kind":"summary", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}},{"kind":"consumer_price", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}, "compare":{"kind":"compare_consumer_price", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}, "currency_symbols":{}}},{"kind":"price", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}, "compare":{"kind":"compare_price", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}, "currency_symbols":{}}},{"kind":"sale_price", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}, "compare":{"kind":"compare_sale_price", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}, "currency_symbols":{}}},{"kind":"count"},{"kind":"fblike"},{"kind":"icon"},{"kind":"status_icon"},{"kind":"score"},{"kind":"provider_name", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}},{"kind":"color"},{"kind":"bigdata", "font_decoration":{"color":"#000000", "bold":"normal", "underline":"none"}, "bigdata":"다른 사람은 뭘살까?"},{"kind":"shipping"},{"kind":"pageview"}]';
			if($categoryData['list_info_settings']){
				$categoryData['list_info_settings'] = str_replace("\"{","{",$categoryData['list_info_settings']);
				$categoryData['list_info_settings'] = str_replace("}\"","}",$categoryData['list_info_settings']);
			}
			if($categoryData['m_list_info_settings']){
				$categoryData['m_list_info_settings'] = str_replace("\"{","{",$categoryData['m_list_info_settings']);
				$categoryData['m_list_info_settings'] = str_replace("}\"","}",$categoryData['m_list_info_settings']);
			}

		}else{
/*######################## 19.03.28 gcs yun jy : #1816 신제품 페이지 e */

			/* 카테고리 정보 */
			$categoryData = $this->categorymodel->get_category_data($code);
		}

		if	($categoryData) {
			if	($categoryData['image_decoration_type'] == 'favorite')
				$categoryData['list_image_decorations'] = $categoryData['image_decoration_favorite'];
			if	($categoryData['goods_decoration_type'] == 'favorite')
				$categoryData['list_info_settings']		= $categoryData['goods_decoration_favorite'];

			$categoryData['list_style'] = $get_display_style ? $get_display_style : $categoryData['list_style'];

			if	($categoryData['list_style'] == 'lattice_b') {
				if	($categoryData['list_image_size_lattice_b'])
					$categoryData['list_image_size'] = $categoryData['list_image_size_lattice_b'];
				if	($categoryData['list_count_w_lattice_b'])
					$categoryData['list_count_w'] = $categoryData['list_count_w_lattice_b'];
				if	($categoryData['list_count_h_lattice_b'])
					$categoryData['list_count_h'] = $categoryData['list_count_h_lattice_b'];
			}

			if	($categoryData['list_style'] == 'list') {
				if	($categoryData['list_image_size_list'])
					$categoryData['list_image_size'] = $categoryData['list_image_size_list'];
				$categoryData['list_count_w'] = 1;
				if	($categoryData['list_count_h_list'])
					$categoryData['list_count_h'] = $categoryData['list_count_h_list'];
			}

		}


		/*######################## 19.05.20 gcs yun jy : #2200 모바일에서 상품 노출 개수가 홀수라 짝수로 맞추기 s */
		if($this->realMobileSkinVersion > 2 && $this->mobileMode) {
			if(($categoryData['list_count_w'] * $categoryData['list_count_h']) % 2 > 0) {
				$totViewCnt = $categoryData['list_count_w'] * $categoryData['list_count_h']; //원래 총 노출 개수
				$categoryData['list_count_w'] = 2;
				$categoryData['list_count_h'] = ($totViewCnt+1) / $categoryData['list_count_w'];//1개만 더 추가
			}
		}
		/*######################## 19.05.20 gcs yun jy : #2200 모바일에서 상품 노출 개수가 홀수라 짝수로 맞추기 e */

		if($this->realMobileSkinVersion > 2 && $this->mobileMode && $categoryData['m_list_use'] == 'y'){
			$categoryData['list_default_sort']		= $categoryData['m_list_default_sort'];
			$categoryData['list_style']				= $categoryData['m_list_style'];
			$categoryData['list_count_w']			= $categoryData['m_list_style'] == 'lattice_responsible' && $categoryData['m_list_count_r'] ? $categoryData['m_list_count_r'] : $categoryData['m_list_count_w'];
			$categoryData['list_count_h']			= $categoryData['m_list_style'] == 'lattice_responsible' && $categoryData['m_list_count_r'] ? 1 : $categoryData['m_list_count_h'];
			$categoryData['list_paging_use']		= $categoryData['m_list_paging_use'];
			$categoryData['list_image_size']		= $categoryData['m_list_image_size'];
			$categoryData['list_text_align']		= $categoryData['m_list_text_align'];
			$categoryData['list_goods_status']		= $categoryData['m_list_goods_status'];

			$categoryData['list_image_decorations']	= $categoryData['m_list_image_decorations'];
			$categoryData['list_info_settings']		= $categoryData['m_list_info_settings'];
			if	($categoryData['m_image_decoration_type'] == 'favorite')
				$categoryData['list_image_decorations'] = $categoryData['m_image_decoration_favorite'];
			if	($categoryData['m_goods_decoration_type'] == 'favorite')
				$categoryData['list_info_settings']		= $categoryData['m_goods_decoration_favorite'];
		}
		$this->categoryData = $categoryData;
		
		if($this->input->get('pageType') !='new') {
			
			if(!$categoryData['category_code']) {
				//카테고리가 올바르지 않습니다.
				pageRedirect('/main/index', getAlert('et025'), 'self');
				exit;
			}

			$code	= $categoryData['category_code'];

			/* 동영상/플래시매직 치환 */
			$categoryData['top_html']	= showdesignEditor($categoryData['top_html']);

			$childCategoryData = $this->categorymodel->get_list($code,array(
				"hide_in_navigation = '0'",
				"level >= 2"
			));
			if(strlen($code)>4 && !$childCategoryData){
				$childCategoryData = $this->categorymodel->get_list(substr($code,0,strlen($code)-4),array(
					"hide_in_navigation = '0'",
					"level >= 2"
				));
			}
			$depth1CategoryData	= $this->categorymodel->get_category_data(substr($code,0,4));
			$this->template->assign(array('depth1CategoryData'=>$depth1CategoryData));

			/* 접근제한 */
			if(!$this->managerInfo && !$this->providerInfo){
				$categoryGroup = array();
				for($i=4;$i<=strlen($code);$i+=4){
					$tmpCode = substr($code,0,$i);
					$categoryGroupTmp = $this->categorymodel->get_category_groups($tmpCode);
					if($categoryGroupTmp) $categoryGroup = $categoryGroupTmp;
					//else break;
				}
				if($categoryGroup){
					if($this->userInfo){
						$allowType = true;
						$allowGroup = true;
						$groupPms = array();
						$typePms = array();

						$this->load->model('membermodel');
						$memberData = $this->membermodel->get_member_data($this->userInfo['member_seq']);
						foreach($categoryGroup as $data) {
							if($data["group_seq"]) {
								$groupPms[] = $data;
							}
							if($data["user_type"]) {
								$typePms[] = $data;
							}
						}

						if(count($groupPms) > 0) {
							$allowGroup = false;
							foreach($groupPms as $data) {
								if($data['group_seq'] == $memberData['group_seq']){
									$allowGroup = true;
									break;
								}
							}
						}

						if(count($typePms) > 0) {
							$allowType = false;
							foreach($typePms as $data) {
								if($data['user_type'] == 'default' && ! $memberData['business_seq']){
									$allowType = true;
									break;
								}
								if($data['user_type'] == 'business' && $memberData['business_seq']){
									$allowType = true;
									break;
								}
							}
						}
						if(!$allowType || !$allowGroup){
							$this->load->helper('javascript');
							//해당 카테고리에 접근권한이 없습니다.
							pageBack(getAlert('et026'));
						}
					}else{
						$this->load->helper('javascript');
						alert(getAlert('et026'));
						$url = "/member/login?return_url=".$_SERVER["REQUEST_URI"];
						pageRedirect($url,'');
						exit;
					}
				}
				if($categoryData['catalog_allow']=='none'){
					//접속할 수 없는 카테고리페이지입니다.
					pageBack(getAlert('et027'));
					exit;
				}
				if($categoryData['catalog_allow']=='period' && $categoryData['catalog_allow_sdate'] && $categoryData['catalog_allow_edate']){
					if(date('Y-m-d') < $categoryData['catalog_allow_sdate'] || $categoryData['catalog_allow_edate'] < date('Y-m-d')){
						//접속할 수 없는 카테고리페이지입니다.
						pageBack(getAlert('et027'));
						exit;
					}
				}
			}
		}

		/* 쇼핑몰 타이틀 */
		if($this->config_basic['shopCategoryTitleTag'] && $categoryData['title']){
			$title = str_replace("{카테고리명}",$categoryData['title'],$this->config_basic['shopCategoryTitleTag']);
			$this->template->assign(array('shopTitle'=>$title));
		}

		//메타테그 치환용 정보
		/*######################## 19.03.28 gcs yun jy : #1844 신제품 페이지 s */
		if($this->input->get('pageType') =='new') {
			$add_meta_info['category']		= $categoryData['title'];
		}else{
		/*######################## 19.03.28 gcs yun jy : #1844 신제품 페이지 e */
			$add_meta_info['category']		= $this->categorymodel->get_category_name($code);
		}
		$this->template->assign(array('add_meta_info'=>$add_meta_info));

		$perpage = $aGetParams['perpage'] ? $aGetParams['perpage'] : $categoryData['list_count_w'] * $categoryData['list_count_h'];//######################## 18.07.13 gcs yun jy : 더보기 오류 패치
		$perpage = $perpage ? $perpage : 10;
		$list_default_sort = $categoryData['list_default_sort'] ? $categoryData['list_default_sort'] : 'popular';

		$perpage_min = $categoryData['list_count_w']*$categoryData['list_count_h'];
		if($perpage != $categoryData['list_count_w']*$categoryData['list_count_h']){
			$categoryData['list_count_h'] = ceil($perpage/$categoryData['list_count_w']);
		}

		/**
		 * list setting
		**/
		//######################## 18.07.13 gcs yun jy : 더보기 오류 패치(aGetParams 로 변경)
		$sc						= array();
		$sc['sort']				= $sort ? $sort : $list_default_sort;
		$sc['page']				= (!empty($aGetParams['page'])) ?		intval($aGetParams['page']):'1';
		$sc['perpage']			= $perpage;
		$sc['image_size']		= $categoryData['list_image_size'];
		$sc['list_style']		= $aGetParams['display_style'] ? $aGetParams['display_style'] : $categoryData['list_style'];
		$sc['list_goods_status']	= $categoryData['list_goods_status'];

		/*######################## 19.03.28 gcs yun jy : #1844 신제품 페이지 s */
		if($this->input->get('pageType') =='new') {
			$sc['pageType'] = $this->input->get('pageType');
			$sc['sort'] = 'newly';	
			
			if($_GET['code']) {
				$sc['category_code']	= $code;
			}
		}else{
		/*######################## 19.03.28 gcs yun jy : #1844 신제품 페이지 e */
			$sc['category_code']	= $code;
		}
		$sc['brands']			= !empty($aGetParams['brands'])		? $aGetParams['brands'] : array();
		$sc['brand_code']		= !empty($aGetParams['brand_code'])	? $aGetParams['brand_code'] : '';
		$sc['search_text']		= !empty($aGetParams['search_text'])	? $aGetParams['search_text'] : '';
		$sc['old_search_text']	= !empty($aGetParams['old_search_text'])	? $aGetParams['old_search_text'] : '';

		if( $this->mobileMode || $this->storemobileMode ){
			if(!preg_match("/^mobile/",$sc['list_style'])) $sc['list_style']	= "mobile_".$sc['list_style'];
			$sc['m_list_use']	= $categoryData['m_list_use'];		// 모바일에서 모바일정렬대로 노출
		}else{
			$sc['list_style']		= preg_replace("/^mobile_/","",$sc['list_style']);
		}

/*######################## 18.07.13 gcs yun jy : #1042 필터 기능 s */
		$sc['provider_arr']			= !empty($aGetParams['provider_arr'])		? $aGetParams['provider_arr'] : array(); //입점사 검색

		if($aGetParams['goodsStatus']) { //품절상품만 보기
			$sc['list_goods_status'] = $aGetParams['goodsStatus'];
		}
/*######################## 18.07.13 gcs yun jy : #1042 필터 기능 e */



		//######################## 18.07.13 gcs yun jy : 더보기 오류 패치(aGetParams 로 변경)
		if( !empty($aGetParams['start_price']) && !empty($aGetParams['end_price']) && $aGetParams['end_price'] < $aGetParams['start_price'] ) {//상품가격 검색시 시작가격과 마지막가격 비교
			$start_price			= $aGetParams['start_price'];
			$end_price				= $aGetParams['end_price'];
			$aGetParams['end_price']		= $start_price;
			$aGetParams['start_price']	= $end_price;
		}
		$sc['start_price']		= !empty($aGetParams['start_price'])	? $aGetParams['start_price'] : '';
		$sc['end_price']		= !empty($aGetParams['end_price'])	? $aGetParams['end_price'] : '';
		$sc['color']			= !empty($aGetParams['color'])		? $aGetParams['color'] : '';
		$sc['sc_top']			= !empty($aGetParams['sc_top'])		? $aGetParams['sc_top'] : '';

		$list	= $this->goodsmodel->goods_list($sc);

		// 모바일Ver3에서 상품데이터를 json로 처리
		if($aGetParams['returnJsonData']){  //######################## 18.07.13 gcs yun jy : 더보기 오류 패치(aGetParams 로 변경)
			$images = get_goods_images($list['record'][0]['goods_seq_array']);
			foreach($list['record'] as $i=>$row){
				$list['record'][$i]['images'] = $images[$row['goods_seq_array']];
			}
			echo json_encode($list);
			exit;
		}
		//$data_count	= $this->countmodel->get($code)->row_array(); //20181017 gcskmh속도 처리 page에서 이미 전체 갯수 세고 있음

		/*######################## 18.11.09 gcs jdh : 총갯수 s */
		//$sc['pageType'] = 'new';	########## 2019-09-20 [gcs]sdh : VOC 접수 내용 수정-PC/모바일 상품 카테고리 총개수 불일치
		$sc['gettotalcnt'] = 'y';
		$tcnt = $this->goodsmodel->goods_list($sc);	
		$list['page']['totalpage'] = ceil($tcnt['cnt']/$sc['perpage']);
		$list['page']['totalcount'] = $tcnt['cnt'];
		/*######################## 18.11.09 gcs jdh : 총갯수 e */

		//$list['page']['totalcount']	= (int) $list['page']['totalcount'];
		$this->template->assign($list);

		if($categoryData['list_paging_use']=='n') $this->template->assign(array('page'=>array('totalcount'=>count($list['record']))));
		$this->template->assign(array(
			'categoryCode'			=> $code,
			'categoryTitle'			=> $categoryData['title'],
			'categoryData'			=> $categoryData,
			'childCategoryData'		=> $childCategoryData,
		));
//		$this->template->assign('back_page', $back_page);
//		$this->template->assign('back_goods_no', $back_goods_no);
		$this->getboardcatalogcode = $code;//상품후기 : 게시판추가시 이용됨

		//mobile search_top_text
		if($sc['search_text']) {
			$arr_search_text = explode("\n",$aGetParams['old_search_text']); //######################## 18.07.13 gcs yun jy : 더보기 오류 패치
			if(!in_array($sc['search_text'],$arr_search_text)) $arr_search_text[] = $sc['search_text'];
			$sc['search_top_text'] = array();
			foreach($arr_search_text as $search_text){
				if(trim($search_text)){
					$sc['search_top_text'][] = trim($search_text);
				}
			}
			$old_search_top_text = implode("\n",$sc['search_top_text']);
		}
		$this->template->assign('old_search_top_text',$old_search_top_text);
		
		/*######################## 19.07.31 gcs yun jy : #2686 카테고리별 배너 노출 (재개발) s */
		//하위카테고리 배너 노출
		$childData = $this->categorymodel->getChildCategory($code,true,'catalog');
		$this->template->assign("childData",$childData);
		/*######################## 19.07.31 gcs yun jy : #2686 카테고리별 배너 노출 (재개발) e */

		/**
		 * display
		**/
		//빅데이터를 위해 최근 상품을 기준으로 한다

		$this->bigdataGoodsSeq = $list['record'][0]['goods_seq'];
		$display_key = $this->goodsdisplay->make_display_key();
		$this->goodsdisplay->set('display_key',$display_key);
		$this->goodsdisplay->set('style',$sc['list_style']);
		$this->goodsdisplay->set('count_w',$categoryData['list_count_w']);
		$this->goodsdisplay->set('count_w_lattice_b',$categoryData['list_count_w_lattice_b']);
		$this->goodsdisplay->set('count_h',$categoryData['list_count_h']);
		$this->goodsdisplay->set('image_decorations',$categoryData['list_image_decorations']);
		$this->goodsdisplay->set('image_size',$categoryData['list_image_size']);
		$this->goodsdisplay->set('text_align',$categoryData['list_text_align']);
		$this->goodsdisplay->set('info_settings',$categoryData['list_info_settings']);
		$this->goodsdisplay->set('displayTabsList',array($list));
		$this->goodsdisplay->set('displayGoodsList',$list['record']);
		$this->goodsdisplay->set('mobile_h',$categoryData['m_list_mobile_h']);
		$this->goodsdisplay->set('m_list_use',$categoryData['m_list_use']);
		$this->goodsdisplay->set('img_optimize',$categoryData['img_opt_lattice_a']);
		$this->goodsdisplay->set('img_opt_lattice_a',$categoryData['img_opt_lattice_a']);
		$this->goodsdisplay->set('img_padding',$categoryData['img_padding_lattice_a']);
		$this->goodsdisplay->set('count_h_lattice_b',$categoryData['list_count_h_lattice_b']);
		$this->goodsdisplay->set('count_h_list',$categoryData['list_count_h_list']);

		if( strstr($categoryData['list_info_settings'],"fblike") && ( !$this->__APP_LIKE_TYPE__ || $this->__APP_LIKE_TYPE__ == 'API')) {//라이크포함시
			$goodsDisplayHTML = $this->is_file_facebook_tag;
			define('FACEBOOK_TAG_PRINTED','YES');
			$goodsDisplayHTML .= "<div id='{$display_key}' class='designCategoryGoodsDisplay' designElement='categoryGoodsDisplay'>";
		}else{
			$goodsDisplayHTML = "<div id='{$display_key}' class='designCategoryGoodsDisplay' designElement='categoryGoodsDisplay'>";
		}

		$goodsDisplayHTML .= $this->goodsdisplay->print_(true);
		$goodsDisplayHTML .= "</div>";

		$tmpGET = $aGetParams; //######################## 18.07.13 gcs yun jy : 더보기 오류 패치
		unset($tmpGET['sort']);
		unset($tmpGET['page']);
		unset($tmpGET['sc_top']);
		unset($tmpGET['m_code']);
		$sortUrlQuerystring = getLinkFilter('',array_keys($tmpGET));

/*######################## 19.11.26 gcs yun jy : #3768  카테고리 정렬 > 업데이트순 기능 추가 s */
		if($this->rm3768 =='Y') {
			$this->goodsdisplay->orders['update']	= '업데이트순';
		}
/*######################## 19.11.26 gcs yun jy : #3768 카테고리 정렬 > 업데이트순 기능 추가 e */

		$this->template->assign(array(
			'goodsDisplayHTML'		=> $goodsDisplayHTML,
			'sortUrlQuerystring'	=> $sortUrlQuerystring,
			'sort'					=> $sc['sort'],
			'sc'					=> $sc,
			'orders'				=> $this->goodsdisplay->orders,
			'perpage_min'			=> $perpage_min,
			'list_style'			=> $sc['list_style'],
			'sc_top'				=> $sc_top
		));
		

/*######################## 18.07.13 gcs yun jy : 더보기 오류 패치 s */
		if($aGetParams['ajax']){ 
			// 모바일 카테고리 페이지 개선 2018-06-08
			if( $aGetParams['pagever'] == 1 ) {
				$result = array();
				$result['mpage'] = $list['page']['totalpage'] == $list['page']['nowpage'] ? '1' : '0';
				$result['html'] = $goodsDisplayHTML;
			} else {
				echo $goodsDisplayHTML;
			}
/*######################## 18.07.13 gcs yun jy : 더보기 오류 패치 e */
		}else if($categoryData["plan"] == "y"){
			$this->print_layout($this->skin.'/goods/_catalog_plan.html');
		}else{
			$this->print_layout($this->template_path());
		}

		//GA통계
		if($this->ga_auth_commerce_plus){
			$ga_params['item'] = $list['record'];
			$ga_params['page'] = "카테고리:".$categoryData['title'];
/*######################## 18.07.13 gcs yun jy : 더보기 오류 패치 s */
			if( $aGetParams['pagever'] == 1 ) { 
				$result['html'] .= google_analytics($ga_params,"list_count");
			} else {
				echo google_analytics($ga_params,"list_count");
			}
/*######################## 18.07.13 gcs yun jy : 더보기 오류 패치 e */
		}

		if($aGetParams['pagever'] == 1) echo json_encode($result); //######################## 18.07.13 gcs yun jy : 더보기 오류 패치 s


		//#############  2018.10.16 gcs ksm : #501 로딩이미지 감추기 (로딩이미지는 스킨/_modules/common/html_header.html 에 있음)
		if(!$_GET['ajax']){ echo "<script>loadingStop('body',true);</script>"; }
		//#############  2018.10.16 gcs ksm : #501 로딩이미지 감추기



	}

	public function category(){
		$code = $_GET['code'];
		$this->load->model('categorymodel');
		$result = $this->categorymodel->get_list($code);
		echo json_encode($result);
	}

	public function child_brand(){
		$code = $_GET['code'];
		$this->load->model('brandmodel');
		$result = $this->brandmodel->get_list($code);
		echo json_encode($result);
	}

	public function brand_main() {
		$this->load->model("brandmodel");
		$this->load->model("brandclassificationmodel");
		$this->load->model('brandcountrymodel');
		//정렬 @2017-05-16
		$params["oKey"]		= 'name';
		$params["oType"]	= 'asc';
		$brandCountry = $this->brandcountrymodel->_select_list($params);
		//정렬 @2017-05-16
		$params["oKey"]		= 'title';
		$params["oType"]	= 'asc';
		$data["classification"] = $this->brandclassificationmodel->_select_list($params);
		$this->template->assign(array('country'=>$brandCountry));
		$this->template->assign($data);
		$this->print_layout($this->template_path());
	}

	//브랜드별
	public function brand(){
		$this->load->model('goodsdisplay');
		$this->load->model('brandmodel');
		$this->load->model('brandcountrymodel');

		$code = isset($_GET['code']) ? $_GET['code'] : '';
		$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
		$get_display_style = $_GET['display_style'];

		/*모바일 페이징 limit 조정 코드*/
		if(isset($_GET['m_code'])) {
			$m_code = $_GET['m_code'];
			$sc_top = $_GET['sc_top'];
		}

		if($_GET['designMode'] && !$code){
			$query = $this->db->query("select category_code from fm_brand where level>1 order by category_code asc limit 1");
			$tmp = $query->row_array();
			$code = $_GET['code'] = $tmp['category_code'];
		}

		/* 브랜드 정보 */
		$categoryData = $this->brandmodel->get_brand_data($code);

		if	($categoryData) {
			if	($categoryData['image_decoration_type'] == 'favorite')
				$categoryData['list_image_decorations'] = $categoryData['image_decoration_favorite'];
			if	($categoryData['goods_decoration_type'] == 'favorite')
				$categoryData['list_info_settings']		= $categoryData['goods_decoration_favorite'];

			$categoryData['list_style'] = $get_display_style ? $get_display_style : $categoryData['list_style'];

			if	($categoryData['list_style'] == 'lattice_b') {
				if	($categoryData['list_image_size_lattice_b'])
					$categoryData['list_image_size'] = $categoryData['list_image_size_lattice_b'];
				if	($categoryData['list_count_w_lattice_b'])
					$categoryData['list_count_w'] = $categoryData['list_count_w_lattice_b'];
				if	($categoryData['list_count_h_lattice_b'])
					$categoryData['list_count_h'] = $categoryData['list_count_h_lattice_b'];
			}

			if	($categoryData['list_style'] == 'list') {
				if	($categoryData['list_image_size_list'])
					$categoryData['list_image_size'] = $categoryData['list_image_size_list'];
				$categoryData['list_count_w'] = 1;
				if	($categoryData['list_count_h_list'])
					$categoryData['list_count_h'] = $categoryData['list_count_h_list'];
			}
		}

		if($this->realMobileSkinVersion > 2 && $this->mobileMode && $categoryData['m_list_use'] == 'y'){
			$categoryData['list_default_sort'] = $categoryData['m_list_default_sort'];
			$categoryData['list_style'] = $categoryData['m_list_style'];
			$categoryData['list_count_w'] = $categoryData['m_list_style'] == 'lattice_responsible' && $categoryData['m_list_count_r'] ? $categoryData['m_list_count_r'] : $categoryData['m_list_count_w'];
			$categoryData['list_count_h'] = $categoryData['m_list_style'] == 'lattice_responsible' && $categoryData['m_list_count_r'] ? 1 : $categoryData['m_list_count_h'];
			$categoryData['list_paging_use'] = $categoryData['m_list_paging_use'];
			$categoryData['list_image_size'] = $categoryData['m_list_image_size'];
			$categoryData['list_text_align'] = $categoryData['m_list_text_align'];
			$categoryData['list_image_decorations'] = $categoryData['m_list_image_decorations'];
			$categoryData['list_info_settings'] = $categoryData['m_list_info_settings'];
			$categoryData['list_goods_status'] = $categoryData['m_list_goods_status'];
			if	($categoryData['m_image_decoration_type'] == 'favorite')
				$categoryData['list_image_decorations'] = $categoryData['m_image_decoration_favorite'];
			if	($categoryData['m_goods_decoration_type'] == 'favorite')
				$categoryData['list_info_settings']		= $categoryData['m_goods_decoration_favorite'];
		}

		$childCategoryData = $this->brandmodel->get_list($code,array(
			"hide = '0'",
			"level >= 2"
		));
		if(strlen($code)>4 && !$childCategoryData){
			$childCategoryData = $this->brandmodel->get_list(substr($code,0,strlen($code)-4),array(
				"hide = '0'",
				"level >= 2"
			));
		}

		/* 접근제한 */
		if(!$this->managerInfo && !$this->providerInfo){
			$categoryGroup = array();
			for($i=4;$i<=strlen($code);$i+=4){
				$tmpCode = substr($code,0,$i);
				$categoryGroupTmp = $this->brandmodel->get_brand_groups($tmpCode);
				if($categoryGroupTmp) $categoryGroup = $categoryGroupTmp;
				//else break;
			}

			if($categoryGroup){
				if($this->userInfo){
					$this->load->model('membermodel');
					$memberData = $this->membermodel->get_member_data($this->userInfo['member_seq']);

					$groupPms = array();
					$typePms = array();
					foreach($categoryGroup as $data) {
						if($data["group_seq"]) {
							$groupPms[] = $data;
						}
						if($data["user_type"]) {
							$typePms[] = $data;
						}
					}

					$allowGroup = true;
					if(count($groupPms) > 0) {
						$allowGroup = false;
						foreach($groupPms as $data) {
							if($data['group_seq'] == $memberData['group_seq']){
								$allowGroup = true;
								break;
							}
						}
					}

					$allowType = true;
					if(count($typePms) > 0) {
						$allowType = false;
						foreach($typePms as $data) {
							if($data['user_type'] == 'default' && ! $memberData['business_seq']){
								$allowType = true;
								break;
							}
							if($data['user_type'] == 'business' && $memberData['business_seq']){
								$allowType = true;
								break;
							}
						}
					}

					if(!$allowType || !$allowGroup){
						$this->load->helper('javascript');
						//해당 브랜드에 접근권한이 없습니다.
						pageBack(getAlert('et028'));
					}
				}else{
					$this->load->helper('javascript');
					alert(getAlert('et028'));
					$url = "/member/login?return_url=".$_SERVER["REQUEST_URI"];
					pageRedirect($url,'');
					exit;
				}
			}

			if($categoryData['catalog_allow']=='none'){
				//접속할 수 없는 브랜드페이지입니다.
				pageBack(getAlert('et029'));
				exit;
			}
			if($categoryData['catalog_allow']=='period' && $categoryData['catalog_allow_sdate'] && $categoryData['catalog_allow_edate']){
				if(date('Y-m-d') < $categoryData['catalog_allow_sdate'] || $categoryData['catalog_allow_edate'] < date('Y-m-d')){
					pageBack(getAlert('et029'));
					exit;
				}
			}
		}

		/* 쇼핑몰 타이틀 */
		if($this->config_basic['shopCategoryTitleTag'] && $categoryData['title']){
			$title = str_replace("{카테고리명}",$categoryData['title'],$this->config_basic['shopCategoryTitleTag']);
			$this->template->assign(array('shopTitle'=>$title));
		}

		$perpage = $_GET['perpage'] ? $_GET['perpage'] : $categoryData['list_count_w'] * $categoryData['list_count_h'];
		$perpage = $perpage ? $perpage : 10;
		$list_default_sort = $categoryData['list_default_sort'] ? $categoryData['list_default_sort'] : 'popular';

		$perpage_min = $categoryData['list_count_w']*$categoryData['list_count_h'];
		if($perpage != $categoryData['list_count_w']*$categoryData['list_count_h']){
			$categoryData['list_count_h'] = ceil($perpage/$categoryData['list_count_w']);
		}

		/* 동영상/플래시매직 치환 */
		$categoryData['top_html'] = showdesignEditor($categoryData['top_html']);

		/**
		 * list setting
		**/
		$sc=array();
		$sc['sort']				= $sort ? $sort : $list_default_sort;
		$sc['page']				= (!empty($_GET['page'])) ?		intval($_GET['page']):'1';
		if($categoryData['list_paging_use']=='n'){
			$sc['limit']			= $perpage;
		}else{
			$sc['perpage']			= $perpage;
		}
		$sc['image_size']		= $categoryData['list_image_size'];
		$sc['list_style']		= $_GET['display_style'] ? $_GET['display_style'] : $categoryData['list_style'];

		if( $this->mobileMode || $this->storemobileMode ){
			if(!preg_match("/^mobile/",$sc['list_style']))
				$sc['list_style']= "mobile_".$sc['list_style'];
		}else{
			$sc['list_style']	= preg_replace("/^mobile_/","",$sc['list_style']);
		}

		$sc['list_goods_status']= $categoryData['list_goods_status'];

		$sc['category_code']	= !empty($_GET['category_code'])	? $_GET['category_code'] : '';
		$sc['brand_code']		= $code;
		$sc['search_text']		= !empty($_GET['search_text'])	? $_GET['search_text'] : '';
		$sc['old_search_text']	= !empty($_GET['old_search_text'])	? $_GET['old_search_text'] : '';

		if( !empty($_GET['start_price']) && !empty($_GET['end_price']) && $_GET['end_price'] < $_GET['start_price'] ) {//상품가격 검색시 시작가격과 마지막가격 비교
			$start_price		=$_GET['start_price'];
			$end_price			=$_GET['end_price'];
			$_GET['end_price']		=$start_price;
			$_GET['start_price']	=$end_price;
		}
		$sc['start_price']		= !empty($_GET['start_price'])	? $_GET['start_price'] : '';
		$sc['end_price']		= !empty($_GET['end_price'])	? $_GET['end_price'] : '';
		$sc['color']			= !empty($_GET['color'])		? $_GET['color'] : '';
		$sc['categoryar']			= !empty($_GET['categoryar'])		? $_GET['categoryar'] : array();

		if(isset($m_code) && $m_code != '') {
			$sc['m_code'] = $m_code;
		}
/*######################## 18.07.13 gcs yun jy : #1042 필터 기능 s */
		$sc['provider_arr']			= !empty($_GET['provider_arr'])		? $_GET['provider_arr'] : array(); //입점사 검색

		if($_GET['goodsStatus']) { //품절상품만 보기
			$sc['list_goods_status'] = $_GET['goodsStatus'];
		}
/*######################## 18.07.13 gcs yun jy : #1042 필터 기능 e */

		$list	= $this->goodsmodel->goods_list($sc);

		$this->template->assign($list);



		if($categoryData['list_paging_use']=='n'){
			$this->template->assign(array('page'=>array('totalcount'=>count($list['record']))));
		}

		//메타테그 치환용 정보
		if($categoryData['country_seq'] > 0){
			$brand_country	= $this->brandcountrymodel->_select_row(array('seq'=>$categoryData['country_seq']));
		}
		$add_meta_info['brand_title']		= $categoryData['title'];
		$add_meta_info['brand_eng_title']	= $categoryData['title_eng'];
		$add_meta_info['brand_country']		= ($categoryData['country_seq'] > 0) ? $brand_country['name'] : '';

		$this->template->assign(array(
			'categoryCode'			=> $code,
			'categoryTitle'			=> $categoryData['title'],
			'categoryData'			=> $categoryData,
			'childCategoryData'		=> $childCategoryData,
			'add_meta_info'			=> $add_meta_info
		));
		$this->getboardcatalogcode = $code;//상품후기 : 게시판추가시 이용됨

		//mobile search_top_text
		if($sc['search_text']) {
			$arr_search_text = explode("\n",$_GET['old_search_text']);

			if(!in_array($sc['search_text'],$arr_search_text)) $arr_search_text[] = $sc['search_text'];

			$sc['search_top_text'] = array();
			foreach($arr_search_text as $search_text){
				if(trim($search_text)){
					$sc['search_top_text'][] = trim($search_text);
				}
			}

			$old_search_top_text = implode("\n",$sc['search_top_text']);
		}
		$this->template->assign('old_search_top_text',$old_search_top_text);

		/* ######################## 17.02.16 gcs yjy : 하위 카테고리 정보 s */
		if($this->rm2686=='Y'){
		$childData = $this->brandmodel->getChildBrand($code,true,'catalog');
		$this->template->assign("childData",$childData);
		}
		/* ######################## 17.02.16 gcs yjy : 하위 카테고리 정보 e */



		/**
		 * display
		**/
		//빅데이터를 위해 최근 상품을 기준으로 한다
		$this->bigdataGoodsSeq = $list['record'][0]['goods_seq'];
		$display_key = $this->goodsdisplay->make_display_key();
		$this->goodsdisplay->set('display_key',$display_key);
		$this->goodsdisplay->set('style',$sc['list_style']);
		$this->goodsdisplay->set('count_w',$categoryData['list_count_w']);
		$this->goodsdisplay->set('count_w_lattice_b',$categoryData['list_count_w_lattice_b']);
		$this->goodsdisplay->set('count_h',$categoryData['list_count_h']);
		$this->goodsdisplay->set('image_decorations',$categoryData['list_image_decorations']);
		$this->goodsdisplay->set('image_size',$categoryData['list_image_size']);
		$this->goodsdisplay->set('text_align',$categoryData['list_text_align']);
		$this->goodsdisplay->set('info_settings',$categoryData['list_info_settings']);
		$this->goodsdisplay->set('displayTabsList',array($list));
		$this->goodsdisplay->set('displayGoodsList',$list['record']);
		$this->goodsdisplay->set('mobile_h',$categoryData['m_list_mobile_h']);
		$this->goodsdisplay->set('m_list_use',$categoryData['m_list_use']);
		$this->goodsdisplay->set('img_optimize',$categoryData['img_opt_lattice_a']);
		$this->goodsdisplay->set('img_opt_lattice_a',$categoryData['img_opt_lattice_a']);
		$this->goodsdisplay->set('img_padding',$categoryData['img_padding_lattice_a']);
		$this->goodsdisplay->set('count_h_lattice_b',$categoryData['list_count_h_lattice_b']);
		$this->goodsdisplay->set('count_h_list',$categoryData['list_count_h_list']);

		if(strstr($categoryData['list_info_settings'],"fblike")  && ( !$this->__APP_LIKE_TYPE__ || $this->__APP_LIKE_TYPE__ == 'API') ) {//라이크포함시
			$goodsDisplayHTML = $this->is_file_facebook_tag;
			define('FACEBOOK_TAG_PRINTED','YES');
			$goodsDisplayHTML .= "<div id='{$display_key}' class='designBrandGoodsDisplay' designElement='brandGoodsDisplay'>";
		}else{
			$goodsDisplayHTML = "<div id='{$display_key}' class='designBrandGoodsDisplay' designElement='brandGoodsDisplay'>";
		}
		$goodsDisplayHTML .= $this->goodsdisplay->print_(true);
		$goodsDisplayHTML .= "</div>";

		$tmpGET = $_GET;
		unset($tmpGET['sort']);
		unset($tmpGET['page']);
		unset($tmpGET['m_code']);
		unset($tmpGET['sc_top']);
		$sortUrlQuerystring = getLinkFilter('',array_keys($tmpGET));

		$this->template->assign(array(
			'goodsDisplayHTML'		=> $goodsDisplayHTML,
			'sortUrlQuerystring'	=> $sortUrlQuerystring,
			'sort'					=> $sc['sort'],
			'sc'					=> $sc,
			'orders'				=> $this->goodsdisplay->orders,
			'perpage_min'			=> $perpage_min,
			'list_style'			=> $sc['list_style'],
			'sc_top'				=> $sc_top
		));
		$this->getboardbrandcode = $code;//게시판추가시 상품후기에서 이용됨

		if($_GET['ajax']){
			echo $goodsDisplayHTML;
		}else{
			$this->print_layout($this->template_path());
		}

		//GA통계
		if($this->ga_auth_commerce_plus){
			$ga_params['item'] = $list['record'];
			$ga_params['page'] = "브랜드:".$categoryData['title'];
			echo google_analytics($ga_params,"list_count");
		}
	}


	public function category_list(){
		$this->load->model('categorymodel');

		$category_code = $_GET['code'];
		$result = $this->categorymodel->get_list($category_code);
		$this->template->assign('loop',$result);

		if(!$result){
			header("location:../goods/catalog?code=" . $category_code);
			exit;
		}

		$categorys['category_code'] = $this->categorymodel->split_category($category_code);
		if($categorys['category_code']){
			foreach($categorys['category_code'] as $code){
				$categorys['category'][] = $this->categorymodel->one_category_name($code);

			}
			$this->template->assign('categorys',$categorys);
		}

		$this->print_layout($this->template_path());
	}

	public function option_stock(){
		$cfg = config_load('order');

		$goods_seq = (int) $_GET['no'];

		$option1 = $_GET['option1'];
		$option2 = $_GET['option2'];
		$option3 = $_GET['option3'];
		$option4 = $_GET['option4'];
		$option5 = $_GET['option5'];

		$check_result = check_stock_option($goods_seq,$option1,$option2,$option3,$option4,$option5,0,$cfg,'view_stock');
		echo json_encode($check_result);
	}

	public function suboption_stock(){
		$cfg = config_load('order');

		$goods_seq = (int) $_GET['no'];

		$title = $_GET['title'];
		$option = $_GET['option'];

		$check_result = check_stock_suboption($goods_seq,$title,$option,0,$cfg,'view_stock');

		echo json_encode($check_result);
	}

	public function option($goods_seq=null){
		$this->goodsmodel->option($goods_seq);
	}

	public function option_join($goods_seq=null){
		$this->goodsmodel->option_join($goods_seq);
	}

	// 제품 상세 페이지
	public function view(){

		secure_vulnerability('goods', 'no', $_GET['no']);
		$no	= (int) $_GET['no'];
//		$code = isset($_GET['code']) ? $_GET['code'] : '';
//		$back_page = isset($_GET['back_page']) ? $_GET['back_page'] : '';
//		$this->template->assign('code', $code);
//		$this->template->assign('back_page', $back_page);
		if(!$no){
			$list	= $this->goodsmodel->goods_list(array());
			if(isset($list['record'][0]))	$no	= $list['record'][0]['goods_seq'];
		}

		//2016-04-27 jhr 신규스킨에만 preload 를 적용.
		$preload = false;
		$skin_configuration = skin_configuration($this->skin);

		if	(!empty($skin_configuration['patch_version'])) $preload = true;

		// 배송정책 추가 :: 2016-07-14 lwh
		$result	= $this->goodsmodel->get_goods_view($no);

		// 인증유형 표시안함
		if(is_array($result['assign']['additions'])) {
			foreach($result['assign']['additions'] as $k=>$v) {
				if($v['title'] == '인증유형') { // 11번가 인증번호 필수 입력 카테고리로 인한 조치
					unset($result['assign']['additions'][$k]);
				}
			}
		}

		// 성인인증 세션 없을 경우 성인인증 페이지로 이동 :: 2015-03-10 lwh
		$adult_auth	= $this->session->userdata('auth_intro');
		if($adult_auth['auth_intro_yn'] == '' && $result['goods']['adult_goods'] == 'Y' && (!$this->managerInfo && !$this->providerInfo))
		{
			$return_page = !empty($this->userInfo['member_seq']) ? "/member/adult_auth" : "/intro/adult_only";
			$return_url	= $return_page . "?return_url=".urldecode("/goods/view?no=".$no);
			//해당상품은 성인인증이 필요합니다.\n성인인증 페이지로 이동합니다
			$msg = getAlert('gv001');
			$content = "if(confirm('".$msg."')){document.location.replace('".$return_url."');}else{
				if(opener){self.close();}else{history.back();}
			}";
			echo js($content);
			exit;
		}

		if	($result['status'] == 'error'){
			switch($result['errType']){
				case 'echo':
					echo $result['msg'];
					exit;
				break;
				case 'back':
					pageBack($result['msg']);
					exit;
				break;
				case 'redirect':
					alert($result['msg']);
					pageRedirect($result['url'],'');
					exit;
				break;
			}
		}else{
			$template_dir	= $this->template->template_dir;
			$compile_dir	= $this->template->compile_dir;
			$goods			= $result['goods'];
			$options		= $result['options'];		// 여기 추가
			$suboptions		= $result['suboptions'];	// 여기 추가
			$inputs			= $result['inputs'];
			$category		= $result['category'];
			$alerts			= $result['alerts'];

			// 성인몰이 아닐경우 관리자에게 성인인증상품 알림 :: 2015-03-19 lwh
			$arrBasic = ($this->config_basic)?$this->config_basic:config_load('basic');
			if($arrBasic['operating'] != "adult" && $this->managerInfo && $result['goods']['adult_goods'] == 'Y'){
				//성인 상품입니다.
				$alerts[] = getAlert('gv055');
			}

			//모바일 VER3 스킨에서 상품상세이미지 분할처리
			$mobile_contentscnt = preg_match_all("/<IMG([^>]*)src[\s]*=[\s]*([\"']?)([^>\"']+)[\"']?([^>]*)>/i",$result['assign']['goods']['mobile_contents'], $matches);
			if(count($matches[0])>0){
				$result['assign']['goods']['mobile_contents_images_true'] = true;
				$result['assign']['goods']['mobile_contents_images'] = array();
				foreach($matches[0] as $k=>$v){
					$v = preg_replace("/([\s]*)src([\s]*=)/i"," data-original=",$v);
					$result['assign']['goods']['mobile_contents_images'][] = $v;
				}
			}

			# 판매가에 대한 비교통화
			$sale_price		= $result['assign']['goods']['sale_price'];
			# 비교통화 노출
			$this->template->include_('showCompareCurrency');
			$compare_options = array('position'=>'side','display'=>'layer','width'=>'120px');
			$result['assign']['goods']['sale_price_compare'] = showCompareCurrency('',$sale_price,'return',$compare_options);

			if	($result['assign'])foreach($result['assign'] as $key => $val){
				$this->template->assign(array($key	=> $val));
			}
			// 옵션 분리형
			if($goods['option_view_type']=='divide' && $options){
				$options_n0 = $this->goodsmodel->option($goods['goods_seq']);
				$this->template->assign(array('options_n0'	=> $options_n0));
			}

			// 옵션 조합형
			if($goods['option_view_type']=='join' && $options){
				$options_join = $this->goodsmodel->option_join($goods['goods_seq']);
				$this->template->assign(array('options_join'	=> $options_join));
			}

			$foption		= $this->goodsmodel->get_first_options($goods, $options, 'view');
			if	($goods['option_view_type'] == 'join'){
				$option_data[0]['title']		= $options[0]['option_title'];
				$option_data[0]['newtype']		= $goods['divide_newtype'][0];
				$option_data[0]['options']		= $foption;
				$option_depth					= 1;
			}else{
				if	($goods['option_divide_title'])foreach($goods['option_divide_title'] as $k => $tit){
					$option_data[$k]['title']		= $tit;
					$option_data[$k]['newtype']		= $goods['divide_newtype'][$k];
					if	($k == 0)	$option_data[$k]['options']	= $foption;
					$option_depth++;
				}
			}
			$this->template->assign(array('option_depth'	=> $option_depth));
			$this->template->assign(array('option_data'		=> $option_data));

			// 옵션 선택 박스
			$option_select_path	= str_replace('view.html', '_select_options.html', $this->template_path());
			$this->template->define('OPTION_SELECT', $option_select_path);


			$international_shipping_info_path	= str_replace('view.html', '_international_shipping_info.html', $this->template_path());
			$this->template->define('INTERNATIONAL_SHIPPING_INFO', $international_shipping_info_path);

			// 착불배송 사용여부
			$this->load->helper('shipping');
			$shipping = use_shipping_method();

			$this->template->assign(array('shipping'=>$shipping));

			$expectGoodsChk = false;

			// 비과세 상품 버튼 비노출
			if($goods['tax'] == 'exempt') $expectGoodsChk = true;

			//skin_version @2016-10-13 pjm
			$this->template->assign(array('skin_version'=>$skin_configuration['skin_version']));

			// 네이버 체크아웃(기본통화가 원화 일때만)
			if( $goods['goods_status'] == 'normal' &&  !$goods['string_price_use'] && $this->config_system['basic_currency'] == "KRW"){
				$navercheckout		= config_load('navercheckout');

/*######################## 18.08.20 gcs yun jy : #205 특정등급일 경우, 주문시 별도 처리 s */
				if($this->sitegubun == 'p'){
					$groupInfo = $this->membermodel->get_group_info($this->userInfo["group_seq"]);
					if($groupInfo['order_limit'] =='y') {					
						$navercheckout['use'] = 'n';
					}
				}

/*######################## 18.08.20 gcs yun jy : #205 특정등급일 경우, 주문시 별도 처리 e */


				$marketing_admin	= $this->session->userdata('marketing');
				if(
						$navercheckout['use'] == 'y'											// "사용모드"일때
					||	($navercheckout['use'] == 'test' && $this->managerInfo)					// "테스트모드"이고 관리자아이디일때
					||	($navercheckout['use'] == 'test' && $marketing_admin=='nbp') // "테스트모드"이고 회원아이디 gabia일때
				){

					// 비과세 상품 버튼 비노출
					// if($goods['tax'] == 'exempt') $expectGoodsChk = true;

					// 착불배송 사용여부
					$this->load->model('Providershipping');
					$tmp_shipping = $this->Providershipping->get_provider_shipping($goods['provider_seq']);
					$able_shipping_method = array_keys($tmp_shipping['shipping_method']);
					if(in_array('postpaid',$able_shipping_method)){
						$this->template->assign(array('use_postpaid'=>1));
					}

					# 네이버페이 구매 불가 체크
					$not_buy_chk		= $this->goodsmodel->npay_not_buy_check($goods,'','','',$navercheckout);
					$not_buy_npay		= $not_buy_chk['not_buy_npay'];
					$not_buy_msg		= $not_buy_chk['not_buy_msg'];
					$expectCategoryChk	= $not_buy_chk['expectCategoryChk'];
					$expectGoodsChk		= $not_buy_chk['expectGoodsChk'];

					if(!($this->fammerceMode || $this->storefammerceMode) ){

						$this->template->template_dir = BASEPATH."../partner";
						$this->template->compile_dir	= BASEPATH."../_compile/";

						$this->template->assign(array('navercheckout'=>$navercheckout));

						if($navercheckout['version']=='2.1'){ // 네이버페이 api 2.1

							$npay_btn = array("A",1,2);
							if($this->_is_mobile_agent){
								$navercheckout_btn = $navercheckout['npay_btn_mobile_goods'];
							}else{
								$navercheckout_btn = $navercheckout['npay_btn_pc_goods'];
							}
							$npay_btn = explode("-",$navercheckout_btn);
							$this->template->assign(array('npay_btn'=>$npay_btn));
							$this->template->assign(array('not_buy_npay'=>$not_buy_npay,'not_buy_msg'=>$not_buy_msg));
							$this->template->define(array('navercheckout'=>'naverpay2.1.html'));
							$navercheckout_tpl = $this->template->fetch('navercheckout');
							$this->template->assign(array('navercheckout_tpl'=>$navercheckout_tpl));

						}else{
							if(!$expectGoodsChk && !$expectCategoryChk){
								$this->template->define(array('navercheckout'=>'navercheckout.html'));
								$navercheckout_tpl = $this->template->fetch('navercheckout');
								$this->template->assign(array('navercheckout_tpl'=>$navercheckout_tpl));
							}
						}
					}
				}
			}


			// 통계서버로 통계데이터 전달
			$GAHTML	= "<script>statistics_firstmall('view','".$goods['goods_seq']."','','');</script>";

			/* 고객리마인드서비스 상세유입로그 */
			$this->load->helper('reservation');
			$curation = array("action_kind"=>"goodsview","goods_seq"=>$goods['goods_seq']);
			curation_log($curation);

			//GA통계
			if($this->ga_auth_commerce_plus){
				$ga_params['item'] = $result["goods"];
				$ga_params['action'] = "click";
				$reffer = $_SERVER['HTTP_REFERER'];
				if($reffer){
					$ga_params["page"] = getPage_forGA($reffer);
					if(strstr($reffer, "page/index") !== false){

						$this->load->model('eventmodel');
						$reffer_event = explode("?",$reffer);

						parse_str($reffer_event[1]);

						$event_type = "event";
						if($this->eventmodel->is_gift_template_file($tpl)){
							$event_type = "gift";
							$query = $this->db->query("select *, if(current_date() between start_date and end_date,'진행 중',if(end_date < current_date(),'종료','시작 전')) as status from fm_".$event_type." where tpl_path=?",array($tpl));
						}else{//
							$query = $this->db->query("select *, if(CURRENT_TIMESTAMP() between start_date and end_date,'진행 중',if(end_date < CURRENT_TIMESTAMP(),'종료','시작 전')) as status from fm_".$event_type." where tpl_path=?",array($tpl));
						}

						$data = $query->row_array();

						$ga_params_event['event_seq'] = $data['event_seq'];
						$ga_params_event['title'] = $data['title'];
						$ga_params_event['tpl_path'] = $data['tpl_path'];
						$ga_params_event['action'] = "promo_click";
						$GAHTML	.= google_analytics($ga_params_event,"promotion");
						//이벤트 페이지도 기록해준다
						$ga_params["page"] = "이벤트:".$data['title'];
					}
				}
				$GAHTML	.= google_analytics($ga_params,"view_count");
				//클릭과 동시에 세부정보 비율 카운트해준다
				$ga_params['action'] = "detail";
				$GAHTML	.= google_analytics($ga_params,"view_count");
			}

			//메타테그 치환용 정보
			$add_meta_info['summary']		= $goods['summary'];
			$add_meta_info['goods_name']	= $goods['goods_name'];
			$add_meta_info['brand_title']	= $goods['brand_title'];
			$add_meta_info['category']		= implode(' > ', (array)$goods['category']);
			$add_meta_info['keyword']		= $goods['keyword'];
			$this->template->assign(array('add_meta_info'=>$add_meta_info));

			//상품상세 프리로드 사용 @2016-04-27 jhr
			$this->template->assign(array('preload'=>$preload));

			$this->template->ga_html = $GAHTML;
			$this->template->template_dir = $template_dir;
			$this->template->compile_dir = $compile_dir;
			$this->print_layout($this->template_path());

			// 가격대체문구 사용여부
			//echo "<script>var gl_string_price_use = 0;</script>";
			if( $goods['string_price_use'] ){
				//echo "<script>var gl_string_price_use = ".$goods['string_price_use'].";</script>";
			}
			// 관리자 표시용 메시지 출력
			foreach($alerts as $msg){
				alert($msg);
			}

			// 일별 통계 로그 2015-10-27 jhr
			$this->load->model('dailystatsmodel');
			$this->dailystatsmodel->view_log($goods);
		}
	}

	/* 모바일용 상세설명 */
	// 2016-04-27 jhr 상품상세 preload에서도 사용
	public function view_contents(){
		secure_vulnerability('goods', 'no', $_GET['no']);
		$no = (int) $_GET['no'];

		if(!$no){
			$list	= $this->goodsmodel->goods_list(array());
			if(isset($list['record'][0])){
				$no = $list['record'][0]['goods_seq'];
			}
		}

		//2016-04-27 jhr 신규스킨에만 preload 를 적용.
		$preload = false;
		$skin_configuration = skin_configuration($this->skin);
		if	(!empty($skin_configuration['patch_version'])) $preload = true;
		$this->template->assign(array('preload'=>$preload));

		$goods = $this->goodsmodel->get_goods($no);
		$options = $this->goodsmodel->get_goods_option($no,array('option_view'=>'Y'));
		foreach($options as $k => $opt){
			/* 대표가격 */
			if($opt['default_option'] == 'y'){
				$goods['price'] 			= $opt['price'];
				$goods['sale_price'] 		= $opt['price'];
				$goods['consumer_price'] 	= $opt['consumer_price'];
				$goods['reserve'] 			= $opt['reserve'];
				if( $opt['option_title'] ) $goods['option_divide_title'] = explode(',',$opt['option_title']);
				if( $opt['newtype'] ) $goods['divide_newtype'] = explode(',',$opt['newtype']);
			}
			$options[$k]['opt_join'] = implode('/',$optJoin);
		}

		// 가격대체문구 여부 설정
		$tmp_string_price = get_string_price($goods, $this->userInfo);
		if($tmp_string_price)	$goods['string_price_use'] = true;
		else					$goods['string_price_use'] = false;


		// 모바일 상세 설명 생성
		if( !$goods['mobile_contents'] )
		{
			$goods['mobile_contents'] = $this->goodsmodel->set_mobile_contents($goods['contents'],$goods['goods_seq']);
		}

		$cfg_goods = config_load('goods');
		if($goods['video_use'] == 'Y' ) {
			$video_size = explode("X" , $goods['video_size']);
			$goods['video_size0'] = $video_size[0];
			$goods['video_size1'] = $video_size[1];

			$video_size_mobile = explode("X" , $goods['video_size_mobile']);
			$goods['video_size_mobile0'] = $video_size_mobile[0];
			$goods['video_size_mobile1'] = $video_size_mobile[1];
		}else{
			unset($goods['file_key_w'],$goods['file_key_i'],$goods['video_size']);
			$goods['video_use']	= 'N';
		}
		//동영상리스트
		$this->load->model('videofiles');
		$videosc['tmpcode']= $goods['videotmpcode'];
		$videosc['upkind']= 'goods';
		$videosc['type']= 'contents';
		$videosc['viewer_use']= 'Y';
		$videosc['orderby']= 'sort ';
		$videosc['sort']= 'asc, seq desc ';
		$goodsvideofiles = $this->videofiles->videofiles_list_all($videosc);
		if($goodsvideofiles['result']) foreach($goodsvideofiles['result']as $k => $data){
			//동영상
			if( $this->session->userdata('setMode')=='mobile' && $data['file_key_i'] ){//모바일이면서 file_key_i 값이 있는 경우
				$goodsvideofiles['result'][$k]['uccdomain_thumbnail']		= uccdomain('thumbnail',$data['file_key_i']);
				$goodsvideofiles['result'][$k]['uccdomain_fileswf']			= uccdomain('fileswf',$data['file_key_i']);
				$goodsvideofiles['result'][$k]['uccdomain_fileurl']			= uccdomain('fileurl',$data['file_key_i']);
			}elseif( uccdomain('thumbnail',$data['file_key_w']) && $data['file_key_w'] ) {
				$goodsvideofiles['result'][$k]['uccdomain_thumbnail']		= uccdomain('thumbnail',$data['file_key_w']);
				$goodsvideofiles['result'][$k]['uccdomain_fileswf']			= uccdomain('fileswf',$data['file_key_w']);
				$goodsvideofiles['result'][$k]['uccdomain_fileurl']			= uccdomain('fileurl',$data['file_key_w']);
			}
		}

		if($goodsvideofiles['result']) $this->template->assign('goodsvideofiles',$goodsvideofiles['result']);

		if(!defined('__ISADMIN__')) {
			$this->template->assign('designMode',false);
		}

		/* 동영상/플래시매직 치환 */
		$goods['contents'] = showdesignEditor($goods['contents']);
		$goods['mobile_contents'] = showdesignEditor($goods['mobile_contents']);

		//동영상
		if( $this->session->userdata('setMode')=='mobile' && $goods['file_key_i'] ){//모바일이면서 file_key_i 값이 있는 경우
			$goods['uccdomain_thumbnail']		= uccdomain('thumbnail',$goods['file_key_i']);
			$goods['uccdomain_fileswf']			= uccdomain('fileswf',$goods['file_key_i']);
			$goods['uccdomain_fileurl']			= uccdomain('fileurl',$goods['file_key_i']);
		}elseif( uccdomain('thumbnail',$goods['file_key_w']) && $goods['file_key_w'] ) {
			$goods['uccdomain_thumbnail']		= uccdomain('thumbnail',$goods['file_key_w']);
			$goods['uccdomain_fileswf']			= uccdomain('fileswf',$goods['file_key_w']);
			$goods['uccdomain_fileurl']			= uccdomain('fileurl',$goods['file_key_w']);
		}

		$preload = false;
		if	($_GET['view_preload']) $preload = true;

		$this->template->assign(array('preload'=>$preload));
		$this->template->assign(array('goodsData'=>$goods,'goods'=>$goods));

		if($_GET['zoom']){
			$file_path	= $this->template_path();
			$this->template->define(array('tpl'=>$file_path));
			$this->template->print_("tpl");
		}else{
			$this->print_layout($this->template_path());
		}
	}

	/* 모바일용 티켓상품 위치 */
	public function view_location(){
		secure_vulnerability('goods', 'no', $_GET['no']);
		$no = (int) $_GET['no'];
		if (!$no) pageBack('잘못된 접근입니다.');//취약점 보안강화
		$this->load->model('goodsmodel');

		$goods = $this->goodsmodel->get_goods($no);
		if (!$goods) pageBack('잘못된 접근입니다.');//취약점 보안강화

		// 티켓상품 위치서비스 사용여부 lwh 2014-04-01
		if($this->mobileMode)	$mapview_use	= $goods['pc_mapview'];
		else					$mapview_use	= $goods['m_mapview'];

		$options = $this->goodsmodel->get_goods_option($no);

		if($options)foreach($options as $k => $opt){

			$opt['opspecial_location'] = get_goods_options_print_array($opt);

			/* 티켓상품 위치서비스 사용시 배열 추가 lwh 2014-04-01 */
			if($mapview_use=='Y'){
				$mapArr[$k]['o_seq']			= $opt['option_seq'];
				$mapArr[$k]['option']			= $opt['option'.$opt['opspecial_location']['address']];
				$mapArr[$k]['address']			= $opt['address']. " " .$opt['addressdetail'];
				$mapArr[$k]['address_street']	= $opt['address_street'];
				$mapArr[$k]['biztel']			= $opt['biztel'];
			}
		}

		if($mapview_use=='Y'){
			$this->template->assign('mapArr', $mapArr);
		}

		$this->print_layout($this->template_path());
	}

	public function view_review(){
		secure_vulnerability('goods', 'no', $_GET['no']);
		$no = (int) $_GET['no'];
		//if (!$no) pageBack('잘못된 접근입니다.');//취약점 보안강화
		$this->load->model('goodsmodel');

		if(!$no){
			$list	= $this->goodsmodel->goods_list(array());
			if(isset($list['record'][0])){
				$no = $list['record'][0]['goods_seq'];
			}
		}

		$goods = $this->goodsmodel->get_goods($no);
		if (!$goods) pageBack('잘못된 접근입니다.');//취약점 보안강화

		// 모바일 상세 설명 생성
		if( !$goods['mobile_contents'] )
		{
			$goods['mobile_contents'] = $this->goodsmodel->set_mobile_contents($goods['contents'],$goods['goods_seq']);
		}

		$this->template->assign(array('goods'=>$goods));
		$this->print_layout($this->template_path());
	}

	public function contents(){
		secure_vulnerability('goods', 'no', $_GET['no']);
		$no = (int) $_GET['no'];
		if (!$no) pageBack('잘못된 접근입니다.');//취약점 보안강화

		$this->load->model('goodsmodel');
		$goods = $this->goodsmodel->get_goods($no);
		if (!$goods) pageBack('잘못된 접근입니다.');//취약점 보안강화

		// 모바일 상세 설명 생성
		if( !$goods['mobile_contents'] )
		{
			$goods['mobile_contents'] = $this->goodsmodel->set_mobile_contents($goods['contents'],$goods['goods_seq']);
		}

		$cfg_goods = config_load('goods');
		if($goods['video_use'] == 'Y' ) {
			$video_size = explode("X" , $goods['video_size']);
			$goods['video_size0'] = $video_size[0];
			$goods['video_size1'] = $video_size[1];

			$video_size_mobile = explode("X" , $goods['video_size_mobile']);
			$goods['video_size_mobile0'] = $video_size_mobile[0];
			$goods['video_size_mobile1'] = $video_size_mobile[1];
		}else{
			unset($goods['file_key_w'],$goods['file_key_i'],$goods['video_size']);
			$goods['video_use']	= 'N';
		}
		//동영상리스트
		$this->load->model('videofiles');
		$videosc['tmpcode']= $goods['videotmpcode'];
		$videosc['upkind']= 'goods';
		$videosc['type']= 'contents';
		$videosc['viewer_use']= 'Y';
		$videosc['orderby']= 'sort ';
		$videosc['sort']= 'asc, seq desc ';
		$goodsvideofiles = $this->videofiles->videofiles_list_all($videosc);
		if($goodsvideofiles['result']) foreach($goodsvideofiles['result']as $k => $data){
			//동영상
			if( $this->session->userdata('setMode')=='mobile' && $data['file_key_i'] ){//모바일이면서 file_key_i 값이 있는 경우
				$goodsvideofiles['result'][$k]['uccdomain_thumbnail']		= uccdomain('thumbnail',$data['file_key_i']);
				$goodsvideofiles['result'][$k]['uccdomain_fileswf']			= uccdomain('fileswf',$data['file_key_i']);
				$goodsvideofiles['result'][$k]['uccdomain_fileurl']			= uccdomain('fileurl',$data['file_key_i']);
			}elseif( uccdomain('thumbnail',$data['file_key_w']) && $data['file_key_w'] ) {
				$goodsvideofiles['result'][$k]['uccdomain_thumbnail']		= uccdomain('thumbnail',$data['file_key_w']);
				$goodsvideofiles['result'][$k]['uccdomain_fileswf']			= uccdomain('fileswf',$data['file_key_w']);
				$goodsvideofiles['result'][$k]['uccdomain_fileurl']			= uccdomain('fileurl',$data['file_key_w']);
			}
		}

		if($goodsvideofiles['result']) $this->template->assign('goodsvideofiles',$goodsvideofiles['result']);

		$this->template->assign('designMode',false);

		/* 동영상/플래시매직 치환 */
		$goods['contents'] = showdesignEditor($goods['contents']);
		$goods['mobile_contents'] = showdesignEditor($goods['mobile_contents']);

		//동영상
		if( $this->session->userdata('setMode')=='mobile' && $goods['file_key_i'] ){//모바일이면서 file_key_i 값이 있는 경우
			$goods['uccdomain_thumbnail']		= uccdomain('thumbnail',$goods['file_key_i']);
			$goods['uccdomain_fileswf']			= uccdomain('fileswf',$goods['file_key_i']);
			$goods['uccdomain_fileurl']			= uccdomain('fileurl',$goods['file_key_i']);
		}elseif( uccdomain('thumbnail',$goods['file_key_w']) && $goods['file_key_w'] ) {
			$goods['uccdomain_thumbnail']		= uccdomain('thumbnail',$goods['file_key_w']);
			$goods['uccdomain_fileswf']			= uccdomain('fileswf',$goods['file_key_w']);
			$goods['uccdomain_fileurl']			= uccdomain('fileurl',$goods['file_key_w']);
		}

		$this->template->assign(array('goods'=>$goods));
		$this->print_layout($this->template_path());
	}


	public function zoom()
	{
		secure_vulnerability('goods', 'no', $_GET['no']);
		$no = (int) $_GET['no'];
		if (!$no) pageBack('잘못된 접근입니다.');//취약점 보안강화
		$this->load->model('goodsmodel');

		$sessionMember = ( $this->session->userdata('user') )?$this->session->userdata('user'):$_SESSION['user'];
		$this->template->assign('goodsImageSize',config_load('goodsImageSize'));

		$goods = $this->goodsmodel->get_goods($no);
		if (!$goods) pageBack('잘못된 접근입니다.');//취약점 보안강화
		$goods['title']			= strip_tags($goods['goods_name']);
		$images = $this->goodsmodel->get_goods_image($no);

		// 성인인증 세션 없을 경우 성인이미지 교체 :: 2015-03-10 lwh
		$adult_auth	= $this->session->userdata('auth_intro');
		if($adult_auth['auth_intro_yn'] == '' && $goods['adult_goods'] == 'Y' && (!$this->managerInfo && !$this->providerInfo)){
			foreach($images as $ar => $image){
				foreach($image as $vi => $img){
					$images[$ar][$vi]['image']	= "/data/skin/".$this->skin."/images/common/19mark.jpg";
				}
			}
		}

		$this->template->assign(array('goods'=>$goods));
		$this->template->assign(array('images'=>$images));
		$this->print_layout($this->template_path());
	}

	public function view2()
	{
		secure_vulnerability('goods', 'no', $_GET['no']);
		$no = (int) $_GET['no'];
		if (!$no) pageBack('잘못된 접근입니다.');//취약점 보안강화
		$this->load->model('goodsmodel');

		$goods = $this->goodsmodel->get_goods($no);
		if (!$goods) pageBack('잘못된 접근입니다.');//취약점 보안강화
		$images = $this->goodsmodel->get_goods_image($no);
		$additions = $this->goodsmodel->get_goods_addition($no);
		$options = $this->goodsmodel->get_goods_option($no);
		$suboptions = $this->goodsmodel->get_goods_suboption($no);
		$inputs = $this->goodsmodel->get_goods_input($no);

		foreach($options as $k => $opt){
			/* 대표가격 */
			if($opt['default_option'] == 'y'){
				$goods['price'] 			= $opt['price'];
				$goods['consumer_price'] 	= $opt['consumer_price'];
				$goods['reserve'] 			= $opt['reserve'];
				if( $opt['option_title'] ) $goods['option_divide_title'] = explode(',',$opt['option_title']);
				if( $opt['newtype'] ) $goods['divide_newtype'] = explode(',',$opt['newtype']);
			}
			$options[$k]['opt_join'] = implode('/',$optJoin);
		}


		$this->template->assign(array('goods'=>$goods));
		$this->template->assign(array('options'=>$options));
		$this->template->assign(array('additions'=>$additions));
		$this->template->assign(array('images'=>$images));
		$this->template->assign(array('images'=>$images));

		$this->print_layout($this->template_path());
	}

	public function cart()
	{
		$this->print_layout($this->template_path());
	}

	public function review()
	{
		$this->print_layout($this->template_path());
	}


	public function qna()
	{
		$this->print_layout($this->template_path());
	}

	public function search()
	{
		$this->load->model('goodsdisplay');
		$this->load->model('statsmodel');

		// 디스플레이 임시 코드명
		$display_key = $this->goodsdisplay->make_display_key();

		$category = !empty($_GET['category1']) ? $_GET['category1'] : '';
		$category = !empty($_GET['category2']) ? $_GET['category2'] : $category;
		$category = !empty($_GET['category3']) ? $_GET['category3'] : $category;
		$category = !empty($_GET['category4']) ? $_GET['category4'] : $category;
		$sort = !empty($_GET['sort']) ? $_GET['sort'] : '';

		/* 카테고리 정보 */
		if($_GET['category_code']){
			$this->load->model('categorymodel');
			$this->categoryData = $categoryData = $this->categorymodel->get_category_data($_GET['category_code']);
		}

		if( $this->mobileMode || $this->storemobileMode ){
			$display_ins_arr = $this->goodsmodel->get_goods_display_insert('mobile','모바일 상품검색리스트','search');
			$display = $display_ins_arr['display'];
		}else{
			$display_ins_arr = $this->goodsmodel->get_goods_display_insert('pc','상품검색리스트','search');
			$display = $display_ins_arr['display'];

			// 검색페이지는 기본 리스팅스타일이 list형이므로, lattice_a로 변경했을 때 최소 가로개수를 4개로 지정해줌
			if($display['style']!='lattice_a' && $_GET['display_style']=='lattice_a' && $display['count_w']<3){
				$display['count_w'] = 4;
			}
		}

		$perpage = $_GET['perpage'] ? $_GET['perpage'] : $display['count_w'] * $display['count_h'];
		$perpage = $perpage ? $perpage : 10;
		$list_default_sort = $display['default_sort'] ? $display['default_sort'] : 'popular';

		$perpage_min = $display['count_w']*$display['count_h'];
		if($perpage != $display['count_w']*$display['count_h']){
			$display['count_h'] = ceil($perpage/$display['count_w']);
		}

		/**
		 * list setting
		**/
		$sc=array();
		$sc['sort']				= $sort ? $sort : $list_default_sort;
		$sc['page']				= (!empty($_GET['page'])) ?		intval($_GET['page']):'1';
		$sc['perpage']			= $perpage ? $perpage : 10;
		$sc['image_size']		= $display['image_size'];
		$sc['list_style']		= $_GET['display_style'] ? $_GET['display_style'] : $display['style'];
		if( $this->mobileMode || $this->storemobileMode ){
			if(!preg_match("/^mobile/",$sc['list_style']))
				$sc['list_style']= "mobile_".$sc['list_style'];
		}else{
			$sc['list_style']	= preg_replace("/^mobile_/","",$sc['list_style']);
		}

		$sc['list_goods_status']= $display['goods_status'];

		$sc['category_code']	= !empty($_GET['category_code'])	? $_GET['category_code'] : $category;
		$sc['brands']			= !empty($_GET['brands'])		? $_GET['brands'] : array();
		$sc['brand_code']		= !empty($_GET['brand_code'])	? $_GET['brand_code'] : '';
		$sc['search_text']		= !empty($_GET['search_text'])	? $_GET['search_text'] : '';
		$sc['old_search_text']	= !empty($_GET['old_search_text'])	? $_GET['old_search_text'] : '';

		if( !empty($_GET['start_price']) && !empty($_GET['end_price']) && $_GET['end_price'] < $_GET['start_price'] ) {//상품가격 검색시 시작가격과 마지막가격 비교
			$start_price		=$_GET['start_price'];
			$end_price			=$_GET['end_price'];
			$_GET['end_price']		=$start_price;
			$_GET['start_price']	=$end_price;
		}
		$sc['start_price']		= !empty($_GET['start_price'])	? $_GET['start_price'] : '';
		$sc['end_price']		= !empty($_GET['end_price'])	? $_GET['end_price'] : '';
		$sc['color']			= !empty($_GET['color'])		? $_GET['color'] : '';

		$_GET['category1'] = $sc['category_code'];

		// 검색어 저장
		if($sc['search_text'] && $_GET['keyword_log_flag'] == 'Y'){
			$this->statsmodel->insert_search_stats(trim(urldecode($sc['search_text'])));
			unset($_GET['keyword_log_flag']);
		}

		// 배송그룹 검색 :: 2016-08-31 lwh
		if($_GET['ship_grp_seq']){
			$sc['ship_grp_seq'] = $_GET['ship_grp_seq'];
		}

		/* 카테고리 접근제한 조건 */
		$this->load->model('membermodel');
		$memberData = $this->membermodel->get_member_data($this->userInfo['member_seq']);
		if(!empty($this->userInfo['member_seq']) && $memberData['group_seq']) {
			$sc['member_group_seq']	= $memberData['group_seq'];
		}

		$list	= array();

/*######################## 18.07.13 gcs yun jy : #1042 필터 기능 s */
		$sc['provider_arr']			= !empty($_GET['provider_arr'])		? $_GET['provider_arr'] : array(); //입점사 검색

		if($_GET['goodsStatus']) { //품절상품만 보기
			$sc['list_goods_status'] = $_GET['goodsStatus'];
		}
/*######################## 18.07.13 gcs yun jy : #1042 필터 기능 e */


		if($sc['search_text']||$sc['category_code']||$sc['ship_grp_seq']||$sc['color']) {

			if($_GET['insearch']){
				$sc['insearch'] = $_GET['insearch'];
			}else{
				$_GET['old_category1'] = $_GET['category1'];
				$_GET['old_category2'] = $_GET['category2'];
				$_GET['old_category3'] = $_GET['category3'];
				$_GET['old_category4'] = $_GET['category4'];
				$_GET['old_search_key'] = $sc['search_key'];
				$_GET['old_search_text'] = $sc['search_text'];
			}

			$list = $this->goodsmodel->goods_list($sc);

		/*######################## 18.11.09 gcs jdh : 총갯수 s */
		$sc['gettotalcnt'] = 'y';
		$tcnt = $this->goodsmodel->goods_list($sc);
		$list['page']['totalpage'] = ceil($tcnt['cnt']/$sc['perpage']);
		$list['page']['totalcount'] = $tcnt['cnt'];
		/*######################## 18.11.09 gcs jdh : 총갯수 e */

		}

		//빅데이터를 위해 최근 상품을 기준으로 한다
		$this->bigdataGoodsSeq = $list['record'][0]['goods_seq'];

		//mobile search_top_text
		if($sc['search_text']) {
			$arr_search_text = explode("\n",$_GET['old_search_text']);

			if(!in_array($sc['search_text'],$arr_search_text)) $arr_search_text[] = $sc['search_text'];

			$sc['search_top_text'] = array();
			foreach($arr_search_text as $search_text){
				if(trim($search_text)){
					$sc['search_top_text'][] = trim($search_text);
				}
			}

			$old_search_top_text = implode("\n",$sc['search_top_text']);
		}
		$this->template->assign('old_search_top_text',$old_search_top_text);

		$this->template->assign($list);

		/**
		 * display
		**/
		$this->goodsdisplay->set('platform',$display['platform']);
		$this->goodsdisplay->set('style',$sc['list_style']);
		$this->goodsdisplay->set('count_w',$display['count_w']);
		$this->goodsdisplay->set('count_w_lattice_b',$display['count_w_lattice_b']);
		$this->goodsdisplay->set('kind', $display['kind']);
		//$this->goodsdisplay->set('perpage',$perpage);
		//$this->goodsdisplay->set('navigation_paging_style', $display['navigation_paging_style']);
		if($perpage){
			$this->goodsdisplay->set('count_h',ceil($perpage/$display['count_w']));
		}else{
			$this->goodsdisplay->set('count_h',$display['count_h']);
		}
		$this->goodsdisplay->set('image_decorations',$display['image_decorations']);
		$this->goodsdisplay->set('image_size',$display['image_size']);
		$this->goodsdisplay->set('text_align',$display['text_align']);
		$this->goodsdisplay->set('info_settings',$display['info_settings']);
		$this->goodsdisplay->set('display_key',$display_key);
		$this->goodsdisplay->set('displayTitle',$display['title']);
		$this->goodsdisplay->set('title',$display['title']);
		$this->goodsdisplay->set('APP_USE',$this->__APP_USE__);
		$this->goodsdisplay->set('mobile_h',$display['mobile_h']);
		$this->goodsdisplay->set('m_list_use',$display['m_list_use']);
		######################### 2019-05-28 [gcs] sdh : VOC #34202 상품리스트 레이아웃 깨짐현상 #########################
		$this->goodsdisplay->set('img_optimize', $display['img_opt_lattice_a']);
		$this->goodsdisplay->set('img_opt_lattice_a', $display['img_opt_lattice_a']);
		$this->goodsdisplay->set('img_padding', $display['img_padding_lattice_a']);
		######################### 2019-05-28 [gcs] sdh : VOC #34202 상품리스트 레이아웃 깨짐현상 #########################
		$this->goodsdisplay->set('displayTabsList',array($list));
		$this->goodsdisplay->set('displayGoodsList',!empty($list['record'])?$list['record']:array());
		if(!$this->fammerceMode){
			$this->goodsdisplay->set('target','_blank');
		}
		$goodsDisplayHTML = "<div id='{$display_key}' class='designSearchGoodsDisplay' designElement='searchGoodsDisplay' displaySeq='{$display['display_seq']}'>";
		$goodsDisplayHTML .= $this->goodsdisplay->print_(true);
		$goodsDisplayHTML .= "</div>";

		$tmpGET = $_GET;
		unset($tmpGET['sort']);
		unset($tmpGET['page']);
		$sortUrlQuerystring = getLinkFilter('',array_keys($tmpGET));

		$orders = $this->goodsdisplay->orders;
		unset($orders['popular']);

		$this->template->assign(array(
			'categoryData'			=> $categoryData,
			'goodsDisplayHTML'		=> $goodsDisplayHTML,
			'sortUrlQuerystring'	=> $sortUrlQuerystring,
			'sort'					=> $sc['sort'],
			'sc'					=> $sc,
			'orders'				=> $this->goodsdisplay->orders,
			'perpage_min'			=> $perpage_min,
			'list_style'			=> $sc['list_style'],
		));

		//mobile search_top_text
		if($sc['search_text']) {
			$arr_search_text = explode("\n",$_GET['old_search_text']);

			if(!in_array($sc['search_text'],$arr_search_text)) $arr_search_text[] = $sc['search_text'];

			$sc['search_top_text'] = array();
			foreach($arr_search_text as $search_text){
				if(trim($search_text)){
					$sc['search_top_text'][] = trim($search_text);
				}
			}

			$old_search_top_text = implode("\n",$sc['search_top_text']);
		}

		$this->template->assign('old_search_top_text',$old_search_top_text);


		if($_GET['ajax']){
			echo $goodsDisplayHTML;
		}else{
			$this->print_layout($this->template_path());
		}

		//GA통계
		if($this->ga_auth_commerce_plus){
			$ga_params['item'] = $list['record'];
			$ga_params['page'] = "검색어:".$sc['search_text'];
			echo google_analytics($ga_params,"list_count");
		}
	}

	public function user_select(){
		$file_path	= $this->template_path();


		$referer = parse_url($_SERVER['HTTP_REFERER']);
		if( strstr($referer['path'],'/mypage/mygdreview_') || $_GET['order3month'] ) {
			$order3month = true;
		}
		$this->template->assign('order3month',$order3month);

		$this->template->define(array('tpl'=>$file_path));
		$this->template->print_("tpl");
	}

	public function user_select_list(){

		$this->tempate_modules();
		$file_path	= $this->template_path();

		$referer = parse_url($_SERVER['HTTP_REFERER']);
		if( strstr($referer['path'],'/mypage/mygdreview_') || $_GET['order3month'] ) {
			//$order3month = true;
		}
		$this->template->assign('order3month',$order3month);
		if(isset($_GET['mborder']))$mborder = true;
		if(!isset($_GET['goodsStatus']))$_GET['goodsStatus'] = "";
		if(!isset($_GET['goodsView']))$_GET['goodsView'] = "";
		if(!isset($_GET['sort']))$_GET['sort'] = 0;
		if(!isset($_GET['page']))$_GET['page'] = 1;
		$page = $_GET['page'];

		$where = $subWhere = $whereStr = "";
		$bind = array();

		$arg_list = func_get_args();

		if( isset($_GET['selectCategory4']) &&  $_GET['selectCategory4'] ){
			$subWhere = " and l.category_code = ?";
			$bind[] = $_GET['selectCategory4'];
		}else if( isset($_GET['selectCategory3']) && $_GET['selectCategory3'] ){
			$subWhere = " and l.category_code = ?";
			$bind[] = $_GET['selectCategory3'];
		}else if( isset($_GET['selectCategory2']) && $_GET['selectCategory2'] ){
			$subWhere = " and l.category_code = ?";
			$bind[] = $_GET['selectCategory2'];
		}else if( isset($_GET['selectCategory1']) && $_GET['selectCategory1'] ){
			$subWhere = " and l.category_code = ?";
			$bind[] = $_GET['selectCategory1'];
		}

		/* 카테고리 접근제한 조건 */
		$this->load->model('membermodel');
		$memberData = $this->membermodel->get_member_data($this->userInfo['member_seq']);
		$memberData['mtype'] = $memberData['mtype'] == 'member' ? 'default' : $memberData['mtype'];

		$sqlSelectClause	= ", group_concat(cg.group_seq) as allow_category_user_group, group_concat(cg.user_type) as allow_category_user_type";
		$groupByQuery		= " group by goods_seq ";
		$havingQuery	= " having ((allow_category_user_group IS NULL OR find_in_set('{$memberData['group_seq']}', allow_category_user_group)) ";
		$havingQuery	.= " AND (allow_category_user_type IS NULL OR find_in_set('{$memberData['mtype']}', allow_category_user_type))) ";

		if($subWhere){
			if( isset($_GET['selectCategory1']) && $_GET['selectCategory1'] ){
				$subWhereQuery = "g.goods_seq in
				(
					select goods_seq from (
						select l.goods_seq".$sqlSelectClause." from fm_category_link l
						left join fm_category_group as cg on l.category_code = cg.category_code
						where 1 ".$subWhere.$groupByQuery.$havingQuery."
					) gp
				)
				";
			}else{
				$subWhereQuery = "(
				g.goods_seq in
				(
					select goods_seq from (
						select l.goods_seq".$sqlSelectClause." from fm_category_link l
						left join fm_category_group as cg on l.category_code = cg.category_code
						where 1 ".$subWhere.$groupByQuery.$havingQuery."
					) gp
				)
				or
					not exists (select goods_seq from fm_category_link where goods_seq = g.goods_seq)
				)
				";
			}
			$where[] = $subWhereQuery;
		}

		if( isset($_GET['selectGoodsName']) && $_GET['selectGoodsName'] ){
			$where[] = "g.goods_name like ?";
			$bind[] = '%'.$_GET['selectGoodsName'].'%';
		}
		if( isset($_GET['selectStartPrice']) && $_GET['selectStartPrice'] ){
			$where[] = "o.price >= ?";
			$bind[] = $_GET['selectStartPrice'];
		}
		if( isset($_GET['selectEndPrice']) && $_GET['selectEndPrice'] ){
			$where[] = "o.price <= ?";
			$bind[] = $_GET['selectEndPrice'];
		}

		if( $_GET['goodsStatus'] ){
			$where[] = "g.goods_status = ?";
			$bind[] = $_GET['goodsStatus'];
		}

		if( $_GET['goodsView'] ){
			$where[] = "g.goods_view = ?";
			$bind[] = $_GET['goodsView'];
		}

		if( $_GET['provider_status'] ){
			$where[] = "g.provider_status = ?";
			$bind[] = $_GET['provider_status'];
		}

		if( $_GET['order_seq'] ){
			$where[] = "ord.order_seq = ?";
			$bind[] = $_GET['order_seq'];
		}


		if($where){
			$whereStr = ' and '.implode(' and ',$where);
		}

		$arrSort = array('g.goods_seq desc','g.goods_seq asc','g.purchase_ea desc','g.purchase_ea asc','g.page_view desc','g.page_view asc','g.review_count desc','g.review_count asc');
		$sortStr = " order by " .$arrSort[$_GET['sort']];

		$now_date = date('Y-m-d');

		if($order3month || $mborder || $_GET['order_seq'] ){
			if(!$subWhere){
				if(!$this->arr_step)	$this->arr_step = config_load('step');
				if(!$this->arr_payment)	$this->arr_payment = config_load('payment');
				if(!$this->cfg_order)	$this->cfg_order = config_load('order');
				$endday3 = date("Y-m-d 23:59:59");
				$startday3 = date("Y-m-d 00:00:00", strtotime("-3 month"));
				$query = "select g.goods_seq,g.goods_name,o.price, o.consumer_price, ord.order_seq,g.string_price_use,g.string_price,g.display_terms,g.display_terms_text,g.display_terms_color".$sqlSelectClause."
				from fm_order_item orditm
				left join fm_order ord on orditm.order_seq=ord.order_seq
				left join fm_goods g on g.goods_seq=orditm.goods_seq
				left join fm_goods_option o on o.goods_seq=g.goods_seq
				left join fm_category_link l on l.goods_seq = g.goods_seq
				left join fm_category_group as cg on l.category_code = cg.category_code
				";
				$query .= "
				where
					o.default_option ='y'
					AND (g.goods_view = 'look'
					or ( g.display_terms = 'AUTO' and g.display_terms_begin <= '".$now_date."' and g.display_terms_end >= '".$now_date."'))
					AND ord.member_seq = '{$this->userInfo[member_seq]}'
					AND (ord.step = '70' OR ord.step = '75')
					AND g.goods_type != 'gift'
					".$whereStr.$groupByQuery.$havingQuery.$sortStr;
					//group by orditm.order_seq AND ord.regist_date between '".$startday3."' and '".$endday3."'
			}else{
				if(!$this->arr_step)	$this->arr_step = config_load('step');
				if(!$this->arr_payment)	$this->arr_payment = config_load('payment');
				if(!$this->cfg_order)	$this->cfg_order = config_load('order');
				$endday3 = date("Y-m-d 23:59:59");
				$startday3 = date("Y-m-d 00:00:00", strtotime("-3 month"));
				$query = "select g.goods_seq,g.goods_name,o.price, o.consumer_price, ord.order_seq,g.string_price_use,g.string_price,g.display_terms,g.display_terms_text,g.display_terms_color
				from fm_order_item orditm
				left join fm_order ord on orditm.order_seq=ord.order_seq
				left join fm_goods g on g.goods_seq=orditm.goods_seq
				left join fm_goods_option o on o.goods_seq=g.goods_seq
				";
				$query .= "
				where
					o.default_option ='y'
					AND (g.goods_view = 'look'
					or ( g.display_terms = 'AUTO' and g.display_terms_begin <= '".$now_date."' and g.display_terms_end >= '".$now_date."'))
					AND ord.member_seq = '{$this->userInfo[member_seq]}'
					AND (ord.step = '70' OR ord.step = '75')
					AND g.goods_type != 'gift'
					".$whereStr.$sortStr;
					//group by orditm.order_seq AND ord.regist_date between '".$startday3."' and '".$endday3."'
			}
		}else{
			if(!$subWhere){
				$query = "select g.goods_seq,g.goods_name,o.price,o.consumer_price,g.string_price_use,g.string_price,g.display_terms,g.display_terms_text,g.display_terms_color".$sqlSelectClause."
				from fm_goods g
				inner join fm_goods_option o on o.goods_seq=g.goods_seq
				left join fm_category_link l on l.goods_seq = g.goods_seq
				left join fm_category_group as cg on l.category_code = cg.category_code ";
				$query .= "
				where
					o.default_option ='y' AND (g.goods_view = 'look' or ( g.display_terms = 'AUTO' and g.display_terms_begin <= '".$now_date."' and g.display_terms_end >= '".$now_date."')) AND g.goods_type != 'gift' ".$whereStr.$groupByQuery.$havingQuery.$sortStr;
			}else{
				$query = "select g.goods_seq,g.goods_name,o.price,o.consumer_price,g.string_price_use,g.string_price,g.display_terms,g.display_terms_text,g.display_terms_color
				from fm_goods g
				inner join fm_goods_option o on o.goods_seq=g.goods_seq";
				$query .= "
				where
					o.default_option ='y' AND (g.goods_view = 'look' or ( g.display_terms = 'AUTO' and g.display_terms_begin <= '".$now_date."' and g.display_terms_end >= '".$now_date."')) AND g.goods_type != 'gift' ".$whereStr.$sortStr;
			}
		}
		$result = select_page(10,$page,10,$query,$bind);
		$result['page']['querystring'] = get_args_list();
		foreach($result['record'] as $recorddata){
			$recorddata['image'] = viewImg($recorddata['goods_seq'],'thumbView');

			/* 회원 대체 가격 추가 leewh 2014-12-30 */
			$recorddata['string_price']		= get_string_price($recorddata);
			$recorddata['string_price_use']	= 0;
			if	($recorddata['string_price'] != '')	$recorddata['string_price_use']	= 1;

			//예약 상품의 경우 문구를 넣어준다 2016-11-07
			$recorddata['goods_name']		= get_goods_pre_name($recorddata);

			$record[] = $recorddata;
		}
		unset($result['record']);
		$result['record'] = $record;
		$this->template->assign($result);
		$this->template->define(array('tpl'=>$file_path));
		$this->template->print_("tpl");
	}


	public function qna_catalog()
	{
		if(isset($_GET['goods_seq'])){
			secure_vulnerability('goods', 'no', $_GET['goods_seq']);
			$_GET['goods_seq'] = (int) $_GET['goods_seq'];
		}
		$this->boardurl = $this->mygdqna->boardurl;
		$this->_board_list($this->mygdqnatbl);
		$this->template->assign('boardurl',$this->boardurl);//link url
		if($_GET['goods_seq']) $this->template->assign('designMode',false);
		$this->print_layout($this->template_path());
	}

	public function qna_view()
	{
		$this->boardurl = $this->mygdqna->boardurl;
		$this->_board_view($this->mygdqnatbl);
	}

	public function qna_write()
	{
		$this->boardurl = $this->mygdqna->boardurl;
		$this->_board_write($this->mygdqnatbl);
		$this->template->assign('boardurl',$this->boardurl);//link url
	}

	public function review_catalog()
	{
		if(isset($_GET['goods_seq'])){
			secure_vulnerability('goods', 'no', $_GET['goods_seq']);
			$_GET['goods_seq'] = (int) $_GET['goods_seq'];
		}
		$this->boardurl = $this->mygdreview->boardurl;
		$this->_board_list($this->mygdreviewtbl);
		$this->template->assign('boardurl',$this->boardurl);//link url
		if($_GET['goods_seq']) $this->template->assign('designMode',false);
		$this->print_layout($this->template_path());
	}

	public function review_view()
	{
		$this->boardurl = $this->mygdreview->boardurl;
		$this->_board_view($this->mygdreviewtbl);
		$this->template->assign('boardurl',$this->boardurl);//link url
	}


	public function review_write()
	{
		$this->boardurl = $this->mygdreview->boardurl;
		$this->_board_write($this->mygdreviewtbl);
		$this->template->assign('boardurl',$this->boardurl);//link url
	}
	//board controller list
	private function _board_list($boardid)
	{
		define('BOARDID',$boardid);
		$_GET['iframe'] = 1;
		if( BOARDID == 'goods_qna' ) {
			$this->load->model('Goodsqna','Boardmodel');
		}elseif( BOARDID == 'goods_review' ) {
			$this->load->model('Goodsreview','Boardmodel');
		}elseif( BOARDID == 'bulkorder' ) {//대량구매게시판
			$this->load->model('Boardbulkorder','Boardmodel');
		}else{
			$this->load->model('Boardmodel');
		}
		$sql['whereis']	= ' and id= "'.$boardid.'" ';
		$sql['select']		= ' * ';
		$this->manager = $this->Boardmanager->managerdataidck($sql);//게시판정보
		getboardicon();//icon setting

		if (!isset($this->manager['id'])) pageBack('존재하지 않는 게시판입니다.');
		get_auth($this->manager, '', 'read', $isperm);//접근권한체크
		$this->manager['isperm_read'] = ($isperm['isperm_read'] === true)?'':'_no';
		$this->manager['fileperm_read']= (isset($isperm['fileperm_read']))?$isperm['fileperm_read']:'';
		if ( $isperm['isperm_read'] === false ) {
			if(!defined('__ISADMIN__')) {
				//$this->boardurl->perm = $this->Boardmanager->realboardpermurl.BOARDID.'&popup=1&returnurl=';						//접근권한
				//if(!empty($_GET['popup']) ) {pageClose('접근권한이 없습니다!');}else{pageRedirect($this->boardurl->perm,'');}
			}
		}

		get_auth($this->manager, '', 'write', $isperm);//접근권한체크
		$this->manager['isperm_write'] = ($isperm['isperm_write'] === true)?'':'_no';//'.$this->manager['isperm_write'].'

		$this->template->assign('manager',$this->manager);
		$this->template->assign('designMode',false);
		$this->lists('goods');
	}

	//board controller view
	private function _board_view($boardid)
	{
		define('BOARDID',$boardid);
		$_GET['iframe'] = 1;
		if( BOARDID == 'goods_qna' ) {
			$this->load->model('Goodsqna','Boardmodel');
		}elseif( BOARDID == 'goods_review' ) {
			$this->load->model('Goodsreview','Boardmodel');
		}elseif( BOARDID == 'bulkorder' ) {//대량구매게시판
			$this->load->model('Boardbulkorder','Boardmodel');
		}else{
			$this->load->model('Boardmodel');
		}

		$sql['whereis']	= ' and id= "'.$boardid.'" ';
		$sql['select']		= ' * ';
		$this->manager = $this->Boardmanager->managerdataidck($sql);//게시판정보
		getboardicon();//icon setting

		get_auth($this->manager, $data, 'read', $isperm);//접근권한체크
		if(!defined('__ISADMIN__')) {
			//if ( $isperm['isperm_read'] === false ) pageRedirect($isperm['fileperm_read'],'');
		}
		$this->manager['isperm_read'] = ($isperm['isperm_read'] === true)?'':'_no';

		get_auth($this->manager, $data, 'write', $isperm);//접근권한체크
		$this->manager['isperm_write']	= ($isperm['isperm_write'] === true)?'':'_no';//등록권한
		$this->manager['isperm_moddel'] = ( $isperm['isperm_moddel'] === true)?'':'_no';//수정/삭제권한

		if( $this->manager['isperm_moddel'] == '_no' )  {

			if( ($data['mseq'] != $this->userInfo['member_seq'] && defined('__ISUSER__') === true ) || ( !empty($data['mseq']) && !defined('__ISUSER__') ) ) {
				$this->manager['isperm_moddel'] = '_mbno';//버튼숨김(회원 > 본인만 가능함
			}else{
				// 비번입력후 브라우저를 닫기전까지는 등록/삭제가능함
				$ss_pwwrite_name = 'board_pwwrite_'.BOARDID;
				$boardpwwritess = $this->session->userdata($ss_pwwrite_name);
				if ( strstr($boardpwwritess,'['.$data['seq'].']') && !empty($boardpwwritess)) {
					$this->manager['isperm_moddel'] = '';//비회원 > 접근권한있음
				}
			}
		}
		if( BOARDID == 'goods_review' ) {
			$reserves = ($this->reserves)?$this->reserves:config_load('reserve');//마일리지자동지급관련
			if( !$this->isplusfreenot['ispoint'] ) {//포인트 미사용
				if( $reserves['autopoint_video'] >0 ) $reserves['autopoint_video'] = 0;
				if( $reserves['autopoint_photo'] >0 ) $reserves['autopoint_photo'] = 0;
				if( $reserves['autopoint_review'] >0 ) $reserves['autopoint_review'] = 0;
			}
			### 특정기간
			/**
			if($reserves['bbs_start_date'] && $reserves['bbs_end_date']){
				$today = date("Y-m-d");
				if($today>=$reserves['bbs_start_date'] && $today<=$reserves['bbs_end_date']){
					$reserves['autoemoney_photo']	= $reserves['emoneyBbs_limit'];
					$reserves['autoemoney_review']	= $reserves['emoneyBbs_limit'];

					$reserves['autopoint_photo']	= $reserves['pointBbs_limit'];
					$reserves['autopoint_review']	= $reserves['pointBbs_limit'];
				}
			}
			**/
			$this->template->assign('reserves',$reserves);
			$this->session->unset_userdata('sess_order');//비회원주문번호세션제거
		}

		$this->template->assign('manager',$this->manager);
		$this->template->assign('designMode',false);
		$this->goods_board_view('goods');
	}

	//board controller write
	private function _board_write($boardid)
	{
		define('BOARDID',$boardid);
		$_GET['iframe'] = 1;
		if( BOARDID == 'goods_qna' ) {
			$this->load->model('Goodsqna','Boardmodel');
		}elseif( BOARDID == 'goods_review' ) {
			$this->load->model('Goodsreview','Boardmodel');
		}elseif( BOARDID == 'bulkorder' ) {//대량구매게시판
			$this->load->model('Boardbulkorder','Boardmodel');
		}else{
			$this->load->model('Boardmodel');
		}

		if( BOARDID == 'goods_review' ) {
			$reserves = ($this->reserves)?$this->reserves:config_load('reserve');//마일리지자동지급관련
			if( !$this->isplusfreenot['ispoint'] ) {//포인트 미사용
				if( $reserves['autopoint_video'] >0 ) $reserves['autopoint_video'] = 0;
				if( $reserves['autopoint_photo'] >0 ) $reserves['autopoint_photo'] = 0;
				if( $reserves['autopoint_review'] >0 ) $reserves['autopoint_review'] = 0;
			}
			### 특정기간
			/**
			if($reserves['bbs_start_date'] && $reserves['bbs_end_date']){
				$today = date("Y-m-d");
				if($today>=$reserves['bbs_start_date'] && $today<=$reserves['bbs_end_date']){
					$reserves['autoemoney_photo']	= $reserves['emoneyBbs_limit'];
					$reserves['autoemoney_review']	= $reserves['emoneyBbs_limit'];

					$reserves['autopoint_photo']	= $reserves['pointBbs_limit'];
					$reserves['autopoint_review']	= $reserves['pointBbs_limit'];
				}
			}
			**/
			$this->template->assign('reserves',$reserves);
			$this->session->unset_userdata('sess_order');//비회원주문번호세션제거
		}

		$sql['whereis']	= ' and id= "'.$boardid.'" ';
		$sql['select']		= ' * ';
		$this->manager = $this->Boardmanager->managerdataidck($sql);//게시판정보
		getboardicon();//icon setting
		$this->template->assign('manager',$this->manager);
		$this->template->assign('designMode',false);
		$this->write('goods');
	}

	public function goods_display_all(){

		$display_seq = (int) $_GET['display_seq'];

		// 디스플레이 설정 데이터
		$query  = $this->db->query("select * from fm_design_display where display_seq = ?",$display_seq);
		$display = $query->row_array();

		$this->template->assign('title',$display['title']);
		$this->template->assign('perpage',20);
		$this->template->assign('display_seq',$display_seq);
		$this->print_layout($this->template_path());
	}

	/* 상품 재입고알림 신청화면 */
	public function restock_notify_apply(){
		$this->load->model('membermodel');

		secure_vulnerability('goods', 'no', $_GET['goods_seq']);
		$no = (int) $_GET['goods_seq'];
		if (!$no) pageBack('잘못된 접근입니다.');//취약점 보안강화
		$goods = $this->goodsmodel->get_goods($no);
		if (!$goods) pageBack('잘못된 접근입니다.');//취약점 보안강화
		$memberData = $this->membermodel->get_member_data($this->userInfo['member_seq']);
		$options = $this->goodsmodel->get_goods_option($no);
		$suboptions = $this->goodsmodel->get_goods_suboption($no);
		$inputs = $this->goodsmodel->get_goods_input($no);

		if(isset($options[0]['option_divide_title'])) $goods['option_divide_title'] = $options[0]['option_divide_title'];
		if(isset($options[0]['divide_newtype'])) $goods['divide_newtype'] = $options[0]['divide_newtype'];

		$this->template->assign(array(
			'no'			=> $no,
			'goods'			=> $goods,
			'memberData'	=> $memberData,
			'options'	=> $options,
			'suboptions'	=> $suboptions,
			'inputs'	=> $inputs,
		));
		$this->template->define('tpl',$this->template_path());
		$this->template->print_('tpl');
	}

	//	브랜드 목록
	public function brand_list(){
		$this->load->model('brandmodel');

		$sc = $_GET;

		$sqlFromClause = " from fm_brand as a
			left join fm_brand_link as g on g.category_code = a.category_code
			left join fm_provider_charge as c on c.category_code = a.category_code
			left join fm_provider as d on d.provider_seq = c.provider_seq
		 ";
		$sqlWhereClause = " where a.category_code!='' and a.hide='0' ";

		if(!empty($sc['so_category']))
		{
			$sqlFromClause .= " inner join fm_category_link so_category on so_category.goods_seq = g.goods_seq";
			$sqlWhereClause .= " and so_category.category_code in ('".implode("','",$sc['so_category'])."')";
		}

		if(!empty($sc['so_brand']))
		{
			$sqlFromClause .= " inner join fm_brand_link so_brand on so_brand.goods_seq = g.goods_seq";
			$sqlWhereClause .= " and so_brand.category_code in ('".implode("','",$sc['so_brand'])."')";
		}

		if(!empty($sc['so_option1']))
		{
			$sqlFromClause .= " inner join fm_goods_option so_option_1 on so_option_1.goods_seq=g.goods_seq";
			$sqlWhereClause .= " and so_option_1.option1 in ('".implode("','",$sc['so_option1'])."')";
		}

		if(!empty($sc['so_option2']))
		{
			$sqlFromClause .= " inner join fm_goods_option so_option_2 on so_option_2.goods_seq=g.goods_seq";
			$sqlWhereClause .= " and so_option_2.option2 in ('".implode("','",$sc['so_option2'])."')";
		}

		if(!empty($sc['so_rate']))
		{
			$sqlFromClause .= " inner join fm_goods_option so_rate on (so_rate.goods_seq=g.goods_seq and so_rate.default_option='y')";
			$tmpWheres = array();
			foreach($sc['so_rate'] as $rate){
				$tmpWheres[] = "round(100-so_rate.price/so_rate.consumer_price*100) between '{$rate}' and '".($rate+9)."'";
			}
			$sqlWhereClause .= " and (".implode(" or ", $tmpWheres).")";
		}


		$sqlOrderbyClause = "order by best asc, a.title asc";

		if(!empty($sc['brand_prefix_group']) || !empty($sc['brand_prefix'])){
			if($sc['brand_prefix_group']=='alpha'){
				if($sc['brand_prefix']=='123'){
					$sqlWhereClause .= " and (
						substring(a.title,1,1) in ('0','1','2','3','4','5','6','7','8','9')
						or
						substring(a.title_eng,1,1) in ('0','1','2','3','4','5','6','7','8','9')
					)
					";
				}elseif($sc['brand_prefix']){
					$prefix = strtolower(substr($sc['brand_prefix'],0,1));
					$sqlWhereClause .= " and substring(a.title_eng,1,1) = '{$sc['brand_prefix']}'";
				}else{
					$prefix = strtolower(substr($sc['brand_prefix'],0,1));
					$sqlWhereClause .= " and substring(a.title_eng,1,1) in ('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z')";
				}
				$sqlOrderbyClause = "order by best asc, a.title_eng asc";
			}
			if($sc['brand_prefix_group']=='korean'){
				if($sc['brand_prefix']){
					$arr = array(
						'ㄱ' => array('가','나'),
						'ㄴ' => array('나','다'),
						'ㄷ' => array('다','라'),
						'ㄹ' => array('라','마'),
						'ㅁ' => array('마','바'),
						'ㅂ' => array('바','사'),
						'ㅅ' => array('사','아'),
						'ㅇ' => array('아','자'),
						'ㅈ' => array('자','차'),
						'ㅊ' => array('차','카'),
						'ㅋ' => array('카','타'),
						'ㅌ' => array('타','파'),
						'ㅍ' => array('파','하'),
						'ㅎ' => array('하','힣')
					);
					$prefix = $arr[$sc['brand_prefix']];

					$sqlWhereClause .= " and a.title >= '{$prefix[0]}'";
					$sqlWhereClause .= " and a.title < '{$prefix[1]}'";
				}else{
					$sqlWhereClause .= " and a.title >= '가'";
					$sqlWhereClause .= " and a.title <= '힣'";
				}
			}
		}

		$query = $this->db->query("select a.*,d.provider_name {$sqlFromClause} {$sqlWhereClause}
		group by a.category_code
		{$sqlOrderbyClause}
		");
		$list = $query->result_array();

		$codeUrlQueryVals = $_GET;
		unset($codeUrlQueryVals['code']);
		unset($codeUrlQueryVals['brand_prefix']);
		unset($codeUrlQueryVals['brand_prefix_group']);
		$codeUrlQuerystring = getLinkFilter('',array_keys($codeUrlQueryVals));

		$this->template->assign(array(
			'list'					=> $list,
			'codeUrlQuerystring'	=> $codeUrlQuerystring
		));
		$this->print_layout($this->template_path());
	}

	//개인결제
	public function personal()
	{
		$this->load->model('goodsdisplay');
		$this->load->model('categorymodel');

		$code = isset($_GET['code']) ? $_GET['code'] : '';
		$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

		/* 카테고리 정보 */
		$categoryData = $this->categorymodel->get_category_data($code);

		$code = $categoryData['category_code'];

		$childCategoryData = $this->categorymodel->get_list($code,array(
			"hide = '0'",
			"level >= 2"
		));
		if(strlen($code)>4 && !$childCategoryData){
			$childCategoryData = $this->categorymodel->get_list(substr($code,0,strlen($code)-4),array(
				"hide = '0'",
				"level >= 2"
			));
		}

		/**
		 * list setting
		**/
		$sc=array();
		$sc['sort']				= $sort ? $sort : $list_default_sort;
		$sc['page']				= (!empty($_GET['page'])) ?		intval($_GET['page']):'1';
		$sc['search_text']		= (!empty($_GET['search_text']))?str_replace(array('"',"'"),"",$_GET['search_text']):'';
		$sc['category']			= $code;
		$sc['perpage']			= $_GET['perpage'] ? $_GET['perpage'] : 10;
		$sc['image_size']		= $categoryData['list_image_size'];
		$sc['list_style']		= $_GET['display_style'] ? $_GET['display_style'] : '';
		$sc['list_goods_status']= $categoryData['list_goods_status'];

		if($_GET['so_brand'])	$sc['so_brand']		= $_GET['so_brand'];
		if($_GET['so_option1'])	$sc['so_option1']	= $_GET['so_option1'];
		if($_GET['so_option2'])	$sc['so_option2']	= $_GET['so_option2'];


		/* 쇼핑몰 타이틀 */
		if($this->config_basic['shopCategoryTitleTag'] && $categoryData['title']){
			$title = str_replace("{카테고리명}",$categoryData['title'],$this->config_basic['shopCategoryTitleTag']);
			$this->template->assign(array('shopTitle'=>$title));
		}

		$str_where_order = " AND regist_date >= '".date('Y-m-d',strtotime("-5 day"))." 00:00:00'";

		$key = get_shop_key();
		$query = "
				select * from (
				SELECT title, order_user_name, total_price, order_seq, enuri, member_seq, order_email, order_phone, order_cellphone, person_seq,
					regist_date,
					(SELECT userid FROM fm_member WHERE member_seq=pr.member_seq) userid,
					(SELECT AES_DECRYPT(UNHEX(email), '{$key}') as email FROM fm_member WHERE member_seq=pr.member_seq) mbinfo_email,
					(SELECT group_name FROM fm_member m,fm_member_group g WHERE m.group_seq=g.group_seq and m.member_seq=pr.member_seq) group_name,
					(select goods_name from fm_goods where goods_seq
						in (select goods_seq from fm_person_cart where person_seq = pr.person_seq) limit 1) goods_name,
					(select count(goods_seq) from fm_person_cart where person_seq = pr.person_seq) item_cnt
				FROM fm_person pr where order_seq is null ".$str_where_order.") t ".$str_where. "
		";

		$list = select_page($sc['perpage'],$sc['page'],10,$query,'');
		$list['page']['querystring'] = get_args_list();
		$list['search_yn'] = $search_yn;

		$this->template->assign($list);

		$this->template->assign(array(
			'categoryCode'			=> $code,
			'categoryTitle'			=> $categoryData['title'],
			'categoryData'			=> $categoryData,
			'childCategoryData'		=> $childCategoryData,
		));
		$this->getboardcatalogcode = $code;//상품후기 : 게시판추가시 이용됨


		/**
		 * display
		**/
		//빅데이터를 위해 최근 상품을 기준으로 한다
		$this->bigdataGoodsSeq = $list['record'][0]['goods_seq'];
		$sc['list_style'] = "person";
		$display_key = $this->goodsdisplay->make_display_key();
		$this->goodsdisplay->set('display_key',$display_key);
		$this->goodsdisplay->set('style',$sc['list_style'] ? $sc['list_style'] : $categoryData['list_style']);
		$this->goodsdisplay->set('count_w',$categoryData['list_count_w']);
		$this->goodsdisplay->set('count_h',$categoryData['list_count_h']);
		$this->goodsdisplay->set('image_decorations',$categoryData['list_image_decorations']);
		$this->goodsdisplay->set('image_size',$categoryData['list_image_size']);
		$this->goodsdisplay->set('text_align',$categoryData['list_text_align']);
		$this->goodsdisplay->set('info_settings',$categoryData['list_info_settings']);
		$this->goodsdisplay->set('displayTabsList',array($list));
		$this->goodsdisplay->set('displayGoodsList',$list['record']);
		$goodsDisplayHTML = "<div id='{$display_key}' class='designPersonalGoodsDisplay' designElement='personalGoodsDisplay'>";
		$goodsDisplayHTML .= $this->goodsdisplay->print_(true);
		$goodsDisplayHTML .= "</div>";

		unset($_GET['sort']);
		unset($_GET['perpage']);
		$sortUrlQuerystring = getLinkFilter('',array_keys($_GET));

		$this->template->assign(array(
			'goodsDisplayHTML'		=> $goodsDisplayHTML,
			'sortUrlQuerystring'	=> $sortUrlQuerystring,
			'sort'					=> $sc['sort'],
			'sc'					=> $sc,
			'orders'				=> $this->goodsdisplay->orders,
		));


		$this->print_layout($this->template_path());

	}



	public function sizeguide()
	{
		$this->load->model('categorymodel');
		$code = isset($_GET['code']) ? $_GET['code'] : '';
		/* 카테고리 정보 */
		$categoryData = $this->categorymodel->get_category_data($code);

		$tabtype = array();
		$type = ($_GET['code'])?1:0;
		$this->template->assign('code',$type);
		$this->template->define(array('tpl'=>$this->skin.'/goods/_sizeguide.html'));
		$this->template->print_('tpl');
	}

	public function display_related_goods(){

		$goods_seq	 = (int) $_GET['goods_seq'];

		// 기존 소스
		$goods = $this->goodsmodel->get_goods($goods_seq);
		$loop = get_related_goods($goods_seq, $goods['relation_type'], ($goods['relation_count_w']*$goods['relation_count_h']));
		$this->template->assign('goods',$goods);
		$this->template->assign('loop',$loop);

		// 신규 소스
		$this->load->model('goodsdisplay');
		$sql = "select * from fm_design_display where kind = 'relation'";
		$query = $this->db->query($sql);
		$display = $query->row_array();
		if(!$display){
			$this->goodsmodel->get_goods_relation_display_seq();
			$sql = "select * from fm_design_display where kind = 'relation'";
			$query = $this->db->query($sql);
			$display = $query->row_array();
		}

		if($goods['relation_count_w']==0 && $goods['relation_count_h']==0){
			$display['count_w'] = 4;
			$display['count_h'] = 1;
		}else{
			$display['count_w'] = $goods['relation_count_w'];
			$display['count_h'] = $goods['relation_count_h'];
		}

		// 페이머스에서는 가로 3개만 노출
		if(($this->fammerceMode || $this->storefammerceMode) && $display['count_w'] > 3){
			$display['count_w'] = 3;
		}

		$display['image_size'] = $goods['relation_image_size'];
		$display['auto_criteria'] = $goods['relation_criteria'];

		if($goods['relation_type']=='AUTO'){
			$sc = $this->goodsdisplay->search_condition($display['auto_criteria'], array(),'relation');
			if(!$sc['category'] && $sc['selectGoodsRelationCategory']) {
				$category_code = $this->goodsmodel->get_goods_category_default($goods_seq);
				$category_code = $category_code['category_code'];
				$sc['category'] = $category_code;
			}

			$sc['sort']		= $sc['auto_order'];
			$sc['display_seq']		= $display['display_seq'];
			$sc['display_tab_index']= 0;
			$sc['page']				= 1;
			$sc['perpage']			= $display['count_w']*$display['count_h'];
			$sc['image_size']		= $display['image_size'];
			$sc['goods_seq_exclude']= $goods['goods_seq'];

			if($this->goodsdisplay->info_settings_have_eventprice($display['info_settings'])){
				$sc['join_event']	= true;
			}

			$list = $this->goodsmodel->goods_list($sc);
		}else{
			$sc['relation'] = $goods['goods_seq'];
			$list = $this->goodsmodel->goods_list($sc);
		}

		if($list['record']){
			$display_key = $this->goodsdisplay->make_display_key();
			$this->goodsdisplay->set('display_key',$display_key);
//			$this->goodsdisplay->set('title',$display['title']);
			$this->goodsdisplay->set('style',$display['style']);
			$this->goodsdisplay->set('count_w',$display['count_w']);
			$this->goodsdisplay->set('count_h',$display['count_h']);
			$this->goodsdisplay->set('image_decorations',$display['image_decorations']);
			$this->goodsdisplay->set('image_size',$display['image_size']);
			$this->goodsdisplay->set('text_align',$display['text_align']);
			$this->goodsdisplay->set('info_settings',$display['info_settings']);
			$this->goodsdisplay->set('display_key',$display_key);
			$this->goodsdisplay->set('displayGoodsList',$list['record']);
			$this->goodsdisplay->set('displayTabsList',array($list));
			$this->goodsdisplay->set('tab_design_type',$display['tab_design_type']);

			//슬라이딩 스타일 기능 추가 2015-10-13 jhr
			$remain = '';
			if($display['style']=='rolling_h' && $display['h_rolling_type'] != 'moveSlides'){
				$remain_cnt = $display['count_w']-(count($list['record'])%$display['count_w']);
				if($remain_cnt <= 0 || $remain_cnt >= $display['count_w']) $remain_cnt = 0;
				for($r_i=0;$r_i<$remain_cnt;$r_i++) $remain .= '<div class="slide">&nbsp;</div>';
			}
			$this->goodsdisplay->set('remain',$remain);
			$this->goodsdisplay->set('h_rolling_type',$display['h_rolling_type']);

			$goodsRelationDisplayHTML = "<div id='{$display_key}' class='designGoodsRelationDisplay' designElement='goodsRelationDisplay' displaySeq='{$display['display_seq']}'>";
			$goodsRelationDisplayHTML .= $this->goodsdisplay->print_(true);
			$goodsRelationDisplayHTML .= "</div>";

			$layout_config = layout_config_autoload($this->skin,'basic');

			$this->template->assign(array('layout_config'=>$layout_config['basic'],'record'=>$list['record'],'goodsRelationDisplayHTML'=>$goodsRelationDisplayHTML));
		}

		$this->template->define(array('tpl'=>$this->skin.'/goods/_display_related_goods.html'));
		$this->template->print_('tpl');

	}

	public function display_goods_images(){

		$goods_seq				= (int) $_GET['goods_seq'];
		$image_slide_type = $_GET['image_slide_type'];

		$images = $this->goodsmodel->get_goods_image($goods_seq);
		$goods_info	= $this->goodsmodel->get_goods($goods_seq);

		// 성인인증 이미지 변경 :: 2015-03-13 lwh
		$adult_auth	= $this->session->userdata('auth_intro');
		if($adult_auth['auth_intro_yn'] == '' && $goods_info['adult_goods'] == 'Y' && (!$this->managerInfo && !$this->providerInfo)){
			foreach($images as $k => $arr_data){
				foreach($arr_data as $z => $val)
				$images[$k][$z]['image']	= "/data/skin/".$this->skin."/images/common/19mark.jpg";
			}
		}

		$this->template->assign(array(
			'images' => $images,
			'goods_seq' => $goods_seq
		));

		$this->template->define(array('tpl'=>$this->skin.'/goods/_display_goods_images_'.$image_slide_type.'.html'));
		$this->template->print_('tpl');

	}

	//지역별
	public function location(){
		$this->load->model('goodsdisplay');
		$this->load->model('locationmodel');

		$code = isset($_GET['code']) ? $_GET['code'] : '';
		$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
		$get_display_style = $_GET['display_style'];

		if($_GET['designMode'] && !$code){
			$query = $this->db->query("select location_code from fm_location where level>1 order by location_code asc limit 1");
			$tmp = $query->row_array();
			$code = $_GET['code'] = $tmp['location_code'];
		}

		/*모바일 페이징 limit 조정 코드*/
		if(isset($_GET['m_code'])) {
			$m_code = $_GET['m_code'];
			$sc_top = $_GET['sc_top'];
		}

		/* 브랜드 정보 */
		$categoryData = $this->locationmodel->get_location_data($code);

		if	($categoryData) {
			if	($categoryData['image_decoration_type'] == 'favorite')
				$categoryData['list_image_decorations'] = $categoryData['image_decoration_favorite'];
			if	($categoryData['goods_decoration_type'] == 'favorite')
				$categoryData['list_info_settings']		= $categoryData['goods_decoration_favorite'];

			$categoryData['list_style'] = $get_display_style ? $get_display_style : $categoryData['list_style'];

			if	($categoryData['list_style'] == 'lattice_b') {
				if	($categoryData['list_image_size_lattice_b'])
					$categoryData['list_image_size'] = $categoryData['list_image_size_lattice_b'];
				if	($categoryData['list_count_w_lattice_b'])
					$categoryData['list_count_w'] = $categoryData['list_count_w_lattice_b'];
				if	($categoryData['list_count_h_lattice_b'])
					$categoryData['list_count_h'] = $categoryData['list_count_h_lattice_b'];
			}

			if	($categoryData['list_style'] == 'list') {
				if	($categoryData['list_image_size_list'])
					$categoryData['list_image_size'] = $categoryData['list_image_size_list'];
				$categoryData['list_count_w'] = 1;
				if	($categoryData['list_count_h_list'])
					$categoryData['list_count_h'] = $categoryData['list_count_h_list'];
			}
		}

		if($this->realMobileSkinVersion > 2 && $this->mobileMode && $categoryData['m_list_use'] == 'y'){
			$categoryData['list_default_sort'] = $categoryData['m_list_default_sort'];
			$categoryData['list_style'] = $categoryData['m_list_style'];
			$categoryData['list_count_w'] = $categoryData['m_list_style'] == 'lattice_responsible' && $categoryData['m_list_count_r'] ? $categoryData['m_list_count_r'] : $categoryData['m_list_count_w'];
			$categoryData['list_count_h'] = $categoryData['m_list_style'] == 'lattice_responsible' && $categoryData['m_list_count_r'] ? 1 : $categoryData['m_list_count_h'];
			$categoryData['list_paging_use'] = $categoryData['m_list_paging_use'];
			$categoryData['list_image_size'] = $categoryData['m_list_image_size'];
			$categoryData['list_text_align'] = $categoryData['m_list_text_align'];
			$categoryData['list_image_decorations'] = $categoryData['m_list_image_decorations'];
			$categoryData['list_info_settings'] = $categoryData['m_list_info_settings'];
			$categoryData['list_goods_status'] = $categoryData['m_list_goods_status'];
			if	($categoryData['m_image_decoration_type'] == 'favorite')
				$categoryData['list_image_decorations'] = $categoryData['m_image_decoration_favorite'];
			if	($categoryData['m_goods_decoration_type'] == 'favorite')
				$categoryData['list_info_settings']		= $categoryData['m_goods_decoration_favorite'];
		}

		$childCategoryData = $this->locationmodel->get_list($code,array(
			"hide = '0'",
			"level >= 2"
		));
		if(strlen($code)>4 && !$childCategoryData){
			$childCategoryData = $this->locationmodel->get_list(substr($code,0,strlen($code)-4),array(
				"hide = '0'",
				"level >= 2"
			));
		}

		/* 쇼핑몰 타이틀 */
		if($this->config_basic['shopCategoryTitleTag'] && $categoryData['title']){
			$title = str_replace("{카테고리명}",$categoryData['title'],$this->config_basic['shopCategoryTitleTag']);
			$this->template->assign(array('shopTitle'=>$title));
		}

		$perpage = $_GET['perpage'] ? $_GET['perpage'] : $categoryData['list_count_w'] * $categoryData['list_count_h'];
		$perpage = $perpage ? $perpage : 10;
		$list_default_sort = $categoryData['list_default_sort'] ? $categoryData['list_default_sort'] : 'popular';

		$perpage_min = $categoryData['list_count_w']*$categoryData['list_count_h'];
		if($perpage != $categoryData['list_count_w']*$categoryData['list_count_h']){
			$categoryData['list_count_h'] = ceil($perpage/$categoryData['list_count_w']);
		}

		/* 동영상/플래시매직 치환 */
		$categoryData['top_html'] = showdesignEditor($categoryData['top_html']);

		/**
		 * list setting
		**/
		$sc=array();
		$sc['sort']				= $sort ? $sort : $list_default_sort;
		$sc['page']				= (!empty($_GET['page'])) ?		intval($_GET['page']):'1';
		if($categoryData['list_paging_use']=='n'){
			$sc['limit']			= $perpage;
		}else{
			$sc['perpage']			= $perpage;
		}
		$sc['image_size']		= $categoryData['list_image_size'];
		$sc['list_style']		= $_GET['display_style'] ? $_GET['display_style'] : $categoryData['list_style'];

		if( $this->mobileMode || $this->storemobileMode ){
			if(!preg_match("/^mobile/",$sc['list_style']))
				$sc['list_style']= "mobile_".$sc['list_style'];
		}else{
			$sc['list_style']	= preg_replace("/^mobile_/","",$sc['list_style']);
		}

		$sc['list_goods_status']= $categoryData['list_goods_status'];

		$sc['category_code']	= !empty($_GET['category_code'])	? $_GET['category_code'] : '';
		$sc['brands']			= !empty($_GET['brands'])		? $_GET['brands'] : array();
		$sc['brand_code']		= !empty($_GET['brand_code'])	? $_GET['brand_code'] : '';
		$sc['location_code']	= $code;
		$sc['search_text']		= !empty($_GET['search_text'])	? $_GET['search_text'] : '';
		$sc['old_search_text']	= !empty($_GET['old_search_text'])	? $_GET['old_search_text'] : '';

		if( !empty($_GET['start_price']) && !empty($_GET['end_price']) && $_GET['end_price'] < $_GET['start_price'] ) {//상품가격 검색시 시작가격과 마지막가격 비교
			$start_price		=$_GET['start_price'];
			$end_price			=$_GET['end_price'];
			$_GET['end_price']		=$start_price;
			$_GET['start_price']	=$end_price;
		}
		$sc['start_price']		= !empty($_GET['start_price'])	? $_GET['start_price'] : '';
		$sc['end_price']		= !empty($_GET['end_price'])	? $_GET['end_price'] : '';
		$sc['color']			= !empty($_GET['color'])		? $_GET['color'] : '';

		if(isset($m_code) && $m_code != '') {
			$sc['m_code'] = $m_code;
		}

		$list	= $this->goodsmodel->goods_list($sc);
		$this->template->assign($list);

		if($categoryData['list_paging_use']=='n'){
			$this->template->assign(array('page'=>array('totalcount'=>count($list['record']))));
		}

		//메타테그 치환용 정보
		$add_meta_info['location']		= $categoryData['title'];

		$this->template->assign(array(
			'categoryCode'			=> $code,
			'categoryTitle'			=> $categoryData['title'],
			'categoryData'			=> $categoryData,
			'childCategoryData'		=> $childCategoryData,
			'add_meta_info'			=> $add_meta_info
		));
		$this->getboardcatalogcode = $code;//상품후기 : 게시판추가시 이용됨

		/**
		 * display
		**/
		$display_key = $this->goodsdisplay->make_display_key();
		$this->goodsdisplay->set('display_key',$display_key);
		$this->goodsdisplay->set('style',$sc['list_style']);
		$this->goodsdisplay->set('count_w',$categoryData['list_count_w']);
		$this->goodsdisplay->set('count_w_lattice_b',$categoryData['list_count_w_lattice_b']);
		$this->goodsdisplay->set('count_h',$categoryData['list_count_h']);
		$this->goodsdisplay->set('image_decorations',$categoryData['list_image_decorations']);
		$this->goodsdisplay->set('image_size',$categoryData['list_image_size']);
		$this->goodsdisplay->set('text_align',$categoryData['list_text_align']);
		$this->goodsdisplay->set('info_settings',$categoryData['list_info_settings']);
		$this->goodsdisplay->set('displayTabsList',array($list));
		$this->goodsdisplay->set('displayGoodsList',$list['record']);
		$this->goodsdisplay->set('mobile_h',$categoryData['m_list_mobile_h']);
		$this->goodsdisplay->set('m_list_use',$categoryData['m_list_use']);
		$this->goodsdisplay->set('img_optimize',$categoryData['img_opt_lattice_a']);
		$this->goodsdisplay->set('img_opt_lattice_a',$categoryData['img_opt_lattice_a']);
		$this->goodsdisplay->set('img_padding',$categoryData['img_padding_lattice_a']);
		$this->goodsdisplay->set('count_h_lattice_b',$categoryData['list_count_h_lattice_b']);
		$this->goodsdisplay->set('count_h_list',$categoryData['list_count_h_list']);

		if(strstr($categoryData['list_info_settings'],"fblike") && ( !$this->__APP_LIKE_TYPE__ || $this->__APP_LIKE_TYPE__ == 'API') ) {//라이크포함시
			$goodsDisplayHTML = $this->is_file_facebook_tag;
			define('FACEBOOK_TAG_PRINTED','YES');
			$goodsDisplayHTML .= "<div id='{$display_key}' class='designLocationGoodsDisplay' designElement='locationGoodsDisplay'>";
		}else{
			$goodsDisplayHTML = "<div id='{$display_key}' class='designLocationGoodsDisplay' designElement='locationGoodsDisplay'>";
		}
		$goodsDisplayHTML .= $this->goodsdisplay->print_(true);
		$goodsDisplayHTML .= "</div>";

		$tmpGET = $_GET;
		unset($tmpGET['sort']);
		unset($tmpGET['page']);
		unset($tmpGET['m_code']);
		unset($tmpGET['sc_top']);
		$sortUrlQuerystring = getLinkFilter('',array_keys($tmpGET));

		$this->template->assign(array(
			'goodsDisplayHTML'		=> $goodsDisplayHTML,
			'sortUrlQuerystring'	=> $sortUrlQuerystring,
			'sort'					=> $sc['sort'],
			'sc'					=> $sc,
			'orders'				=> $this->goodsdisplay->orders,
			'perpage_min'			=> $perpage_min,
			'list_style'			=> $sc['list_style'],
			'sc_top'				=> $sc_top
		));

		if($_GET['ajax']){
			echo $goodsDisplayHTML;
		}else{
			$this->print_layout($this->template_path());
		}

		//GA통계
		if($this->ga_auth_commerce_plus){
			$ga_params['item'] = $list['record'];
			$ga_params['page'] = "지역:".$categoryData['title'];
			echo google_analytics($ga_params,"list_count");
		}

	}

	public function recently(){
		$result = array();
		$today_view = $_COOKIE['today_view'];
		if( $today_view ) {
			$today_view = unserialize($today_view);
			krsort($today_view);
			$result = $this->goodsmodel->get_goods_list($today_view,'thumbScroll');

			//--> sale library 할인 적용 사전값 전달
			$cfg_reserve	= ($this->reserves) ? $this->reserves : config_load('reserve');
			$applypage						= 'wish';
			$param['cal_type']				= 'list';
			$param['total_price']			= 0;
			$param['member_seq']			= $this->userInfo['member_seq'];
			$param['group_seq']				= $this->userInfo['group_seq'];
			$this->sale->set_init($param);
			$this->sale->preload_set_config($applypage);
			//<-- //sale library 할인 적용 사전값 전달

			// 로그인 후 비회원 가격대체문구 노출 오류 수정 leewh 2014-12-05
			$adult_auth	= $this->session->userdata('auth_intro');
			if ($result) {
				foreach ($result as $key => $goods) {

					//----> sale library 적용
					unset($param, $sales);
					$param['consumer_price']		= $goods['consumer_price'];
					$param['total_price']			= $goods['price'];
					$param['price']					= $goods['price'];
					$param['ea']					= 1;
					$param['category_code']			= $goods['r_category'];
					$param['goods_seq']				= $goods['goods_seq'];
					$param['goods']					= $goods;
					$this->sale->set_init($param);
					$sales	= $this->sale->calculate_sale_price($applypage);

					$goods['org_price']				= ($goods['consumer_price']) ? $goods['consumer_price'] : $goods['price'];
					$goods['sale_price']			= $sales['result_price'];
					// 포인트
					$goods['point']		= (int) $this->goodsmodel->get_point_with_policy($sales['result_price']) + $sales['tot_point'];
					// 마일리지
					$goods['reserve']	= (int) $this->goodsmodel->get_reserve_with_policy($goods['reserve_policy'],$sales['result_price'],$cfg_reserve['default_reserve_percent'],$goods['reserve_rate'],$goods['reserve_unit'],$goods['reserve']) + $sales['tot_reserve'];

					$this->sale->reset_init();
					unset($sales);
					//<---- sale library 적용

					if	($goods['sale_price'])	$goods['price']	= $goods['sale_price'];
					$goods['string_price'] = get_string_price($goods);
					$goods['string_price_use']	= 0;
					if	($goods['string_price'] != '')	$goods['string_price_use']	= 1;


					$record[$key]	= $goods;

					// 성인인증 이미지 변경 :: 2015-03-13 lwh
					if($adult_auth['auth_intro_yn'] == '' && $value['adult_goods'] == 'Y' && (!$this->managerInfo && !$this->providerInfo)){
						$result[$key]['image']	= "/data/skin/".$this->skin."/images/common/19mark.jpg";
					}
				}
			}
		}

		$this->template->assign('record',$record);
		$this->print_layout($this->template_path());
	}


	public function recently_option(){

		$cfg_order = config_load('order');

		$goods_seq = (int) $_GET['no'];
		$this->load->model('wishmodel');
		$this->load->model('categorymodel');

		$categorys = $this->goodsmodel->get_goods_category($goods_seq);
		if($categorys) foreach($categorys as $key => $data_category){
			if( $data_category['link'] == 1 ){
				$category_code = $this->categorymodel->split_category($data_category['category_code']);
			}
		}

		$goods = $this->goodsmodel->get_goods($goods_seq);
		$suboptions = $this->goodsmodel->get_goods_suboption($goods_seq);

		// 가격대체문구 여부 설정 :: 2015-07-22 lwh
		$tmp_string_price = get_string_price($goods, $this->userInfo);
		if($tmp_string_price)	$goods['string_price_use'] = true;
		else					$goods['string_price_use'] = false;

		// 배송정보 가져오기
		$delivery = $this->goodsmodel->get_goods_delivery($goods);
		$shipping_policy = $this->goodsmodel->get_shipping_policy($goods);
		if($delivery['price'] == 0){
			$shipping_policy['summary']['delivery'] = "무료";
		}

		$this->template->assign(array('shipping_policy'=>$shipping_policy));

		$images = $this->goodsmodel->get_goods_image($goods_seq);
		$goods['image'] = $images[1]['thumbView']['image'];

		$inputs = $this->goodsmodel->get_goods_input($goods_seq,array('option_view'=>'Y'));
		$options = $this->goodsmodel->get_goods_option($goods_seq,array('option_view'=>'Y'));
		foreach($options as $k => $opt){
			/* 대표가격 */
			if($opt['default_option'] == 'y'){
				$goods['price'] 			= $opt['price'];
				$goods['sale_price'] 		= $opt['price'];
				$goods['consumer_price'] 	= $opt['consumer_price'];
				$goods['reserve'] 			= $opt['reserve'];
				if( $opt['option_title'] ) $goods['option_divide_title'] = explode(',',$opt['option_title']);
			}
			$options[$k]['opt_join'] = implode('/',$optJoin);

			// 재고 체크
			$opt['chk_stock'] = check_stock_option($goods['goods_seq'],$opt['option1'],$opt['option2'],$opt['option3'],$opt['option4'],$opt['option5'],$opt['ea'],$cfg_order);
			if( $opt['chk_stock'] ) $runout = false;
			$options[$k]['chk_stock'] = $opt['chk_stock'];
		}

		if($suboptions) foreach($suboptions as $key => $tmp){
			foreach($tmp as $k => $opt){
				$opt['chk_stock'] = check_stock_suboption($goods['goods_seq'],$opt['suboption_title'],$opt['suboption'],$ea,$cfg_order);
				if( $opt['chk_stock'] ){
					$sub_runout = true;
				}

				$suboptions[$key][$k] = $opt;
			}
		}

// 여기서부터 추가
		if	(isset($options[0]['option_divide_title']))
			$goods['option_divide_title']	= $options[0]['option_divide_title'];
		if	(isset($options[0]['divide_newtype']))
			$goods['divide_newtype']		= $options[0]['divide_newtype'];

		$foption		= $this->goodsmodel->get_first_options($goods, $options);
		if	($goods['option_view_type'] == 'join'){
			$option_data[0]['title']		= $options[0]['option_title'];
			$option_data[0]['newtype']		= $goods['divide_newtype'][0];
			$option_data[0]['options']		= $foption;
			$option_depth					= 1;
		}else{
			if	($goods['option_divide_title'])foreach($goods['option_divide_title'] as $k => $tit){
				$option_data[$k]['title']		= $tit;
				$option_data[$k]['newtype']		= $goods['divide_newtype'][$k];
				if	($k == 0)	$option_data[$k]['options']	= $foption;
				$option_depth++;
			}
		}

		$this->template->assign(array('option_depth'	=> $option_depth));
		$this->template->assign(array('option_data'		=> $option_data));
// 여기까지 추가

		$file = str_replace('recently_option','_recently_option',$this->template_path());
		$this->template->assign(array('options'=>$options));
		$this->template->assign(array('goods'=>$goods));
		$this->template->assign(array('suboptions'=>$suboptions));
		$this->template->assign(array('inputs'=>$inputs));
		$this->template->define(array('LAYOUT'=>$file));
		$this->template->print_('LAYOUT');
	}

	public function design_display_tab(){
		$display_seq		= $_POST['display_seq'];
		$tab_index			= $_POST['tab_index'];
		$_GET['page']		= !empty($_POST['page']) ? $_POST['page'] : null;
		$perpage			= !empty($_POST['perpage']) ? $_POST['perpage'] : null;
		$display_ajax_call	= $_POST['display_ajax_call'];
		$display_paging		= $_POST['display_paging'];
		$hash_paging		= $_POST['hash_paging'];

		$this->designDisplayTabAjaxIdx=$tab_index;

		if($_POST['category']){
			$this->template->include_('showCategoryRecommendDisplay');
			showCategoryRecommendDisplay($_POST['category']);
		}else if($_POST['brand']){
			$this->template->include_('showBrandRecommendDisplay');
			showBrandRecommendDisplay($_POST['brand']);
		}else if($_POST['location']){
			$this->template->include_('showLocationRecommendDisplay');
			showLocationRecommendDisplay($_POST['location']);
		}else {
			$this->template->include_('showDesignDisplay');
			showDesignDisplay($display_seq,$perpage,$display_paging,'cach',$display_ajax_call,$hash_paging);
		}

	}

	/* 티켓상품 위치 서비스 Ajax용 :: 2014-04-02 lwh */
	public function coupon_location_ajax(){
		$goods_seq	= $_POST['goods_seq'];
		$option_seq	= $_POST['option_seq'];
		$width		= $_POST['width'];

		$res	= $this->goodsmodel->get_goods_option($goods_seq);

		if($res)foreach($res as $key => $opt){
			if($opt['option_seq']==$option_seq){
				$opt['opspecial_location'] = get_goods_options_print_array($opt);
				$option			= $opt['option'.$opt['opspecial_location']['address']];
				$address		= $opt['address'] . " " . $opt['addressdetail'];
				$address_street	= $opt['address_street'];
				$biztel			= $opt['biztel'];
			}
		}

		/* 위치 구하기 */
		$this->load->library('SofeeXmlParser');
		$maparr = $this->config_basic;

		$view_name	= $option;
		$addr = urlencode($address);
		//2016-05-03 jhr 네이버맵 api변경
		if	(!$maparr['mapKey'] || $maparr['naverMapKey'] == 'Client'){
			$client_id = $maparr['map_client_id'];
			$client_secret = $maparr['map_client_secret'];
			$ch = curl_init();
			$encoding="utf-8";
			$coord="latlng";
			$output="json";
			$qry_str = "?encoding=".$encoding."&coord=".$coord."&output=".$output."&query=".$addr;
			$headers = array(
				"X-Naver-Client-Id: {$client_id}",
				"X-Naver-Client-Secret: {$client_secret}"
			);
			$url="https://openapi.naver.com/v1/map/geocode";
			curl_setopt($ch, CURLOPT_URL, $url.$qry_str);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$res =curl_exec($ch);
			curl_close($ch);
			$ret = json_decode($res, true);

			if	( $ret['result']['items'][0]['point']['x'] )
				$point = array('x'=>$ret['result']['items'][0]['point']['y'], 'y'=>$ret['result']['items'][0]['point']['x']);
			else
				$point = array('x'=>$ret['result']['items']['point']['y'], 'y'=>$ret['result']['items'][0]['point']['x']);
		}else{
			$xmlParser = new SofeeXmlParser();
			$key = $maparr['mapKey'];
			$url = "http://openapi.map.naver.com/api/geocode.php?key=".$key."&encoding=utf-8&coord=latlng&query=".$addr;
			$xmlParser->parseFile($url);
			$tree = $xmlParser->getTree();

			if($tree['geocode']['item'][0]['point']['x']['value']){
				$point = array('y'=>$tree['geocode']['item'][0]['point']['x']['value'], 'x'=>$tree['geocode']['item'][0]['point']['y']['value']);
			}else{
				$point = array('y'=>$tree['geocode']['item']['point']['x']['value'], 'x'=>$tree['geocode']['item']['point']['y']['value']);
			}
		}

		$this->template->assign(array('option'=>$option));
		$this->template->assign(array('address'=>$address));
		$this->template->assign(array('street'=>$address_street));
		$this->template->assign(array('biztel'=>$biztel));
		$this->template->assign(array('width'=>$width));
		$this->template->assign(array('x_lang'=>$point['x']));
		$this->template->assign(array('y_lang'=>$point['y']));

		$this->template->define(array('LAYOUT'=>$this->template_path()));
		$this->template->print_('LAYOUT');
	}

	//모바일스킨>최근 본상품 삭제
	public function goods_del()
	{
		$goods_seq_ar = $_POST['goods_seq'];

		$today_num = 0;
		$today_view = $_COOKIE['today_view'];
		if( $today_view ) $today_view = unserialize($today_view);
		if( $today_view ) foreach($today_view as $v){
			$today_num++;
			if( count($today_view) > 50 && $today_num == 1 ) continue;
			if( in_array($v,$goods_seq_ar)) continue;
			$data_today_view[] = $v;
		}

		if( $data_today_view ) $data_today_view = serialize($data_today_view);
		setcookie('today_view',$data_today_view,time()+86400,'/');
		$callback = "parent.document.location.reload();";
		//최근 본상품을 삭제하였습니다.
		openDialogAlert(getAlert('et067'),400,140,'parent',$callback);

	}

	/* 우측퀵메뉴 기능개선 선택한 최근본상품 삭제 leewh 2014-06-05 */
	public function goods_recent_del() {
		$goods_seq = (int) $_POST['goods_seq'];
		$msg = "fail";

		$today_view = unserialize($_COOKIE['today_view']);
		$totalcnt = count($today_view);
		if (in_array($goods_seq, $today_view)) {
			$del_key = array_keys($today_view,$goods_seq);
			unset($today_view[$del_key[0]]);
			$tmp_data = array_values($today_view);
			if ($tmp_data) {
				$today_view = serialize($tmp_data);
				setcookie('today_view',$today_view,time()+86400,'/');
			} else {
				setcookie('today_view','',time()-3600,'/');
			}
			$msg="ok";
		}
		if($msg == 'ok' ) $totalcnt = intval($totalcnt-1);
		echo json_encode(array("msg"=>$msg,"totalcnt"=>$totalcnt));
	}

	public function request_getstock_all(){
		if	(!$this->cfg_scm)		$this->cfg_scm		= config_load('scm');
		if	(!$this->cfg_order)		$this->cfg_order		= config_load('order');

		$goods_seq			= (int) trim($_POST['goods_seq']);
		$option_seq		= (int) trim($_POST['option_seq']);

		if(!$goods_seq){
			$goods_seq		= (int) trim($_GET['goods_seq']);
			$option_seq	= (int) trim($_GET['option_seq']);
		}

		if( $option_seq ){
			$arr_option['o.option_seq'] = $option_seq;
		}
		$tmp_options = $this->goodsmodel->get_option_all($goods_seq,$arr_option);

		$arr_title = explode(',',$tmp_options[0]['option_title']);
		$option_count = count($arr_title);

		foreach ($tmp_options as $k => $opt){

			$options[$opt['option_seq']]['option_count']	= $option_count;
			for($oi=1;$oi<6;$oi++){
				$options[$opt['option_seq']]['title'.$oi]	= $arr_title[$oi-1];
				$options[$opt['option_seq']]['option'.$oi]	= $opt['option'.$oi];
			}
			$options[$opt['option_seq']]['packages']		= $opt['packages'];
			$options[$opt['option_seq']]['stock']			= $opt['stock'];
			$options[$opt['option_seq']]['badstock']		= $opt['badstock'];
			$reservation_field = 'reservation'.$this->cfg_order['ableStockStep'];
			$options[$opt['option_seq']]['ablestock']	= $opt['stock'] - $opt[$reservation_field];
			$options[$opt['option_seq']]['supply_price']	= $opt['supply_price'];

			// 재배열
			$options[$opt['option_seq']]['package_yn']		= $opt['package_yn'];
			$options[$opt['option_seq']]['goods_seq']		= $opt['goods_seq'];
			$options[$opt['option_seq']]['goods_code']		= $opt['goods_code'];
			$options[$opt['option_seq']]['option_seq']		= $opt['option_seq'];
			$options[$opt['option_seq']]['option_type']		= $opt['option_type'];
			$options[$opt['option_seq']]['package_count']	= $opt['package_count'];
			$options[$opt['option_seq']]['packages']		= $opt['packages'];
			$options[$opt['option_seq']]['weight']			= $opt['weight'];
			$options[$opt['option_seq']]['option_view']		= $opt['option_view'];
			$options[$opt['option_seq']]['store_name']		= '매장';
			for($in=0;$in < 6; $in++){
				if($opt['package_goods_name'.($in+1)]){
					$options[$opt['option_seq']]['packages'][$in]['package_goods_name'] = $opt['package_goods_name'.($in+1)];
					$options[$opt['option_seq']]['packages'][$in]['package_option'] = $opt['package_option'.($in+1)];
					$options[$opt['option_seq']]['packages'][$in]['package_unit_ea'] = $opt['package_unit_ea'.($in+1)];
				}
			}
			$options[$opt['option_seq']]['store_name']		= $this->config_system['admin_env_name'];

		}

		echo json_encode($options);
	}

	//상품상세
	public function view_preload(){
		$no		= (int) $_GET['no'];
		$result	= $this->goodsmodel->get_goods_view($no);

		//모바일 VER3 스킨에서 상품상세이미지 분할처리
		$mobile_contentscnt = preg_match_all("/<IMG([^>]*)src[\s]*=[\s]*([\"']?)([^>\"']+)[\"']?([^>]*)>/i",$result['assign']['goods']['mobile_contents'], $matches);
		if(count($matches[0])>0){
			$result['assign']['goods']['mobile_contents_images_true'] = true;
			$result['assign']['goods']['mobile_contents_images'] = array();
			foreach($matches[0] as $k=>$v){
				$v = preg_replace("/([\s]*)src([\s]*=)/i"," data-original=",$v);
				$result['assign']['goods']['mobile_contents_images'][] = $v;
			}
		}

		if	($result['assign'])foreach($result['assign'] as $key => $val){
			$this->template->assign(array($key	=> $val));
		}

		$file_path = str_replace('view_preload','_view_detail',$this->template_path());
		$this->template->define(array('tpl'=>$file_path));
		$this->template->print_("tpl");
	}

	// 상품 그룹 자세히 보기 :: 2016-07-20 lwh
	public function shipping_detail_info(){

		$this->tempate_modules();
		$file_path	= $this->template_path();

		$this->load->model('shippingmodel');
		$this->load->model('cartmodel');

		$mode			= ($_GET['mode']) ? $_GET['mode'] : 'goods';
		$goods_seq		= $_GET['goods_seq'];
		$cart_seq		= $_GET['cart_seq'];
		$nation			= $_GET['nation'];
		$grp_seq		= $_GET['grp_seq'];
		$set_seq		= $_GET['set_seq'];
		$store_seq		= $_GET['store_seq'];
		$hop_date		= $_GET['hop_date'];
		$reserve_txt	= $_GET['reserve_txt'];
		$cart_table		= $_GET['cart_table'];	//개인결제/관리자주문의
		$admin_mode		= $_GET['admin_mode'];	//개인결제/관리자주문의 장바구니, 주문서
		$prepay_info	= ($_GET['prepay_info']) ? $_GET['prepay_info'] : 'delivery';
		$direct_store	= ($_GET['direct_store']) ? $_GET['direct_store'] : 'N';
		$ship_gl_arr	= '';
		$ship_gl_list	= '';

		// 상품정보 추출 :: 2016-11-14 lwh
		if	($goods_seq){
			$this->load->model('goodsmodel');
			$goods_info = $this->goodsmodel->get_goods($goods_seq);
		}

		// 장바구니 및 주문페이지에서 필요한 값 추출 :: 2016-07-28 lwh
		if	($mode != 'goods' && $cart_seq){
			if	(!$grp_seq){
				if($cart_table == "person"){
					$this->load->model('personcartmodel');
					$cart_info	= $this->personcartmodel->get_cart($cart_seq);
				}else{
					$cart_info	= $this->cartmodel->get_cart($cart_seq);
				}
				$grp_seq	= $cart_info['shipping_group_seq'];
				$set_seq	= $cart_info['shipping_set_seq'];
				$store_seq	= $cart_info['shipping_store_seq'];
			}
		}

		// 그룹정보 추출
		$grp_info	= $this->shippingmodel->get_shipping_group($grp_seq);

		// 요약 정보 추출
		$ship_summary = $this->shippingmodel->get_shipping_group_summary($grp_seq);

		// 국가목록 추출
		if($direct_store == 'N' && $ship_summary['gl_shipping_yn'] == 'Y'){
			$ship_gl_arr	= $this->shippingmodel->get_gl_shipping($grp_seq);
			if(!$ship_gl_arr){
				$ship_gl_arr= $this->shippingmodel->get_gl_shipping();
				if($nation != 'korea')	$nation = 'global';
			}
			$ship_gl_list	= $this->shippingmodel->split_nation_str($ship_gl_arr);
		}

		if($nation == 'korea' || $nation == 'global'){
			$params		= array('delivery_nation'=>$nation);
			$now_nation	= '대한민국';
			if($nation == 'global')	$now_nation	= '해외국가';
			$sel_gl_str = 'KOREA';
		}else{
			$params		= array('area_detail_address_txt'=>$nation);
			$now_nation	= $nation;
			preg_match("/\([^가-힣]*\)/",$now_nation,$selmath);
			$sel_kr_str = trim(str_replace($selmath,"",$now_nation));
			$sel_gl_str = trim(preg_replace("/[\(,\)]/","",$selmath[0]));
		}

		if($direct_store == 'Y'){
			$params		= array('direct_store'=>$direct_store);
		}
		// 설정 리스트추출
		$set_list	= $this->shippingmodel->load_shipping_set_list($grp_seq, $params);

		// 배송설정 정보 추출
		$set_seq_arr				= array_keys($set_list);
		$set_seq_default			= $set_seq_arr[0];
		if(!$set_seq)	$set_seq	= $set_seq_default;

		$set_info	= $set_list[$set_seq];
		if(!$set_info){	exit; }	// 상세정보 지정안된경우 빈값 return

		// 매장수령일때
		if($set_info['store_use'] == 'Y'){
			foreach($set_info['shipping_address_seq'] as $k => $add_seq){
				// 주소 추출
				$set_info['shipping_address_txt'][$k] = trim(preg_replace('/\([^\)]*\)/','',$set_info['shipping_address_full'][$k]));

				// 수령매장시 재고목록 추출
				if($set_info['store_supply_set'][$k] == 'Y'){
					//$set_info['shipping_wh_supply'][$k] = $this->창고($add_seq);
				}else{
					$set_info['shipping_wh_supply'][$k] = null;
				}

				if	($store_seq && $store_seq == $add_seq){
					$store_info['store_seq'] = $set_info['shipping_address_seq'][$k];
					$store_info['shipping_store_name'] = $set_info['shipping_store_name'][$k];
					$store_info['shipping_address_txt'] = $set_info['shipping_address_txt'][$k];
				}
			}

			if	(!$store_seq){
				$store_info['store_seq'] = $set_info['shipping_address_seq'][0];
				$store_info['shipping_store_name'] = $set_info['shipping_store_name'][0];
				$store_info['shipping_address_txt'] = $set_info['shipping_address_txt'][0];
			}
		}

		// 예약배송 상품인 경우 희망배송일 설정을 삭제 :: 2017-01-03 lwh
		if($goods_info['goods_seq'] && $goods_info['display_terms_type'] == 'LAYAWAY'){
			$reserve_res = $this->goodsmodel->get_reserve_goods($goods_seq);
			if($reserve_res['goods_seq']){
				unset($set_info['shipping_opt_type']['hop']);
				unset($set_info['hopeday_limit_set']);
				unset($set_info['hop_use']);
				$goods_info['reserve_ship_txt'] = '';
				if($reserve_txt)
					$goods_info['reserve_ship_txt'] = $reserve_txt;
				else
					$goods_info['reserve_ship_txt'] = $reserve_res['reserve_ship_txt'];
			}
		}else{
			// 희망배송일 필수 값 지정 :: 2016-12-27 lwh
			if(!$hop_date && $set_info['hop_use'] == 'Y' && $set_info['hopeday_required'] == 'Y'){
				$hop_date = $this->shippingmodel->get_hop_date($set_info);
			}
		}

		// 기본 정보
		$this->template->assign(array('mode'=>$mode,'admin_mode'=>$admin_mode,'cart_table'=>$cart_table));
		$this->template->assign(array('goods_info'=>$goods_info));
		$this->template->assign(array('grp_info'=>$grp_info));
		$this->template->assign(array('set_list'=>$set_list));
		$this->template->assign(array('set_info'=>$set_info));
		$this->template->assign(array('store_info'=>$store_info));
		$this->template->assign(array('now_nation'=>trim($now_nation)));
		$this->template->assign(array('sel_gl_str'=>trim($sel_gl_str)));
		// 장바구니 정보
		if($mode != 'goods'){
			$this->template->assign(array('cart_seq'=>$cart_seq));
			$this->template->assign(array('hop_date'=>$hop_date));
		}
		// 매장픽업 바로 구매여부
		$this->template->assign(array('direct_store'=>$direct_store));
		// 요약정보
		$this->template->assign(array('ship_summary'=>$ship_summary));
		// 국가목록
		$this->template->assign(array('ship_gl_arr'=>$ship_gl_arr));
		$this->template->assign(array('ship_gl_list'=>$ship_gl_list));
		// 배송비 선/착불 정보
		$this->template->assign(array('prepay_sel'=>$prepay_info));
		$this->template->define(array('tpl'=>$file_path));
		$this->template->print_("tpl");
	}

	// 매장 수령용 지도 그리기 :: 2016-07-21 lwh
	public function store_map_info(){
		$this->tempate_modules();
		$file_path	= $this->template_path();

		$params['height']	= $_GET['height'];
		$params['width']	= $_GET['width'];
		$params['addr']		= $_GET['addr'];
		$params['name']		= $_GET['name'];

		$this->template->assign(array('params'=>$params));
		$this->template->define(array('tpl'=>$file_path));
		$this->template->print_("tpl");
	}

	// 희망배송일 달력 그리기 :: 2016-07-21 lwh
	public function hop_calendar_pop(){
		$this->tempate_modules();
		$file_path	= $this->template_path();

		$grp_seq	= $_GET['grp_seq'];
		$set_seq	= $_GET['set_seq'];
		$select_day	= $_GET['hop_select_date'];

		// 배송가능일자 추출
		$this->load->model('shippingmodel');
		$set_info = $this->shippingmodel->get_shipping_set($set_seq,'shipping_set_seq');
		// 당일여부 가공
		$set_info['today_use'] = ($set_info['hopeday_limit_set'] == 'time') ? 'Y' : 'N';
		// 요일선택 여부 가공
		$hopeday_limit_week = array(0,0,0,0,0,0,0);
		if($set_info['hopeday_limit_week']) for($i=0; $i<7; $i++){
			$hopeday_limit_week[$i] = substr($set_info['hopeday_limit_week'],$i,1);
		}

		// 달력 기본값 설정
		$year	= trim($_GET['year'])	? trim($_GET['year'])	: date('Y');
		$month	= trim($_GET['month'])	? trim(str_pad($_GET['month'],2,"0",STR_PAD_LEFT))	: date('m');
		$nowyear= date('Y');
		$nowmon	= date('m');
		$nowday	= date('d');
		$nowtime= date('Hi');
		$mTime	= strtotime($year.'-'.$month.'-01');
		$sWeek	= date('w', $mTime) + 1;
		$eDay	= date('t', $mTime) + $sWeek;

		// 달력 라인수 계산
		$rows		= 0;
		$dayCnt		= date('t', $mTime);
		if	($sWeek > 1){
			$rows	= 1;
			$dayCnt	= date('t', $mTime) - (7 - date('w', $mTime));
		}
		$rows		+= ceil($dayCnt / 7);
		$maxCell	= $rows * 7;

		// 달력 배열
		$required_day = '';
		for ($k = 1; $k <= $maxCell; $k++){
			if	($k >= $sWeek && $eDay > $k)	$nDay++;
			else								$nDay	= 0;

			$calendar[$k]['day']	= $nDay;
			$calendar[$k]['week']	= ($k-1) % 7; // 요일 추출

			// 가능여부 체크
			$nowYMD = $nowyear.$nowmon.$nowday;
			$selYmd = $year.str_pad($month, 2, "0", STR_PAD_LEFT).str_pad($nDay, 2, "0", STR_PAD_LEFT);

			$calendar[$k]['pos'] = 'N';
			if	($nowYMD <= $selYmd){

				$sel_day = str_pad($month, 2, "0", STR_PAD_LEFT) . '-' . str_pad($nDay, 2, "0", STR_PAD_LEFT);
				$sel_full_day = $year . '-' . $sel_day;

				// 정상일자 체크
				if($calendar[$k]['day'] == 0){
					$calendar[$k]['pos'] = 'N';
					continue;
				}

				// 배송일자 설정 체크
				if($set_info['today_use'] == 'N' && $set_info['hopeday_limit_set'] == 'day'){
					$day_sel_date = date('Ymd',mktime(0,0,0, date("m"), date("d") + $set_info['hopeday_limit_val'],  date("Y")));
					if($day_sel_date > $selYmd){
						$calendar[$k]['pos'] = 'N';
						continue;
					}
				}

				// 요일 체크
				if($hopeday_limit_week[$calendar[$k]['week']]=='1'){
					$calendar[$k]['pos'] = 'N';
					continue;
				}

				// 반복 불가 일자 체크
				if(strpos($set_info['hopeday_limit_repeat_day'], $sel_day)===false){
					$calendar[$k]['pos'] = 'Y';
				}else{
					$calendar[$k]['pos'] = 'N';
					continue;
				}

				// 지정 불가 일자 체크
				if(strpos($set_info['hopeday_limit_day'], $sel_full_day)===false){
					$calendar[$k]['pos'] = 'Y';
				}else{
					$calendar[$k]['pos'] = 'N';
					continue;
				}

				// 당일 여부 체크
				if($nowYMD == $selYmd && $set_info['today_use'] == 'Y'){
					if($set_info['hopeday_limit_val'] > $nowtime)
							$calendar[$k]['pos'] = 'Y';
					else	$calendar[$k]['pos'] = 'N';
				}
			}

			// 기본값 설정
			if(!$required_day && $calendar[$k]['pos'] == 'Y' && $set_info['hopeday_required'] == 'Y' && $nowyear.$nowmon == $year.str_pad($month, 2, "0", STR_PAD_LEFT)){
				$required_day = $calendar[$k]['day'];
			}
		}

		// 기존 선택된 값 지정
		if($select_day && date('Ym',strtotime($select_day)) == date('Ym',strtotime($year.str_pad($month, 2, "0", STR_PAD_LEFT).'01'))){
			$required_day = date('d',strtotime($select_day));
		}

		$this->template->assign('sel_date', array('year'=>$year,'month'=>$month));
		$this->template->assign('hop_select_date', $select_day);
		$this->template->assign('required_day', number_format($required_day));
		$this->template->assign('set_info', $set_info);
		$this->template->assign(array('calendar'=>$calendar));
		$this->template->define(array('tpl'=>$file_path));
		$this->template->print_("tpl");
	}

	# GET으로 받은 금액의 비교통화 가져오기
	public function get_compare_currency(){

		$this->template->include_('showCompareCurrency');
		$price = showCompareCurrency('',$_GET['price'],'return');

		echo $price;
	}

	public function get_goods_default_option(){
		$goods_seq = $_GET['goods_seq'];
		if	(!$goods_seq)
			$default_option = '';
		else
			$default_option = $this->goodsmodel->get_goods_default_option($goods_seq);
		echo json_encode(array('result'=>$default_option));
	}

/*######################## 19.01.02 gcs yun jy : #827 급상승 검색어 s */
	public function popular_keyword() {
		$this->template->include_('showPopularSearchList');
		$tt = showPopularSearchList('goodsPage');

		$this->print_layout($this->template_path());
	}
/*######################## 19.01.02 gcs yun jy : #827 급상승 검색어 e */

/*######################## 19.01.09 gcs yun jy : #1042 검색 필터 기능(상단 검색 버튼 클릭시 노출화면) s */
	
	public function search_main() {
		$this->print_layout($this->template_path());
	}

/*######################## 19.01.09 gcs yun jy : #1042 검색 필터 기능 e */

/*######################## 19.03.26 gcs yun jy : #1816 모바일> 브랜드리스트 (배너 노출) s */
	public function brand_view() {
		$this->print_layout($this->template_path());
	}
/*######################## 19.03.26 gcs yun jy : #1816 모바일> 브랜드리스트 (배너 노출) e */


/*######################## 19.08.16 gcs yun jy : #2387 검색 개선 s */
	public function filterCateShow() {

		$sc = $_GET;
		
		//검색된 상품 기준으로 그 상품과 관련된 카테고리, 브랜드, 입점사 노출 
		if($_GET['target'] == 'category') {
			$list['category'] = $this->goodsmodel->goodsSearchTargetList($sc,'category',$_GET['kind']);

		}else if($_GET['target'] == 'brand') {
			$list['brand'] = $this->goodsmodel->goodsSearchTargetList($sc,'brand',$_GET['kind']);
			
		}else if($_GET['target'] == 'provider') {
			if(serviceLimit('H_AD')) { //입점몰일때만 처리
				$list['provider'] = $this->goodsmodel->goodsSearchTargetList($sc,'provider',$_GET['kind']);
			}
		}else if($_GET['target'] == 'all') {
			$list['category'] = $this->goodsmodel->goodsSearchTargetList($sc,'category',$_GET['kind']);
			$list['brand'] = $this->goodsmodel->goodsSearchTargetList($sc,'brand',$_GET['kind']);

			if(serviceLimit('H_AD')) { //입점몰일때만 처리
				$list['provider'] = $this->goodsmodel->goodsSearchTargetList($sc,'provider',$_GET['kind']);
			}
		}

		echo json_encode($list);

	}
/*######################## 19.08.16 gcs yun jy : #2387 검색 개선 e */
}

/* End of file goods.php */
/* Location: ./app/controllers/goods.php */