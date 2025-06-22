<?
session_start(); 

$url = "../";

################# 로그아웃 ################
SetCookie("a_my_id","",-999,"/"); 
SetCookie("a_sid","",-999,"/"); 
SetCookie("PHPSESSID","",-999,"/"); 
SetCookie("tempid","",-999,"/");

$_SESSION["a_myname"] = ''; 
$_SESSION["a_myemail"] = ''; 
$_SESSION["a_mycommi"] = '';

header ("Location: $url");
?>