<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initLayout() {
		Zend_Layout::startMvc();
	}

	protected function _initAcl() {
		$aclSetup = new App_Acl_Setup();
	}
	
	/*
	protected function _initView() {
	    $view = new Zend_View();
	    $view->doctype('XHTML1_STRICT');
	    $view->env = APPLICATION_ENV;
	    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
	protected function _initView() {
    $view = new Zend_View();
    $view->doctype('XHTML1_STRICT');
    $view->env = APPLICATION_ENV;
    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    $viewRenderer->setView($view);
    return $view;
  }    $viewRenderer->setView($view);
	    return $view;
	}
	*/
	protected function _initView() {
	    $view = new Zend_View();
	    $view->doctype('HTML5');
	    $view->env = APPLICATION_ENV;
	    $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
	    $viewRenderer->setView($view);
	    return $view;
	}

	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
	  		'namespace' => '',
	  		'basePath' => APPLICATION_PATH));

	  	return $moduleLoader;
  	}

  	protected function _initNavigationConfig() {

        $filename     = realpath(APPLICATION_PATH . '/configs/navigation.xml');
        $filename_app = realpath(APPLICATION_PATH . '/configs/config.xml');

        $config = new Zend_Config_Xml($filename);
 
        $this->registerPluginResource('view');
        $view = $this->bootstrap('view')->getResource('view');
 
        $navigation = $view->navigation();
        $navigation->addPages($config);

        $config_app = new Zend_Config_Xml($filename_app);
		Zend_Registry::set('config_app', $config_app);
  	}

}

