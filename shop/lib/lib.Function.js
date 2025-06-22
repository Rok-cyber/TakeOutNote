function $() {
	var elements = new Array();
	for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];
		if (typeof element == 'string')
			element = document.getElementById(element);
		if (arguments.length == 1)
			return element;
		elements.push(element);
	}
	return elements;
}

String.prototype.trim = function() {
	return this.replace(/(^\s*)|(\s*$)/g, ""); 
}

function setPng24(obj) {
    obj.width=obj.height=1;
	obj.className=obj.className.replace(/\bpng24\b/i,'');
	obj.style.filter =
	"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+ obj.src +"',sizingMethod='image');"
	obj.src=''; 
	return '';
}

function str_replace ( search, replace, subject ) {
    // Replace all occurrences of the search string with the replacement string
    // 
    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_str_replace/
    // +       version: 801.3120
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
    // *     returns 1: 'Kevin.van.Zonneveld'

    var result = "";
    var prev_i = 0;
    for (ii = subject.indexOf(search); ii > -1; ii = subject.indexOf(search, ii)) {
        result += subject.substring(prev_i, ii);
        result += replace;
        ii += search.length;
        prev_i = ii;
    }

	return result + subject.substring(prev_i, subject.length);
}

function in_array(needle, haystack, strict) {
    // Checks if a value exists in an array
    // 
    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_in_array/
    // +       version: 801.3120
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: in_array('van', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: true

    var found = false, key, strict = !!strict;

    for (key in haystack) {
        if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle)) {
            found = true;
            break;
        }
    }

    return found;
}

function getCookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) {
		return null;
	}
	if ( start == -1 ) return null;
	var end = document.cookie.indexOf( ";", len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}

function setcookie(name, value, expires, path, domain, secure) {
    // Send a cookie
    // 
    // +    discuss at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_setcookie/
    // +       version: 801.1916
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // *     example 1: setcookie('author_name', 'Kevin van Zonneveld');
    // *     returns 1: true

    expires instanceof Date ? expires = expires.toGMTString() : typeof(expires) == 'number' && (expires = (new Date(+(new Date) + expires * 1e3)).toGMTString());
    var r = [name + "=" + escape(value)], s, i;
    for(i in s = {expires: expires, path: path, domain: domain}){
        s[i] && r.push(i + "=" + s[i]);
    }
    return secure && r.push("secure"), document.cookie = r.join(";"), true;
}

function delCookie(name) {
	setCookie(name,'',3600,"/",domain=window.document.domain||window.location.hostname);	
	setcookie(name,'',-999,"/",domain=window.document.domain||window.location.hostname);
}

/*****************  숫자 체크 스크립트 *********************/
function ckNum(obj) {    	
	for(var ii = 0; ii < obj.value.length; ii++) { 
        var chr = obj.value.substr(ii,1); 		
        if((chr < '0' || chr > '9') && chr!='.') {
			return false;
        } 
    }	
	return true;
}

function onlyNum(obj) {    
	var val = obj.value;
	var ii = val.length-1;
	var chr1 = val.substr(ii,1); 
	var chr2 = val.substr(0,1); 
	var regExp =/[^0-9.]/gi;

	try{	
		if(regExp.test(chr2)) {		
			obj.value = obj.value.substr(1,ii+1); 			
			return;
		}	

		if(regExp.test(chr1)) {
			obj.value = obj.value.substr(0,ii); 
			return;
		}

		if(regExp.test(val)) {
			for(jj=1;jj<i;jj++){
				if(regExp.test(val.substr(jj,1))) {
					obj.value = obj.value.substr(0,jj) + obj.value.substr(jj+1,ii-jj); 
					return;
				}
			}
		}	
	} catch (ex) {
		obj.value = 0;		
		return false;
	}
}

/***************** 금액 표시 스크립트 *********************/
function number_format( number, decimals, dec_point, thousands_sep ) {
    // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: number_format(1234.5678, 2, '.', '');
    // *     returns 1: 1234.57

    var ii, jj, kw, kd, km;

    // input sanitation & defaults
    if( isNaN(decimals = Math.abs(decimals)) ){
        decimals = 0;
    }
    if( dec_point == undefined ){
        dec_point = ".";
    }
    if( thousands_sep == undefined ){
        thousands_sep = ",";
    }

    ii = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if( (jj = ii.length) > 3 ){
        jj = jj % 3;
    } else{
        jj = 0;
    }

    km = (jj ? ii.substr(0, jj) + thousands_sep : "");
    kw = ii.substr(jj).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    kd = (decimals ? dec_point + Math.abs(number - ii).toFixed(decimals).slice(2) : "");


    return km + kw + kd;
}

/*
function number_format(num) {
	var num = num.toString();
	num = num.replace(/,/g, "");	
	var result = '';

	for(var i=0; i<num.length; i++) {
		var tmp = num.length-(i+1);
		if(i%3==0 && i!=0) result = ',' + result;
		result = num.charAt(tmp) + result;
	}

	return result;
}
*/

function UpdateChar(limit,name,fname,mbox) {	

	f = eval("document."+fname)
	Msg = eval("f."+name+".value")		
	Lmt  = eval("document.getElementById('b_"+name+"')")	
	var Len = MsgLen(Msg);
	Lmt.innerHTML = Len;
	if (Len > limit) {
		if(mbox==1) messageBox.show("최대 " + limit + "byte이므로 초과된 글자수는 자동으로 삭제됩니다.",'300','60');
		else alert("최대 " + limit + "byte이므로 초과된 글자수는 자동으로 삭제됩니다.");
		Msg = Msg.replace(/\r\n$/, "");
		eval("f."+name).value = MsgCut(Msg, limit,Lmt);
		
	}
}

function UpdateChar2(limit,limit2,name,fname) {	

	f = eval("document."+fname)
	Msg = eval("f."+name+".value")		
	Lmt  = eval("document.getElementById('b_"+name+"')")
	Scnt = eval("document.getElementById('c_"+name+"')")
	var Len = MsgLen(Msg);
	Lmt.innerHTML = Len;	
	if (Len > limit2) {
		alert("최대 " + limit2 + "byte이므로 초과된 글자수는 자동으로 삭제됩니다.");
		Msg = Msg.replace(/\r\n$/, "");
		eval("f."+name).value = MsgCut(Msg, limit2,Lmt);		
	}
	else {
		if(Len>80) Scnt.innerHTML = 2;
		else Scnt.innerHTML = 1;
		//Scnt.innerHTML = Math.ceil(Len/limit);
	}
}

function MsgLen(Msg) {
	var nbytes = 0;

	for (i=0; i<Msg.length; i++) {
		var ch = Msg.charAt(i);
		if(escape(ch).length > 4) nbytes += 2;
		else if (ch == '\n') {
			if (Msg.charAt(i-1) != '\r') nbytes += 1;			
		} 
		else if (ch == '<' || ch == '>') nbytes += 4;
		else nbytes += 1;		
	}

	return nbytes;
}

function MsgCut(Msg, Max, Lmt){
	var inc = 0;
	var nbytes = 0;
	var M = "";
	var ML = Msg.length;	
	
	for (i=0; i<ML; i++) {
		var ch = Msg.charAt(i);
		if (escape(ch).length > 4) inc = 2;
		else if (ch == '\n') {
			if (Msg.charAt(i-1) != '\r') inc = 1;
		}
	    else if (ch == '<' || ch == '>') inc = 4;
		else inc = 1;
		
		if ((nbytes + inc) > Max) break;
		
		nbytes += inc;
		M += ch;
	}
	Lmt.innerHTML = nbytes;
	return M;
}

/* 사용방법
<script  type="text/javascript"> 
setem = new setEmbed(); 
setem.init('flash','/fla/main_menu.swf','100%','80'); //타입,경로,가로,세로
setem.parameter('wmode','transparent'); //파라미터
setem.parameter('FlashVars','main=<?=$_PAGE[MENU]?>'); //값전달
setem.show(); 
</script> 
*/

