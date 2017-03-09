<?php
/**
 *  * You are allowed to use this API in your web application.
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
 */
PayEngineCw_Util::bootstrap();

require_once 'PayEngineCw/ContextRequest.php';
require_once 'PayEngineCw/Util.php';
require_once 'Customweb/Core/Url.php';
require_once 'PayEngineCw/TransactionCleanUpBean.php';
require_once 'Customweb/DependencyInjection/Bean/Provider/Editable.php';
require_once 'Customweb/Cache/Backend/Memory.php';
require_once 'Customweb/DependencyInjection/Container/Default.php';
require_once 'PayEngineCw/Entity/PaymentCustomerContext.php';
require_once 'Customweb/Core/DateTime.php';
require_once 'Customweb/Asset/Resolver/Composite.php';
require_once 'Customweb/Storage/Backend/Database.php';
require_once 'Customweb/Payment/Authorization/IAdapterFactory.php';
require_once 'PayEngineCw/ContextCleanUpBean.php';
require_once 'Customweb/DependencyInjection/Bean/Provider/Annotation.php';
require_once 'Customweb/Payment/Authorization/DefaultPaymentCustomerContext.php';
require_once 'PayEngineCw/ConfigurationAdapter.php';
require_once 'PayEngineCw/Database/Driver.php';
require_once 'Customweb/Util/Html.php';
require_once 'Customweb/Database/Migration/Manager.php';
require_once 'Customweb/Asset/Resolver/Simple.php';
require_once 'PayEngineCw/LayoutRenderer.php';
require_once 'PayEngineCw/EntityManager.php';

class PayEngineCw_Util {

	private function __construct(){}
	private static $methods = array();
	private static $basePath = NULL;
	private static $container = null;
	private static $entityManager = null;
	private static $driver = null;
	private static $paymentCustomerContexts = array();

	/**
	 * This method loads a order.
	 *
	 * @param integer $orderId
	 * @return Order Object
	 */
	public static function loadOrderObjectById($orderId){
		if (defined('WOOCOMMERCE_VERSION') && version_compare(WOOCOMMERCE_VERSION, '2.0.0') >= 0 && class_exists('WC_Order')) {
			return new WC_Order($orderId);
		}
		else {
			return new woocommerce_order($orderId);
		}
	}

	public static function bootstrap(){
		set_include_path(
				implode(PATH_SEPARATOR, 
						array(
							get_include_path(),
							realpath(dirname(__FILE__)),
							realpath(dirname(dirname(__FILE__))) 
						)));
		require_once dirname(dirname(dirname(__FILE__))) . '/lib/loader.php';
	}

	/**
	 * This method returns the base path to the plugin.
	 *
	 * @return string Base Path
	 */
	public static function getBasePath(){
		if (self::$basePath === NULL) {
			self::$basePath = dirname(dirname(dirname(__FILE__)));
		}
		return self::$basePath;
	}

	public static function addPaymentMethods($gateways = array()){
		$methods = self::getPaymentMethods();
		foreach ($methods as $class_name) {
			$gateways[] = $class_name;
		}
		return $gateways;
	}

	public static function getPaymentMethods($includeClass = true){
		if (count(self::$methods) <= 0) {
			if ($handle = opendir(self::getBasePath() . '/payment_methods')) {
				while (false !== ($file = readdir($handle))) {
					if (!is_dir(self::getBasePath() . '/' . $file) && $file !== '.' && $file !== '..' && substr($file, -4, 4) == '.php') {
						$class_name = substr($file, 0, -4);
						self::$methods[] = $class_name;
					}
				}
				closedir($handle);
			}
		}
		
		if ($includeClass) {
			foreach (self::$methods as $method) {
				self::includePaymentMethod($method);
			}
		}
		return self::$methods;
	}

	public static function includePaymentMethod($methodClassName){
		$methodClassName = strip_tags($methodClassName);
		if (!class_exists($methodClassName)) {
			$fileName = self::getBasePath() . '/payment_methods/' . $methodClassName . '.php';
			if (!file_exists($fileName)) {
				throw new Exception(
						"The payment method class could not be included, because it was not found. Payment Method Name: '" . $methodClassName .
								 "' File Path: " . $fileName);
			}
			require_once $fileName;
		}
	}

	/**
	 *
	 * @param string $methodClassName
	 * @return PayEngineCw_PaymentMethod
	 */
	public static function getPaymentMehtodInstance($methodClassName){
		self::includePaymentMethod($methodClassName);
		return new $methodClassName();
	}

