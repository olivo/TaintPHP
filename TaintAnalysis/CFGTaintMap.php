<?php

// Class that contains all the taint maps for a CFG.

class CFGTaintMap {

      private $UserTaintMap = null;
      private $SecretTaintMap = null;
      private $ReturnsSecretTaint = False;
      private $ReturnsUserTaint = False;

      public function __construct($userTaintMap, $secretTaintMap) {

      	     $this->UserTaintMap = $userTaintMap;
      	     $this->SecretTaintMap = $secretTaintMap;
	     $this->ReturnsSecretTaint = False;
	     $this->ReturnsUserTaint = False;
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

      public function getReturnsSecretTaint() {
      	     return $this->ReturnsSecretTaint;
      }

      public function setReturnsSecretTaint($returnsSecretTaint) {
      	     $this->ReturnsSecretTaint = $returnsSecretTaint;
      }

      public function getReturnsUserTaint() {
      	     return $this->ReturnsUserTaint;
      }

      public function setReturnsUserTaint($returnsUserTaint) {
      	     $this->ReturnsUserTaint = $returnsUserTaint;
      }
}
?>