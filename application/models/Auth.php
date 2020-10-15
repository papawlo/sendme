<?php
class Model_Auth
{
    
    public static function login($login, $senha)
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('usuarios')
                    ->setIdentityColumn('login')
                    ->setCredentialColumn('senha')
                    ->setCredentialTreatment('MD5(?)');

        $authAdapter->setIdentity($login)
                    ->setCredential($senha);

        $select = $authAdapter->getDbSelect();
        $select->where('status = "ativo"');

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);

        if ( $result->isValid() ) {

            $info = $authAdapter->getResultRowObject(null, 'senha');

            $usuarioTable = new Model_Usuario();
            $usuarioRowset = $usuarioTable->find($info->id_usuario);
            $usuario = $usuarioRowset->current();

            $storage = $auth->getStorage();
            $storage->write($usuario);

            return true;
        } else {
            throw new Exception('Nome de usuário ou senha inválida');
        }

    }
}