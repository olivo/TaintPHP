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

      	     $UserTaintedFunctions = array();
      	     $UserTaintedArrays = array();
	     $SecretTaintedFunctions = array();
	     $SecretTaintedArrays = array();

	     TaintSource::addPredefinedTaintSources();
      }

      private static function addPredefinedTaintSources() {

      	      $UserTaintedArrays["_GET"] = 1;
	      $UserTaintedArrays["_POST"] = 1;

	      $SecretTaintedFunctions["mysql_query"] = 1;
      }

      // Function that returns true if the expression is user-tainted.
      public static function isUserTaintSource($expr) {

      	     if ($expr instanceof PhpParser\Node\Expr\ArrayDimFetch) {

	     	if(isset($UserTaintedArrays[$expr->var->name])) {
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
		if(isset($SecretTaintedFunctions[$expr->name])) {
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