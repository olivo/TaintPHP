<?php

include_once "PHP-Parser-master/lib/bootstrap.php";
include_once "TaintAnalysis/TaintAnalysis.php";
include_once "CFG/CFG.php";

$filename = $argv[1];
	
// Obtain the CFGs of the main function, auxiliary functions and function signatures.
$fileCFGInfo = CFG::construct_file_cfgs($filename);

fileTaintAnalysis($fileCFGInfo);

?>