	public static function getPluginUrl($controller, array $params = array(), $action = null){
		if (isset($_REQUEST['wpml-lang'])) {
			$params['wpml-lang'] = $_REQUEST['wpml-lang'];
		}
		
		else if (defined('ICL_LANGUAGE_CODE')) {
			$params['wpml-lang'] = ICL_LANGUAGE_CODE;
		}
		else if (function_exists('wpml_get_current_language')) {
			$params['wpml-lang'] = wpml_get_current_language();
		}
		
		$params['cwcontroller'] = $controller;
		if (!empty($action)) {
			$params['cwaction'] = $action;
		}
		$url = new Customweb_Core_Url(get_permalink(get_option('woocommerce_payenginecw_page')));
		
		$shopForceSSLCheckout = self::getShopOption('woocommerce_force_ssl_checkout');
		if (($shopForceSSLCheckout == 'yes' || $shopForceSSLCheckout == 'true') && is_checkout()) {
			$url->setScheme('https')->setPort(443);
		}
		$url->appendQueryParameters($params);
		$complete = $url->toString();
		
		return apply_filters('woocommerce_payenginecw_plugin_url', $complete, $url->getBaseUrl() . $url->getPath(), 
				$url->getQueryAsArray());
	}

	public static function getResourcesUrl($path){
		return plugins_url(null, dirname(dirname(__FILE__))) . '/resources/' . $path;
	}

	public static function getPermalinkIdModified($id){
		$language = get_bloginfo('language');
		if (isset($_REQUEST['wpml-lang'])) {
			$language = $_REQUEST['wpml-lang'];
		}
		else if (defined('ICL_LANGUAGE_CODE')) {
			$language = ICL_LANGUAGE_CODE;
		}
		else if (function_exists('wpml_get_current_language')) {
			$language = wpml_get_current_language();
		}
		
		if (function_exists('icl_object_id')) {
			$id = icl_object_id($id, 'page', true, $language);
		}
		else {
			$id = apply_filters('wpml_object_id', $id, 'page', true, $language);
		}
		return $id;
	}

	public static function installPlugin(){
		global $wpdb;
		$manager = new Customweb_Database_Migration_Manager(self::getDriver(), dirname(__FILE__) . '/Migration/', 
				$wpdb->prefix . 'woocommerce_payenginecw_schema_version');
		$manager->migrate();
		
		//Create Page
		$optionValue = get_option('woocommerce_payenginecw_page');
		$pageContent = '[woocommerce_payenginecw]';
		$pageSlug = 'woo_payenginecw';
		$pageTitle = 'ConCardis Checkout';
		
		if ($optionValue > 0) {
			$pageObject = get_post($optionValue);
			if ('page' === $pageObject->post_type && !in_array($pageObject->post_status, 
					array(
						'pending',
						'trash',
						'future',
						'auto-draft' 
					))) {
				// Valid page is already in place
				return;
			}
			else if ('page' === $pageObject->post_type && in_array($pageObject->post_status, 
					array(
						'pending',
						'trash',
						'future',
						'auto-draft' 
					))) {
				//Page available in false state
				$pageId = $optionValue;
				$pageData = array(
					'ID' => $pageId,
					'post_status' => 'publish' 
				);
				remove_action('pre_post_update', 'wp_save_post_revision');
				wp_update_post($pageData);
				add_action('pre_post_update', 'wp_save_post_revision');
				return;
			}
		}
		
		$pageData = array(
			'post_status' => 'publish',
			'post_type' => 'page',
			'post_name' => $pageSlug,
			'post_title' => $pageTitle,
			'post_content' => $pageContent,
			'comment_status' => 'closed' 
		);
		$pageId = wp_insert_post($pageData);
		
		update_option('woocommerce_payenginecw_page', $pageId);
		
		// Append order status for pending payments
		if (!term_exists('payenginecw-pending', 'shop_order_status')) {
			wp_insert_term('payenginecw-pending', 'shop_order_status',
					array(
						'description' => 'Orders with that order status are currently in the checkout of ConCardis.',
						'slug' => 'payenginecw-pending'
					));
		}
	}

	public static function uninstallPlugin(){
		$optionValue = get_option('woocommerce_payenginecw_page');
		if ($optionValue) {
			wp_trash_post($optionValue);
		}
	}

	public static function renderHiddenFields($fields){
		return Customweb_Util_Html::buildHiddenInputFields($fields);
	}