function setEmbed() { 
	var obj = new String; 
	var parameter = new String; 
	var embed = new String; 
	var html = new String; 
	var allParameter = new String; 
	var clsid = new String; 
	var codebase = new String; 
	var pluginspace = new String; 
	var embedType = new String; 
	var src = new String; 
	var width = new String; 
	var height = new String; 

    
	this.init = function( getType , s ,w , h ) { 
      
		if ( getType == "flash") { 
			clsid = "D27CDB6E-AE6D-11cf-96B8-444553540000";        
			codebase = "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0"; 
			pluginspage = "http://www.macromedia.com/go/getflashplayer"; 
			embedType = "application/x-shockwave-flash"; 
		} 
		/* type 추가 
		else if ( ) { 
		
		} 
		*/ 
            
		parameter += "<param name='movie' value='"+ s + "'>\n";  
		parameter += "<param name='quality' value='high'>\n";    
      
		src = s; 
		width = w; 
		height = h; 
	}		
  
	this.parameter = function( parm , value ) {      
		parameter += "<param name='"+parm +"' value='"+ value + "'>\n";        
		allParameter += " "+parm + "='"+ value+"'"; 
	}  
  
	this.show = function() { 
		if ( clsid ) { 
			obj = "<object classid=\"clsid:"+ clsid +"\" codebase=\""+ codebase +"\" width='"+ width +"' height='"+ height +"'>\n"; 
		} 
      
		embed = "<embed src='" + src + "' pluginspage='"+ pluginspage + "' type='"+ embedType + "' width='"+ width + "' height='"+ height +"'"+ allParameter +" ></embed>\n"; 
      
		if ( obj ) { 
			embed += "</object>\n"; 
		} 
		
		html = obj + parameter + embed;       
		document.write( html );  
	} 
  
}


function MsgScroll() {
    this.name = "MsgScroll";  //스크롤 명(객체)
    this.msgs = new Array(); // 메세지 배열정의
    this.msgcnt =0;  //메세지 배열번호 정의
    this.stop = 0; // 정지 유무
    this.height = 100; // 레이어 높이
    this.width = 100;  //레이어 넓이
    this.speed = 50; // 간격 조정(속도)
    this.currentSpeed = 0; // 현재 간격 조정(속도) 
    this.pauseDelay = 1000; // 정지 시간
    this.pauseMouseover = false; //마우스를 올렸을때 정지 유무
	this.viewcnt = 1; // 보여줄라인수

    this.add = function(str) {  //메세지 첨가 메서드          
        this.msgs[this.msgcnt] = str;
        this.msgcnt = this.msgcnt + 1;
    }

    this.start = function() {
       this.init();
       setTimeout(this.name+'.scroll()',this.speed);
     }
    
    this.init =function() {
        document.write('<div id="'+this.name+'" style="height:'+(this.viewcnt*this.height)+'px;width:'+this.width+'px;position:relative;overflow:hidden;"  OnMouseOver="'+this.name+'.onmouseover();" OnMouseOut="'+this.name+'.onmouseout();">');
        for(var i = 0; i < this.msgcnt; i++) {
            document.write('<div id="'+this.name+'msg'+i+'"style="left:0px;width:'+this.width+'px;position:absolute;top:'+(this.height*i+1)+'px;">');
            document.write(this.msgs[i]);
            document.write('</div>');
        }
		document.write('</div>');
    }
      
    this.scroll = function() {
        if (!this.stop) { 
            this.speed = this.currentSpeed;
            for (i = 0; i < this.msgcnt; i++) {
                   obj = document.getElementById(this.name+'msg'+i).style;
                   obj.top = (parseInt(obj.top) - 1) + 'px';          
                   if (parseInt(obj.top) <= this.height*(-1)) obj.top = (this.height * (this.msgcnt-1)) + 'px';
                   if (parseInt(obj.top) == 0) this.speed = this.pauseDelay                   
            } 
     
        }
        window.setTimeout(this.name+".scroll()",this.speed);
    }

    

    this.onmouseover = function() {
        if (this.pauseMouseover) this.stop = true;
    }
       
    this.onmouseout = function() {
        if (this.pauseMouseover) this.stop = false;
    }

}

