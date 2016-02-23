<?php
// Class representing an application's callgraph.
// The nodes of the graph represent function signatures,
// and an edge from f to g states that f calls g in its body.

class CallGraph {

      // Roots of the callgraph
      private $Roots = new SplObjectStorage();

      // Map from function signatures to nodes in the callgraph
      private $Nodes = new SplObjectStorage();
      
      public function __construct($roots, $nodes) {

      	     $this->Roots = $roots;
	     $this->Nodes = $nodes;
      }

}
?>