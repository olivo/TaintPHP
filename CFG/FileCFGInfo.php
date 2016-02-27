<?php

// Class representing information collected relating to a CFG.

class FileCFGInfo {

      private $mainCFG = NULL;
      private $functionCFGs = NULL;
      private $functionRepresentations = NULL;

      public function __construct($mainCFG, $functionCFGs = NULL, $functionRepresentations = NULL) {

      	     $this->mainCFG = $mainCFG;
	     $this->functionCFGs = $functionCFGs;
	     $this->functionRepresentations = $functionRepresentations;
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
}

?>