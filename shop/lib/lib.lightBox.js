/**************************************************************************************
####	previl LightBox 
####	Beta 2008
####	http://dev.previl.net
***************************************************************************************/

var pLightBox = {
	init: function() {
		this.isInit		= true;		
		this.Width		= 0;
		this.Height		= 0;
		this.Top		= 0;
		this.Opacity	= 0;
		
		this._Dark		= document.createElement("DIV");
		this._Light		= document.createElement("DIV");		
		this._Loading	= document.createElement("DIV");		
		this._Dark.id		= "pDarkLayer";
		this._Light.id		= "pLightLayer";
		this._Loading.id	= "pLoadingLayer";
		this._Light.className= "inDark";		
		this._Dark.className= "inDarkLayer";		
		this._Dark.style.height	=  (document.documentElement.scrollHeight)? document.documentElement.scrollHeight+'px' : document.body.scrollHeight+'px';
		this._Loading.innerHTML = "Loading...";
		this._Loading.className = "loading";			
		try {
			tElement = document.getElementsByTagName("body")[0];  
			if (typeof(tElement) === "undefined" || tElement === null) {			
				alert('Could not find the BODY element.');
				return false;
			}		
		} catch (ex) {
			return false;
		}

		var pBody = document.createElement('DIV');
		pBody.id = "lightBox";
		tElement.appendChild(pBody);
		
		pBody.appendChild(this._Light);			
		pBody.appendChild(this._Dark);
		pBody.appendChild(this._Loading);		
				
		this.aCheckList = new Array();		
		
	},
	show: function(url, type,width,height,title,opacity,callback) {
        var self = (this) ? this:'';
		if(!this.isInit) this.init();						

		if(opacity) this.Opacity	= opacity;
		else this.Opacity	= 50;

		if(this.Opacity>0) {
			this._Dark.style.top		= "0px";		
			this._Dark.style.left		= "0px";
			if (this._Dark.filters) {	
				try {
					this._Dark.filters.item("DXImageTransform.Microsoft.Alpha").opacity = this.Opacity;
				} catch (e) {			
					this._Dark.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + this.Opacity + ')';
				}
			} else {
				this._Dark.style.opacity = this.Opacity / 100;
			}    
			this._Dark.style.display	= "block";
		}

		if(!this.Width) this.Width	= this.getWidth();
		if(!this.Height) this.Height	= this.getHeight();		
		this.setCenter(this._Loading);


		conBox	= document.createElement("DIV");
		conBox.id			= 'boxLayer';
		conBox.style.width	= eval(width) + 2 + 'px';
		conBox.innerHTML	= "<div style='float:left; width:5px;' class='boxTopLeft'></div>";
		conBox.innerHTML += "<div id='boxTitle' style='float:left; width:"+(width-8)+"px;' class='boxTop'><div style='float:left; padding-left:8px;'>"+title+"</div><div id='pLightBoxClose' onclick='pLightBox.hide();"+callback+"'></div></div>";
		conBox.innerHTML += "<div style='float:right; width:5px;' class='boxTopRight'></div>";

		conBoxBody	= document.createElement("DIV");
		conBoxBody.className = "boxBody";

		switch(type) {
			case "image" :
				tag				= document.createElement("img");
				tag.id			= "pImage";
				tag.src			= url;			
				tag.className	= "boxCenter";		
				if(width) tag.style.width = width + "px";
				if(height) tag.style.height = height + "px";
				tag.onclick		= function() { self.hide(); };
				conBoxBody.appendChild(tag)
			break;

			case "iframe" :
				tag				= document.createElement("iframe");
				tag.id			= "pIframe";
				tag.src			= url;
				if(width) tag.width = width - 6 + "px";
				if(height) tag.height = height + "px";
				tag.frameBorder = "0";
				tag.Border = "0";
				conBoxBody.appendChild(tag)					
			break;

			case "html" :
				conBoxBody.innerHTML = url;
			break;
		}

		conBox.appendChild(conBoxBody);
		this._Light.appendChild(conBox);	
            
		this.initCheckBoxs("object");
		this.initCheckBoxs("select");			
		
		this._Loading.style.display	= "none";
		this._Light.className		= "inBright";		
		this.setCenter(this._Light,'Y');	
		this.hideCheckBox();		
	},
	cgSize: function(width,height,title,callback) {
		conBox	= document.getElementById("boxLayer");
		conBox.style.width	= eval(width) + 2 + 'px';
		document.getElementById("boxTitle").style.width = eval(width)- 8 + 'px';
		tag	= document.getElementById("pIframe");
		if(width) tag.width = eval(width) - 6 + "px";
		if(height) tag.height = eval(height) + "px";
		this.setCenter(this._Loading);		
	},
	hide: function() {
		this._Light.className		= "inDark";	
		this._Dark.style.display	= "none";		
		this.showCheckBox();
		this._Light.innerHTML = '';

	},
	setCenter: function(obj,main){
		if (!obj) return;

		var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);

		var left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft) + (w-(obj.width||parseInt(obj.style.width)||obj.offsetWidth))/2);
		var top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) + (h-(obj.height||parseInt(obj.style.height)||obj.offsetHeight))/2);		 		
		
		if(main=='Y') this.Top = parseInt(top-(document.body.scrollTop || document.documentElement.scrollTop));
		obj.style.left = left + "px";
		obj.style.top  = top + "px";
	},
	initCheckBoxs: function(sTagName) {
		var aCheckList = document.getElementsByTagName(sTagName);
		for(var i=0; i<aCheckList.length; i++) {
			this.aCheckList.push(aCheckList.item(i));			
		}
	},
	hideCheckBox: function() {
		for(var i=0; i<this.aCheckList.length; i++) {
			this.aCheckList[i].style.visibility = "hidden";
		}
	},
	showCheckBox: function() {
		for(var i=0; i<this.aCheckList.length; i++) {
			this.aCheckList[i].style.visibility = "visible";
		}
	},
	getHeight: function() {
		var Height = WHeight = 0;

		if (window.innerHeight && window.scrollMaxY) Height = window.innerHeight + window.scrollMaxY;
		else if (document.body.scrollHeight > document.body.offsetHeight) Height = document.body.scrollHeight;
		else Height = document.body.offsetHeight;		
		
		if (self.innerHeight) WHeight = self.innerHeight;
		else if (document.documentElement && document.documentElement.scrollHeight && document.documentElement.clientHeight ) { 
			if( document.documentElement.scrollHeight > document.documentElement.clientHeight ) WHeight = document.documentElement.scrollHeight;
			else WHeight = document.documentElement.clientHeight;			
		} 
		else if (document.body) WHeight = document.body.clientHeight;
		
		if(Height < WHeight) Height = WHeight;

		if(!((navigator.userAgent.toLowerCase().indexOf("msie") != -1) && (navigator.userAgent.toLowerCase().indexOf("opera") == -1)) ) {
			Height -= 16;			
		}
		return Height;
	
	},
	getWidth: function() {
		var Width = WWidth = 0;

		if (window.innerWidth && window.scrollMaxX) Width = window.innerWidth + window.scrollMaxX;
		else if (document.body.scrollWidth > document.body.offsetWidth)Width = document.body.scrollWidth;
		else Width = document.body.offsetWidth;
		
		if (self.innerWidth) WWidth = self.innerWidth;
		else if (document.documentElement && document.documentElement.scrollWidth && document.documentElement.clientWidth ) { 
			if( document.documentElement.scrollWidth > document.documentElement.clientWidth ) WWidth = document.documentElement.scrollWidth;
			else WWidth = document.documentElement.clientWidth;			
		} 
		else if (document.body) WWidth = document.body.clientWidth;
	     
		if(Width < WWidth) Width = WWidth;				

		if(!((navigator.userAgent.toLowerCase().indexOf("msie") != -1) && (navigator.userAgent.toLowerCase().indexOf("opera") == -1)) ) {
			Width -= 16;			
		}
		return Width;
	}
}

