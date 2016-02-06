<?php

include_once "PHP-Parser-master/lib/bootstrap.php";
include_once "TaintAnalysis/taint_analysis.php";
include_once "CFG/CFG.php";

$filename = $argv[1];
	
// Obtain the CFGs of the main function, auxiliary functions and function signatures.
$file_cfgs = CFG::construct_file_cfgs($filename);

taint_analysis($file_cfgs[0],$file_cfgs[1],$file_cfgs[2]);

?>