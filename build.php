<?php
$out = "Build\n=====\n\n";
$out .= "Testing Github build with PHP.\n\n";
if(getenv('GITHUB_ACTIONS')){
	$out .= "is Github action\n\n";
}else{
	$out .= "not Github action\n\n";
}
$out .= "ENV\n-----\n\n";
$out .= "```\n";
$out .= json_encode($_ENV, JSON_PRETTY_PRINT) . "\n";
$out .= "```\n";
$out .= "Github ENV variables\n-----\n\n";
$out .= "GITHUB_RUN_ID: " . getenv('GITHUB_RUN_ID') . "\n";
$out .= "GITHUB_RUN_NUMBER: " . getenv('GITHUB_RUN_NUMBER') . "\n";
$out .= "GITHUB_SHA: " . getenv('GITHUB_SHA') . "\n";


file_put_contents(__DIR__ . '/dist/build.txt', $out);