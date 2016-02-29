<?php

include_once "CFGNode.php";
include_once "CFGNodeCond.php";
include_once "CFGNodeLoopHeader.php";
include_once "CFGNodeStmt.php";
include_once "FileCFGInfo.php";
include_once "FunctionSignature.php";
include_once(dirname(__FILE__) . '/../PHP-Parser-master/lib/bootstrap.php');
include_once "StmtProcessing.php";


// Class representing an entire CFG.
// It contains an entry CFG node, and an exit CFG node.
// The CFG can be traversed by going through the successors of 
// the CFG nodes until the exit node.

class CFG {

      // Entry node.
      public $entry = NULL;

      // Exit node.
      public $exit = NULL;
      
      function __construct() {

      	       $this->entry = new CFGNode();
	       $this->exit = new CFGNode();
      }

	
      // Construct the Control Flow Graph (CFG) from a 
      // sequence of statements.
      static function construct_cfg($stmts) {

	       $prettyPrinter = new PhpParser\PrettyPrinter\Standard;

	       // Creating an empty entry node.	
	       $cfg = new CFG();

	       $entry = new CFGNode();

	       $cfg->entry = $entry;
	       
	       $current_node = $entry;

	       foreach($stmts as $stmt)  {

	         print "Processing statement:\n";
	       	 printStmts(array($stmt));

		 // Assignment statement.
	       	 if ($stmt instanceof PhpParser\Node\Expr\Assign) {

		 	  print "Found assignment statement\n";
			  $assign_node = CFG::processExprAssign($stmt);
			  $current_node->successors[] = $assign_node;
			  $assign_node->parents[] = $current_node;
			  $current_node = $assign_node;
			  print "Constructed assignment node\n";
		  }
		 // Assignment with operation statement.
	       	 else if ($stmt instanceof PhpParser\Node\Expr\AssignOp) {

		 	  print "Found assignment with operation statement\n";
			  $assign_op_node = CFG::processExprAssignOp($stmt);
			  $current_node->successors[] = $assign_op_node;
			  $assign_op_node->parents[] = $current_node;
			  $current_node = $assign_op_node;
			  print "Constructed assignment with operation node\n";
		  }
		 //  Pre-decrement expression.
	       	 else if ($stmt instanceof PhpParser\Node\Expr\PreDec) {

		 	  print "Found a pre-decrement expression\n";
			  $predec_node = CFG::processExprPreDec($stmt);
			  $current_node->successors[] = $predec_node;
			  $predec_node->parents[] = $current_node;
			  $current_node = $predec_node;
			  print "Constructed pre-decrement expression.\n";
		  }
		 // Unset statement.
	       	 else if ($stmt instanceof PhpParser\Node\Stmt\Unset_) {

		 	  print "Found unset statement\n";
			  $unset_node = CFG::processStmtUnset($stmt);
			  $current_node->successors[] = $unset_node;
			  $unset_node->parents[] = $current_node;
			  $current_node = $unset_node;
			  print "Constructed unset node\n";
		  }
		 // Global declaration statement.
	       	 else if ($stmt instanceof PhpParser\Node\Stmt\Global_) {

		 	  print "Found global statement\n";
			  $global_node = CFG::processStmtGlobal($stmt);
			  $current_node->successors[] = $global_node;
			  $global_node->parents[] = $current_node;
			  $current_node = $global_node;
			  print "Constructed global node\n";
		  }
		 // Break statement.
	       	 else if ($stmt instanceof PhpParser\Node\Stmt\Break_) {

		 	  print "Found break statement\n";
			  $break_node = CFG::processStmtBreak($stmt);
			  $current_node->successors[] = $break_node;
			  $break_node->parents[] = $current_node;
			  $current_node = $break_node;
			  print "Constructed break node\n";
		  }
		 // Return statement.
	       	 else if ($stmt instanceof PhpParser\Node\Stmt\Return_) {

		 	  print "Found return statement\n";
			  $return_node = CFG::processStmtReturn($stmt);
			  $current_node->successors[] = $return_node;
			  $return_node->parents[] = $current_node;
			  $current_node = $return_node;
			  print "Constructed return node\n";
		  }
		  // If statement.
		  else if ($stmt instanceof PhpParser\Node\Stmt\If_) {

		          // Note: What happens on if-elsif-elsif-else ?
		          print "Found conditional statement\n";
			  $if_nodes = CFG::processStmtIf($stmt);

			  // Connect the current node with the 
			  // conditional node of the if.
			  $current_node->successors[]=$if_nodes[0];

			  // The previously processed node is the parent of 
			  // the conditional node.
			  $if_nodes[0]->parents[] = $current_node; 

			  // Make the current node, the node that 
			  // joins the branches of the if.
			  $current_node = $if_nodes[1];
			  
		       	  print "Constructed conditional node\n";

		  // Method call statement.
		  } else if ($stmt instanceof PhpParser\Node\Expr\MethodCall) {

		 	  print "Found method call statement\n";
			  $method_call_node = CFG::processExprMethodCall($stmt);
			  $current_node->successors[] = $method_call_node;
			  $method_call_node->parents[] = $current_node;
			  $current_node = $method_call_node;
			  print "Constructed method call node\n";

		  // Function call statement.
		  } else if ($stmt instanceof PhpParser\Node\Expr\FuncCall) {

		 	  print "Found function call statement\n";
			  $function_call_node = CFG::processExprFuncCall($stmt);
			  $current_node->successors[] = $function_call_node;
			  $function_call_node->parents[] = $current_node;
			  $current_node = $function_call_node;
			  print "Constructed function call node\n";
		  
		  // Static function call statement.
		  } else if ($stmt instanceof PhpParser\Node\Expr\StaticCall) {

		 	  print "Found static call statement\n";
			  $static_call_node = CFG::processExprStaticCall($stmt);
			  $current_node->successors[] = $static_call_node;
			  $static_call_node->parents[] = $current_node;
			  $current_node = $static_call_node;
			  print "Constructed static call node\n";		  

		  // Loops: Foreach, For or While statement.
		  } else if ($stmt instanceof PhpParser\Node\Stmt\Foreach_ || $stmt instanceof PhpParser\Node\Stmt\For_ 
		             || $stmt instanceof PhpParser\Node\Stmt\While_) {

		 	  print "Found " . gettype($stmt) . " statement\n";
			  // Returns a pair with the loop header 
			  // and a dummy exit node that follows the
			  // loop.
			  $loop_nodes = CFG::processStmtLoop($stmt);

			  // Connect the current node to the loop header.
			  $current_node->successors[] = $loop_nodes[0];
			  $loop_nodes[0]->parents[] = $current_node;

			  // Make the dummy exit node of the loop
			  // the current node.
			  $current_node = $loop_nodes[1];

			  print "Constructed " . gettype($stmt) . " node\n";
		  } else {	       		      
		       	  print "WARNING: Couldn't construct CFG node.\n";
		  	  print "The statement is of class ".(get_class($stmt))."\n";

	          	  print "Has keys\n";

			  /*
		  	  foreach($stmt as $key => $value) {
			  	print "Key=".($key)."\n";
		   	  }
			  */
			  
			  // Constructing dummy node.
			  $dummy_node = new CFGNode();
			  $current_node->successors[] = $dummy_node;
			  $dummy_node->parents[] = $current_node;
			  $current_node = $dummy_node;
		  }




	        }
	 
	// Create a dummy exit node, and make a pointer
	// from the last processed node to the exit node.
	$cfg->exit = new CFGNode();
	$current_node->successors[] = $cfg->exit;
	$cfg->exit->parents[] = $current_node;

	return $cfg;
					
}

// Constructs a node for an assignment expression.
static function processExprAssign($exprAssign) {

	// $exprAssign has keys 'var' and 'expr'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $exprAssign;

	return $cfg_node;
}

// Constructs a node for an assignment with operation expression.
static function processExprAssignOp($exprAssignOp) {

	// $exprAssign has keys 'var' and 'expr'.
	// It can be extended by classes Div, Minus, Plus, etc.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $exprAssignOp;

	return $cfg_node;
}

// Constructs a node for a pre-decrement expression.
static function processExprPreDec($exprPreDec) {

	// $exprPreDec has key 'var'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $exprPreDec;

	return $cfg_node;
}

// Constructs a node for an assignment expression.
static function processStmtUnset($stmtUnset) {

	// $stmtUnset has keys 'vars'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $stmtUnset;

	return $cfg_node;
}

// Constructs a node for a global declaration statement.
static function processStmtGlobal($stmtGlobal) {

	// $stmtGlobal has keys 'vars'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $stmtGlobal;

	return $cfg_node;
}

// Constructs a node for a break statement.
static function processStmtBreak($stmtBreak) {

	// $stmtBreak has keys 'num'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $stmtBreak;

	return $cfg_node;
}

// Constructs a node for a return statement.
static function processStmtReturn($stmtReturn) {

	// $stmt has key 'expr'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $stmtReturn;

	return $cfg_node;
}


// WARNING: Doesn't handle interprocedural case yet.
// Constructs a node for a method call expression.
static function processExprMethodCall($exprMethodCall) {

	// $exprMethodCall has keys 'var', 'name' and 'args'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $exprMethodCall;

	return $cfg_node;
}

// WARNING: Doesn't handle interprocedural case yet.
// Constructs a node for a function call expression.
static function processExprFuncCall($exprFuncCall) {

	// exprFuncCall has keys 'name' and 'args'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $exprFuncCall;

	return $cfg_node;
}

// WARNING: Doesn't handle interprocedural case yet.
// Constructs a node for a static call expression.
static function processExprStaticCall($exprStaticCall) {

	// exprFuncCall has keys 'class', 'name' and 'args'.

	$cfg_node = new CFGNodeStmt();
	$cfg_node->stmt = $exprStaticCall;

	return $cfg_node;
}

// Constructs a node for an if statement.
// 1) Creates a node for each condition, and constructs an array  called condition array.
// 2) Creates a CFG for each conditioned block, and constructs an array called body array.
// 3) It creates a dummy exit node that all the statement blocks will converge into.
// 4) Links all the exits of the body CFGs to the exit dummy node.
// 5) Links each condition node to its corresponding body array.
// 6) Links each condition node to its next condition node.
// 7) Links the last condition node to the next body CFG if it exists, or the dummy exit node otherwise.
// It returns the first condition node and dummy exit nodes.

static function processStmtIf($stmtIf) {

	// stmtIf has keys 'cond', 'stmts', 'elseifs', and 'else'.

	// Array of CFG nodes representing the conditions.
	$cond_nodes = array();

	// Array of CFGs representing the bodies of each conditional branch.
	$body_nodes = array();

	// Create and add the top condition node.
	$cond_node = new CFGNodeCond();
	$cond_node->expr = $stmtIf->cond;
	$cond_nodes[] = $cond_node;
	
	// Create and add the true branch of the top condition node.
	$body_node = CFG::construct_cfg($stmtIf->stmts);
	$body_nodes[] = $body_node;

	// Create and add the condition nodes for the else if clauses.
	foreach($stmtIf->elseifs as $elseif) {

		$cond_node = new CFGNodeCond();
		$cond_node->expr = $elseif->cond;		
		$cond_nodes[] = $cond_node;

		$body_node = CFG::construct_cfg($elseif->stmts);
		$body_nodes[] = $body_node;
	
	 }

	 // Create and add the else body node if it exists
	 if ($stmtIf->else) {
	 	$body_node = CFG::construct_cfg($stmtIf->else->stmts);
		$body_nodes[] = $body_node;
	  }

	// Create a dummy exit node from which the branch CFGs point to.
	$dummy_exit = new CFGNodeStmt();

	// Link the exits of all the body nodes to the dummy exit node.
	foreach($body_nodes as $body_node) { 
	
		    $body_node->exit->successors[] = $dummy_exit;
		    $dummy_exit->parents[] = $body_node->exit;
	}

	// Link the condition nodes to their corresponding entries of the body nodes.
	for($i=0;$i<count($cond_nodes);$i++) {

		$cond_nodes[$i]->successors[] = $body_nodes[$i]->entry;
		$body_nodes[$i]->entry->parents[] = $cond_nodes[$i];
	}

	// Link each condition node to the next condition node.
	for($i=0;$i<count($cond_nodes)-1;$i++) {

		$cond_nodes[$i]->successors[] = $cond_nodes[$i+1];
		$cond_nodes[$i+1]->parents[] = $cond_nodes[$i];
	}

	//Link the last condition node to the next body node if it exists or the dummy exit node.
	$last_index = count($cond_nodes)-1;
	if ($last_index+1<count($body_nodes)) {
	
		$cond_nodes[$last_index]->successors[] = $body_nodes[$last_index+1]->entry;
		$body_nodes[$last_index+1]->entry->parents[] = $cond_nodes[$last_index];
	} else {
	
		$cond_nodes[$last_index]->successors[] = $dummy_exit;
		$dummy_exit->parents[] = $cond_nodes[$last_index];
	}

	// Return the top condition node and the dummy exit node.
	return array($cond_nodes[0],$dummy_exit);
}

// Constructs a node for an include expression.
// WARNING: Not implemented;
static function processExprInclude($exprInclude) {

	// exprInclude has keys 'expr' and 'type'.
	print("WARNING:Expr Include not handled properly.\n");
	$cfg_node = new CFGNodeStmt();

	return $cfg_node;
}


// Constructs a node of loop.
// 1) Creates a CFG node for the loop condition that
// acts as the loop header.
// 2) Creates a CFG of the body of the loop.
// 3) Links the exit of the body CFG to the loop header CFG.
// 4) Creates an exit dummy node.
// 5) Links the condition node to the CFG of the body and the dummy
// exit node.
static function processStmtLoop($stmtLoop) {

	// Create the CFG node for the loop header.
	$header_node = new CFGNodeLoopHeader();

	$header_node->expr = $stmtLoop;

	if ($stmtLoop instanceof PhpParser\Node\Stmt\Foreach_) {

	   $header_node->loop_type = CFGNodeLoopHeader::FOREACH_LOOP;
	}
	else if ($stmtLoop instanceof PhpParser\Node\Stmt\For_) {

	   $header_node->loop_type = CFGNodeLoopHeader::FOR_LOOP;
	}
	else if ($stmtLoop instanceof PhpParser\Node\Stmt\While_) {

	   $header_node->loop_type = CFGNodeLoopHeader::WHILE_LOOP;
	}
	else {

	   print "ERROR Unrecognized loop type while construction CFG node.\n";
	}

	// Create the dummy exit node.
	$dummy_exit = new CFGNodeStmt();

	// Create the CFG for the body of the loop.
	$body_cfg = CFG::construct_cfg($stmtLoop->stmts);

	// Link the exit of the body CFG to the loop header.
	$body_cfg->exit->successors[] = $header_node;
	$header_node->parents[] = $body_cfg->exit;

	// Assert that the edge from the exit of the body CFG to 
	// the loop header is a backedge
	$body_cfg->exit->has_backedge = TRUE;

	// Link the header node to the entry of the body CFG.
	$header_node->successors[] = $body_cfg->entry;
	$body_cfg->entry->parents[] = $header_node;

	// Link the header node to the dummy exit node.
	$header_node->successors[] = $dummy_exit;
	$dummy_exit->parents[] = $header_node;

	return array($header_node,$dummy_exit);
}

// Prints a CFG starting from the root node.
// WARNING: Only printing the true branches of the conditionals.
function print_preorder_cfg() {
	 
	 print "Starting to print CFG\n";

	 $visited = new SplObjectStorage();

	 CFG::print_preorder($this->entry, $visited);
}

// TODO: Finish writing the preorder traversal.
function print_preorder($cfg_node, $visited) {

	 if (!$cfg_node || $visited->contains($cfg_node)) {
	 		return;
	 }

	 $visited->attach($cfg_node);

	 if (CFGNode::isCFGNodeStmt($cfg_node)) {

	    if ($cfg_node->stmt) {
	       printStmts(array($cfg_node->stmt));
	    }
	 } else if (CFGNode::isCFGNodeCond($cfg_node)) {
	       // TODO: Figure out how to print 
	       // conditional nodes.
	       print("WARNING: Conditional node not printed\n");
	 }
	 
	 for ($i = 0; $i < count($cfg_node->successors); $i++) {

	     CFG::print_preorder($cfg_node->successors[$i], $visited);
	 }
}