var prevOnResize = window.onresize;
window.onresize = function () {	
	if(document.getElementById('pLightLayer')) {
		if(prevOnResize != undefined) prevOnResize();	
		tObj = document.getElementById('pLightLayer');
		var left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft) + ((pLightBox.getWidth()/2)-((tObj.width||parseInt(tObj.style.width)||tObj.offsetWidth)/2)));
		tObj.style.left = left + "px";
	}
}

/*
var prevOnScroll = window.onscroll;
window.onscroll = function () {
	
	if(document.getElementById('pLightLayer')) {		
					
		function moveLayer(){		
			tObj = document.getElementById('pLightLayer');
			if(tObj==null) return;
			tTo  = (document.body.scrollTop || document.documentElement.scrollTop) + pLightBox.Top;
			tFrom= parseInt (tObj.style.top);

			if (tFrom != tTo) {
				yOffset = Math.ceil(Math.abs(tTo - tFrom) / 15);  //속도
				
				if (tTo < tFrom) yOffset = -yOffset;          
				
				tObj.style.top = (tFrom + yOffset) + 'px';		
				tId=setTimeout(moveLayer,50); 
			} 
			else {
				if(typeof(tId) != "undefined") clearTimeout(tId);			
			}	
		}
		moveLayer();
	}
}

*/

/*********************************************************************
*	LightBox Css
*********************************************************************

body { width:100%; height:100%; margin: 0px; padding: 0px; }
div.inBright { z-index: 99999; position:absolute; visibility:visible; display:block; left:0px; top:0px; border:10px }
div.inDark { z-index: 99999; position:absolute; visibility:hidden; display:none; left:-10000px; top:-10000px; width:1px; height:1px; }
div.inDarkLayer {z-index: 99997; position:absolute; display:none; width:100%; height:100%; background-color:#000000;}
img.boxCenter { cursor:pointer; border: solid 4px #000000; background-color: #FFFFFF; }
div.loading { z-index: 99998; background-image:url('bigBlackWaiting.gif'); background-repeat:no-repeat; background-position:center; text-align:center; position:absolute; font-family: verdana,tahoma; font-size: 9pt; color: #ffffff; padding-top:60px;}
*/