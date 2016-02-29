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

      // Map from function signatures to nodes in the callgraph
      private $Nodes;
      
      public function __construct() {

      	     $this->Roots = array();
	     $this->Nodes = array();
      }

      public function getCallGraphNode($functionRepresentation) {
      	     return $this->Nodes[$functionRepresentation->toString()];
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

		 // TODO: check for function calls on non statement nodes.
		 if(CFGNode::isCFGNodeStmt($node)) {

		     $stmt = $node->getStmt();

	             if($stmt instanceof PhpParser\Node\Expr\MethodCall || $stmt instanceof PhpParser\Node\Expr\FuncCall 
		        || $stmt instanceof PhpParser\Node\Expr\StaticCall) {

		         print "The node.\n";
		         $node->printCFGNode();
		         print "The class " . get_class($stmt) . "\n";
			 $invokedFunctionName = $stmt->name;
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
			         $this->addEdge($callGraphNode, $this->getCallGraphNode($signature));
			     }

			 } else {
			     // Check to see if the generic node has been added previously.

			     if(isset($this->Nodes[$invokedSignature->toString()])) {
			     	 print "Found signature of generic node.\n";
			         $genericNode = $this->getCallGraphNode($invokedSignature);
		             } else {
			         $genericNode = new CallGraphNode($invokedSignature);
			         $this->addNode($genericNode);
		             }

			     $this->addEdge($callGraphNode, $genericNode);
			 }
	              }
		  }
	
		  // Add unexplored successors.	  
		  foreach($node->getSuccessors() as $successor) {
		      if(!$nodeSet->contains($successor)) {
		          $nodeSet->attach($successor);
		      	  $q->enqueue($successor);
		      } 
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

      // Call graph printout function.
      public function printCallGraph() {
      	     foreach($this->Nodes as $functionSignature => $node) {
	         print("=== NODE ===\n");
	         $node->printCallGraphNode();
	     }
      }
}
?>