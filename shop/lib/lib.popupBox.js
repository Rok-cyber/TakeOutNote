/**************************************************************************************
####	previl PopupBox 
####	Beta 2010
####	http://dev.previl.net
***************************************************************************************/

var pPopupBoxObj = null;
var pPopupBoxObjPre = null;
var pPopupBox = function(url, top, left, width, height, num) {	

	this.dragStatus = false;
	this.posX		= 0;
	this.posY		= 0;
	this.tmpX		= 0;
	this.tmpY		= 0;
	this.url		= url;
	this.top		= top;
	this.left		= left;
	this.width		= width;
	this.height		= height;

	this._Light		= document.createElement("DIV");		
	this._Loading	= document.createElement("DIV");				
	
	this._Light.className= "inDark";		
	this._Loading.innerHTML = "Loading...";
	this._Loading.className = "ploading";			
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
	pBody.id = "popupBox_"+num;
	tElement.appendChild(pBody);
	
	pBody.appendChild(this._Light);			
	pBody.appendChild(this._Loading);	
					
	this.aCheckList = new Array();		
}

pPopupBox.prototype.show = function() {
	var self = (this) ? this:'';
	var req = null;
	var tmp = null;		

	conBox	= document.createElement("DIV");
	conBox.className	= 'pboxLayer';
	
	conBoxBody	= document.createElement("DIV");
	conBoxBody.className = "pboxBody";
	
	try { req = new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {
		try { req = new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {
			try { req = new XMLHttpRequest(); } catch(e) {}
		}
	}
	if (req == null) {
		tag				= document.createElement("iframe");
		tag.id			= "pIframe";
		tag.src			= this.url;
		tag.width = this.width + "px";
		tag.height = this.height + "px";
		tag.frameBorder = "0";
		tag.Border = "0";
		tag.scrolling = "no";
		conBoxBody.appendChild(tag)							
	}
	else {		
		req.open("GET", this.url, false);
		req.send(null);
		tmp = req.responseText.split('<!-- LAYER AREA  -->')
		conBoxBody.style.width = this.width + "px";
		conBoxBody.style.height = this.height + "px";
		conBoxBody.innerHTML = tmp[1];
		this._Light.onmousedown = function (e) { self.dragStart(e); };
	}
	
	conBox.appendChild(conBoxBody);
	this._Light.appendChild(conBox);	
		
	//this.initCheckBoxs("object");
	this.initCheckBoxs("select");			
	
	this._Loading.style.display	= "none";
	this._Light.className		= "pinBright";		
	this._Light.style.left = this.left + "px";
	this._Light.style.top  = this.top + "px";

	this.hideCheckBox();		
}

pPopupBox.prototype.hide = function() {
	this._Light.className		= "pinDark";	
	this.showCheckBox();
	this._Light.innerHTML = '';
}
		
pPopupBox.prototype.initCheckBoxs = function(sTagName) {
	var aCheckList = document.getElementsByTagName(sTagName);
	for(var i=0; i<aCheckList.length; i++) {
		this.aCheckList.push(aCheckList.item(i));			
	}
}

pPopupBox.prototype.hideCheckBox = function() {
	for(var i=0; i<this.aCheckList.length; i++) {
		this.aCheckList[i].style.visibility = "hidden";
	}
}

pPopupBox.prototype.showCheckBox = function() {
	for(var i=0; i<this.aCheckList.length; i++) {
		this.aCheckList[i].style.visibility = "visible";
	}
}	

pPopupBox.prototype.cancelEvent = function (e) {
	return false;
}

pPopupBox.prototype.callbacks = function(e){ 
	if(e.preventDefault) {  
		e.preventDefault(); 
	} 
}

pPopupBox.prototype.dragStart = function (event) {
	var self = (this) ? this:'';
	this.dragStatus = true;
	this._Light.style.cursor = 'move'; 		
	var isMsie = document.all ? true : false; 

	pPopupBoxObjPre = pPopupBoxObj;
	if(pPopupBoxObjPre) pPopupBoxObjPre._Light.style.zIndex = "99999";
	pPopupBoxObj = this;
	this._Light.style.zIndex = "100000";
	
	if (isMsie) {
		var e = window.event;
		window.document.attachEvent('onmousemove', self.draging);
		window.document.attachEvent('onselectstart', self.cancelEvent);
		window.document.attachEvent('onmouseup', self.dragEnd);
	}
	else {
		var e = event;
		window.document.addEventListener('mousemove', self.draging, false);
		window.document.addEventListener('mouseup', self.dragEnd, false);
		window.document.addEventListener('selectstart', self.cancelEvent,false);	
		window.document.addEventListener('mousedown', self.callbacks, false);
	}
	this.posX = parseInt(e.clientX);
	this.posY = parseInt(e.clientY);		
	this.tmpX = this.posX - parseInt(this._Light.style.left);
	this.tmpY = this.posY - parseInt(this._Light.style.top);
}

pPopupBox.prototype.dragEnd = function (e) {
	pPopupBoxObj.dragStatus = false;
	pPopupBoxObj._Light.style.cursor = 'default'; 	
}

pPopupBox.prototype.draging = function (e) {				
	if (pPopupBoxObj.dragStatus) {		
		if (parseInt(e.clientX - pPopupBoxObj.posX) > 5 || parseInt(e.clientX - pPopupBoxObj.posX) < -5) {
			pPopupBoxObj._Light.style.left = parseInt(e.clientX) - pPopupBoxObj.tmpX + 'px';				
			pPopupBoxObj.posX = e.clientX;
		}
		if (parseInt(e.clientY - pPopupBoxObj.posY) > 5 || parseInt(e.clientY - pPopupBoxObj.posY) < -5) {
			pPopupBoxObj._Light.style.top = parseInt(e.clientY) - pPopupBoxObj.tmpY + 'px';				
			pPopupBoxObj.posY = e.clientY;
		}
		return false;
	}	
}

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