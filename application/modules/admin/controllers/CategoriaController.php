<?php

class Admin_CategoriaController extends App_Controller_Admin {

    public function initialize() {
        $this->model = new Model_Categoria();
        $this->view->pageTitle = 'Categorias';
        $this->view->pageTitleList = 'Listagem';
        $this->view->pageTitleNew = 'Novo';
        $this->view->pageTitleEdit = 'Editar';

        $modelCategria = new Model_Categoria();
        $_categorias = $modelCategria->fetchAll("status='ativo' AND parent_categoria IS NULL")->toArray();


        foreach ($_categorias as $categoria) {
            $categoriasPai[$categoria[id_categoria]] = $categoria[categoria];
        }
        $this->view->categoriasPai = $categoriasPai;
    }

    protected function indexAction() {

        $page = $this->_getParam('page', 1);
        $per_page = $this->_getParam('per_page', 20);
         
        $rows = $this->model->fetchAll($this->select);

        $this->view->paginator = $this->paginator($rows, $per_page, $page);

        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    protected function beforeSave(&$row, $action) {
        $dados = $this->getPost();
        $row->parent_categoria = $row->parent_categoria == "" ? NULL : $row->parent_categoria;
    }

}

