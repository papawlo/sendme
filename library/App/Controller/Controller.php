<?php
class App_Controller_Controller extends Zend_Controller_Action
{

    protected $_userSession;

    protected function initialize() {}
    protected function defineSelect() {}
    protected function beforeSave(&$row, $action) {}
    protected function afterSave(&$row, $action) {}
    protected function setDataToRelations($row=null) {}

    public function init() {
        $this->beforeInitialize();
        $this->initialize();
        //$this->checkLogin();
        $this->afterInitialize();
    }

    public function checkLogin() {
        $auth = Zend_Auth::getInstance();

        if ( !$auth->hasIdentity() ) {
            App_Helpers_LastVisited::saveThis($this->_request->getRequestUri());
            return $this->_helper->redirector->goToRoute( array('module' => 'default', 'controller' => 'login'), null, true);
        } else {
            $user = $auth->getStorage()->read();

            if ( $user->role != 'cliente' ) {
                return $this->_helper->redirector->goToRoute( array('module' => 'admin', 'controller' => 'auth'), null, true);
            }
        }

        $this->_userSession = $this->view->userSession = $user;
    }

    protected function beforeInitialize() {
        
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->view->controller = $this->controller = $this->getControllerName();
        $this->view->module = $this->module = $this->getModuleName();
        $this->view->action = $this->action = $this->getActionName();
        $this->view->notice = array();
        $this->view->params = $this->params = array();

        $this->config_app = Zend_Registry::get('config_app');

    }

    protected function afterInitialize() {
     	/*   
        $this->select = $this->model
          ->select(Zend_Db_Table::SELECT_WITH_FROM_PART);

        $this->defineSelect();
		*/
    }

    protected function getControllerName() {
        return $this->getRequest()->getControllerName();
    }

    /**
    * Obtém o nome do módulo
    *
    * @return string
    */
    protected function getModuleName() {
        return $this->getRequest()->getModuleName();
    }

    /**
    * Obtém o nome da action
    *
    * @return string
    */
    protected function getActionName() {
        return $this->getRequest()->getActionName();
    }

    /**
    * Encapsula o método isPost
    *
    * @return boolean
    */
    protected function isPost() {
        return $this->getRequest()->isPost();
    }

    /**
    * Encapsula o método isGet
    *
    * @return boolean
    */
    protected function isGet() {
        return $this->getRequest()->isGet();
    }

    /**
    * Encapsula o método getParams
    *
    * @return array
    */
    protected function getParams() {
        return $this->getRequest()->getParams();
    }

    /**
    * Encapsula o método getPost
    *
    * @return array
    */
    protected function getPost() {
        return $this->getRequest()->getPost();
    }

    /**
    * Encapsula o método isGet do objeto de request para facilitar uso
    *
    * @return boolean
    */
    protected function isXmlHttpRequest() {
        return $this->getRequest()->isXmlHttpRequest();
    }

    public function getThumbnailSize($name) {
        $size_selected = '';
        $sizes = $this->config_app->imagens->size;

        foreach($sizes as $size) {
            if ( $size->name == $name ) {
                $size_selected = $size;
            }
        }
    }
	
}