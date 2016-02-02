<?php

// Class for representing a set of tainted variables.

class TaintedVariables {

      private $set = array();

      public function __construct() {

      	     $this->set = array();
      }

      public function contains($var) {

      	     return array_key_exists($var, $this->set);
      }

      public function attach($var) {

      	     print "Attaching " . $var . "\n";
      	     $this->set[$var] = true;
	     $this->printTaintedVariables();
      }

      public function addAll($tainted_variable_set) {

      	     foreach ($tainted_variable_set->set as $var => $val) {
	     	     print "Adding " . $var . " " . $val . "\n";
	     	     $this->set[$var] = $val;
	     }
      }

      public function count() {

      	     return count(array_keys($this->set));
      }

      public function printTaintedVariables() {

      	     print "The size is " . $this->count() . "\n";
      	     print "[";
	     foreach ($this->set as $var => $val) {

	     	     print " " . $var;
	     }
	     print "]\n";
      }
}
?>