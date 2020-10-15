<?php

class Admin_AuthController extends Zend_Controller_Action {

    public function init() {
        $context = $this->_helper->getHelper('AjaxContext');
        $context->addActionContext('login', 'json')
                ->initContext();
    }

    public function indexAction() {
        return $this->_helper->redirector('login');
    }

    public function loginAction() {
        $this->_helper->layout->disableLayout();

        $flashMessenger = $this->_helper->FlashMessenger;
        $this->view->messages = $flashMessenger->getMessages();

        if ($this->_request->isPost()) {

            $this->_helper->viewRenderer->setNoRender(true);

            $login = $this->_request->getPost('login');
            $senha = $this->_request->getPost('senha');
            $result = Model_Auth::login($login, $senha);
            
            $auth = Zend_Auth::getInstance();
            $user = $auth->getStorage()->read();

            try {
                $result = Model_Auth::login($login, $senha);

                if ($result) {
                    $auth = Zend_Auth::getInstance();
                    $user = $auth->getStorage()->read();

                    // if ($user->role == 'supersadmin') {
                    return $this->_helper->redirector->goToRoute(array('module' => 'admin', 'controller' => 'index', 'action' => 'index'));
                    // } else {
                    //     return $this->_helper->redirector->goToRoute( array('module' => 'default', 'controller' => 'index', 'action' => 'index'));
                    // }
                }
            } catch (Exception $e) {
                //echo $e->getMessage();
                $flashMessenger->addMessage(array("error" => $e->getMessage()));

                $this->_helper->redirector('index', 'auth', 'admin');
            }
        }
    }

    public function logoutAction() {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'index'));
    }

}

