<?php
/**
 * Keeps track of the messages that are returned from the Salsa framework
 * so we can display them.
 */
class DIAMessages {
    public $validation = array();
    public $errors = array();
    public $success = NULL;
    public $content = NULL;
    
    public function addValidation($array) {
        if (!empty($array)) {
            $this->validation = array_merge($this->validation, $array);
        }
    }
    
    public function addError($msg) {
        $this->errors[] = $msg;
    }
    
    public function addErrors($array) {
        if (!empty($array)) {
            $this->errors = array_merge($this->errors, $array);
        }
    }
    
    public function setSuccess($msg) {
        $this->success = $msg;
    }
    
    public function setContent($msg) {
        $this->content = $msg;
    }
    
    public function hasErrors() {
        return !empty($this->validation) || !empty($this->errors);
    }
    
    public function hasMessages() {
        return $this->hasErrors() || !empty($this->success) || !empty($this->content);
    }
}