<?php

// Class that contains sources of taint.

include_once(dirname(__FILE__) . '/../PHP-Parser-master/lib/bootstrap.php');

class TaintSource {

      // Names of functions that contain sources of user taint.
      private static $userTaintedFunctions = array();

      // Names of arrays that contain sources of user taint.
      private static $UserTaintedArrays = array();

      // Names of functions that contain sources of secret taint.
      private static $SecretTaintedFunctions = array();

      // Names of functions that contain sources of secret taint.
      private static $SecretTaintedArrays = array();

      public function initializeTaintSources() {

      	     TaintSource::$UserTaintedFunctions = array();
      	     TaintSource::$UserTaintedArrays = array();
	     TaintSource::$SecretTaintedFunctions = array();
	     TaintSource::$SecretTaintedArrays = array();

	     TaintSource::addPredefinedTaintSources();
      }

      private static function addPredefinedTaintSources() {

      	      TaintSource::$UserTaintedArrays["_GET"] = 1;
	      TaintSource::$UserTaintedArrays["_POST"] = 1;

	      TaintSource::$SecretTaintedFunctions["mysql_query"] = 1;
      }

      // Function that returns true if the expression is user-tainted.
      public static function isUserTaintSource($expr) {

      	     if ($expr instanceof PhpParser\Node\Expr\ArrayDimFetch) {

	     	if(isset(TaintSource::$UserTaintedArrays[(string)$expr->var->name])) {
		    return True;
		}
	     }

	     return False;
      }

      // Function that returns true if the expression is secret-tainted.
      public static function isSecretTaintSource($expr) {

      	     if ($expr instanceof PhpParser\Node\Expr\MethodCall || $expr instanceof PhpParser\Node\Expr\FuncCall 
	         || $expr instanceof PhpParser\Node\Expr\StaticCall) {
	     	
		// Check if it's an invocation of a tainting function.
		if(isset(TaintSource::$SecretTaintedFunctions[(string)$expr->name])) {
		    return True;
		}

		// Check if any arguments is tainted.
		foreach($expr->args as $arg) {
		    if(TaintSource::isSecretTaintSource($arg)) {
		        return True;
		    }
		}
	     }
      
      	     return False;
      }
}
?>