	// Obtain the function declarations from a list of statements,
	// and return the mapping from function names to their CFGs
	// , as well as the mapping from function names to function
	// signatures.
	static function process_function_definitions($stmts) {
	       
	       // Map from function names to CFG.
	       $cfgMap = array();

	       // Map from function name to function signature.
	       $signatureMap = array();

	       foreach($stmts as $stmt) {

	       		      if($stmt instanceof PhpParser\Node\Stmt\Function_ || $stmt instanceof PhpParser\Node\Stmt\ClassMethod) {
			      // TODO include file and class name to the function signature.
			      	       $signature = new FunctionSignature(NULL, NULL, $stmt->name, $stmt->returnType);

			      	       $name = $stmt->name;

			      	       $cfg = CFG::construct_cfg($stmt->stmts);
			      	       $cfgMap[(string)$stmt->name] = $cfg;
			      	       $signatureMap[(string)$stmt->name] = $signature;
	       		      }
		}


	       return array($cfgMap,$signatureMap);
	       	       
	 }

// Opens a file, constructs the CFGs of the inner functions and 
// the main function, and returns the mapping from function names
// to CFGs, main CFG, and the mapping from function signatures
// to CFGs.
static function construct_file_cfgs($fileName) {

	print "Constructing CFGs for file ".($fileName)."\n";
	 	
	$file = fopen($fileName,"r");

	$parser = new PhpParser\Parser(new PhpParser\Lexer);

	$contents = fread($file,filesize($fileName));

	$stmts=array();

	try {
		$stmts = $parser->parse($contents);	
	} catch(PhpParser\Error $e) {
	  	echo 'Parse Error: ',$e->getMessage();
	}


	echo "There are ".count($stmts)." statements.\n"; 	

	// Construct the CFGs for all the functions defined in 
	// the file.

	echo "Constructing the CFG map of functions.\n";

	$className = "";

	// If there is only one statement and it's a class definition,
	// extract the inner class functions.
	if (count($stmts) == 1 && ($stmts[0] instanceof PhpParser\Node\Stmt\Class_)) {

	         print "Constructing CFG for class\n";
		 $className = $stmts[0]->name;
		 $function_definitions = CFG::process_function_definitions($stmts[0]->stmts);
	} else {

		 $function_definitions = CFG::process_function_definitions($stmts);
	}


	$function_cfgs = $function_definitions[0];
	$function_signatures = $function_definitions[1];


	echo "Finished construction of the CFG map of functions.\n";
	echo "Found ".(count($function_signatures))." inner functions.\n";
	echo "The function names are:\n";
	foreach($function_signatures as $name => $signature)
				     print $name."\n";

	// Construct the CFG of the main procedure of the file.
	echo "Constructing the main CFG.\n";
	$main_cfg = CFG::construct_cfg($stmts);
	echo "Finished construction of the main CFG.\n";

	echo "The main in-order traversal of the CFG is:\n";
	$main_cfg->print_preorder_cfg();

/*
	echo "The CFGs of the inner functions are:\n";
	     foreach($function_cfgs as $name => $inner_cfg) {
		print "The CFG of ".$name." is :\n";
		$inner_cfg->print_cfg();
	 }
*/

	fclose($file);
		 
	return new FileCFGInfo($main_cfg, $function_cfgs, $function_signatures, $className, $fileName);
}
	

}

?>