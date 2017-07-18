<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

App::uses('Controller', 'Controller');

class AppController extends Controller {

    public $uses = array('Notification', 'Printhouse', 'Order', 'Invoice', 'InvoiceTransaction', 'GoogleAnalytic');
    public $components = array('Auth', 'Session', 'Email', 'Cookie', 'Custom', 'RequestHandler');
    public $helpers = array('Html', 'Form', 'Cache', 'Session', 'Custom');

    /**
     * Before filter logic
     *
     */
    public function beforeFilter() {
        parent::beforeFilter();

        if (!$this->Session->read('Auth.User.id')) {
            if ($this->Cookie->read('Auth')) {
                $this->Session->write('Auth', $this->Cookie->read('Auth'));
            }
        }

        $this->loadModel('Staffpermission');
        if ($this->Session->read('Auth.User.type') == 3) {
            $staffPermission = $this->Staffpermission->findByUserid($this->Session->read('Auth.User.id'));

            if (!empty($staffPermission)) {
                $this->set('staffPermission', $staffPermission);
            } else {
                $this->set('staffPermission', NULL);
            }
        }

        if (is_null($this->Auth->user())) {
            Security::setHash('md5');
            //$this->Auth->authenticate = array(AuthComponent::ALL => array('userModel' => 'User','fields' => array('username' => 'email','password' => 'password'),'scope' => array('User.is_active' => 1)),'Form');
            $this->Auth->authenticate = array('Form' => array('fields' => array('username' => 'email', 'password' => 'password'), 'scope' => array('User.is_active' => 1, 'User.is_complete' => 1)));
        }
        
        if (Configure::read() == 0):
            $this->cakeError('error404');
        endif;
    }

}
