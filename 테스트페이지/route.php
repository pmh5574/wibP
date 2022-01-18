<?php
/**
 * This is commercial software, only users who have purchased a valid license
 * and accept to the terms of the License Agreement can install and use this
 * program.
 *
 * Do not edit or add to this file if you wish to upgrade Godomall5 to newer
 * versions in the future.
 *
 * @copyright ⓒ 2016, NHN godo: Corp.
 * @link http://www.godo.co.kr
 */

/**
 * ------------- === 경고 === -------------
 * 해당 파일은 절대 수정하지 마십시요.
 * 자동으로 패치가 되며, 예고 없이 변경될 수 있습니다.
 */
define('SYSPATH', realpath(__DIR__. '/../system'));
define('ABSPATH', substr(str_replace('system', '', SYSPATH), 0, -1));
define('USERPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('USERSRCPATH', USERPATH . 'data' . DIRECTORY_SEPARATOR . 'module');

// version control
$version = require_once SYSPATH . '/config/app/version.php';
if (file_exists(USERPATH . 'config/app/version.php')) {
    $version = require_once USERPATH . 'config/app/version.php';
}
define('SYSSRCPATH', SYSPATH . DIRECTORY_SEPARATOR . $version[0]);

// use class
use Framework\ClassLoader\Resolver\DefaultClassPathResolver;
use Framework\Debug\Exception\HttpException;
use Framework\Http\Request;
use Framework\StaticProxy\Proxy\Session;

// bootstrap.
$loader = require_once SYSPATH . '/autoload.php';
$godomall5 = require_once SYSPATH . '/bootstrap.php';

// sets the user base path.
$godomall5->setUserBasePath(__DIR__);

// adds the class loader to the container.
$godomall5['loader'] = $loader;
$godomall5['request'] = new Request();

// boot & run.
if ($godomall5->boot()) {
    // adds the default classpath resolver for user classes. (prepend)
    try {
        // preview user development
        if ($godomall5['request']->get()->has('__gd5_work_preview') || Session::has('workPreview')) {
            $userWorksResolver = new DefaultClassPathResolver($godomall5['user.path']->get('data', 'module'));
            $userWorksResolver->setExcludedNamespace(['Framework', 'Bundle', 'Core']);
            $godomall5['loader']->addClassPathResolver($userWorksResolver, true);
        } else {
            $userRealResolver = new DefaultClassPathResolver($godomall5['user.path']->get('module'));
            $userRealResolver->setExcludedNamespace(['Framework', 'Bundle', 'Core']);
            $godomall5['loader']->addClassPathResolver($userRealResolver, true);
        }

        // run application
        $godomall5->run();

    } catch (Exception $e) {
        throw $e;
    }
} else {
    throw new HttpException(null, 500);
}
