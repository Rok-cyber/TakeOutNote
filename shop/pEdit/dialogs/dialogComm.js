var selectColor = '';
function setColorPicker(){	
	var tag=null;
	var col= new Array();    		
	col[0] = new Array("#ffeeee","#ffcccc","#ffaaaa","#ff8888","#ff6666","#ff4444","#ff2222","#ff0000","#ee0000","#cc0000","#aa0000","#880000","#770000","#660000","#550000","#440000","#330000");
	col[1] = new Array("#eeffee","#ccffcc","#aaffaa","#88ff88","#66ff66","#44ff44","#22ff22","#00ff00","#00ee00","#00cc00","#00aa00","#008800","#007700","#006600","#005500","#004400","#003300");
	col[2] = new Array("#eeeeff","#ccccff","#aaaaff","#8888ff","#6666ff","#4444ff","#2222ff","#0000ff","#0000ee","#0000cc","#0000aa","#000088","#000077","#000066","#000055","#000044","#000033");
	col[3] = new Array("#ffffee","#ffffcc","#ffffaa","#ffff88","#ffff66","#ffff44","#ffff22","#ffff00","#eeee00","#cccc00","#aaaa00","#888800","#777700","#666600","#555500","#444400","#333300");
	col[4] = new Array("#ffeeff","#ffccff","#ffaaff","#ff88ff","#ff66ff","#ff44ff","#ff22ff","#ff00ff","#ee00ee","#cc00cc","#aa00aa","#880088","#770077","#660066","#550055","#440044","#330033");
	col[5] = new Array("#ffddd0","#ffe0aa","#ffdd88","#ffcc77","#ffbb66","#ffaa55","#ffaa44","#ff9944","#ff8833","#ff7722","#ff6622","#ee5522","#dd4411","#cc3300","#aa2200","#882200","#662200");
	col[6] = new Array("#eeffff","#ccffff","#aaffff","#88ffff","#66ffff","#44ffff","#22ffff","#00ffff","#00eeee","#00cccc","#00aaaa","#008888","#007777","#006666","#005555","#004444","#003333");
	col[7] = new Array("#ffffff","#eeeeee","#dddddd","#cccccc","#bbbbbb","#aaaaaa","#a0a0a0","#999999","#888888","#777777","#666666","#555555","#444444","#333333","#222222","#111111","#000000");
		
	var div = document.createElement("div");
	div.id				= "colorPicker";
	div.className		= "WebEditorDiv";
	div.style.display	= "none";
	div.style.position	= "absolute";
	div.style.backgroundColor = "#f5f5f5";			
	div.style.border	= "1px solid #cccccc";
	div.style.padding	= "4px";	
	div.style.width		= "248px";	
	div.style.height	= "90px";
	document.body.appendChild(div);
		
	for(var i=0; i<8; i++) {		
		for(var j=0; j<17; j++)	{
			color = col[i][j];
			tag		= document.createElement("a");
			tag.style.backgroundColor = color;				
			tag.style.cursor="pointer";	
			tag.onclick = function() { cpSelect(this.style.backgroundColor); div.style.display='none'; };		
			div.appendChild(tag);	
		}			
		div.appendChild(document.createElement("br"));
	}

	
}
		
function cpShow(obj){
	div	= document.getElementById('colorPicker');	
	var left	= obj.offsetLeft;
	var parent	= obj.offsetParent;
	var top		= obj.offsetTop;
	var parent	= obj.offsetParent;
	while(parent) {	top += parent.offsetTop; parent = parent.offsetParent; }
	parent = obj.offsetParent;
	while(parent) {	left += parent.offsetLeft; parent = parent.offsetParent; }		
	div.style.top	= top - parseInt(div.style.height)-16+"px";				
	div.style.left	= left+"px";				
	div.style.display="";		
	selectColor		= obj;
}

function cpSelect(color){		

    selectColor.style.backgroundColor = color;			
	if(typeof(g_sBorderColor)!="undefined") g_sBorderColor = color;
}

function hexColor(id) { 
    var color = document.getElementById(id).style.backgroundColor; 
	var reg = /^rgb\(([\d]+), ?([\d]+), ?([\d]+)\)$/; 
    var hex = color.match(reg); 

    if(hex) { 
        var code = "#" + toHex(hex[1]) + toHex(hex[2]) + toHex(hex[3]); 
    } else code = color;    
    return code; 
} 

function toHex(dec) { 
    var number = Number(dec); 
    return (number<16?"0":"")+number.toString(16); 
} 
