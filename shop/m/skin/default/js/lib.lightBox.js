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
				if(width) tag.style.width = "100%";
				if(height) tag.height = height + "px";
				tag.frameBorder = "0";
				tag.Border = "0";
				conBoxBody.appendChild(tag)					
			break;

			case "html" :
				conBoxBody.innerHTML = url;
			break;
		}

		this._Light.appendChild(conBoxBody);	
            
		this.initCheckBoxs("object");
		this.initCheckBoxs("select");			
		
		this._Loading.style.display	= "none";
		this._Light.className		= "inBright";		
		this.setCenter(this._Light,'Y');	
		this.hideCheckBox();		
	},
	cgSize: function(height,callback) {
		tag	= document.getElementById("pIframe");
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
		var top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) + (h-(obj.height||parseInt(obj.style.height)||obj.offsetHeight))/2);		 		
		
		if(main=='Y') this.Top = parseInt(top-(document.body.scrollTop || document.documentElement.scrollTop));
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