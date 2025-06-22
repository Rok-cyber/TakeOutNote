/**************************************************************************************
####	previl MessagetBox 
####	Beta 2008
####	http://dev.previl.net
***************************************************************************************/

var messageBox = {
	rtnFunction : null,
	init: function() {
		this.isInit		= true;	
		this.opacity	= 0;		
		this.fadeType	= null;
		this.rtnValue	= null;
		this.dragStatus = false;
		this.posX		= 0;
		this.posY		= 0;
			
		this._Title		= document.createElement("DIV");
		this._Message	= document.createElement("DIV");
		this._Confirm	= document.createElement("DIV");
		this._Title.id	= "titleLayer";
		this._Message.id= "messageLayer";
		this._Confirm.id= "confirmLayer";
		this._Title.className	= "title";
		this._Message.className = "message";
		this._Confirm.className = "confirm";
		
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
		pBody.id = "messageDiv";
		tElement.appendChild(pBody);
		
		this.pBody = document.getElementById("messageDiv");					
		if( this.pBody != null ) {				
			this.pBody.appendChild(this._Title);			
			this.pBody.appendChild(this._Message);			
			this.pBody.appendChild(this._Confirm);
		}
		else {
			document.write(this._Title.outerHTML);
			document.write(this._Message.outerHTML);
			document.write(this._Confirm.outerHTML);			
		}

		this._Title.onmousedown = function (e) { messageBox.dragStart(e); };

		this.aCheckList = new Array();
	},
	
	show: function(message,width,height,types,rtnObj,getBtn) {				
		if(!this.isInit) this.init();
		if(!types) types = "알림";
        if(this.opacity==100 || this.opacity==1) {		
			this.opacity				= 0;
			this.fadeType				= null;
			this._Message.innerHTML		= '';
			this._Confirm.innerHTML		= '';
			this.pBody.style.display	= 'none';
		}
				
		this.pBody.style.width= width+'px';		 
		this.pBody.style.height= height+'px';		 
		this._Title.innerHTML = types;
		this._Message.innerHTML = message;

		var btnStr = '';
		if(getBtn==null) getBtn = "확인";
		var btnArr = getBtn.split(',');
		for (i=0, cnt=btnArr.length;i<cnt;i++) { 
			btnStr += ' <input style="padding-top:2px;" type="button" onfocus="blur();" class="btnBox" onclick="messageBox.setValue(this.value);messageBox.hide();" value="' + btnArr[i] + '">'; 
		}
		this._Confirm.innerHTML = btnStr;

		this.pBody.style.display= 'block';		 
		this.setCenter(this.pBody);
		
		if (this.pBody.filters) this.pBody.style.filter = 'alpha(opacity=0)';
		else this.pBody.style.opacity = this.pBody.style.MozOpacity = 0;
		
		this.initCheckBoxs("object");
		this.initCheckBoxs("select"); 	
		this.fadeIn();		
		//this.hideCheckBox();			
		
		if (rtnObj != null) this.returnFunction = rtnObj;
		else this.returnFunction = null;

		if(cnt==1) setTimeout(this.fadeOut,3000); 			
	},

	hide: function() {						
		this.fadeOut();					
		if (this.returnFunction != null) this.returnFunction();
	},

	setValue: function(value) {						
		this.rtnValue = value;
	},

	getValue: function() {						
		return this.rtnValue;
	},

	cancelEvent: function (e) {
		return false;
	},

	callbacks : function(e){ 
		if(e.preventDefault) {  
			e.preventDefault(); 
		} 
	},

	dragStart: function (event) {
		this.dragStatus = true;
        this._Title.style.cursor = 'move'; 		
		var isMsie = document.all ? true : false; 
		if (isMsie) {
			var e = window.event;
			window.document.attachEvent('onmousemove', messageBox.draging);
			window.document.attachEvent('onselectstart', messageBox.cancelEvent);
			window.document.attachEvent('onmouseup', messageBox.dragEnd);
		}
		else {
			var e = event;
			window.document.addEventListener('mousemove', messageBox.draging, false);
			window.document.addEventListener('mouseup', messageBox.dragEnd, false);
			window.document.addEventListener('selectstart', messageBox.cancelEvent,false);	
			window.document.addEventListener('mousedown', messageBox.callbacks, false);
		}
		this.posX = parseInt(e.clientX);
		this.posY = parseInt(e.clientY);		
	},
	dragEnd: function (e) {
		messageBox.dragStatus = false;
		messageBox._Title.style.cursor = 'default'; 	
	},
	draging: function (e) {				
		if (messageBox.dragStatus) {
			if (parseInt(e.clientX - messageBox.posX) > 5 || parseInt(e.clientX - messageBox.posX) < -5) {
				messageBox.pBody.style.left = parseInt(e.clientX) - parseInt(messageBox.pBody.offsetWidth) / 3 + document.documentElement.scrollLeft + 'px';
				messageBox.posX = e.clientX;
			}
			if (parseInt(e.clientY - messageBox.posY) > 5 || parseInt(e.clientY - messageBox.posY) < -5) {
			messageBox.pBody.style.top = parseInt(e.clientY) - 15 + document.documentElement.scrollTop + 'px';
			messageBox.posY = e.clientY;
			}
			return false;
		}
	},

	fadeIn: function() {			        
		if(messageBox.fadeType=='out') return;
        
		if (messageBox.opacity < 100) {
			
			messageBox.opacity += 20;			
			if (messageBox.opacity < 0) messageBox.opacity = 100;
			if (messageBox.pBody.filters) {	
				try {
					messageBox.pBody.filters.item("DXImageTransform.Microsoft.Alpha").opacity = messageBox.opacity;
				} catch (e) {			
					messageBox.pBody.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + messageBox.opacity + ')';
				}
			} else messageBox.pBody.style.opacity = messageBox.opacity / 100;
			messageBox.fadeType = 'in';
			tId = setTimeout(messageBox.fadeIn,30); 
		}
		else {
			if(typeof(tId) != "undefined") clearTimeout(tId);	
			messageBox.fadeType = '';
		}
	},

	fadeOut: function() {				
		if(messageBox.fadeType=='in') return;

        if (messageBox.opacity > 0) {
			
			messageBox.opacity -= 25;			
			if (messageBox.opacity > 100) messageBox.opacity = 0;
			if (messageBox.pBody.filters) {	
				try {
					messageBox.pBody.filters.item("DXImageTransform.Microsoft.Alpha").opacity = messageBox.opacity;
				} catch (e) {			
					messageBox.pBody.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + messageBox.opacity + ')';
				}
			} else messageBox.pBody.style.opacity = messageBox.opacity / 100;
			messageBox.fadeType = 'out';
			tId = setTimeout(messageBox.fadeOut,30); 
		} 			
		else {
			if(typeof(tId) != "undefined") clearTimeout(tId);
			messageBox.fadeType = '';
			messageBox._Title.innerHTML		= '';
			messageBox._Message.innerHTML		= '';
			messageBox._Confirm.innerHTML		= '';
			messageBox.pBody.style.display	= 'none';
			messageBox.showCheckBox();
		}
	},

    setCenter: function(obj){		
		if (!obj) return;
        
		var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);

		var left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft) + (w-(obj.width||parseInt(obj.style.width)||obj.offsetWidth))/2) + 'px';
		var top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) + (h-(obj.height||parseInt(obj.style.height)||obj.offsetHeight))/2) + 'px';		 
		
		obj.style.left = parseInt(left) + "px";
		obj.style.top  = parseInt(top) + "px";
		
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
	}
}
