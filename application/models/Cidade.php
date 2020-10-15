<?php
class Model_Cidade extends Zend_Db_Table_Abstract
{
    protected $_name = 'cidades';

    protected $_primary = 'id';

    public function getByEstado($sigla, $order = 'cidade ASC', $limit = 0) {
    	$select = $this->select()
    				   ->where('estado = ?', $sigla)
    				   ->order($order);

    	if ($limit) {
    		$select->limit($limit);
    	}

    	return $this->fetchAll($select);
    }

    public static function getCidadesCadastradas() {
        $modelEstabelecimento = new Model_Estabelecimento();
        $select = $modelEstabelecimento->select()
                                        ->setIntegrityCheck(false)
                                        ->from('estabelecimento')
                                        ->join('cidades', 'cidades.id = estabelecimento.cidade', array('cidades.id','cidades.cidade'))
                                        ->group('cidades.id')
                                        ->order('cidades.cidade ASC');

        //echo $select->__toString(); exit();

        $results = $modelEstabelecimento->fetchAll($select);

        return $results;
    }

}