function MM_preloadImages() { //v3.0
	var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function itemScroll(name,cnt,vcnt,width) {	
	this.name = name+'Div';
	this.iname = name+'Img';
	this.iskin = shop_skin;
	this.obj = document.getElementById(name).style;
    this.count = 0;
	this.cnt = cnt;  
	this.vcnt = vcnt;  
	this.destination = '';
	this.width = width;  
	this.tid = null;
    this.stop = false; // 정지 유무
	this.stop2 = false; // 정지 유무
    this.speed = 10; // 간격 조정(속도)
    this.pauseDelay = 1500; // 정지 시간
    this.pauseMouseover = true; //마우스를 올렸을때 정지 유무
	this.img1 = new Image;
	this.img2 = new Image;
	this.img1.src = this.iskin+'img/main/icon_scroll_dot.gif';
	this.img2.src = this.iskin+'img/main/icon_scroll_dot_on.gif';
	
	this.scroll = function() {
        if(this.cnt<1) return;
		var xOffset, destination, timeoutNextCheck;		
		timeoutNextCheck = this.speed;			        

		if (this.stop==false) { 
			if(!this.destination) {
				if(this.count > (this.cnt*2)) this.count = 1;
				if(this.count <= this.cnt) this.destination =  -(this.count*this.width);	
				else this.destination =  -(this.cnt*this.width) + ((this.count-this.cnt)*this.width);	
			}
		
		 	xOffset = Math.ceil(Math.abs(this.destination - parseInt(this.obj.left)) / 15);  
			
			if(this.count <= this.cnt) xOffset = -xOffset;
		    this.obj.left = parseInt (this.obj.left, 10) + xOffset + 'px';			
            			
			if (parseInt(this.obj.left) == this.destination)  { 						
				if(this.count>0) {
					if(this.count <= this.cnt) {
						if(document.getElementById(this.iname+this.count)) document.getElementById(this.iname+this.count).src = this.img1.src;
						if(document.getElementById(this.iname+(this.count+this.vcnt))) document.getElementById(this.iname+(this.count+this.vcnt)).src = this.img2.src;
					}
					else {
						tmps = (this.count - (2*(this.count-this.cnt))+1);
						if(document.getElementById(this.iname+(tmps))) document.getElementById(this.iname+(tmps)).src = this.img2.src;
						if(document.getElementById(this.iname+(tmps+this.vcnt))) document.getElementById(this.iname+(tmps+this.vcnt)).src = this.img1.src;
					}
				}
				this.count++; 
				this.destination ='';
				if(this.stop2==true) {					
					this.stop = true;
					this.stop2 = false;
				}

				timeoutNextCheck = this.pauseDelay; 
			}					
		} 
		window.setTimeout(this.name+".scroll()",timeoutNextCheck);
    }    

    this.mover = function() {
        if (this.pauseMouseover) {
			if(this.destination=='') this.stop = true;
			else this.stop2 = true;
		}
    }
       
    this.mout = function() {
        this.stop = false;			
    }

	this.imover = function(num) {
		if(this.cnt<1) return;
        if (this.pauseMouseover) this.stop = true;				
		if(num>this.cnt + 1) {
			num = this.cnt + 1 - ((this.cnt+this.vcnt) - eval(num));
			if(num<1) num = this.cnt;
		}		

		for(i=1;i<=(this.vcnt+this.cnt);i++) 
			document.getElementById(this.iname+(i)).src = this.img1.src;

		if(num<=this.cnt) {
			for(i=num;i<(eval(num)+this.vcnt);i++) 
				document.getElementById(this.iname+(i)).src = this.img2.src;		
		}		
		else {			
			for(i=(this.cnt+1);i<=(this.cnt+this.vcnt);i++) 
				document.getElementById(this.iname+(i)).src = this.img2.src;		
		}
		
		this.count = eval(num-1);	
		this.destination = '';
		this.move();
    }

	this.imout = function() {
        this.stop = false;			
    }

	this.move = function() {
		if(!this.destination) {
			if(this.count > (this.cnt*2)) this.count = 1;			
			if(this.count <= this.cnt) this.destination =  -(this.count*this.width);	
			else this.destination =  -(this.cnt*this.width) + ((this.count-this.cnt)*this.width);	
		}		

		if(parseInt(this.obj.left) > this.destination) {
			xOffset = Math.ceil(Math.abs(parseInt(this.obj.left) - this.destination) / 15);  		
			xOffset = -xOffset;
		} else {
			xOffset = Math.ceil(Math.abs(parseInt(this.obj.left) - this.destination) / 15);  					
		}
		this.obj.left = parseInt (this.obj.left, 10) + xOffset + 'px';

		if (parseInt(this.obj.left) == this.destination)  {
			this.destination = '';	
			if(this.tid) clearTimeout(this.tid);	
			this.stop = false;
			this.stop2 = false;
		} 
		else this.tid = window.setTimeout(this.name+".move()",this.speed);		
	}
}

function itemMove(name,tcnt,vcnt) {	
	this.name = name+'Div';
	this.iname = name+'Img';
	this.gname = name+'Goods';
	this.iskin = shop_skin;
    this.count = 1;	
	this.tcnt = tcnt;	
	this.vcnt = vcnt;  
	this.block = Math.ceil(this.tcnt/this.vcnt);
    this.stop = false; // 정지 유무    
    this.pauseDelay = 3500; // 정지 시간
    this.pauseMouseover = true; //마우스를 올렸을때 정지 유무
	this.img1 = new Image;
	this.img2 = new Image;
	this.img1.src = this.iskin+'img/main/icon_scroll_dot.gif';
	this.img2.src = this.iskin+'img/main/icon_scroll_dot_on.gif';
	
	this.Move = function() {
        if(this.tcnt<=this.vcnt) return;		

		if(!this.stop) {		
			if(this.count > this.block) this.count=1;
			
			var start = this.vcnt*((this.count)-1)+1;		
			
			for(i=1;i<=this.tcnt;i++) 
				document.getElementById(this.gname+(i)).style.display = 'none';			

			for(i=1;i<=this.block;i++) {
				tmps = (this.vcnt*(i-1))+1;
				document.getElementById(this.iname+tmps).src = this.img1.src;
			}
			
			for(i=start;i<eval(start)+this.vcnt;i++) 
				if(i<=this.tcnt) document.getElementById(this.gname+(i)).style.display = 'block';
			
			document.getElementById(this.iname+(start)).src = this.img2.src;
			this.count++;
		}		
		window.setTimeout(this.name+".Move()",this.pauseDelay);
    }    
    
	this.imover = function(num) {
		if (this.pauseMouseover) this.stop = true;

		for(i=1;i<=this.tcnt;i++) 
			document.getElementById(this.gname+(i)).style.display = 'none';			

		for(i=1;i<=this.block;i++) {
			tmps = (this.vcnt*(i-1))+1;
			document.getElementById(this.iname+tmps).src = this.img1.src;
		}
		
		for(i=num;i<eval(num)+this.vcnt;i++) {		
			if(i<=this.tcnt) document.getElementById(this.gname+(i)).style.display = 'block';
		}		
		
		document.getElementById(this.iname+(num)).src = this.img2.src;
	}

	this.imout = function() {
        this.stop = false;			
    }	

	this.mover = function() {
        if (this.pauseMouseover) this.stop = true;
    }
       
    this.mout = function() {
        this.stop = false;			
    }
}


function selectBox(id,fName,Main,moveNum,top,left) {	
	this.id			= id;
	this.Main		= Main;
	this.defTop		= top;
	this.defLeft	= left;
	if(moveNum)	this.moveNum = moveNum;
	else this.moveNum = 1;
	this.iskin		= shop_skin;
	this.itemName	= new Array();
	this.itemValue	= new Array();
	this.itemCnt	= 0;
	this.fName		= fName;
	this.secNum		= 0;
	this.tCnt		= 0;
	this.hide		= false;
	this.bColor		= "#999";
	this.fStyle		= "font-family:굴림,verdana;font-size:12px;";
	this._tArea		= document.createElement("DIV");
	this._sArea		= document.createElement("DIV");				
	this._hArea		= document.createElement("DIV");					

	this.img1 = new Image;
	this.img2 = new Image;
	this.img1.src = this.iskin+'img/common/icon_select_down.gif';
	this.img2.src = this.iskin+'img/common/icon_select_up.gif';
	   
	this.addItem = function(name, value){ 
		this.itemName[this.itemCnt]		= name;
		this.itemValue[this.itemCnt]	= value;
		this.itemCnt++;
	}

	this.show = function(mode){
		var self = (this)?this:'';
		var pBody = document.getElementById(this.id);			
		if(document.all) {
			this._tArea.style.styleFloat = 'left';
			this._sArea.style.styleFloat = 'left';
		}
		else {
			this._tArea.style.cssFloat = 'left';
			this._sArea.style.cssFloat = 'left';
		}

		this._tArea.style.width		= (parseInt(pBody.style.width) - 22) + 'px';	
		this._tArea.style.padding	= '2px 0 0 4px';	
		this._tArea.style.height	= '16px';
		this._tArea.style.overflow	= 'hidden';

		this._tArea.innerHTML		= "<div style='"+this.fStyle+"'>"+this.itemName[0]+"</div>";
		this._sArea.style.width		= '18px';	
		this._sArea.style.height	= '16px';	
		this._sArea.style.overflow	= 'hidden';
		this._sArea.style.background	= "url("+this.iskin+"img/common/select_line.gif) repeat-y";

		tag	= document.createElement("img");
		tag.id				= "sImg"+this.id;
		tag.src				= this.img1.src;
		tag.style.cursor	= 'pointer';
		tag.style.padding	= "7px 0 0 3px";
		tag.onclick	= function() { self.open(this); }
		this._sArea.appendChild(tag);
		
		this._hArea.style.position	= 'absolute';
		this._hArea.style.clear		= 'both';		
		
		if(this.defTop) this._hArea.style.top		= this.defTop + 'px';			
		else this._hArea.style.top		= parseInt(pBody.style.height) + 1 + 'px';				
		
		if(this.itemCnt>20) {
			this._hArea.style.height	= '360px';
			this._hArea.style.overflow	= 'auto';
		}

		if(mode=='cate') {
			if(this.defLeft) this._hArea.style.left		= this.defLeft + 'px';
			else this._hArea.style.left		= '-2px';
			this._hArea.style.border	= '1px solid #CCC';
			this._hArea.style.width		= parseInt(pBody.style.width) + 2 + 'px';
		}
		else if(mode=='color') {
			if(this.defLeft) this._hArea.style.left		= this.defLeft + 'px';
			else this._hArea.style.left		= '-2px';
			this._hArea.style.border	= '2px solid #eaeaea';
			this._hArea.style.width		= parseInt(pBody.style.width) + 'px';
		}
		else {
			if(this.defLeft) this._hArea.style.left		= this.defLeft + 'px';
			else this._hArea.style.left		= '-1px';
			this._hArea.style.border	= '1px solid ' + this.bColor;
			this._hArea.style.width		= parseInt(pBody.style.width) +'px';
		}

		this._hArea.style.borderTop	= '0px';
		this._hArea.style.backgroundColor = '#FFF';
		this._hArea.style.display	= 'none';		

		for(i=1;i<=this.itemCnt;i++) {
			tag	= document.createElement("div");			
			tag.style.height	= '18px';			
			if(i==1) tag.style.padding	= '2px 0 0 4px';			
			else tag.style.padding	= '0px 0 0 4px';		
			tag.cnt				= i;
			tag.style.cursor	= 'pointer';
			tag.onmouseover		= function() { this.style.backgroundColor = '#fafafa'; }
			tag.onmouseout		= function() { this.style.backgroundColor = ''; }

			tag.innerHTML		= "<div style='"+this.fStyle+"'>"+this.itemName[i-1]+"</div>";
			if(mode=='cate') tag.onclick = function() { self.secNum = this.cnt; self.movePage(); self.hide = true; self.mout(); }
			else tag.onclick = function() { self.secNum = this.cnt; self.hide = true; self.mout(); }
			this._hArea.appendChild(tag);
		}
		
		this._tArea.style.zIndex	= '9999';
		this._sArea.style.zIndex	= '9999';
		this._hArea.style.zIndex	= '9999';

		pBody.appendChild(this._tArea);
		pBody.appendChild(this._sArea);
		pBody.appendChild(this._hArea);

		if(this.secNum!='') {			
			this.fName.value = this.itemValue[this.secNum-1];
			this._tArea.innerHTML = "<div style='"+this.fStyle+"'>"+this.itemName[this.secNum-1]+"</div>";
			this.secNum = '';
		} 	
	}

	this.setNumCk = function(vls) {
		for(i=1;i<=this.itemCnt;i++) {
			if(this.itemValue[i-1]==vls) {
				this.secNum = i;			
				break;
			}
		}		
	}

	this.open = function(obj) {			
		var self = (this)?this:'';
		var obj2 = this._hArea;

		obj.src = this.img2.src;
		obj.style.padding	= '5px 0 0 4px';
		obj.onclick = function() { self.hide = true; self.mout(); }
		
		obj2.style.display = 'block';		
		obj2.onmouseout = function() { self.mout(); }
	}

	this.mout = function() {
		var self = (this)?this:'';
		var obj	 = this._hArea;				
		var obj2 = document.getElementById("sImg"+this.id);

		if(!this.hide) {			
			obj.onmouseover = function() { self.secNumse = ''; self.hide = false; clearTimeout(tHide) } 
			this.hide = true;
			tHide = setTimeout(this.id+"s.mout()",500);
			return;			
		}			
		obj.style.display	= 'none';
		obj2.style.padding	= "7px 0 0 3px";
		obj2.src			= this.img1.src;
		obj2.onclick		= function() { self.open(this); }

		this.hide			= false;
		if(this.secNum!='') {			
			this.fName.value = this.itemValue[this.secNum-1];
			this._tArea.innerHTML = "<font style='"+this.fStyle+"'>"+this.itemName[this.secNum-1]+"</font>";
			this.secNum = '';
		} 		

	}
	
	this.initSelect = function(vls) {
		this.hide = true;

		for(i=1;i<=this.itemCnt;i++) {
			if(this.itemValue[i-1]==vls) this.secNum = i;
		}	
		this.mout();
	}

	this.movePage = function(){		
		if(this.moveNum==1) window.location.href = this.Main+'?channel=main2&cate='+this.itemValue[this.secNum-1];
		else if(this.moveNum==2) window.location.href = this.Main+'?channel=brand&uid='+this.itemValue[this.secNum-1];
		else if(this.moveNum==6) window.location.href = this.Main+'?channel=special&uid='+this.itemValue[this.secNum-1];
		else if(this.moveNum==3) window.location.href = this.Main+'?channel=event&uid='+this.itemValue[this.secNum-1];
		else if(this.moveNum==4) getLists.cgLimit(this.itemValue[this.secNum-1],1);
		else if(this.moveNum==5) getAfters.cgLimit(this.itemValue[this.secNum-1]);
		else if(this.moveNum==7) getQna.cgLimit(this.itemValue[this.secNum-1]);
	}
}


/*
'ㄱ','ㄲ','ㄴ','ㄷ','ㄸ','ㄹ','ㅁ','ㅂ','ㅃ','ㅅ','ㅆ','ㅇ','ㅈ','ㅉ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ';//초성 19개 
'ㅏ','ㅐ','ㅑ','ㅒ','ㅓ','ㅔ','ㅕ','ㅖ','ㅗ','ㅘ','ㅙ','ㅚ','ㅛ','ㅜ','ㅝ','ㅞ','ㅟ','ㅠ','ㅡ','ㅢ','ㅣ';//중성 21개 
'ㄱ','ㄲ','ㄳ','ㄴ','ㄵ','ㄶ','ㄷ','ㄹ','ㄺ','ㄻ','ㄼ','ㄽ','ㄾ','ㄿ','ㅀ','ㅁ','ㅂ','ㅄ','ㅅ','ㅆ','ㅇ','ㅈ','ㅊ','ㅋ','ㅌ','ㅍ','ㅎ');//종성 28개 
*/

function getJamoCodes(t) { 
    var c = t.charCodeAt(0), c = c<0x3130?0:c<0x3164?c-0x3130:c<0xac00?0:c<0xd7a5?c+68:0; 
	var ck_arr = Array('','1','2','1,10','3','3,13','3,19','4','6','6,1','6,7','6,8','6,10','6,17','6,18','6,19','7','8','8,10','10','11','12','13','15','16','17','18','19');
    if (c>51) arr_var = Array((c-c%588)/588-74,((c-c%28)/28)%21+1,c%28); 
    else arr_var =  Array(c<3?c:c<4?0:c<5?c-1:c<7?0:c<10?c-3:c<17?0:c<20?c-10:c<21?0:c<31?c-11:0,c<31?0:c-30,0); 
	
	if(arr_var[0]==0) return '';
	rtn_value = arr_var[0];

	if(arr_var[1]>0) {
		rtn_value = rtn_value +","+(arr_var[1]);	
		if(arr_var[2]) {		
			if(ck_arr[arr_var[2]]) arr_var[2] = ck_arr[arr_var[2]];		
			rtn_value = rtn_value +","+(arr_var[2]);
		}
	}
	return rtn_value;
} 

function autoTextBox(id,fName,fName2) {	
	this.id			= id;
	this.iskin		= shop_skin;
	this.fName		= fName;	
	this.fName2		= fName2;	
	this.aObj		= new AjaxObject;  
	this.secNum		= 0;
	this.tCnt		= 0;
	this.tmpVls		= null;	
	this.tmpRtns	= null;	
	this.status		= true;
	this.changes	= false;
	this.focus		= 0;
	this.fStyle		= "font-family:굴림,verdana;font-size:12px;";
	this.fStyle2	= "font-family:돋움,Tahoma;font-size:8pt;letter-spacing:-1px;";
	this.autoMsg	= "<span style='"+this.fStyle+";height:40px;line-height:200%'>자동완성 기능을 사용하고 있습니다.</span>";
	this.bColor		= "#999";
	this.bgColor	= "#dadada";
	this.fColor		= "#3399ff";
	this._tArea		= document.createElement("DIV");
	this._sArea		= document.createElement("DIV");				
	this._hArea		= document.createElement("DIV");					

	this.img1 =  new Image;
	this.img2 =  new Image;
	this.img3 =  new Image;
	this.img4 =  new Image;
	this.img1.src = this.iskin+'img/common/icon_select_down.gif';
	this.img2.src = this.iskin+'img/common/icon_select_up.gif';
	this.img3.src = this.iskin+'img/common/icon_select_down2.gif';
	this.img4.src = this.iskin+'img/common/icon_select_up2.gif';
	   
	this.show = function(){
		var self = (this)?this:'';
		var pBody = document.getElementById(this.id);			
		if(document.all) {
			this._tArea.style.styleFloat = 'left';
			this._sArea.style.styleFloat = 'left';
		}
		else {
			this._tArea.style.cssFloat = 'left';
			this._sArea.style.cssFloat = 'left';
		}
		
		this._tArea.style.width		= (parseInt(pBody.style.width) - 22) + 'px';	
		this._tArea.style.padding	= '1px 0 0 4px';	
		this._tArea.innerHTML		= "<input type='text' id='autoQuery' name='"+this.fName+"' style='"+this.fStyle+";border:0px;width:95%'  onkeydown='"+this.id+"s.autoSend(event,this.value,1);' onkeyup='"+this.id+"s.autoSend(event,this.value,2);' onFocus='"+this.id+"s.open(\"1\")' onBlur='"+this.id+"s.blur()'>"; 
		this._sArea.style.width		= '18px';	
		this._sArea.style.height	= '18px';	
		
		tag	= document.createElement("img");
		tag.id				= "sImg"+this.id;
		tag.src				= this.img1.src;
		tag.style.cursor	= 'pointer';
		tag.style.padding	= "7px 0 0 3px";
		tag.onclick			= function() { self.open(); }
		this._sArea.appendChild(tag);
		this._sArea.onmouseover = function() { self.focus = 1; }
		this._sArea.onmouseout	= function() { self.focus = 0; }
		
		this._hArea.style.position	= 'absolute';
		this._hArea.style.clear		= 'both';
		this._hArea.style.left		= '-1px';
		this._hArea.style.top		= parseInt(pBody.style.height) +1 + 'px';		
		this._hArea.style.width		= parseInt(pBody.style.width) +'px';
		this._hArea.style.border	= '1px solid ' + this.bColor;
		this._hArea.style.borderTop	= '0px';
		this._hArea.style.backgroundColor = '#FFF';		
		this._hArea.style.display	= 'none';	
		this._hArea.onmouseover = function() { self.focus = 2; }
		this._hArea.onmouseout	= function() { self.focus = 0; }
		
		tag = document.createElement("DIV");
		tag.innerHTML = "<div id='autoBox"+this.id+"' style='padding:4px;text-align:center'>"+this.autoMsg+"</div>";
		tag.innerHTML += "<div style='border-top:1px solid "+this.bColor+";background-color:"+this.bgColor+";height:21px;line-height:200%;overflow:hidden'><span id='autoBtn1"+this.id+"'></span><span id='autoBtn2"+this.id+"' style='"+this.fStyle2+";float:right;padding-right:4px;cursor:pointer' onclick=\""+this.id+"s.change();\">기능끄기</span></div>";
         
		this._hArea.appendChild(tag);	
		
		pBody.appendChild(this._tArea);
		pBody.appendChild(this._sArea);
		pBody.appendChild(this._hArea);
	}

	this.autoSend = function(event,vls,ud) {
		if(!this.status) return; 		
		if(!vls) {			
			if(this.tCnt>0) {
				obj = document.getElementById('autoBox'+this.id);
				obj.style.height = '40px';
				obj.style.overflow = '';
				obj.innerHTML = this.autoMsg;
				this.tCnt = 0;
				this.tmpRtns = null;
				return;
			}
			else return;
		}
		vls = vls.toLowerCase(); 
		
		if(isMsie)  e = window.event;
		else e = event;

		switch (eval(e.keyCode)) {
			case 40 : 				
				if(ud==2) return;
				if(this.secNum<this.tCnt) {				
					if(this.secNum!=0) {
						sRtn1 = document.getElementById('autoRt' + (this.secNum-1) + '_' + this.id);					
						sRtn1.style.backgroundColor = '';	
						sRtn1.cks = true;
					}
					sRtn2 = document.getElementById('autoRt' + this.secNum + '_' + this.id);										
					sRtn2.style.backgroundColor = '#EFEFEF';						
					sRtn2.cks = false;
					eval("document."+this.fName2+"."+this.fName+".value = sRtn2.word");
					
					if(this.secNum>4) {
						obj = document.getElementById('autoBox'+this.id);
						obj.scrollTop = 18 * (this.secNum-4);						
					}
					this.secNum++;
					this.changes = true;
				}						
				return;
			break;

			case 38 :
				if(ud==2) return;
				if(this.secNum > 0) {				
					this.secNum--;
					
					sRtn1 = document.getElementById('autoRt' + this.secNum + '_' + this.id);					
					sRtn1.style.backgroundColor = '';	
					sRtn1.cks = true;
					
					if(this.secNum!=0) {
						sRtn2 = document.getElementById('autoRt' + (this.secNum - 1) + '_' + this.id);										
						sRtn2.style.backgroundColor = '#EFEFEF';						
						sRtn2.cks = false;
					} 

					if(this.secNum==0) {
						eval("document."+this.fName2+"."+this.fName+".value = this.tmpVls");
						this.changes = false;
					}
					else eval("document."+this.fName2+"."+this.fName+".value = sRtn2.word");
					
					if(this.secNum < (this.tCnt-4)) {
						obj = document.getElementById('autoBox'+this.id);
						obj.scrollTop = 18 * (this.secNum-1);						
					}
				}						
				return;
			break;			

			case 8 :
				this.tmpRtns = null;
			break;		
		}
		
		if(this.changes==true) return;

		if(vls.length>1) {
			var rtns = '';
			for(i=0,cnt=vls.length;i<cnt;i++){
				var ch = vls.charAt(i);
				if(tmp = getJamoCodes(ch)) {
					if(i==0) rtns = tmp;	
					else rtns += ','+tmp;			
				} 
				else rtns += ch;
			}	
		} 
		else {
			if(tmp = getJamoCodes(vls)) var rtns = tmp+'';
			else var rtns = vls;
		}
		
		if(rtns.indexOf(this.tmpRtns) != 0) {
			if(vls == rtns) ints = '&ints=1';
			else ints = '';

			this.aObj.getHttpRequest("/php/autoResult.php?search="+rtns+ints, this.id + "s.autoResult","data");        				
			this.tmpRtns = rtns;			
		} 
		else {
			prt = document.getElementById('autoBox'+this.id);			
			for(i = cnt =0;i<this.tCnt;i++) {
				obj = document.getElementById('autoRt' + i + '_' + this.id);											
				if(obj.sword.indexOf(rtns) != 0) {
					prt.removeChild(obj); 
				}
				else {
					tmps = obj.word.substring(0,vls.length).replace(eval('/(' + vls + '){1,1}/gi'), '<font color=orange>$1</font>');
					tmps = tmps + obj.word.substring(vls.length,obj.word.length);					
					obj.innerHTML =  tmps;					
					obj.id = 'autoRt' + cnt + '_' + this.id;
					cnt++;
				}
			}

			if(cnt==0) {
				prt.style.height = '40px';
				prt.style.overflow = '';
				prt.innerHTML = "<span style='"+this.fStyle+";text-align:center;line-height:200%'>해당단어로 시작하는 검색어가 없습니다.</span>";				
			}	
			else if(cnt<4) {
				prt.style.height = 17 * cnt + 'px';
				prt.style.overflow = '';
			}
			this.tCnt = cnt;
		}
		this.tmpVls = vls;
	}

	this.autoResult = function(data) {
		var self = (this)?this:'';
		obj = document.getElementById('autoBox'+this.id);		
				
		if(typeof(data['item']) != "undefined") {			
			obj.className	= 'barStyle';
			obj.innerHTML = '';			
			
			vls = eval("document."+self.fName2+"."+self.fName+".value");
			for(i=0,cnt=data['item'].length;i<cnt;i++){
				tag = document.createElement("DIV");
				tmps =  data['item'][i]['word'].substring(0,vls.length).replace(eval('/(' + vls + '){1,1}/gi'), '<font color=orange>$1</font>');
				tmps = tmps + data['item'][i]['word'].substring(vls.length,data['item'][i]['word'].length)
				tag.innerHTML		= tmps;
				tag.className		= "autoTextBox";
				tag.align			= 'left';
				tag.style.height	= '17px';
				tag.style.margin	= '0 0 1px 0';
				tag.style.padding	= '2px 0 0 2px';
				tag.style.cursor	= 'pointer';
				tag.id				= 'autoRt' + i + '_' + this.id;
				tag.word			= data['item'][i]['word'];
				tag.sword			= data['item'][i]['sword'];
				tag.cks				= true;
				tag.onmouseover		= function() { this.style.backgroundColor = '#EFEFEF'; }
				tag.onmouseout		= function() { if(this.cks) this.style.backgroundColor = ''; }
				tag.onclick			= function() { eval("document."+self.fName2+"."+self.fName+".value = this.word"); eval("document."+self.fName2+".submit()"); self.close(); }
				obj.appendChild(tag);					
			}
			if(i>0) {
				if(i>6) { 
					obj.style.height = '105px';
					obj.style.overflow = 'auto';
				} else obj.style.height = 17 * i + 'px';
				this.open(document.getElementById('sImg'+this.id));
				this.tCnt = i;
			}
		} 
		else {
			obj.style.height = '40px';
			obj.style.overflow = '';
			obj.innerHTML = "<span style='"+this.fStyle+";text-align:center;line-height:200%'>해당단어로 시작하는 검색어가 없습니다.</span>";
			this.tCnt = 0;
		}
	}

	this.open = function(cks) {	
		var self = (this)?this:'';
		if (self._timer) clearInterval(self._timer);		
		setInterval(function() { self.fireEvent();}, 50);

		if(cks==1 && this.tCnt==0) return;
		
		var obj = document.getElementById('sImg'+self.id);
		var obj2 = this._hArea;
		
		if(this.status) obj.src	= this.img2.src;
		else obj.src = this.img4.src; 

		obj.onclick = function() { self.close(); }
        
		obj.style.padding	= '5px 0 0 4px';		
		obj2.style.display = 'block';						
		eval("document."+this.fName2+"."+this.fName+".focus()");		
	}

	this.fireEvent = function() {
		if (document.createEvent) {    
			var e;
			if (window.KeyEvent) {
		  		e = document.createEvent('KeyEvents');
				e.initKeyEvent('keyup', true, true, window, false, false, false, false, 65, 0);
		  
			} 
			else {
		  		e = document.createEvent('UIEvents');
				e.initUIEvent('keyup', true, true, window, 1);
				e.keyCode = 65;	  
			}		
			document.getElementById('autoQuery').dispatchEvent(e);
		} 
		else {
			var e = document.createEventObject();
			e.keyCode = 65;
			document.getElementById('autoQuery').fireEvent('onkeyup', e);
		}
	}

	this.close = function() {
		var self = (this)?this:'';
		var obj = document.getElementById('sImg'+self.id);
		var obj2 = this._hArea;				

		if(this.status) obj.src	= this.img1.src;
		else obj.src = this.img3.src; 

		obj.style.padding	= "7px 0 0 3px";
		obj.onclick = function() { self.open(); }		
		
		obj2.style.display = 'none';			
		if(this.secNum>0) document.getElementById('autoRt' + (this.secNum - 1)+ '_' + this.id).style.backgroundColor='';						
		if (self._timer) clearInterval(self._timer);
		self._timer = null;
	}

	this.blur = function() {				
		if(this.focus==2) eval("document."+this.fName2+"."+this.fName+".focus()");
		else if(!this.focus) this.close();
	}

	this.change = function() {
		if(this.status) {
			this.status = false;
			this.focus = 0;
			document.getElementById('autoBtn2'+this.id).innerHTML		= "<font style='color:"+this.fColor+"'>기능켜기</font>";		
			document.getElementById('autoBox'+this.id).style.height		= '40px';
			document.getElementById('autoBox'+this.id).style.overflow	= '';
			document.getElementById('autoBox'+this.id).innerHTML		= "<span style='"+this.fStyle+";height:40px;line-height:200%'>자동완성 기능을 사용하고 있지 않습니다.</span>"; 
			this.tmpRtns = null;
			this.tCnt	 = 0;
			this.close();
		} 
		else {
			this.status = true;						
			document.getElementById('autoBtn2'+this.id).innerHTML = "기능끄기";		
			document.getElementById('autoBox'+this.id).innerHTML = this.autoMsg;
			this.autoSend(event,eval("document."+this.fName2+"."+this.fName+".value"));
			document.getElementById('sImg'+this.id).src = this.img2.src;
		}		
	}
}


function itemScroll2(name,tcnt,vcnt,height) {	
	this.name = name+'Div';
	this.obj = document.getElementById(name).style;
    this.count = 1;
	this.tcnt = parseInt(tcnt);  
	this.vcnt = parseInt(vcnt);  
	this.destination = '';
	this.height = parseInt(height);  
	this.dirt	= null;
	this.speed = 10; // 간격 조정(속도)

	this.img1 =  new Image;
	this.img2 =  new Image;
	this.img3 =  new Image;
	this.img4 =  new Image;
	this.img1.src = shop_skin+'img/common/icon_arrow_prev.gif';
	this.img2.src = shop_skin+'img/common/icon_arrow_next.gif';
	this.img3.src = shop_skin+'img/common/icon_arrow_prev_off.gif';
	this.img4.src = shop_skin+'img/common/icon_arrow_next_off.gif';

	if(this.tcnt>this.vcnt) document.getElementById(this.name+'ImgNext').src = this.img2.src;
	
	this.scroll = function(direct) {        		
		if(this.tcnt<this.vcnt) return;		
		if(direct) this.dirt = direct;
		var xOffset;

		if(!this.destination) {
			if(this.dirt=='down'){
				if(this.count>(this.tcnt-this.vcnt)) return;
				this.destination = -(this.count*this.height);				
			}
			else {
				if(this.count==1) return;
				this.destination =  -((this.count-2)*this.height);		
			}
		}
		
		xOffset = Math.ceil(Math.abs(this.destination - parseInt(this.obj.top)) / 10);  			
		if(this.dirt=='down') xOffset = -xOffset;		
		this.obj.top = parseInt (this.obj.top, 10) + xOffset + 'px';			
            			
		if (parseInt(this.obj.top) == this.destination)  { 										
			if(this.dirt=='down') this.count++; 
			else this.count--;
			this.destination ='';
			this.direct = null;
			
			if(this.count>1) document.getElementById(this.name+'ImgPrev').src = this.img1.src;
			else document.getElementById(this.name+'ImgPrev').src = this.img3.src;

			if(this.count<=(this.tcnt-this.vcnt)) document.getElementById(this.name+'ImgNext').src = this.img2.src;
			else document.getElementById(this.name+'ImgNext').src = this.img4.src;
		} 
		else window.setTimeout(this.name+".scroll()",this.speed);
    }        

}

function itemScroll3(name,tcnt,vcnt,width) {	
	this.name = name+'Div';
	this.obj = document.getElementById(name).style;
    this.count = 1;
	this.tcnt = parseInt(tcnt);  
	this.vcnt = parseInt(vcnt);  
	this.destination = '';
	this.width = parseInt(width);  
	this.dirt	= null;
	this.speed = 10; // 간격 조정(속도)

	this.img1 =  new Image;
	this.img2 =  new Image;
	this.img3 =  new Image;
	this.img4 =  new Image;
	this.img1.src = shop_skin+'img/shop/icon_arrow_prev2.gif';
	this.img2.src = shop_skin+'img/shop/icon_arrow_next2.gif';
	this.img3.src = shop_skin+'img/shop/icon_arrow_prev2_off.gif';
	this.img4.src = shop_skin+'img/shop/icon_arrow_next2_off.gif';

	if(this.tcnt>this.vcnt) document.getElementById(this.name+'ImgNext').src = this.img2.src;
	
	this.scroll = function(direct) {        		
		if(this.tcnt<this.vcnt) return;		
		if(direct) this.dirt = direct;
		var xOffset;

		if(!this.destination) {
			if(this.dirt=='right'){
				if(this.count>(this.tcnt-this.vcnt)) return;
				this.destination = -(this.count*this.width);				
			}
			else {
				if(this.count==1) return;
				this.destination =  -((this.count-2)*this.width);		
			}
		}
		
		xOffset = Math.ceil(Math.abs(this.destination - parseInt(this.obj.left)) / 10);  			
		if(this.dirt=='right') xOffset = -xOffset;		
		this.obj.left = parseInt (this.obj.left, 10) + xOffset + 'px';			
            			
		if (parseInt(this.obj.left) == this.destination)  { 										
			if(this.dirt=='right') this.count++; 
			else this.count--;
			this.destination ='';
			this.direct = null;
			
			if(this.count>1) document.getElementById(this.name+'ImgPrev').src = this.img1.src;
			else document.getElementById(this.name+'ImgPrev').src = this.img3.src;

			if(this.count<=(this.tcnt-this.vcnt)) document.getElementById(this.name+'ImgNext').src = this.img2.src;
			else document.getElementById(this.name+'ImgNext').src = this.img4.src;
		} 
		else window.setTimeout(this.name+".scroll()",this.speed);
    }        

}

function itemScroll4(name,cnt,width) {	
	this.name = name+'Div';
	this.iname = name+'Img';
	this.iskin = shop_skin;
	this.obj = document.getElementById(name).style;
    this.count = 0;
	this.cnt = cnt;  
	this.vcnt = 1;  
	this.destination = '';
	this.width = width;  
	this.tid = null;
    this.stop = false; // 정지 유무
	this.stop2 = false; // 정지 유무
	this.first = false;
	this.restart = false;
    this.speed = 10; // 간격 조정(속도)
    this.pauseDelay = 3000; // 정지 시간
    this.pauseMouseover = true; //마우스를 올렸을때 정지 유무
	this.img1 = new Image;
	this.img2 = new Image;
	this.img1.src = this.iskin+'img/main/icon_scroll_dot2.gif';
	this.img2.src = this.iskin+'img/main/icon_scroll_dot2_on.gif';
	
	this.scroll = function() {
        if(this.cnt<1) return;
		var xOffset, destination, timeoutNextCheck;		
		timeoutNextCheck = this.speed;			        

		if (!this.stop) { 
			if(!this.destination) {
				if(this.count == this.cnt) {
					this.first =true;
					this.imover(1);
				}
				if(this.count < this.cnt) this.destination =  -(this.count*this.width);	
				else this.destination =  -(this.cnt*this.width) + ((this.count-this.cnt)*this.width);	
			}
		
		 	xOffset = Math.ceil(Math.abs(this.destination - parseInt(this.obj.left)) / 15);  
			
			if(this.count <= this.cnt) xOffset = -xOffset;
		    this.obj.left = parseInt (this.obj.left, 10) + xOffset + 'px';			
            			
			if (parseInt(this.obj.left) == this.destination)  { 						
				if(this.count>0) {
					if(this.count <= this.cnt) {
						document.getElementById(this.iname+this.count).src = this.img1.src;
						document.getElementById(this.iname+(this.count+this.vcnt)).src = this.img2.src;
					}
					else {
						tmps = (this.count - (2*(this.count-this.cnt))+1);
						document.getElementById(this.iname+(tmps)).src = this.img2.src;
						document.getElementById(this.iname+(tmps+this.vcnt)).src = this.img1.src;
					}
				}
				this.count++; 
				this.destination ='';
				if(this.stop2==true) {					
					this.stop = true;
					this.stop2 = false;
				}

				timeoutNextCheck = this.pauseDelay; 
			}					
		} 
		window.setTimeout(this.name+".scroll()",timeoutNextCheck);
    }    

    this.mover = function() {
        if (this.pauseMouseover) {
			if(this.destination=='') this.stop = true;
			else this.stop2 = true;
		}
    }
       
    this.mout = function() {
        this.stop = false;		
		this.stop2 = false;		
    }

	this.imover = function(num) {
		if(this.cnt<1) return;
		this.restart = true;
        if (this.pauseMouseover) this.stop = true;				
		if(num>this.cnt + 1) {
			num = this.cnt + 1 - ((this.cnt+this.vcnt) - eval(num));
			if(num<1) num = this.cnt;
		}		

		for(i=1;i<=this.cnt;i++) 
			document.getElementById(this.iname+(i)).src = this.img1.src;

		if(num<=this.cnt) {
			for(i=num;i<(eval(num)+this.vcnt);i++) 
				document.getElementById(this.iname+(i)).src = this.img2.src;		
		}		
		else {			
			for(i=(this.cnt+1);i<=(this.cnt+this.vcnt);i++) 
				document.getElementById(this.iname+(i)).src = this.img2.src;		
		}
		
		this.count = eval(num-1);	
		this.destination = '';		
		this.restart = true;
		this.move(1);
    }

	this.imout = function() {
        this.stop = false;			
    }

	this.move = function(ns) {		
		if(this.restart==true) {
			if(!ns) {
				if(this.tid) clearTimeout(this.tid);	
				return;
			}
			else this.restart = false;
		}

		if(!this.destination) {
			if(this.count == this.cnt) this.count = 1;			
			if(this.count < this.cnt) this.destination =  -(this.count*this.width);	
			else this.destination =  -(this.cnt*this.width) + ((this.count-this.cnt)*this.width);	
		}		

		if(parseInt(this.obj.left) > this.destination) {
			xOffset = Math.ceil(Math.abs(parseInt(this.obj.left) - this.destination) / 15);  		
			xOffset = -xOffset;
		} else {
			xOffset = Math.ceil(Math.abs(parseInt(this.obj.left) - this.destination) / 15);  					
		}
		this.obj.left = parseInt (this.obj.left, 10) + xOffset + 'px';

		if (parseInt(this.obj.left) == this.destination)  {
			this.destination = '';							
			if(this.tid) clearTimeout(this.tid);
			if(this.first==true) {
				this.stop = false;
				this.stop2 = false;
				this.first = false;
			}
		} 
		else this.tid = window.setTimeout(this.name+".move()",this.speed);		
	}
}

function boxScroll(name,height) {	
	this.name = name+'Div';
	this.obj = document.getElementById(name).style;
    this.count = 1;
	this.tcnt = 2;  
	this.vcnt = 1;  
	this.destination = null;
	this.height = height;  
	this.snum	= 1;
	this.clEvt	= false;
	this.speed = 5; // 간격 조정(속도)

	this.img1 =  new Image;
	this.img2 =  new Image;
	this.img3 =  new Image;
	this.img4 =  new Image;
	this.img1.src = shop_skin+'img/common/rMenu_today_on.gif';
	this.img2.src = shop_skin+'img/common/rMenu_cart_on.gif';
	this.img3.src = shop_skin+'img/common/rMenu_today.gif';	
	this.img4.src = shop_skin+'img/common/rMenu_cart.gif';

	this.scroll = function(snum,clEvt) {        		
		if(this.clEvt==true && clEvt==1) return;
		if(this.tcnt<this.vcnt) return;		
		
		if(this.snum==snum || !this.snum) {
			if(snum==1) snum=2;
			else snum=1;
		}

		if(snum) this.snum = snum;
		var xOffset;
		
		this.clEvt = true;

		if(!this.destination) {			
			if(this.snum==2){				
				this.destination = -(this.count*this.height);				
				if(document.getElementById(this.name+'ImgToday')) document.getElementById(this.name+'ImgToday').src	= this.img3.src;
				if(document.getElementById(this.name+'ImgCart')) document.getElementById(this.name+'ImgCart').src	= this.img2.src;											
				
			}
			else if(this.snum==1) {
				if(this.count==1) {
					return;
				}
				this.destination =  -((this.count-2)*this.height);		

				if(document.getElementById(this.name+'ImgToday')) document.getElementById(this.name+'ImgToday').src	= this.img1.src;
				if(document.getElementById(this.name+'ImgCart')) document.getElementById(this.name+'ImgCart').src	= this.img4.src;
			}
		}
		
		xOffset = Math.ceil(Math.abs(this.destination - parseInt(this.obj.top)) / 5);  
			
		if(this.snum==2) xOffset = -xOffset;
		
		this.obj.top = parseInt (this.obj.top, 10) + xOffset + 'px';			
            			
		if (parseInt(this.obj.top) == this.destination)  { 										
			if(this.snum==2) this.count = 2; 
			else this.count = 1;
			this.destination = null;
			this.direct = null;
			this.clEvt = false;
		} 
		else window.setTimeout(this.name+".scroll()",this.speed);
    }        

}

function getList(limit,type,order,total,Pstart,qstr,url){
    this.aObj = new AjaxObject; 
	this.limit = limit;
	this.type = type;
	this.order = order;
	this.total = total;
	this.Pstart = Pstart;
	this.qstr = qstr;
	this.list_t = 0;
	this.img_t = 0;
	if(url) this.url = url;
	else this.url = 'php/getList.php';
	
	this.display = function() {
		if(this.total==0) return false;
		var loading = document.getElementById("list_loading");
		var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);
		loading.style.display = 'block';
		loading.style.top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) +  (h-(loading.height||parseInt(loading.style.height)||loading.offsetHeight))/2) + 'px';
		loading.style.left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft)  + (w-(loading.width||parseInt(loading.style.width)||loading.offsetWidth))/2) + 'px';
		this.aObj.getHttpRequest(this.url+"?type="+this.type+"&limit="+this.limit+"&order="+this.order+"&Pstart="+this.Pstart+this.qstr, "getLists.dispList","data"); 
		return false;		
	}

	this.dispList = function (data) {
		document.getElementById("list_loading").style.display = 'none';

		if(typeof(data['error'])!='undefined') {
			alert("리스트를 가져오는 중 에러가 발생했씁니다.");
			return;
		}
		
		if(typeof(data['item'])=='undefined') {
			if(this.type=='list') {
				obj = document.getElementById("list_table");	
				for (i=0,cnt=(obj.rows.length);i<cnt;i++) {				
					obj.deleteRow(0);				
				}		
				start = 0;
				this.list_t = 0;
			}
			else {				
				obj = document.getElementById("list_img");	

				for (i=0,cnt=(obj.childNodes.length);i<cnt;i++) {				
					obj.removeChild(obj.firstChild);
				}		
				start = 0;								
				this.img_t = 0;
			}
			if(document.getElementById("noGoods")!=null) document.getElementById("noGoods").style.display = 'block';
			return;	
		}

		if(document.getElementById("noGoods")!=null) document.getElementById("noGoods").style.display = 'none';


		if(this.type=='list') {
			obj = document.getElementById("list_table");				
			
			if(this.list_t<this.limit && this.list_t!=0) start = this.list_t;
			else {			
				for (i=0,cnt=(obj.rows.length);i<cnt;i++) {				
					obj.deleteRow(0);				
				}		
				start = 0;			
			}		

			for(i=start;i<this.limit;i++) {
				if(typeof(data['item'][i])!='undefined') {
					rtnCell(obj,data['item'][i]['uid'],data['item'][i]['image'],data['item'][i]['link'],data['item'][i]['dragd'],data['item'][i]['name'],data['item'][i]['icon'],data['item'][i]['loc'],data['item'][i]['price'],data['item'][i]['cprice'],data['item'][i]['rese'],data['item'][i]['ccnt'],data['item'][i]['tag'],data['item'][i]['sout'],data['item'][i]['cp_price'],data['item'][i]['rank'],data['item'][i]['cooperate'],data['item'][i]['cate']); 						
				}
			}	
			this.list_t = this.limit;
		} 
		else {
			obj = document.getElementById("list_img");	

			if(this.img_t<this.limit && this.img_t!=0) start = this.img_t;
			else {		
				for (i=0,cnt=(obj.childNodes.length);i<cnt;i++) {	
				  obj.removeChild(obj.firstChild);
				}		
				start = 0;					
			}
			
			cnt = 0;
			for(i=start;i<this.limit;i++) {
				if(typeof(data['item'][i])!='undefined') {
					rtnImg(obj,data['item'][i]['uid'],data['item'][i]['image'],data['item'][i]['link'],data['item'][i]['dragd'],data['item'][i]['name'],data['item'][i]['icon'],data['item'][i]['price'],data['item'][i]['cprice'],data['item'][i]['ccnt'],data['item'][i]['sout'],data['item'][i]['cp_price'],data['item'][i]['rank'],data['item'][i]['cooperate'],data['item'][i]['cate']);
					cnt++;
				}
				this.img_t = this.limit;
			}
			rtnImgLine(obj,cnt);		
		}
	}

	this.cgType = function (type,clEvt) {
		if(this.type==type) return false;
		
		if(document.getElementById("btn_"+type)) {
			document.getElementById("btn_"+type).style.display = 'none';
			document.getElementById("btn_"+this.type).style.display = 'block';			
		}
		
		if(document.getElementById("imgof_"+type)) {
			document.getElementById("imgof_"+type).src = document.getElementById("imgof_"+type).src.substr(0, document.getElementById("imgof_"+type).src.length-7) + "on.gif";
			document.getElementById("imgof_"+this.type).src = document.getElementById("imgof_"+this.type).src.substr(0, document.getElementById("imgof_"+this.type).src.length-6) + "off.gif";
		}

		document.getElementById("list_"+type).style.display = 'block';
		document.getElementById("list_"+this.type).style.display = 'none';
		this.type = type;
			
		for (j = 0;  j< document.listForm.elements.length; j++) {
			if(document.listForm.elements[j].name == 'compare[]' && document.listForm.elements[j].checked == true) { 
				document.listForm.elements[j].checked = false;
			} 
		}

		if(eval("this."+this.type+"_t")==0 || eval("this."+this.type+"_t")!=this.limit) {
			this.display();
		}

		if(clEvt==1) setCookie("mallType",type,3600,"/",domain=window.document.domain||window.location.hostname);

		return false;
	}

	this.cgLimit = function (vls,clEvt) { //ajaxPaging()와 연동
		if(!vls) vls = 10;
		this.limit = vls;	
		aPage.page_record_num = vls;
		aPage.makePage();
		
		if(clEvt==1) {
			aPage.page = 1;
			delCookie("mallPage");
		}

		this.Pstart = 0;
		this.list_t = 0;
		this.img_t = 0;
		this.display();
		aPage.printPage();	

		if(typeof(document.moneySearch) != 'undefined') document.moneySearch.limit.value = vls;		
		setCookie("mallLimit",vls,3600,"/",domain=window.document.domain||window.location.hostname);	
	}

	this.cgOrder = function (vls,clEvt) {
		if(document.getElementById("order_"+this.order).tagName=='IMG') {
			document.getElementById("order_"+this.order).src = str_replace("_on","_off",document.getElementById("order_"+this.order).src);
			document.getElementById("order_"+vls).src = str_replace("_off","_on",document.getElementById("order_"+vls).src);
		}
		else {
			document.getElementById("order_"+this.order).className = 'tab_off small';
			document.getElementById("order_"+vls).className = 'tab_on small';
		}
		this.order = vls;
		this.list_t = 0;
		this.img_t = 0;

		if(typeof(document.moneySearch) != 'undefined') document.moneySearch.order.value = vls;

		if(clEvt==1) setCookie("mallOrder",vls,3600,"/",domain=window.document.domain||window.location.hostname);
		return this.display();
	}

	this.cgBest = function (vls,clEvt) {
		if(document.getElementById("best_"+this.order).tagName=='IMG') {
			document.getElementById("best_"+this.order).src = str_replace("_on","_off",document.getElementById("best_"+this.order).src);
			document.getElementById("best_"+vls).src = str_replace("_off","_on",document.getElementById("best_"+vls).src);
		}
		else {
			document.getElementById("best_"+this.order).className = 'tab_off small';
			document.getElementById("best_"+vls).className = 'tab_on small';
		}
		this.order = vls;
		this.list_t = 0;
		this.img_t = 0;
		
		if(clEvt==1) setCookie("mallBest",vls,3600,"/",domain=window.document.domain||window.location.hostname);

		return this.display();
	}
}

