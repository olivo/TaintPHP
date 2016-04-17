<?php

// Class that contains all the taint information for a file,
// including its main function and auxiliary functions.

class FileTaintMap {

      private $MainTaintMap = null;
      private $FunctionTaintMaps = array();

      public function __construct($mainTaintMap, $functionTaintMaps) {

      	     $this->MainTaintMap = $mainTaintMap;
	     $this->FunctionTaintMaps = $functionTaintMaps;
      }

      public function getMainTaintMap() {

      	     return $this->MainTaintMap;
      }

      public function getFunctionTaintMaps() {

      	     return $this->FunctionTaintMaps;
      }

      public function setMainTaintMap($mainTaintMap) {

      	     $this->MainTaintMap = $mainTaintMap;
      }

      public function setFunctionTaintMaps($functionTaintMaps) {

      	     $this->FunctionTaintMaps = $functionTaintMaps;
      }

      public function addFunctionTaintMap($functionName, $taintMap) {

      	     $this->FunctionTaintMaps[$functionName] = $taintMap;
      }
}
?>