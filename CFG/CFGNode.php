<?php

class CFGNode {

// The set of pointers to successor CFG nodes.
// On conditional nodes, the first successor is the true branch,
// and the second successor is the false branch.
public $successors = array();

// The pointer to the parents CFG node.
public $parents = array();

public function __construct() {
	      	     
	$this->successors = array();
	$this->parents = array();
}

public function isCFGNodeStmt($cfg_node) {

       return ($cfg_node instanceof CFGNodeStmt);
}

public function isCFGNodeCond($cfg_node) {

       return ($cfg_node instanceof CFGNodeCond);
}

public function isCFGNodeLoopHeader($cfg_node) {

       return ($cfg_node instanceof CFGNodeLoopHeader);
}

// Printing function for the node.
public function printCFGNode() {

       print "Generic CFG Node.\n";
}

}
?>