	public static function includeTemplateFile($templateName, $variables = array()){
		if (empty($templateName)) {
			throw new Exception("The given template name is empty.");
		}
		
		$templateName = 'payenginecw_' . $templateName;
		
		$templatesCandidates = array(
			$templateName . '.php' 
		);
		$templatePath = locate_template($templatesCandidates, false, false);
		extract($variables);
		if (!empty($templatePath)) {
			require_once $templatePath;
		}
		else {
			require_once self::getBasePath() . '/theme/' . $templateName . '.php';
		}
	}

	/**
	 * This action is executed, when the form is rendered.
	 *
	 * @param WC_Checkout $checkout
	 */
	public static function actionBeforeCheckoutBillingForm(WC_Checkout $checkout){
		if (PayEngineCw_ConfigurationAdapter::isReviewFormInputActive()) {
			$fieldsToForceUpdate = array(
				'billing_first_name',
				'billing_last_name',
				'billing_company',
				'billing_email',
				'billing_phone' 
			);
			$checkout->checkout_fields['billing'] = self::addCssClassToForceAjaxReload($checkout->checkout_fields['billing'], $fieldsToForceUpdate);
		}
	}

	/**
	 * This action is executed, when the form is rendered.
	 *
	 * @param WC_Checkout $checkout
	 */
	public static function actionBeforeCheckoutShippingForm(WC_Checkout $checkout){
		if (PayEngineCw_ConfigurationAdapter::isReviewFormInputActive()) {
			$fieldsToForceUpdate = array(
				'shipping_first_name',
				'shipping_last_name',
				'shipping_company' 
			);
			$checkout->checkout_fields['shipping'] = self::addCssClassToForceAjaxReload($checkout->checkout_fields['shipping'], $fieldsToForceUpdate);
		}
	}

	private static function addCssClassToForceAjaxReload($fields, $forceFields){
		foreach ($fields as $key => $data) {
			if (in_array($key, $forceFields)) {
				if (isset($data['class']) && is_array($data['class']) && !in_array('address-field', $data['class'])) {
					$fields[$key]['class'][] = 'address-field';
				}
			}
		}
		
		return $fields;
	}

	public static function getBackendOperationAdapterFactory(){
		throw new Exception('Not supported anymore');
	}

	/**
	 *
	 * @return Customweb_DependencyInjection_Container_Default
	 */
	public static function createContainer(){
		if (self::$container === null) {
			$packages = array(
			0 => 'Customweb_PayEngine',
 			1 => 'Customweb_Payment_Authorization',
 		);
			$packages[] = 'PayEngineCw_';
			$packages[] = 'Customweb_Mvc_Template_Php_Renderer';
			$packages[] = 'Customweb_Payment_Update_ContainerHandler';
			$packages[] = 'Customweb_Payment_TransactionHandler';
			$packages[] = 'Customweb_Payment_SettingHandler';
			$provider = new Customweb_DependencyInjection_Bean_Provider_Editable(new Customweb_DependencyInjection_Bean_Provider_Annotation($packages));
			// @formatter:off
			$storage = new Customweb_Storage_Backend_Database(self::getEntityManager(), self::getDriver(), 'PayEngineCw_Entity_Storage');
			$provider->addObject(PayEngineCw_ContextRequest::getInstance())
				->addObject(self::getEntityManager())
				->addObject(self::getDriver())
				->addObject(new PayEngineCw_LayoutRenderer())
				->addObject(new Customweb_Cache_Backend_Memory())
				->add('databaseTransactionClassName', 'PayEngineCw_Entity_Transaction')
				->addObject(self::getAssetResolver())
				->addObject($storage)
				->addObject(new PayEngineCw_ContextCleanUpBean(self::getEntityManager()))
				->addObject(new PayEngineCw_TransactionCleanUpBean(self::getEntityManager()));
			// @formatter:om
			self::$container = new Customweb_DependencyInjection_Container_Default($provider);
		}
		
		return self::$container;
	}

	/**
	 *
	 * @return Customweb_Database_Entity_Manager
	 */
	public static function getEntityManager(){
		if (self::$entityManager === null) {
			$cache = new Customweb_Cache_Backend_Memory();
			self::$entityManager = new PayEngineCw_EntityManager(self::getDriver(), $cache);
		}
		return self::$entityManager;
	}
	
	
	/**
	 * 
	 * @return Customweb_Payment_ITransactionHandler
	 */
	public static function getTransactionHandler(){
		$container = self::createContainer();
		$handler = $container->getBean('Customweb_Payment_ITransactionHandler');
		return $handler;
		
	}

