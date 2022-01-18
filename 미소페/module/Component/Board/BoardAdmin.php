<?php

/**
 * 게시판관리 Class
 *
 * @author sunny, sj
 * @version 1.0
 * @since 1.0
 * @copyright ⓒ 2016, NHN godo: Corp.
 */
namespace Component\Board;

use App;
use Component\Goods\Goods;
use Component\Member\Group\Util as GroupUtil;
use Component\Member\Manager;
use Component\Storage\Storage;
use Cache;
use Component\Database\DBTableField;
use Component\File\DataFileFactory;
use Component\Validator\Validator;
use Framework\Cache\CacheableProxyFactory;
use Framework\Utility\ArrayUtils;
use Request;

class BoardAdmin extends \Bundle\Component\Board\BoardAdmin
{
	
}