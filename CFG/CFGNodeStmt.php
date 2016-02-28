<?php
include_once "CFGNode.php";
include_once "StmtProcessing.php";

// Class that corresponds to an individual statement CFG node (assignment, method calls, etc.).

class CFGNodeStmt extends CFGNode {

// The statement contained in the node.
public $stmt = NULL;

// Determines whether the successor of this node is reached by a back edge.
public $has_backedge = FALSE;

public function __construct() {

       parent::__construct();

       $this->stmt = NULL;

       $this->back_edge = FALSE;
}

public function getStmt() {
       return $this->stmt;
}

// Printout function.
public function printCFGNode() {

       if ($this->stmt) {

              print "[Stmt Node] : ";
       	      printStmts(array($this->stmt));
       }
       else {

              print "[Dummy Node]\n";
       }
}
	
}
?>