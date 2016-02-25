<?php
// Class representing a node of the callgraph.

class CallGraphNode {

      // Representation of a function.
      private $FunctionRepresentation = NULL;

      // Array of predecessors in the call graph.
      private $Predecessors[];

      // Array of successors in the call graph.
      private $Successors[];

      public function __construct($functionRepresentation, $predecessors = array(), $successors = array()) {
      	     
	     $this->FunctionRepresentation = $functionRepresentation;
	     $this->Predecessors = $predecessors;
	     $this->Successors = $successors;
      }

      public function getFunctionRepresentation() {
      	     return $this->FunctionRepresentation;
      }

      public function addPredecessor($callGraphNode) {
      	     $Predecessors[] = $callGraphNode;
      }

      public function addSuccessor($callGraphNode) {
      	     $Successors[] = $callGraphNode;
      }
}
?>