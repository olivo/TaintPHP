<?php
// Represents a map from function names to potential function signature targets.
class FunctionSignatureMap {

      private $map = array();

      public function __construct() {
      	     $map = array();
      }

      public function get($functionName) {
      	     return $this->map[(string)$functionName];
      }
      
      public function contains($functionName) {
      	     return isset($this->map[(string)$functionName]);
      }

      public function add($functionSignature) {
      	     if(!$this->map->contains($functionSignature->getFunctionName())) {
	         $this->map[$functionSignature->getFunctionName()] = array();
	     }
	     $this->map[$functionSignature->getFunctionName()][] = $functionSignature;
      }

      public function addAll($functionSignatures) {
      	     foreach($functionSignatures as $name => $signature) {
	         $this->add($signature);
	     }
      }


      public function printFunctionSignatureMap() {
      	     foreach($this->map as $functionName => $functionSignatures) {
	         print "[" . $functionName . "]" . "\n";
		 foreach($functionSignatures as $functionSignature) {
		     $functionSignature->printFunctionSignature();			     
		 }
	     }
      }
}
?>