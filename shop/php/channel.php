<?
$tpl->define("main","{$skin}/{$channel}.html");
$tpl->scan_area("main");


$tpl->parse("main");
$tpl->tprint("main");
$tpl->close();
?>