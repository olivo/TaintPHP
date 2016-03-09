<?php

// Class representing information collected relating to a CFG.

class FileCFGInfo {

      private $mainCFG = NULL;
      private $functionCFGs = NULL;
      private $functionRepresentations = NULL;
      private $className = "";
      private $fileName = "";

      public function __construct($mainCFG, $functionCFGs = NULL, $functionRepresentations = NULL, $className = "", $fileName = "") {

      	     $this->mainCFG = $mainCFG;
	     $this->functionCFGs = $functionCFGs;
	     $this->functionRepresentations = $functionRepresentations;
	     $this->className = $className;
	     $this->fileName = $fileName;
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

      public function getFileName() {
      	     return $this->fileName;
      }

      public function getCFG($functionSignature) {

      	     if($functionSignature->isMain()) {
	         return $this->mainCFG;
	     } else {
	         return $this->functionCFGs[$functionSignature->getFunctionName()];
	     }
      }
}

?>