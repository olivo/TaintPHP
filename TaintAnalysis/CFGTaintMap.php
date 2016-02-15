<?php

// Class that contains all the taint maps for a CFG.

class CFGTaintMap {

      private $UserTaintMap = null;
      private $SecretTaintMap = null;

      public function __construct($userTaintMap, $secretTaintMap) {

      	     $this->setUserTaintMap($userTaintMap);
      	     $this->setSecretTaintMap($secretTaintMap);
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
}
?>