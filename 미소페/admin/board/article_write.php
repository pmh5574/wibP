<?php
if ($bdWrite['cfg']['bdReplyStatusFl'] == 'y' && $req['mode'] == 'reply') {
//	include "_article_qa_form.php";
	include ($replyUrl);
} else {
	if($req['bdId'] == 'store'  ){
		
		include "_article_write_form_store.php";
	}
	else{
		include "_article_write_form.php";
	}
}
