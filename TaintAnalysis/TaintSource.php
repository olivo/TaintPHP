<?php

// Class that contains sources of taint.

include_once(dirname(__FILE__) . '/../PHP-Parser-master/lib/bootstrap.php');

class TaintSource {

      // Names of functions that contain sources of user taint.
      private static $userTaintedFunctions = array();

      // Names of arrays that contain sources of user taint.
      private static $userTaintedArrays = array();

      // Names of functions that contain sources of secret taint.
      private static $secretTaintedFunctions = array();

      // Names of functions that contain sources of secret taint.
      private static $secretTaintedArrays = array();

      public function initializeTaintSources() {

      	     $userTaintedFunctions = array();
      	     $userTaintedArrays = array();
	     $secretTaintedFunctions = array();
	     $secretTaintedArrays = array();

	     TaintSource::addPredefinedTaintSources();
      }

      private static function addPredefinedTaintSources() {

      	      $userTaintedArrays["_GET"] = 1;
	      $userTaintedArrays["_POST"] = 1;

	      $secretTaintedFunctions["mysql_query"] = 1;
      }

      public static function isUserTaintSource($expr) {

      	     if ($expr instanceof PhpParser\Node\Expr\ArrayDimFetch) {

	     	return isset($userTaintedArrays[$expr->var->name]);
	     }

	     return False;
      }

      public static function isSecretTaintSource($expr) {

      	     if ($expr instanceof PhpParser\Node\Expr\MethodCall) {
	     	
		return isset($userTaintedFunctions[$expr->name]);
	     }
      
      	     return False;
      }

}
?>