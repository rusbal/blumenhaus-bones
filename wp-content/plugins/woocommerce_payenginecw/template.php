<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2016 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
* For backwards compatibility only, template existing configurations
*/
ob_start();
// Force language for WPML
if (isset($_GET['wpml-lang'])) {
	$_SERVER['REQUEST_URI'] = str_replace('wp-content', $_GET['wpml-lang'] . '/wp-content', $_SERVER['REQUEST_URI']);
	if (!isset($_GET['lang'])) {
		$_GET['lang'] = $_GET['wpml-lang'];
	}
}

require_once dirname(__FILE__) . '/lib/loader.php';
require_once 'classes/PayEngineCw/Util.php';
require_once 'PayEngineCw/ContextRequest.php';
require_once 'PayEngineCw/Util.php';
require_once 'Customweb/Util/Html.php';

$httpRequest = PayEngineCw_ContextRequest::getInstance();

$base_dir = dirname ( dirname ( dirname ( dirname ( __FILE__ ) ) ) );
require_once $base_dir . '/wp-load.php';

ob_end_clean();

ob_start();
$vars = array( 
	'title' => __('Payment', 'woocommerce_payenginecw'),
	'content' => '$$$PAYMENT ZONE$$$'
);
PayEngineCw_Util::includeTemplateFile('page', $vars);
$content = ob_get_clean();

$content = Customweb_Util_Html::replaceRelativeUrls($content, get_bloginfo('url'));
$content = Customweb_Util_Html::convertSpecialCharacterToEntities($content);

echo $content;