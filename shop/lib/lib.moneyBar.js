var pmoneyBar = {	
	init: function(id) {
		this.isInit		= true;	
		this.dragStatus = false;
		this.posX		= 0;		
		this.barX		= 0;		
		this.id			= id;
		this.width		= 216;
		this.barWidth	= 19;
		this.maxMoney	= 1000000;
		this.barType	= null;
		this.unit		= "원";
		this.type		= "bar";
		this.smoney		= 0;
		this.emoney		= 0;
		this.callBack	= "rtnValue";
		this.path	= "./";
			
		this._moneyBox		= document.createElement("DIV");
		this._barLeft		= document.createElement("DIV");		
		this._barRight		= document.createElement("DIV");
		this._moneyLeft		= document.createElement("DIV");
		this._moneyRight	= document.createElement("DIV");
		this._moneyBar		= document.createElement("DIV");
		this._moneysBar		= document.createElement("DIV");
		this._btnSearch		= document.createElement("DIV");
		this._inputBox		= document.createElement("DIV");

		this.pBody = document.getElementById(id);							
		if( this.pBody != null ) {				
			this.pBody.appendChild(this._moneyBox);
			this.pBody.appendChild(this._btnSearch);
			this.pBody.appendChild(this._inputBox);
		}
		else {
			document.write(this._moneyBox.outerHTML);
			document.write(this._btnSearch.outerHTML);			
			document.write(this._inputBox.outerHTML);			
		}

		this._moneyBox.appendChild(this._barLeft);						
		this._moneyBox.appendChild(this._barRight);			
		this._moneyBox.appendChild(this._moneyLeft);
		this._moneyBox.appendChild(this._moneyRight);
		this._moneyBox.appendChild(this._moneyBar);
		this._moneyBox.appendChild(this._moneysBar);			
		
		this._moneyBox.id	= "moneyBox";
		this._barLeft.id	= "barLeft";
		this._barRight.id	= "barRight";
		this._moneyLeft.id	= "moneyLeft";
		this._moneyRight.id	= "moneyRight";
		this._moneyBar.id	= "moneyBar";
		this._moneysBar.id	= "moneysBar";
		this._btnSearch.id	= "btnSearch";
		this._inputBox.id	= "inputBox";

		this._barLeft.style.left	= -(this.barWidth) + 'px';
		this._barRight.style.left	= this.width + 'px';
		this._moneyBar.style.width	= this.width + 'px';
		this._moneyRight.style.left	= this.width + 'px';
		this._moneyBar.innerHTML	= "&nbsp;";		
		this._moneyLeft.innerHTML	= this.moneyChange(0);		
		this._moneysBar.style.left	= '0px';
		this._moneysBar.style.width	= this.width + 'px';	
		this._btnSearch.style.left	= this.width + 38 + 'px';	
		this._inputBox.style.width	= this.width + 34 + 'px';	
		this.tmp = '';
	},
	
	show: function (id,callBack,path,width,maxMoney,unit) {	
		var self = (this)?this:'';
		if(!this.isInit) this.init(id);
		if(callBack) this.callBack = callBack;		
		if(path) this.path = path;		
		if(width) this.dwidth = width;		
		if(maxMoney) {
			this.maxMoney = maxMoney;		
			this._moneyRight.innerHTML	= this.moneyChange(this.maxMoney);
		}
		this.emoney = this.maxMoney;

		this.tmps = this.path + "img/common/money_text_bg.gif";

		if(unit) this.unit = unit;

		tag	= document.createElement("img");
		tag.src			= this.path + "img/common/money_sbar_bg.gif";
		tag.style.width = this._moneysBar.style.width;
		tag.style.height= "6px";
		tag.id			= "moneysBar2";
		this._moneysBar.appendChild(tag);	

		tag	= document.createElement("div");
		tag.style.background	= "url("+this.path + "img/common/money_move_left.gif)";
		tag.style.width = "19px";
		tag.style.height = "14px";
		tag.style.cursor= "pointer";
		tag.onmousedown	= function (e) { self.setMoney(e,'Left') };
		this._barLeft.appendChild(tag);	

		tag	= document.createElement("div");
		tag.style.background	= "url("+this.path + "img/common/money_text_bg.gif)";
		tag.style.textAlign		= 'right';
		tag.style.marginTop		= '3px';
		tag.style.width			= "32px";
		tag.style.height		= "21px";
		tag.id					= "LeftMoney"; 

		tag2	= document.createElement("img");
		tag2.src	= this.path + "img/common/money_text_bg_r.gif";
		tag.appendChild(tag2);

		tag.style.position		= 'relative';
		tag.style.left			= '-11px';
		this._barLeft.appendChild(tag);
		
		tag	= document.createElement("div");
		tag.style.background	= "url("+this.path + "img/common/money_move_right.gif)";
		tag.style.width = "19px";
		tag.style.height = "14px";
		tag.style.cursor= "pointer";
		tag.onmousedown	= function (e) { self.setMoney(e,'Right') };
		this._barRight.appendChild(tag);

		tag	= document.createElement("div");
		tag.style.background	= "url("+this.path + "img/common/money_text_bg2.gif)";
		tag.style.textAlign		= 'right';
		tag.style.marginTop		= '3px';
		tag.style.width			= "60px";
		tag.style.height		= "21px";
		tag.id					= "RightMoney"; 

		tag2	= document.createElement("img");
		tag2.src	= this.path + "img/common/money_text_bg2_r.gif";
		tag.appendChild(tag2);

		this._barRight.appendChild(tag);

		tag	= document.createElement("img");
		tag.src	= this.path + "img/common/money_btn_search.gif";
		tag.style.cursor= "pointer";		
		tag.onclick	= function (e) { self.sendValue() };
		this._btnSearch.appendChild(tag);	

		tag	= document.createElement("img");
		tag.src	= this.path + "img/common/money_btn_input.gif";		
		tag.style.cursor= "pointer";
		tag.style.margin = "0 0 0 2px";
		tag.onclick	= function () { self.changeType(this); };
		this._btnSearch.appendChild(tag);	

		this._inputBox.innerHTML = "<input type='text' size='10' value='0' maxlength='10' onkeyup='pmoneyBar.inputMoney(this.value,this)' onfocus=\"this.value=''\" onblur='pmoneyBar.putMoney(this,this.value,0)'> ~ <input type='text' size='10' value='"+this.inputMoney(this.maxMoney)+"' maxlength='10' onfocus=\"this.value=''\" onblur='pmoneyBar.putMoney(this,this.value,"+this.maxMoney+")'>원";
		this._inputBox.style.display = 'none';

		this.barType				= 'Right';
		this._moneyRight.innerHTML	= this.moneyChange(this.maxMoney);		

	},

	setMoney : function (event,type) {	
		var isMsie = document.all ? true : false; 
		this.dragStatus = true;	
		this.barType	= type;
		
		if (isMsie) {
			var e = window.event;
			window.document.attachEvent('onmousemove', pmoneyBar.draging);			
			window.document.attachEvent('onmouseup', pmoneyBar.dragEnd);
			window.document.attachEvent('onselectstart', function(e){ return false;});	
		}
		else {
			var e = event;
			window.document.addEventListener('mousemove', pmoneyBar.draging, false);
			window.document.addEventListener('mouseup', pmoneyBar.dragEnd, false);
			window.document.addEventListener('selectstart', function(e){ return false;},false);	
			window.document.addEventListener('mousedown', pmoneyBar.callbacks, false);
		}

		this.barX = eval("parseInt(this._bar"+type+".style.left)");
		this.posX = parseInt(e.clientX);		
	},

	callbacks : function(e){ 
		if(e.preventDefault) {  
			e.preventDefault(); 
		} 
	},

	dragEnd: function (e) {
		pmoneyBar.dragStatus = false;		
	},

	draging: function (e) {								
		if (pmoneyBar.dragStatus==true){							
			var move = pmoneyBar.barX + e.clientX - pmoneyBar.posX;			
			if(pmoneyBar.barType=='Left') {				
				var lmtBar = parseInt(pmoneyBar._barRight.style.left);
				if ((move >= -(pmoneyBar.barWidth))  && (move <= lmtBar - pmoneyBar.barWidth)) {	
					pmoneyBar._barLeft.style.left	= move  + 'px';											
					pmoneyBar._moneysBar.style.left = (move + pmoneyBar.barWidth) + 'px';
					pmoneyBar._moneysBar.style.width= lmtBar - parseInt(pmoneyBar._moneysBar.style.left) + 'px';
					document.getElementById('moneysBar2').style.width = lmtBar - parseInt(pmoneyBar._moneysBar.style.left) + 'px';
					
					per		= (move + pmoneyBar.barWidth + 1)*100/(pmoneyBar.width + 1);
					pmoneyBar.smoney = (pmoneyBar.maxMoney * Math.round(per)/100);
					
					tmps = "a"+pmoneyBar.smoney;
					leftMargin = 10+(5*(tmps.length-2));					
					pmoneyBar._moneyLeft.innerHTML	= pmoneyBar.moneyChange(pmoneyBar.smoney);
					pmoneyBar._moneyLeft.style.left = (move - leftMargin) + 'px';
				}
			}
			else {
				var lmtBar = parseInt(pmoneyBar._barLeft.style.left);				
				if ((move >= lmtBar + 19) && (move <= pmoneyBar.width)) {
					pmoneyBar._barRight.style.left		= move + 'px';
					pmoneyBar._moneyRight.style.left	= move + 'px';
					pmoneyBar._moneysBar.style.width	= move - parseInt(pmoneyBar._moneysBar.style.left) + 'px'; 		
					document.getElementById('moneysBar2').style.width = move - parseInt(pmoneyBar._moneysBar.style.left) + 'px'; 		

					per = move*100/(pmoneyBar.width + 1);
					pmoneyBar.emoney	= (pmoneyBar.maxMoney * Math.round(per)/100);
					pmoneyBar._moneyRight.innerHTML		= pmoneyBar.moneyChange(pmoneyBar.emoney);				
				}
			}						
			return false;
		}
	},

	moneyChange: function (money) {		
		s = "a" + money;
		slen = s.length;
		cnt = 0;
		snum = "";

		for(i=slen-1; i >= 1 ; i--) {
			cnt = cnt + 1;
			snum = s.substring(i, i+1) + snum;

			if((slen != cnt + 1) && ((cnt % 3) == 0)) 
				snum = "," + snum;
		}		
		if(pmoneyBar.barType=='Right') {
			document.getElementById('RightMoney').style.width = (20 + (slen*6))+'px';
		}
		else if(pmoneyBar.barType=='Left') {
			document.getElementById('LeftMoney').style.width = (20 + (slen*6))+'px';
			document.getElementById('LeftMoney').style.left = '-'+((slen*5)) +'px';

		}

		return "<span class='class1'>" + snum + this.unit +"</span>";
	},

	inputMoney: function (money, obj) {		
		s = "a" + money;
		slen = s.length;
		cnt = 0;
		snum = "";

		for(i=slen-1; i >= 1 ; i--) {			
			nums = s.substring(i, i+1);		            
			if(isNaN(nums)) {
				slen--;
				continue;
			}
			snum = nums + snum;
			cnt = cnt + 1;
			
			if((slen != cnt + 1) && ((cnt % 3) == 0)) 
				snum = "," + snum;
		}
		if(obj) obj.value = snum;
		else return snum;
	},

	changeType: function (obj) {
		if(this.type=='bar') {
			this._moneyBox.style.display = 'none';			
			this._inputBox.style.display = 'block';			
			obj.src = this.path + "img/common/money_btn_bar.gif";	
			this.type = "input";
		}
		else {
			this._moneyBox.style.display = 'block';			
			this._inputBox.style.display = 'none';			
			obj.src = this.path + "img/common/money_btn_input.gif";	
			this.type = "bar";
		}
	
	},

	putMoney: function (obj,money,deft) {
		if(!money) obj.value = this.inputMoney(deft);
		else {
			if(deft==0) this.smoney = money;
			else this.emoney = money;
		}
	},

	sendValue: function() {		 
		 eval( this.callBack+"(this.smoney,this.emoney)" ); 
	}

}


