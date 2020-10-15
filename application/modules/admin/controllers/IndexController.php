<?php

class Admin_IndexController extends App_Controller_Admin
{

    public function initialize() {
        $this->model = new Model_Usuario();
    }

}

