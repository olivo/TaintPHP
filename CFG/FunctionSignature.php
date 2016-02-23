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

	      // Return type of the function.
	      private $ReturnType = NULL;

	      public function __construct($fileName, $className, $functionName, $returnType) {
	      	     
		     $this->FileName = $fileName;
		     $this->ClassName = $className;
		     $this->FunctionName = $functionName;
		     $this->ReturnType = $returnType;
	       }
	}
?>