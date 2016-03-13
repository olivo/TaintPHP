<?php

// Class that contains all the taint maps for a CFG.

class CFGTaintMap {

      private $UserTaintMap = null;
      private $SecretTaintMap = null;
      private $ReturnsTaint = False;

      public function __construct($userTaintMap, $secretTaintMap) {

      	     $this->UserTaintMap = $userTaintMap;
      	     $this->SecretTaintMap = $secretTaintMap;
	     $this->ReturnsTaint = False;
      }

      public function setUserTaintMap($userTaintMap) {

      	     $this->UserTaintMap = $userTaintMap;
      }

      public function setSecretTaintMap($secretTaintMap) {

      	     $this->SecretTaintMap = $secretTaintMap;
      }

      public function getUserTaintMap() {

      	     return $this->UserTaintMap;
      }

      public function getSecretTaintMap() {

      	     return $this->SecretTaintMap;
      }

      public function getReturnsTaint() {
      	     return $this->ReturnsTaint;
      }

      public function setReturnsTaint($returnsTaint) {
      	     $this->ReturnsTaint = $returnsTaint;
      }
}
?>