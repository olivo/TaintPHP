<?php

// Class that contains a map from function signatures to 
// the taint map of its CFG, for the entire application.

class TaintMap{

      private $Map = array();

      public function __construct(){
      	     $this->Map = array();
      }

      public function put($functionSignatureString, $cfgTaintMap){
      	     $this->Map[$functionSignatureString] = $cfgTaintMap;
      }

      public function get($functionSignatureString){
      	     return $this->Map[$functionSignatureString];
      }

      public function contains($functionSignatureString){
      	     return isset($this->Map[$functionSignatureString]);
      }
}
?>