	public static function getAssetResolver(){
		$simple = array();
		$simple[] = new Customweb_Asset_Resolver_Simple(self::getBasePath() . '/assets/', null, 
				array(
					'application/x-smarty',
					'application/x-twig',
					'application/x-phtml' 
				));
		$simple[] = new Customweb_Asset_Resolver_Simple(self::getBasePath() . '/assets/', plugins_url(null, dirname(dirname(__FILE__))) . '/assets/');
		return new Customweb_Asset_Resolver_Composite($simple);
	}

	/**
	 *
	 * @return PayEngineCw_Database_Driver
	 */
	public static function getDriver(){
		if (self::$driver === null) {
			global $wpdb;
			$wpdb->hide_errors();
			self::$driver = new PayEngineCw_Database_Driver($wpdb);
		}
		return self::$driver;
	}

	public static function getAuthorizationAdapterFactory(){
		$container = self::createContainer();
		$factory = $container->getBean('Customweb_Payment_Authorization_IAdapterFactory');
		
		if (!($factory instanceof Customweb_Payment_Authorization_IAdapterFactory)) {
			throw new Exception("The payment api has to provide a class which implements 'Customweb_Payment_Authorization_IAdapterFactory' as a bean.");
		}
		
		return $factory;
	}

	public static function getAuthorizationAdapter($authorizationMethodName){
		return self::getAuthorizationAdapterFactory()->getAuthorizationAdapterByName($authorizationMethodName);
	}

	public static function getAuthorizationAdapterByContext(Customweb_Payment_Authorization_IOrderContext $orderContext){
		return self::getAuthorizationAdapterFactory()->getAuthorizationAdapterByContext($orderContext);
	}


	/**
	 *
	 * @param int $customerId
	 * @return Customweb_Payment_Authorization_IPaymentCustomerContext
	 */
	public static function getPaymentCustomerContext($customerId){
		// Handle guest context. This context is not stored.
		if ($customerId === null || $customerId == 0) {
			if (!isset(self::$paymentCustomerContexts['guestContext'])) {
				self::$paymentCustomerContexts['guestContext'] = new Customweb_Payment_Authorization_DefaultPaymentCustomerContext(array());
			}
			
			return self::$paymentCustomerContexts['guestContext'];
		}
		if (!isset(self::$paymentCustomerContexts[$customerId])) {
			$entities = self::getEntityManager()->searchByFilterName('PayEngineCw_Entity_PaymentCustomerContext', 'loadByCustomerId', 
					array(
						'>customerId' => $customerId 
					));
			if (count($entities) > 0) {
				self::$paymentCustomerContexts[$customerId] = current($entities);
			}
			else {
				$context = new PayEngineCw_Entity_PaymentCustomerContext();
				$context->setCustomerId($customerId);
				self::$paymentCustomerContexts[$customerId] = $context;
			}
		}
		return self::$paymentCustomerContexts[$customerId];
	}

	public static function persistPaymentCustomerContext(Customweb_Payment_Authorization_IPaymentCustomerContext $context){
		if ($context instanceof PayEngineCw_Entity_PaymentCustomerContext) {
			$storedContext = self::getEntityManager()->persist($context);
			self::$paymentCustomerContexts[$storedContext->getCustomerId()] = $storedContext;
		}
	}

	/**
	 * This function has to echo the additional payment information received from the transaction object.
	 * This function has to check if the order was paid with this module.
	 *
	 * @param int $orderId woocommerce orderId
	 * @return void
	 */
	public static function thankYouPageHtml($orderId){
		$transactions = self::getTransactionsByPostId($orderId);
		if(empty($transactions)){
			$transactions = self::getTransactionsByOrderId($orderId);
		}
		$transactionObject = null;
		foreach ($transactions as $transaction) {
			if ($transaction->getTransactionObject() !== null && $transaction->getTransactionObject()->isAuthorized()) {
				$transactionObject = $transaction->getTransactionObject();
				break;
			}
		}
		if ($transactionObject === null) {
			return;
		}
		$paymentInformation = $transactionObject->getPaymentInformation();
		if (!empty($paymentInformation)) {
			echo '<div class="woocommerce_payenginecw-payment-information" id="woocommerce_payenginecw-payment-information">';
			echo "<h2>" . __('Payment Information', 'woocommerce_payenginecw') . "</h2>";
			echo $transactionObject->getPaymentInformation();
			echo '</div>';
		}
	}
	
