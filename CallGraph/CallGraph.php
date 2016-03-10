<?php

include_once "CallGraphNode.php";
include_once(dirname(__FILE__) . '/../CFG/FunctionSignature.php');
include_once(dirname(__FILE__) . '/../PHP-Parser-master/lib/bootstrap.php');

// Class representing an application's callgraph.
// The nodes of the graph represent function signatures,
// and an edge from f to g states that f calls g in its body.

class CallGraph {

      // Roots of the callgraph
      private $Roots;

      // Leaves of the callgraph
      private $Leaves;

      // Map from function signatures to nodes in the callgraph
      private $Nodes;
      
      public function __construct() {

      	     $this->Roots = array();
	     $this->Leaves = array();
	     $this->Nodes = array();
      }

      public function getNode($functionRepresentation) {
      	     return $this->Nodes[$functionRepresentation->toString()];
      }      

      public function getRoots() {
      	     return $this->Roots;
      }

      public function getLeaves() {
      	     return $this->Leaves;
      }

      // Add call graph nodes and edges from the CFGs of a file.
      public function addFileCallGraphInfo($fileCFGInfo, $functionSignatures) {
      	     
	     $fileName = $fileCFGInfo->getFileName();
	     $className = $fileCFGInfo->getClassName();

	     // Add node for the main function.
	     $mainSignature = new FunctionSignature($fileName, $className, "");
	     $mainCallGraphNode = new CallGraphNode($mainSignature);

	     $this->addNode($mainCallGraphNode);
	     
	     // Process the CFG of the main function.
	     $this->callGraphCFGProcessing($fileCFGInfo->getMainCFG(), $mainCallGraphNode, $functionSignatures);
	     
	     foreach($fileCFGInfo->getFunctionCFGs() as $functionName => $functionCFG) {

	     	  $functionSignature = new FunctionSignature($fileName, $className, $functionName);
		  $functionCallGraphNode = new CallGraphNode($functionSignature);
		  $this->addNode($functionCallGraphNode);

	     	  $this->callGraphCFGProcessing($functionCFG, $functionCallGraphNode, $functionSignatures);
	     }

      }

      // Adds call graph nodes and edges from the CFG of the function.
      public function callGraphCFGProcessing($cfg, $callGraphNode, $functionSignatures) {

      	     // Perform BFS on CFG.
      	     $q = new SplQueue();
	     $nodeSet = new SplObjectStorage();

	     $q->enqueue($cfg->entry);

	     while(!$q->isEmpty()) {

	         $node = $q->dequeue();
		 $nodeSet->attach($node);

		 $this->processCFGNode($node, $callGraphNode, $functionSignatures);

		  // Add unexplored successors.	  
		  foreach($node->getSuccessors() as $successor) {
		      if(!$nodeSet->contains($successor)) {
		          $nodeSet->attach($successor);
		      	  $q->enqueue($successor);
		      } 
		  }
             }
      }

      // Processes a CFG node, and potentially adds nodes and edges to the callgraph.
      public function processCFGNode($cfgNode, $callGraphNode, $functionSignatures) {

		 print "The node.\n";
		 $cfgNode->printCFGNode();
		 print "The class " . get_class($cfgNode) . "\n";

		 if(CFGNode::isCFGNodeStmt($cfgNode) && $cfgNode->stmt) {

		     $stmt = $cfgNode->getStmt();

		     if($stmt instanceof PhpParser\Node\Expt\Assign || $stmt instanceof PhpParser\Node\Expr\AssignOp) {
		         $this->processCFGNodeExpr($stmt->expr, $callGraphNode, $functionSignatures);
		     } else {
		       	 $this->processCFGNodeExpr($stmt, $callGraphNode, $functionSignatures);
		     }
		 } else if(CFGNode::isCFGNodeCond($cfgNode) && $cfgNode->expr) {

		     $expr = $cfgNode->expr;
		     if($expr instanceof PhpParser\Node\Expt\Assign || $expr instanceof PhpParser\Node\Expr\AssignOp) {
		         $this->processCFGNodeExpr($expr, $callGraphNode, $functionSignatures);
		     } else {
		       	 $this->processCFGNodeExpr($expr, $callGraphNode, $functionSignatures);
		     }
		 } else if(CFGNode::isCFGNodeLoopHeader($cfgNode)) {
		     // TODO: Process Foreach and For loops.
		     if($cfgNode->isWhileLoop()) {
		         $this->processCFGNodeExpr($cfgNode->expr->cond, $callGraphNode, $functionSignatures);
		     }
		 }
       }

