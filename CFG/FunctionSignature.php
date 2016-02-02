<?php
	// Represents a function signature in terms of its name,
	// parameters and return type.
	// It's used in maps from functions to their CFGs.

	class FunctionSignature {
	      
	      // Name of the function.
	      public $name = NULL;

	      // Parameters of the function.
	      public $params = NULL;

	      // Return type of the function.
	      public $returnType = NULL;

	      public function __construct($name,$params=array(),$returnType=NULL) {
	      	     $this->name = $name;
		     $this->params = $params;
		     $this->returnType = $returnType;
	       }

	}

?>