		/**
	 * This function has to echo the additional payment information received from the transaction object.
	 * This function has to check if the order was paid with this module.
	 *
	 * @param WC_Order $order
	 * @param boolean $sent_to_admin
	 * @param boolean $plain_text
	 * @return void
	 */
	public static function orderEmailHtml($order, $sent_to_admin, $plain_text = false){
	
		$transactionObject = null;
		$transactions = self::getTransactionsByPostId($order->id);
		if(empty($transactions)){
			$transactions = self::getTransactionsByOrderId($order->id);
		}
		$transactions = self::getTransactionsByOrderId($order->id);
		foreach ($transactions as $transaction) {
			if ($transaction->getTransactionObject() !== null && $transaction->getTransactionObject()->isAuthorized()) {
				$transactionObject = $transaction->getTransactionObject();
				break;
			}
		}
		if ($transactionObject === null) {
			return;
		}
		$paymentInformation = $transactionObject->getPaymentInformation();
		if(!empty($paymentInformation)) {
			echo '<div class="woocommerce_payenginecw-email-payment-information" id="woocommerce_payenginecw-email-payment-information">';
			echo "<h2>" . __('Payment Information', 'woocommerce_payenginecw') . "</h2>";
			echo $transactionObject->getPaymentInformation();
			echo '</div>';
		}
	}
	/**
	 * Returns the transaction specified by the transactionId
	 *
	 * @param integer $id The transaction Id
	 * @param boolean $cache load from cache
	 * @return PayEngineCw_Entity_Transaction The matching transactions for the given transaction id
	 */
	public static function getTransactionById($id, $cache = true){
		return self::getEntityManager()->fetch('PayEngineCw_Entity_Transaction', $id, $cache);
	}

	/**
	 * Returns the transaction specified by the transaction number (externalId)
	 *
	 * @param integer $number The transactionNumber
	 * @param boolean $cache load from cache
	 * @return PayEngineCw_Entity_Transaction The matching transactions for the given transactionNumber
	 */
	public static function getTransactionByTransactionNumber($number, $cache = true){
		$transactions = self::getEntityManager()->searchByFilterName('PayEngineCw_Entity_Transaction', 'loadByExternalId', 
				array(
					'>transactionExternalId' => $number 
				), $cache);
		if (empty($transactions)) {
			throw new Exception("No transaction found, for the given transaction number: " . $number);
		}
		return reset($transactions);
	}

	/**
	 * Return all transactions given by the order id
	 *
	 * @param integer $orderId The id of the order
	 * @param boolean $cache load from cache
	 * @return PayEngineCw_Entity_Transaction[] The matching transactions for the given order id
	 */
	public static function getTransactionsByOrderId($orderId, $cache = true){
		class_exists('WC_Order');
		self::getPaymentMethods(true);
		return self::getEntityManager()->searchByFilterName('PayEngineCw_Entity_Transaction', 'loadByOrderId', 
				array(
					'>orderId' => $orderId 
				), $cache);
	}
	
	public static function getTransactionsByPostId($postId, $cache = true){
		class_exists('WC_Order');
		self::getPaymentMethods(true);
		return self::getEntityManager()->searchByFilterName('PayEngineCw_Entity_Transaction', 'loadByPostId',
				array(
					'>postId' => $postId
				), $cache);
	}

	public static function getAuthorizedTransactionByOrderId($orderId){
		class_exists('WC_Order');
		$transactions = self::getTransactionsByOrderId($orderId);
		foreach ($transactions as $transaction) {
			if ($transaction->getTransactionObject() != null && $transaction->getTransactionObject()->isAuthorized()) {
				return $transaction;
			}
		}
		
		return NULL;
	}
	
	public static function getAuthorizedTransactionByPostId($postId){
		class_exists('WC_Order');
		$transactions = self::getTransactionsByPostId($postId);
		foreach ($transactions as $transaction) {
			if ($transaction->getTransactionObject() != null && $transaction->getTransactionObject()->isAuthorized()) {
				return $transaction;
			}
		}
	
		return NULL;
	}

