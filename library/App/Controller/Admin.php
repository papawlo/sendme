<?php
class App_Controller_Admin extends Zend_Controller_Action
{

    protected function initialize() {}
    protected function defineSelect() {}
    protected function beforeSave(&$row, $action) {}
    protected function afterSave(&$row, $action) {}
    protected function setDataToRelations($row=null) {}

    public function init() {
        $this->beforeInitialize();
        $this->initialize();
        $this->checkLogin();
        $this->afterInitialize();
    }

    public function checkLogin() {
        $auth = Zend_Auth::getInstance();

        if ( !$auth->hasIdentity() ) {
            App_Helpers_LastVisited::saveThis($this->_request->getRequestUri());
            return $this->_helper->redirector->goToRoute( array('module' => 'admin', 'controller' => 'auth'), null, true);
        }

        $this->view->userSession = $auth->getStorage()->read();
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
        
        $this->select = $this->model
          ->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
          ->setIntegrityCheck(false)
          ->where('status != ?', 'deletado');

        $this->defineSelect();

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

    protected function paginator($rows, $per_page, $page) {
        $paginator = Zend_Paginator::factory($rows);
        $paginator->setCurrentPageNumber($page)
                  ->setItemCountPerPage($per_page);
     
        return $paginator;
    }

    /**
    * Adiciona informação para ser utilizada na view (via redirect)
    *
    * @return void
    */
    protected function addFlash($msg) {
        //$flash = $this->_helper->getHelper('FlashMessenger');
        //$flash->addMessage($msg);
        $flashMessenger = $this->_helper->FlashMessenger;
        $flashMessenger->addMessage($msg);
    }

    /**
    * Adiciona informação para ser utilizada na view (sem redirect)
    *
    * @return void
    */
    protected function addNotice($notice) {
        $this->view->notice[] = $notice;
    }

    protected function ifCondition404($condition) {
        if($condition) {
            throw new Zend_Controller_Action_Exception('This page dont exist', 404);
        }
    }

    protected function gotoIndex() {
        $this->_helper->redirector('index', $this->controller, $this->module, $this->params);
    }

    private function getModuleScriptsPath() {
        $paths = $this->view->getScriptPaths();
        return $paths[0];
    }
    
    protected function indexAction() {
        $this->view->addScriptPath($this->getModuleScriptsPath());

        $page = $this->_getParam('page', 1);
        $per_page = $this->_getParam('per_page', 20);

        $rows = $this->model->fetchAll($this->select);

        $this->view->paginator = $this->paginator($rows, $per_page, $page);

        $this->view->messages = $this->_helper->flashMessenger->getMessages();

    }

    public function newAction() {
        $this->view->addScriptPath($this->getModuleScriptsPath());

        $this->view->row = $this->model->createRow();
        $this->view->model = $this->model;
        $this->setDataToRelations();

        if($this->isPost()) {
            $this->save($this->view->row);
        }
    }

    /**
    * Exibe formulário e manipula edição
    *
    * @return void
    */
    public function editAction() {
        $this->view->addScriptPath($this->getModuleScriptsPath());

        $id = $this->_request->getParam('id', 0);
        $rowSet = $this->model->find($id);
        $row = $rowSet->current();

        $this->view->row = $row;

        if ($this->isPost()) {
            $this->save($row);
        }
    }

    private function save($row) {

        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        
        if( $this->_request->isPost() ) {

            try {

                $this->db->beginTransaction();
                $row->setFromArray($this->_request->getPost());

                $this->beforeSave($row, $this->action);
                $row->save();
                $this->afterSave($row, $this->action);

                $this->db->commit();

                $display = $this->action == 'edit' ? 'alterado' : 'salvo' ; 
                $this->addFlash(array("sucesso" => "Registro {$display} com sucesso."));

                return $this->gotoIndex();

            } catch (Exception $e) {
                $this->db->rollback();

                $display = $this->action == 'edit' ? 'alterar' : 'salvar' ; 
                $this->addFlash(array("error" => "Erro ao {$display} registro.<br />".$e->getMessage()));

                return $this->gotoIndex();
            }

        }

    }

    /**
    * Exclui o registro
    *
    * @return void
    */
    public function deleteAction() {
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_getParam('id', null);

        try {

            $rowSet = $this->model->find($id);
            $row = $rowSet->current();

            $this->db->beginTransaction();
            $row->setFromArray(array('status' => 'excluido'));

            $row->save();

            $this->db->commit();

            $display = $this->action == 'edit' ? 'alterado' : 'salvo' ; 
            $this->addFlash(array("sucesso" => "Registro deletado com sucesso."));

            return $this->gotoIndex();
        } catch (Zend_Db_Exception $e) {
            $this->db->rollback();
            //echo $e->getMessage();
            $this->addFlash(array("error" => "Erro ao deletar o registro.<br />".$e->getMessage()));
            return $this->gotoIndex();
        }

    }

    public function statusAction() {
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->_getParam('id', null);

        try {

            $rowSet = $this->model->find($id);
            $row = $rowSet->current();

            $this->db->beginTransaction();

            if ($row->status == 'ativo') {
                $row->setFromArray(array('status' => 'inativo'));   
            } else {
                $row->setFromArray(array('status' => 'ativo'));
            }

            $row->save();

            $this->db->commit();

            $this->addFlash(array("sucesso" => "Status alterado com sucesso!!"));

            return $this->gotoIndex();
        } catch (Zend_Db_Exception $e) {
            $this->db->rollback();
            echo $e->getMessage();
           return $this->gotoIndex();
        }

    }

    public function uploadDir() {
        $destino = APPLICATION_PATH . '/../media/'.date('Y').'/'.date('m');

        $return = file_exists($destino);

        if ( $return === false) {
            $result = mkdir($destino, 0777, true);
            chmod($destino, 0777);
        }

        return $destino;
    }

}