<?php

class LoginController extends Zend_Controller_Action
{

    public function initialize() {
        
    }

    public function indexAction() {
    	
    }

    public function loginAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $flashMessenger = $this->_helper->FlashMessenger;
        $this->view->messages = $flashMessenger->getMessages();

        if( $this->_request->isPost() ) {

            
            $login  = $this->_request->getPost('login');
            $senha  = $this->_request->getPost('senha');
            
            try {
                $result = Model_Auth::login($login, $senha);

                if ($result) {
                    $auth = Zend_Auth::getInstance();
                    $user = $auth->getStorage()->read();

                    if ($user->role == 'cliente') {
                        return $this->_helper->redirector->goToRoute( array('module' => 'default', 'controller' => 'index', 'action' => 'index'));
                    } else {
                        return $this->_helper->redirector->goToRoute( array('module' => 'admin', 'controller' => 'index', 'action' => 'index'));
                    }

                }

            } catch (Exception $e) {
                //echo $e->getMessage();
                $flashMessenger->addMessage(array("error" => $e->getMessage()));

                $this->_helper->redirector('index', 'login', 'default');
            }

        }

    }

    public function createAction() {


		if ( $this->_request->isPost() ) {
			$cpf             = $this->_request->getPost('cpf');
			$codigo_ativacao = $this->_request->getPost('codigo_ativacao');

			$modelUsuario = new Model_Usuario();

            if ($codigo_ativacao) {
                $rowUsuario = $modelUsuario->checkNewUser($cpf, $codigo_ativacao);

                if ( !$rowUsuario ) {
                    $this->_helper->redirector->goToRoute( array( 'controller' => 'login', 'action' => 'index' ) );
                }

                $this->view->rowUsuario = $rowUsuario;

            } else {
                $select = $modelUsuario->select()->where('cpf = ?', $cpf)
                                                 ->where('status = ?', 'novo');


                $row = $modelUsuario->fetchRow($select);

                $row->status = 'chave';
                $row->codigo_ativacao = md5( $row->codigo_integracao.time() );

                $row->save();

                $this->view->msg = 'Um novo código de ativação foi gerado para você, verifique seu e-mail para completar o cadastro. Caso você não tenho e-mail cadastrado no sistema, por favor, entre com contato com a Vertical Engenharia.';
            }

		}
		 	
    }

    public function esqueciSenhaAction() {

        $this->view->exibicao = 'form';

        if ( $this->_request->isPost() ) {

            $cpf = $this->_request->getPost('cpf');

            $modelUsuario = new Model_Usuario();

            $select = $modelUsuario->select()->where('cpf = ?', $cpf)
                                             ->where('status IN (?)', array('ativo', 'senha'));


            $row = $modelUsuario->fetchRow($select);

            $row->senha = '';
            $row->codigo_ativacao = md5( $row->codigo_integracao.time() );
            $row->status = 'senha';

            $row->save();

            $this->view->exibicao = 'sucesso';

            // envio de email

        }        

    }

    public function novaChaveAction() {

    }

    public function saveAction() {
    	
    	$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);


        if ( $this->_request->isPost() ) {

        	$dados = $this->_request->getPost();

        	$modelUsuario = new Model_Usuario();

        	$select = $modelUsuario->select()->where('id_user = ?', $dados['id_user'])
        									 ->where('codigo_ativacao = ?', $dados['codigo_ativacao']);

        	$row = $modelUsuario->fetchRow($select);

        	$row->login = ($dados['login'])?$dados['login']:$dados['logincadastrado'];
        	$row->senha = md5($dados['senha']);
        	$row->codigo_ativacao = '';
        	$row->status = 'ativo';

        	$row->save();

        	$flashMessenger = $this->_helper->FlashMessenger;
        	$this->view->messages = 'Seus dados de acesso foram criados com sucesso.';

        	$this->_helper->redirector->goToRoute( array( 'controller' => 'login', 'action' => 'index' ) );

        }

    }

    public function sairAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        return $this->_helper->redirector->goToRoute( array('controller' => 'index', 'action' => 'index'));
    }


    public function validateAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $dados = ($this->_request->getPost())?$this->_request->getPost():$this->_request->getParams();

        if ($dados) {

            $modelUsuario = new Model_Usuario();

            switch ($dados['acao']) {
                case 'login':

                    $select = $modelUsuario->select()->where('login = ?', $dados['dado'])
                                                             ->where('status != ?', 'excluido')
                                                             ->where('role IN (?)', 'cliente');

                    $result = $modelUsuario->fetchAll($select);

                    $rowCount = count($result);

                    if ($rowCount > 0) {
                        echo 'invalid';
                    } else {
                        echo 'valid';
                    }

                    break;

            }


        }

    }

}