	public static function getAliasTransactions($userId, $paymentMethod){
		if (empty($userId)) {
			return array();
		}
		
		$aliases = array();
		$entities = self::getEntityManager()->search('PayEngineCw_Entity_Transaction', 
				'customerId LIKE >customerId AND LOWER(paymentMachineName) LIKE LOWER(>paymentMethod) AND aliasActive LIKE >active AND aliasForDisplay IS NOT NULL AND aliasForDisplay != ""', 
				'createdOn ASC', array(
					'>paymentMethod' => $paymentMethod,
					'>customerId' => $userId,
					'>active' => 'y' 
				));
		
		$knownAlias = array();
		foreach ($entities as $entity) {
			if (!in_array($entity->getAliasForDisplay(), $knownAlias) && $entity->getOrder() !== NULL) {
				$aliases[$entity->getTransactionId()] = $entity;
				$knownAlias[] = $entity->getAliasForDisplay();
			}
		}
		return $aliases;
	}
	
	public static function getAliasTransactionObject($aliasTransactionId, $userId) {
		if ($aliasTransactionId === 'new') {
			return 'new';
		}
		
		if ($aliasTransactionId !== null && !empty($aliasTransactionId)) {
			$transcation = self::getTransactionById($aliasTransactionId);
			if ($transcation !== null && $transcation->getTransactionObject() !== null && $transcation->getCustomerId() == $userId && $userId != 0) {
				return $transcation->getTransactionObject();
			}
		}
		
		return null;
	}

	public static function getFailedTransactionObject($failedTransactionId, $failedValidate) {
		if ($failedTransactionId !== NULL) {
			$dbFailedTransaction = self::getTransactionById($failedTransactionId);
			if ($failedValidate == self::computeTransactionValidateHash($dbFailedTransaction)) {
				return $dbFailedTransaction->getTransactionObject();
			}
		}
		return null;
	}
	
	public static function getShopOption($optionname){
		$option = get_option($optionname);
;
		return $option;
	}

	public static function computeTransactionValidateHash(PayEngineCw_Entity_Transaction $transaction) {
		return substr(sha1($transaction->getCreatedOn()->format("U")), 0, 10);
	}
	
	public static function computeOrderValidationHash($orderId) {
		$wpPost	= get_post($orderId);
		return substr(sha1($wpPost->post_date_gmt.$wpPost->post_password), 0, 10);
	}
		
	
	
	public static function checkToken(PayEngineCw_Entity_ExternalCheckoutContext $context, $parameters =array()) {
		if (!empty($parameters['payenginecw-context-id'])) {
			$token = $parameters['token'];
			if (!empty($token)) {
				if ($token !== null && $context->getSecurityToken() === $token) {
					$expiryDate = $context->getSecurityTokenExpiryDate();
					if ($expiryDate instanceof DateTime) {
						$expiryDate = new Customweb_Core_DateTime($expiryDate);
						if ($expiryDate->getTimestamp() > time()){
							return true;
						}
					}
				}
			}
			throw new Exception("Invalid token");
		}
	}
	
	/**
	 * 
	 * @param String $email
	 * @param String $password
	 * @return $userID
	 * @throws Exception, if non valid email, email, already exists, or creation fails
	 */
	public static function createUser($email, $password, $firstname = null, $lastname = null) {
		// Check the e-mail address
		if ( empty( $email ) || ! is_email( $email ) ) {
			throw new Exception(__( 'Please provide a valid email address.', 'woocommerce' ));
		}
		
		else if ( email_exists( $email ) ) {
			throw new Exception( __( 'An account is already registered with your email address. Please login.', 'woocommerce' ));
		
		}
		//We need a username => first-part of email address
		$username = sanitize_user( current( explode( '@', $email ) ), true );
		
		// Ensure username is unique
		$append     = 1;
		$o_username = $username;
		while ( username_exists( $username ) ) {
			$username = $o_username . $append;
			$append ++;
		}
		if( empty( $password ) ) {
			throw new Exception( __( 'Please enter an account password.', 'woocommerce' ) );
		
		}
		$userData = array(
			'user_login' => $username,
			'user_pass'  => $password,
			'user_email' => $email,
			'role'       => 'customer',
			'first_name' => $firstname,
			'last_name' => $lastname,
		);
		
		$userId = wp_insert_user( $userData );
		if(is_wp_error($userId)){
			throw new Exception(__('Could not create user.', 'woocommerce_payenginecw').' '.$userId->get_error_message());
		}
		return $userId;	
	}
	
	
	
	public static function getCheckoutUrlPageId() {
		
if(function_exists('wc_get_page_id')) {
	return wc_get_page_id('checkout');
}
else {
	$id = PayEngineCw_Util::getShopOption('woocommerce_checkout_page_id');
	$page = apply_filters( 'woocommerce_get_checkout_page_id', $id);
	return $page ? absint( $page ) : -1;
	
}
		
	}

}
