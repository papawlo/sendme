<?php 
class App_Acl_Setup
{

	protected $_acl;

    public function __construct() {
        $this->_acl = new Zend_Acl();
        $this->_initialize();
    }

    protected function _initialize() {
        $this->_setupRoles();
        $this->_setupResources();
        $this->_setupPrivileges();
        $this->_saveAcl();
    }

    protected function _setupRoles() {
        $this->_acl->addRole( new Zend_Acl_Role('guest') );
        $this->_acl->addRole( new Zend_Acl_Role('superadmin'), 'guest' );
    }

    protected function _setupResources() {
        $this->_acl->addResource( new Zend_Acl_Resource('default') );
        $this->_acl->addResource( new Zend_Acl_Resource('default:index'), 'default' );
        $this->_acl->addResource( new Zend_Acl_Resource('default:auth'), 'default' );
        $this->_acl->addResource( new Zend_Acl_Resource('default:error'), 'default' );

        /* ADMIN */
        $this->_acl->addResource( new Zend_Acl_Resource('admin') );
        $this->_acl->addResource( new Zend_Acl_Resource('admin:auth'), 'admin' );
        $this->_acl->addResource( new Zend_Acl_Resource('admin:index'), 'admin' );
        $this->_acl->addResource( new Zend_Acl_Resource('admin:usuario'), 'admin' );
        $this->_acl->addResource( new Zend_Acl_Resource('admin:categoria'), 'admin' );
        $this->_acl->addResource( new Zend_Acl_Resource('admin:produto'), 'admin' );

    }

    protected function _setupPrivileges() {
        $this->_acl->allow('guest', 'default:index', array('index'))
                   ->allow('guest', 'default:auth', array('index', 'login'))
                   ->allow('guest', 'default:error', array('error', 'forbidden'));

        $this->_acl->allow('superadmin', 'admin:index', array('index'))
                   ->allow('superadmin', 'admin:auth', array('index', 'login', 'logout'))
                   ->allow('superadmin', 'admin:usuario', array('index', 'new', 'edit', 'delete', 'status'))
                   ->allow('superadmin', 'admin:categoria', array('index', 'new', 'edit', 'delete', 'status'))      
                   ->allow('superadmin', 'admin:produto', array('index', 'new', 'edit', 'delete', 'status'));
    }

    protected function _saveAcl() {
        $registry = Zend_Registry::getInstance();
        $registry->set('acl', $this->_acl);
    }

}

?>