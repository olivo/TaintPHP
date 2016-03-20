<?php
	// Represents a function signature in terms of its occuring file, class, function name and return type.
	// It's used in maps from functions to their CFGs.

	class FunctionSignature {
	      
	      // Name of the file where the function defined.
	      private $FileName = NULL;

	      // Name of the class where the function is defined.	      
	      private $ClassName = NULL;

	      // Name of the function.
	      private $FunctionName = NULL;

	      public function __construct($fileName, $className, $functionName) {
	      	     
		     $this->FileName = $fileName;
		     $this->ClassName = $className;
		     $this->FunctionName = $functionName;
	      }
	      
	      public function getFileName() {
	      	     return $this->FileName;
	      }

	      public function getClassName() {
	      	     return $this->ClassName;
	      }

	      public function getFunctionName() {
	      	     return $this->FunctionName;
	      }

	      public function isMain() {
	      	     return strcmp($this->FunctionName, "") == 0;
	      }

	      public function toString() {
	      	     return $this->FileName . "," . $this->ClassName . "," . $this->FunctionName;
	      }

	      public function printFunctionSignature() {
	      	     print "(" . $this->getFileName() . ", " . $this->getClassName() . ", " . $this->getFunctionName() . ")" . "\n";
	      }
	}
?>