<?php

class Model_Estabelecimento extends Zend_Db_Table_Abstract {

    protected $_name = 'estabelecimento';
    protected $_primary = 'id_estabelecimento';
    //    protected $_referenceMap = array(
    //        'Categorias' => array(
    //            'columns' => 'categoria',
    //            'refTableClass' => 'categorias',
    //            'refColumns' => 'id_categoria'
    //        ),
    //        'Estabelecimentos' => array(
    //            'columns' => 'estabelecimento',
    //            'refTableClass' => 'estabelecimento',
    //            'refColumns' => 'id_estabelecimento'
    //        ),
    //    );

}