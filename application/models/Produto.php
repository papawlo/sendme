<?php

class Model_Produto extends Zend_Db_Table_Abstract {

    protected $_name = 'produto';
    protected $_primary = 'id_produto';
        protected $_referenceMap = array(
            'Categorias' => array(
                'columns' => 'categoria',
                'refTableClass' => 'categorias',
                'refColumns' => 'id_categoria'
            ),
            'Estabelecimentos' => array(
                'columns' => 'estabelecimento',
                'refTableClass' => 'estabelecimento',
                'refColumns' => 'id_estabelecimento'
            ),
        );

}