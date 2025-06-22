<? 
$tmps = explode("/",$_GET['img']);
$tmps[7] = urlencode($tmps[7]);
$img = join("/",$tmps);
?>

<html><head> 
<meta http-equiv='imagetoolbar' CONTENT='no'> 
<meta http-equiv='content-type' content='text/html; charset=utf-8'>
<title>Photo View</title> 
<style>.dragme{position:relative;}</style> 
</head> 

<body leftmargin=0 topmargin=0 bgcolor=#dddddd style='cursor:arrow;'> 
<table width=100% height=100% cellpadding=0 cellspacing=0>
  <tr>
    <td align=center valign=middle><img src='<?=$img?>' border=0 class='dragme' ondblclick='window.close();' style='cursor:move' title='왼쪽 버튼을 클릭한 후 마우스를 움직여서 보세요.' id="imgs"></td>
  </tr>
</table>
</body>
</html>


<script language='JavaScript1.2'> 
<!-- 
var ie=document.all; 
var nn6=document.getElementById&&!document.all; 
var img = document.getElementById("imgs");
var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);
var isdrag=false; 
var x,y; 
var dobj; 
var limX = -(parseInt(img.width)-w);
var limY = -(parseInt(img.height)-h);

function movemouse(e) 
{ 
  if (isdrag) 
  { 
    
	tmpX = nn6 ? tx + e.clientX - x : tx + event.clientX - x; 		
	if(tmpX>limX && tmpX<0) dobj.style.left = tmpX;
	else if(tmpX<limX) dobj.style.left = limX;
	else dobj.style.left = 0;
	
    tmpY  = nn6 ? ty + e.clientY - y : ty + event.clientY - y; 
	if(tmpY>limY && tmpY<0) dobj.style.top = tmpY;
	else if(tmpY<limY) dobj.style.top = limY;
	else dobj.style.top = 0;

	return false; 
  } 
} 
function selectmouse(e) 
{ 
  var fobj      = nn6 ? e.target : event.srcElement; 
  var topelement = nn6 ? 'HTML' : 'BODY'; 
  while (fobj.tagName != topelement && fobj.className != 'dragme') 
  { 
    fobj = nn6 ? fobj.parentNode : fobj.parentElement; 
  } 
  if (fobj.className=='dragme') 
  { 
    isdrag = true; 
    dobj = fobj; 
    tx = parseInt(dobj.style.left+0); 
    ty = parseInt(dobj.style.top+0); 
    x = nn6 ? e.clientX : event.clientX; 
    y = nn6 ? e.clientY : event.clientY; 
    document.onmousemove=movemouse; 
    return false; 
  } 
} 
document.onmousedown=selectmouse; 
document.onmouseup=new Function('isdrag=false'); 

//--> 
</script> 
