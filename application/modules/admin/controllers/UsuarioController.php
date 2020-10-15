<?php

class Admin_UsuarioController extends App_Controller_Admin {

    public function initialize() {
        $this->model = new Model_Usuario();
        $this->view->pageTitle = 'Usuários';
        $this->view->pageTitleList = 'Listagem';
        $this->view->pageTitleNew = 'Novo';
        $this->view->pageTitleEdit = 'Editar';

        // $modelEstado = new Model_Estado();
        // $this->view->estados = $modelEstado->fetchAll();
    }

    protected function indexAction() {

        $page = $this->_getParam('page', 1);
        $per_page = $this->_getParam('per_page', 20);

        $rows = $this->model->fetchAll($this->select);

        $this->view->paginator = $this->paginator($rows, $per_page, $page);

        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    // protected function defineSelect() {
    // 	$this->select->where('role IN (?)', array('superadmin'));
    // }

    protected function beforeSave(&$row, $action) {
        $dados = $this->getPost();

        //$row->role = 'admin';
        $row->data_cadastro = date('Y-m-d H:i:s');
        $row->status = 'ativo';

        if ($action == 'new') {
            $senha = ($dados['senha']) ? $dados['senha'] : '123456';
        } else {
            $senha = ($dados['senha']) ? $dados['senha'] : '';
        }

        if (!$dados['login']) {
            $row->login = $dados['email'];
        }

        if ($senha) {
            $row->senha = md5($senha);
        }
    }

    public function afterSave(&$row, $action) {
        //aqui fica o envio de e-mail.
    }

    public function validateAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $dados = $this->getPost();

        if ($dados) {

            $modelEstabelecimento = new Model_Usuario();

            switch ($dados['acao']) {

                case 'email':

                    $select = $modelEstabelecimento->select()->where('email = ?', $dados['dado'])
                            ->where('status != ?', 'excluido')
                            ->where('role NOT IN (?)', 'cliente');

                    $result = $modelEstabelecimento->fetchAll($select);

                    $rowCount = count($result);

                    if ($rowCount > 0) {
                        echo 'invalid';
                    } else {
                        echo 'valid';
                    }

                    break;

                case 'login':

                    $select = $modelEstabelecimento->select()->where('login = ?', $dados['dado'])
                            ->where('status != ?', 'excluido')
                            ->where('role NOT IN (?)', 'cliente');

                    $result = $modelEstabelecimento->fetchAll($select);

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

    public function cidadesAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $dados = $this->_request->getPost();

        if ($dados) {
            $modelCidade = new Model_Cidade();

            $result = $modelCidade->getByEstado($dados['estado']);

            $html = '<option value="">Selecione uma Cidade</option>';

            foreach ($result as $item) {
                $sel = '';
                $cidade = utf8_encode($item->cidade);
                if (($item->id == $dados['cidade']) || ($cidade == $dados['cidade'])) {
                    $sel = ' selected="selected"';
                }

                $html .= '<option value="' . $item->id . '"' . $sel . '>' . $cidade . '</option>';
            }

            echo $html;
        }
    }

}

