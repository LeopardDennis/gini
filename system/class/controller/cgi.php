<?php

namespace Controller;

abstract class CGI {

    public $action;
    public $params;
    public $form;
    public $route;

    function __pre_action($action, &$params) { }
    
    function __post_action($action, &$params, $response) { }

    function execute() {

        $action = $this->action ?: '__index';
        $params = (array) $this->params;

        $this->__pre_action($action, $params);
        $response = call_user_func_array(array($this, $action), $params);
        return $this->__post_action($action, $params, $response) ?: $response;
    }
    
    function form($mode = '*') {
        switch($mode) {
        case 'get':
            return $this->form['get'] ?: [];
        case 'post':
            return $this->form['post'] ?: [];
        case 'files':
            return $this->form['files'] ?: [];
        default:
            return array_merge((array)$this->form['get'], (array)$this->form['post']);
        }
    }
    
}
