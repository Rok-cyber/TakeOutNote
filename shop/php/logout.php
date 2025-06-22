<?
session_start(); 

if($_GET['manager']) $url = "../managers/";
else $url = "../index.php";

################# 로그아웃 ################
SetCookie("my_id","",-999,"/"); 
SetCookie("sid","",-999,"/"); 
SetCookie("tempid","",-999,"/");

$_SESSION["myname"] = '';  
$_SESSION["myemail"] = '';  
$_SESSION["myhomepage"] = '';  
$_SESSION["mylevel"] = '';  
$_SESSION["mysale"] = '';  
$_SESSION["mypoint"] = '';  
$_SESSION["mycarr"] = '';  

header ("Location: $url");
?>