/*
<style>
#pmoneyBar { float:left;position:relative;} 
#barLeft { position:absolute; z-index:1; }
#moneyLeft { position:absolute; z-index:1;left:-30px;top:15px;font-family:Tahoma,돋움; font-size:11px; }
#moneyBar { float:left; width:1px; background:url(img/common/money_bar_bg.gif) repeat-x; }
#moneysBar { float:left;width:1px; background:url(img/common/money_sbar_bg.gif) repeat-x;position:absolute;left:12px;top:6px; }
#barRight { position:absolute; z-index:1; }
#moneyRight { position:absolute; z-index:1;top:15px;font-family:Tahoma, 돋움; font-size:11px; }
#btnSearch { position:absolute; z-index:1;top:-4px; }
#inputBox { position:absolute; z-index:1;top:-4px; left:0px; text-align:right }
#inputBox input { border: 0px solid #dadada; background:url('img/common/money_input_bg.gif') no-repeat right 0;line-height:150%;padding-right:6px; height:18px;text-align:right;font-family:Tahoma,돋움; font-size:11px;}

#moneyRight .class1{ background:url('img/common/money_text_bg2.gif') no-repeat left 0; float:left; padding-left:6px; text-decoration:none; height:21px;line-height:240%;}
#moneyRight .class2{ background:url('img/common/money_text_bg2.gif') no-repeat right 0; float:left; padding-right:6px; text-decoration:none; height:21px;line-height:240%;}

#moneyLeft .class1{ background:url('img/common/money_text_bg.gif') no-repeat left 0; float:left; padding-left:6px; text-decoration:none; height:21px;line-height:240%;}
#moneyLeft .class2{ background:url('img/common/money_text_bg.gif') no-repeat right 0; float:left; padding-right:6px; text-decoration:none; height:21px;line-height:240%;}
</style>
*/