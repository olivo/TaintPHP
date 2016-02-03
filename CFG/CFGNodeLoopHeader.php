<?php
include_once "CFGNode.php";
include_once(dirname(__FILE__) . '/../PHP-Parser-master/lib/bootstrap.php');

// Class that represents a loop header.
class CFGNodeLoopHeader extends CFGNode {

// Types of loops.
const FOR_LOOP = 0;
const WHILE_LOOP = 1;
const FOREACH_LOOP = 2;

// Loop expression associated with the header.
public $expr = NULL;

// Type of loop.
public $loop_type = NULL;

public function __construct() {

       parent::__construct();	      	     

       $this->expr = NULL;
       $this->loop_type = NULL;
}

// The loop entry node is the first successor.
public function getLoopEntry() {

       return $this->successors[0];
}

// The loop exit node is the second successor.
public function getLoopExit() {

       return $this->successors[1];
}

public function isForLoop() {

       return $this->loop_type == CFGNodeLoopHeader::FOR_LOOP;
}

public function isWhileLoop() {

       return $this->loop_type == CFGNodeLoopHeader::WHILE_LOOP;
}

public function isForeachLoop() {

       return $this->loop_type == CFGNodeLoopHeader::FOREACH_LOOP;
}

// Printout function.
public function printCFGNode() {

      $prettyPrinter = new PhpParser\PrettyPrinter\Standard;

      print "[Loop Header Node] : ";

      if ($this->loop_type == CFGNodeLoopHeader::FOR_LOOP) {

      	 print "[For Loop] : \n";

	 print "(";

	 foreach ($this->expr->init as $initExpr) {
	 	 
		 print ($prettyPrinter->prettyPrintExpr($initExpr)) . " ; ";	 
	 }

	 print ")\n";

	 print "(";

	 foreach ($this->expr->cond as $condExpr) {
	 	 
		 print ($prettyPrinter->prettyPrintExpr($condExpr)) . " ; ";	 
	 }

	 print ")\n";

	 print "(";

	 foreach ($this->expr->loop as $loopExpr) {
	 	 
		 print ($prettyPrinter->prettyPrintExpr($loopExpr)) . " ; ";	 
	 }

	 print ")\n";

      }
      else if ($this->loop_type == CFGNodeLoopHeader::FOREACH_LOOP) {

      	 print "[Foreach Loop] : \n";

	 print ($prettyPrinter->prettyPrintExpr($this->expr->expr)) . " ;\n";	 
	 print ($prettyPrinter->prettyPrintExpr($this->expr->keyVar)) . " ;\n";	 
	 print ($prettyPrinter->prettyPrintExpr($this->expr->valueVar)) . " ;\n";
      }
      else if ($this->loop_type == CFGNodeLoopHeader::WHILE_LOOP) {

      	 print "[While Loop] : \n";

	 print ($prettyPrinter->prettyPrintExpr($this->expr->cond)) . " ;\n";	 
      }
      else {

      	 print "Unrecognized Loop.\n";
      }

}
	
}
?>