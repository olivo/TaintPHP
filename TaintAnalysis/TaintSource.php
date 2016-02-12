<?php

// Class that contains sources of taint.

static class TaintSource {

      // Names of functions that contain sources of user taint.
      private static $userTaintedFunctions = array();

      // Names of arrays that contain sources of user taint.
      private static $userTaintedArrays = array();

      // Names of functions that contain sources of secret taint.
      private static $secretTaintedFunctions = array();

      // Names of functions that contain sources of secret taint.
      private static $secretTaintedArrays = array();

      public function __construct() {

      	     $this->userTaintedFunctions = array();
      	     $this->userTaintedArrays = array();
	     $this->secretTaintedFunctions = array();
	     $this->secretTaintedArrays = array();

	     addPredefinedTaintSources();
      }

      private void addPredefinedTaintSources() {

      	      $this->userTaintedArrays["_GET"] = 1;      	      
	      $this->userTaintedArrays["_GET"] = 1;      	      
      }
}
?>