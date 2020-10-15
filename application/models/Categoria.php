<?php

class Model_Categoria extends Zend_Db_Table_Abstract {

    protected $_name = 'categorias';
    protected $_primary = 'id_categoria';
    protected $_dependentTables = array('produto');

}