function goTop(desy) {
	var Timer;
	var starty = document.body.scrollTop;
	var oriy = 0;  //top 위치
    var speed = 3;
	
	if(Timer) clearTimeout(Timer);		
			
	if(!desy) desy = starty;
	desy += (oriy - starty) / speed;
	if (desy < oriy) desy = oriy;		
	var posY = Math.ceil(desy);
	window.scrollTo(0, posY);
	if((Math.floor(Math.abs(starty - oriy)) < 1)){
		clearTimeout(Timer);
		window.scroll(0,oriy);
	}
	else if(posY > oriy){
		Timer = setTimeout("goTop("+desy+")",1);//올라가는 속도(낮을수록 빠름)
	}
	else{
		clearTimeout(Timer);
	}
}

function goBottom(desy) {
	var Timer;
	var starty = document.body.scrollTop;  
	var oriy = document.body.scrollHeight;  //Bottom 위치
    var speed = 10;

	if(Timer) clearTimeout(Timer);
				
	if(!desy) desy = starty;
	desy += (oriy - starty) / speed;
	if (desy > oriy) desy = oriy;		
	var posY = Math.ceil(desy);
	window.scrollTo(0, posY);
	if((Math.floor(Math.abs(oriy - starty)) < 1)){
		clearTimeout(Timer);
		window.scroll(0,oriy);
	}
	else if(posY < oriy){
		Timer = setTimeout("goBottom("+desy+")",1);//올라가는 속도(낮을수록 빠름)
	}
	else{
		clearTimeout(Timer);
	}
}

var isMsie = document.all ? true : false; 
if(isMsie) {
	var tmp_now = new Date();
	tmp_now = tmp_now.getYear();
	if(parseInt(tmp_now)<1000) isMsie = false;
}

function playMultimediaFile(obj) {
	try
	{
		el = obj.previousSibling;
		while (el.tagName!="A")
			el = el.previousSibling;
		var sURL = el.href;
		var sID = el.href;

		if (document.getElementById(sID)==null)
		{
			var sHTML = "<EMBED id='"+sID+"' autostart=true src='"+sURL+"'><br>";
			el.insertAdjacentHTML('beforeBegin',sHTML);
		}
	}
	catch(e){}
}