      // Processes the expression from a CFG node, and potentially adds nodes and edges to the callgraph.
       public function processCFGNodeExpr($expr, $callGraphNode, $functionSignatures) {

	             if($expr instanceof PhpParser\Node\Expr\MethodCall || $expr instanceof PhpParser\Node\Expr\FuncCall 
		        || $expr instanceof PhpParser\Node\Expr\StaticCall) {

			 $invokedFunctionName = $expr->name;
			 $fileName = "";
			 $invokedClassName = "";

			 print "The name " . $invokedFunctionName . "\n";
			 /*			 			 
		 	 // TODO: change the class to the holding object.
			 if($stmt instanceof PhpParser\Node\Expr\StaticCall) {
			  	   $invokedClassName = $stmt->class;
			 } else if($stmt instanceof PhpParser\Node\Expr\FuncCall) {
			           $invokedClassName = $className;
			 } else {
			    	   // TODO: Need to infer class from call object.
			    	   $invokedClassName = "";
			 }
			 */
		 	 $invokedSignature = new FunctionSignature($fileName, $invokedClassName, $invokedFunctionName);
			 
			 // If there's a function with the invoked name, there is already a call graph node
			 // created previously, so add an edge from the current node to all of these nodes.
			 // Otherwise, it's a built-in library, so we add a generic node and add an edge.

			 if(isset($functionSignatures[(string)$invokedFunctionName])) {
			     foreach($functionSignatures[(string)$invokedFunctionName] as $name => $signature) {
			         $this->addEdge($callGraphNode, $this->getNode($signature));
			     }

			 } else {
			     // Check to see if the generic node has been added previously.

			     if($this->containsSignature($invokedSignature)) {
			     	 print "Found signature of generic node.\n";
			         $genericNode = $this->getNode($invokedSignature);
		             } else {
			         $genericNode = new CallGraphNode($invokedSignature);
			         $this->addNode($genericNode);
		             }

			     $this->addEdge($callGraphNode, $genericNode);
			 }

			 // Process the arguments of function/method calls.
			 foreach($expr->args as $arg) {
			     $this->processCFGNodeExpr($arg->value, $callGraphNode, $functionSignatures);
			 }
	             }
      }

      // Add all the nodes from a map of function signatures.
      public function addAllNodesFromFunctionSignatures($functionSignatures) {
          
	  foreach($functionSignatures as $name => $signatures) {
	      foreach($signatures as $signature) {
	          $this->addNodeFromFunctionSignature($signature);
	      }
	  }
      }

      // Add a node derived from a function signature
      // to the Nodes set if it doesn't exist already.
      public function addNodesFromFunctionSignature($functionSignature) {

      	     if(!isset($this->Nodes[$functionSignature->toString()])) {
	     	  $this->Nodes[$functionSignature->toString()] = new CallGraphNode($functionSignature);
	     }
      }

      public function addNode($callGraphNode) {

      	     if(!isset($this->Nodes[$callGraphNode->getFunctionRepresentation()->toString()])) {
	     	  $this->Nodes[$callGraphNode->getFunctionRepresentation()->toString()] = $callGraphNode;
	     }
      }
      
      // Add an edge between two call graph nodes.
      public function addEdge($source, $destination) {
      	     
	     $source->addSuccessor($destination);
	     $destination->addPredecessor($source);
      }

      // Checks if there is a call graph node associated to a function signature.
      public function containsSignature($signature) {
          return isset($this->Nodes[$signature->toString()]);
      }

      // WARNING: Only call this method after the call graph has been fully computed
      // Computes the set of roots of the call graph, i.e. nodes without an incoming edge.
      public function computeRootNodes() {

            foreach($this->Nodes as $signature => $node) {
	        if($node->isRoot()) {
		    $this->Roots[] = $node;
		}
	    }
      }

      // WARNING: Only call this method after the call graph has been fully computed
      // Computes the set of leaves of the call graph, i.e. nodes without an incoming edge.
      public function computeLeafNodes() {

            foreach($this->Nodes as $signature => $node) {
	        if($node->isLeaf()) {
		    $this->Leaves[] = $node;
		}
	    }
      }

      // Call graph printout function.
      public function printCallGraph() {
      	     foreach($this->Nodes as $functionSignature => $node) {
	         print("=== NODE ===\n");
	         $node->printCallGraphNode();
	     }
      }

      // Root node printout.
      public function printCallGraphRoots() {
      	     print "=== ROOTS ===\n";
      	     foreach($this->Roots as $node) {
	         $node->printCallGraphNode();
	     }
	     print "=== ===\n";
      }

      // Leaf node printout.
      public function printCallGraphLeaves() {
      	     print "=== LEAVES ===\n";
      	     foreach($this->Leaves as $node) {
	         $node->printCallGraphNode();
	     }
	     print "=== ===\n";
      }
}
?>