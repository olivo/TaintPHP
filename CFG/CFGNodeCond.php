<?php
include_once "CFGNode.php";

// Class that represents a conditional CFG node such as an if conditional and 
// loop header.
class CFGNodeCond extends CFGNode {

// The conditional expression in the node.
public $expr = NULL;

public function __construct() {

       parent::__construct();	      	     

       $expr = NULL;
}

// The true successor of a conditional node is the first successor.
public function getTrueSuccessor() {

       return $this->successors[0];
}

// The false successor of a conditional node is the second successor.
public function getFalseSuccessor() {

       return $this->successors[1];
}

// Printout function.
public function printCFGNode() {

       if ($this->expr) {

       	  print "[Conditional Node] : ";
	  printExpr($this->expr);
       }
       else {
       	      
       	  print "[Conditional Dummy Node]\n";   
       }
}
	
}
?>