<?php

// Class representing information collected relating to a CFG.

class FileCFGInfo {

      private $mainCFG = NULL;
      private $functionCFGs = NULL;
      private $functionRepresentations = NULL;
      private $className = "";

      public function __construct($mainCFG, $functionCFGs = NULL, $functionRepresentations = NULL, $className = "") {

      	     $this->mainCFG = $mainCFG;
	     $this->functionCFGs = $functionCFGs;
	     $this->functionRepresentations = $functionRepresentations;
	     $this->className = "";
      }

      public function getMainCFG() {
      	     return $this->mainCFG;
      }

      public function getFunctionCFGs() {
      	     return $this->functionCFGs;
      }

      public function getFunctionRepresentations() {
      	     return $this->functionRepresentations;
      }
      
      public function getClassName() {
      	     return $this->className;
      }
}

?>