<?php

class Admin_ProdutoController extends App_Controller_Admin {

    public function initialize() {
        $this->model = new Model_Produto();
        $this->view->pageTitle = 'Produtos';
        $this->view->pageTitleList = 'Listagem';
        $this->view->pageTitleNew = 'Novo';
        $this->view->pageTitleEdit = 'Editar';

        $modelCategria = new Model_Categoria();
        $_categorias = $modelCategria->fetchAll("status='ativo'")->toArray();


        foreach ($_categorias as $categoria) {
            $categorias[$categoria["id_categoria"]] = $categoria["categoria"];
        }
        $this->view->categorias = $categorias;

        $modelEstabelecimento = new Model_Estabelecimento();
        $_estabelecimentos = $modelEstabelecimento->fetchAll("status='ativo'")->toArray();


        foreach ($_estabelecimentos as $estabelecimento) {
            $estabelecientos[$estabelecimento["id_estabelecimento"]] = $estabelecimento["nome_fantasia"];
        }
        $this->view->estabelecimentos = $estabelecientos;
    }

    protected function indexAction() {

        $page = $this->_getParam('page', 1);
        $per_page = $this->_getParam('per_page', 20);

        $rows = $this->model->fetchAll($this->select);

        $this->view->paginator = $this->paginator($rows, $per_page, $page);

        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    protected function beforeSave(&$row, $action) {
        $row = $this->getPost();

        $row->data_cadastro = date('Y-m-d H:i:s');
        $row->status = 'ativo';

        $upload = new Zend_File_Transfer_Adapter_Http();

        $upload->setDestination(APPLICATION_PATH . '/../public/uploads/produtos');

        $files = $upload->getFileInfo();

        if ($files['imagem']['name']) {

            foreach ($files as $file => $info) {
                if ($upload->isValid($file)) {
                    $upload->receive($file);

                    $novo = App_Helpers_Formatting::sanitize_file_name($info['name']);

                    rename(APPLICATION_PATH . '/../public/uploads/produtos/' . $info['name'], APPLICATION_PATH . '/../public/uploads/produtos/' . $novo);

                    $row->imagem = $novo;

                    $original_path = APPLICATION_PATH . '/../public/uploads/produtos/' . $novo;
                    $image = new Zend_Image($original_path, new Zend_Image_Driver_Gd);
                    $transform = new Zend_Image_Transform($image);

                    $new_image = $transform->fitToHeight(120);
                    $transform->center();

                    if ($new_image->getHeight() < $new_image->getWidth()) {
                        $new_image = $transform->crop(160, 120);
                    }


                    $new_image->save(APPLICATION_PATH . '/../public/uploads/produtos/thumbnails/' . $novo);
                } else {
                    echo 'Arquivo inválido.';
                }
            }
        }
    }

    public function afterSave(&$row, $action) {
        $row = $this->getPost();


        /*
         * Gerando Thumbnails
         */


        $original_path = APPLICATION_PATH . '/../public/uploads/produtos/' . $row->imagem;
        $image = new Zend_Image($original_path, new Zend_Image_Driver_Gd);
        $transform = new Zend_Image_Transform($image);

        $new_image = $transform->fitToHeight(120);
        $transform->center();

        if ($new_image->getHeight() < $new_image->getWidth()) {
            $new_image = $transform->crop(160, 120);
        }


        $new_image->save(APPLICATION_PATH . '/../public/uploads/empreendimento/thumbnails/' . $row->imagem);
    }

}

