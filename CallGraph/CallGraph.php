<?php

include_once "CallGraphNode.php";
include_once "FunctionSignature.php";
include_once(dirname(__FILE__) . '/../PHP-Parser-master/lib/bootstrap.php');

// Class representing an application's callgraph.
// The nodes of the graph represent function signatures,
// and an edge from f to g states that f calls g in its body.

class CallGraph {

      // Roots of the callgraph
      private $Roots;

      // Map from function signatures to nodes in the callgraph
      private $Nodes;
      
      public function __construct($roots = new SplObjectStorage(), $nodes = new SplObjectStorage()) {

      	     $this->Roots = $roots;
	     $this->Nodes = $nodes;
      }

      public function getCallGraphNode($functionRepresentation) {
      	     return $this->Nodes[$functionRepresentation];
      }      

      // Constructs call graph nodes and edges from the CFGs of a file.
      public function constructFileCallGraph($fileCFGInfo) {
      	     
	     $fileName = $fileCFGInfo->getFileName();
	     $className = $fileCFGInfo->getClassName();

	     $callGraph = new CallGraph();

	     // Add node for the main function.
	     $mainSignature = new FunctionSignature($fileName, $className, "");
	     $callGraph->addNodeFromFunctionRepresentation($mainSignature);
	     
	     // Process the CFG of the main function.
	     $callGraph->callGraphCFGProcessing($fileCFGInfo->getMainCFG(), $mainSignature);
	     
	     foreach($fileCFGInfo->getFunctionCFGs() as $functionName => $functionCFG) {
	     	  $functionSignature = new FunctionSignature($fileName, $className, $functionName);
	          $callGraph->addNodeFromFunctionRepresentation($functionSignature);
	     	  $callGraph->callGraphCFGProcessing($functionCFG, $functionSignature);
	     }

	     return $callGraph;
      }

      // Adds call graph nodes and edges from the CFG of the function.
      public function callGraphCFGProcessing($cfg, $signature) {

      	     $fileName = $signature->getFileName();
	     $className = $signature->getClassName();
	     $functionName = $signature->getFunctionName();

      	     // Perform BFS on CFG.
      	     $q = new SplQueue();
	     $nodeSet = new SplObjectStorage();

	     $q->enqueue($cfg->entry);

	     while(count($q)) {

	         $node = $q->dequeue();
		 $nodeSet->attach($node);

		 // TODO: check for function calls on non statement nodes.
		 if(CFGNode::isCFGNodeStmt($node)) {

		     $stmt = $node->getStmt();

	             if($stmt instanceof PhpParser\Node\Expr\MethodCall || $stmt instanceof PhpParser\Node\Expr\FuncCall 
		        || $stmt instanceof PhpParser\Node\Expr\StaticCall) {
		 	  // TODO: change the class to the holding object.
			  if($stmt instanceof PhpParser\Node\Expr\StaticCall) {
			  	   $invokedClassName = $stmt->class;
			  } else {
			    	   $invokedClassName = $className;
			  }
		 	  $invokedSignature = new FunctionSignature($fileName, $invokedClassName, $stmt->name);
			  $this->addNodeFromFunctionRepresentation($invokedSignature);
			  $this->addEdge($this->getCallGraphNode($signature), $this->getCallGraphNode($invokedSignature));
	              }
		  }
	
		  // Add unexplored successors.	  
		  foreach($cfg->getSuccessors() as $successor) {
		      if(!$nodeSet->contains($successor)) {
		          $nodeSet->attach($successor);
		      } else {
		      	  $q->enqueue($successor);
		      }
		  }
             }
      }

      // Add a node derived from a function signature
      // to the Nodes set if it doesn't exist already.
      public function addNodeFromFunctionRepresentation($functionRepresentation) {

	     $callGraphNode = new CallGraphNode($functionRepresentation);

      	     if(!$Nodes->contains($functionRepresentation)) {
	     	  $Nodes->attach($functionRepresentation, new CallGraphNode($callGraphNode));
	     }
      }
      
      // Add an edge between two call graph nodes.
      public function addEdge($source, $destination) {
      	     
	     $source->addSuccessor($destination);
	     $destination->addPredecessor($source);
      }
}
?>