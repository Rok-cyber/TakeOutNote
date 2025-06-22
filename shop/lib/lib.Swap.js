/*
Copyright (C) 2003-2007, x-wiz.com.
xwzGadgetry : Javascript UI Library for the gadget(or the widget)

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.
- http://www.gnu.org/licenses/lgpl.html
*/

 /**
 * xwzGadgetry : Javascript UI Library for the gadget(or the widget) <http://www.x-wiz.com/>
 * 레이어의 위치 지정 또는 순서 출력을 사용자가 원하는 위치 또는 순서로 배열하도록 하는 함수.
 * 쇼핑몰에서 상품 위치나, 디자인관리에서 디자인 레이어 위치 이동에 따른 배열,
 * 여러 통계 수치나, 입력폼과 출력폼이 동일한 화면에서 출력 순서 변경을 위해 만듦
 *
 * Idea of "drag&drop swap" is from "google ig<http://www.google.co.kr/ig/>" - see dragDepart()
 * Idea of "toggle swap" is from "yahoo.com<http://kr.yahoo.com/>"- see prevToggle()
 * Idea of "the distance" is from "daum.net<http://www.daum.net/>" - see _swapAviate()
 * Idea of "bindAsObject, bindAsEvent" is from "Prototype JavaScript Framework <http://www.prototypejs.org/>"
 *
 *@copyright    x-wiz.com
 *@license        The LGPL License
 *@author        N.Thu Lee
 *@version       1.0 Alpha

 * 파이어폭스 오류수정 및 기능 추가 : previl
  
/**
 +---------------------------------------------------------------------------+
 | 전역 함수 선언
 + --------------------------------------------------------------------------+
 */
/*
 * 해당 메소드를 소유객체(Owner object)로 인스턴스를 미리 묶어서 반환하는 함수, 소유객체와 동일한 인자값을 갖는다.
 *@param {Object, [Number, String, Object, ...], ...} 함수(메소드)를 소유하고 있는 객체, 전달 변수, ...
 *@author N.Thu Lee
 *@copyright x-wiz.com
 *@version 0.1
 *@release 2007.01.12
 */
Function.prototype.bindAsObject=function(){var _$=this,args=[],object=null;for(var i=0,len=arguments.length;i<len;i++)args[i]=arguments[i];object=args.shift();return function(){return _$.apply(object,args);}};
/*
 * 해당 메소드를 소유객체(Owner object)로 인스턴스를 미리 묶어서 반환하는 함수, 이벤트 객체를 전달받으며, 소유객체와 동일한 인자값을 갖는다.
 *@param {Object, [Number, String, Object, ...], ...} 함수(메소드)를 소유하고 있는 객체, 전달 변수, ...
 *@author N.Thu Lee
 *@copyright x-wiz.com
 *@version 0.1
 *@release 2007.01.12
 */
Function.prototype.bindAsEvent=function(){ 	var _$=this,args=[],object=null; 	for(var i=0,len=arguments.length;i<len;i++)args[i]=arguments[i];object=args.shift(); 	return function(e){ 		var body=document.body||document.documentElement,hEvent=(e||window.event),_o=hEvent['element']=hEvent.target||hEvent.srcElement; 		/*객체의 body에서의 Top,Left 위치*/ 		hEvent['offsetTop']=hEvent['offsetLeft']=0; 		while(_o.offsetParent){ 			hEvent['offsetLeft']+=_o.offsetLeft; 			hEvent['offsetTop']+=_o.offsetTop; 			_o=_o.offsetParent; 		}; 		/*이벤트 객체의 속성값 반환*/ 		hEvent['_x']=hEvent.pageX||(hEvent.clientX+(body.scrollLeft-body.clientLeft)); 		hEvent['_y']=hEvent.pageY||hEvent.clientY+body.scrollTop-body.clientTop; 		hEvent['_left']=(hEvent.which&&e.button==0)||!!(hEvent.button&1); 		hEvent['_middle']=(hEvent.which&&e.button==1)||!!(hEvent.button&4); 		hEvent['_right']=(hEvent.which&&e.button==2)||!!(hEvent.button&2); 		hEvent['_key']=(hEvent.keyCode||hEvent.which); 		return _$.apply(object,[hEvent].concat(args)); 	}; };
/*
 * 특정 객체에 이벤트 핸들러 추가 함수
 *@param {Object, String, Function, Bool} 대상 객체, 이벤트 명, 함수, capture
 *@author N.Thu Lee
 *@copyright x-wiz.com
 *@version 0.1
 *@release 2007.01.12
 */
addEventObserve=function(element,name,fpnotify,useCapture){if(element.addEventListener)element.addEventListener(name,fpnotify,useCapture||false);else if(element.attachEvent)element.attachEvent('on'+name,fpnotify);};
/*
 * 특정 객체에 이벤트 핸들러 해제 함수
 *@param {Object, String, Function, Bool} 대상 객체, 이벤트 명, 함수, capture
 *@author N.Thu Lee
 *@copyright x-wiz.com
 *@version 0.1
 *@release 2007.01.12
 */
freeEventObserve=function(element,name,fpnotify,useCapture){	if(element.removeEventListener)element.removeEventListener(name,fpnotify,useCapture||false);else if(element.detachEvent)element.detachEvent('on'+name,fpnotify);};/**

/*
 * Opacity style Apply
 *@param {Object}		element-style 요소를 갖는 객체
 *@param {Number}	nValue-투명도
 *@author N.Thu Lee
 *@copyright x-wiz.com
 *@version 0.1
 *@release 2007.01.12
 */
notifyOpacity=function(element, nValue){ 	
	try{ 		
		if(element.style.filter!=null){/*IE*/ 
			element.style.filter=nValue==null?'':'alpha(opacity='+nValue+')'; 			
			if(!element.currentStyle||!element.currentStyle.hasLayout){element.style.zoom=1;}
		}else if(element.style.opacity!=null){/**/ 			
			element.style.opacity=nValue==null?null:nValue/100; 		
		}else if(element.style['-moz-opacity']!=null){/* Moz's*/ 			
			element.style['-moz-opacity']=nValue==null?null:nValue/100; 		
		}else if(element.style.MozOpacity!=null){/*FF*/ 			
			element.style.MozOpacity=nValue==null?null:nValue/100; 		
		}else if(element.style['-khtml-opacity']!=null){/*KDE*/ 			
			element.style['-khtml-opacity']=nValue==null?null:nValue/100; 		
		}else{ 		} 	
	}catch(e){}; 
};

/**
 +---------------------------------------------------------------------------+
 | xwzGadgetry 
 + --------------------------------------------------------------------------+
 */
/**
 * xwzGadgetry 생성.
 *@param {String}	byID 해당 객체를 포함하고있는 객체
 *@param {Number}	nColumns 각 Row로 분활될 Column갯수
 *@param {Number}	Distance 각 대상 객체 간의 간격
 *@return {Object}	Returns a new xwzGadgetry.
 *@access public
 *@constructor
 */
var xwzGadgetry=function(byID,tagName,Columns,Distance){ 	
	this.version='1.0b'; 	
	this.sName='__xwzSRW_'+byID; 	
	this.Gadgetry=document.getElementById(byID); 	
	if(this.Gadgetry==null)return null; 	
	this.initialize=false;/*초기화 여부*/ 	
	this.tagName=tagName||'SPAN'; 	
	this.nDistance=Distance||8;/*객체들간의 간격*/ 	
	this.nCells=Columns||1;/*Column 갯수*/ 	
	this.nOpacity=86;/*이동간의 투명도설정*/ 	
	this.nStrata=5;/*레이어 이동 단계 갯수*/ 	
	this.isMotion=true; 	
	this.zIndex=0;/*deep index*/ 	
	this.Nodes=[];/*객체 배열변수*/ 	
	this.Atlas=[];/*각 좌표값 배열변수*/ 	
	this.Queue=[];/*이동대상 배열변수*/ 	
	this.resTime=null;/*시간*/ 	
	this.Observers=[]; 		
	this.keelblocks=null;/**/ 	
	this.talebearing=''; 		
	/*드래그 앤 드랍,Resize 관련 변수*/ 	
	this._trace	={dX:0,dY:0,nTarget:null,nSource:null};
	/*마우스 이벤트 정보*/ 	
	this.grips		={lt:null,lm:null,lb:null,ct:null,cb:null,rt:null,rm:null,rb:null}; 	
	this.sizeinfo	=null; 
};


/**
 * 레이어 위치가 변경되어 완료되었을 때 지정된 함수를 호출
 *@method onArrival
 *@see #saveSequel()
 */
xwzGadgetry.prototype.onArrival=null;
/**
 *@memberof xwzGadgetry
 *@access public
 */
/*resize option*/ xwzGadgetry.prototype.noResize=false;
/*bordercolor*/ xwzGadgetry.prototype.borderColor='#bbbbbb';
/*resize grip*/ xwzGadgetry.prototype.grip={color:'red',size:8,minWidth:null,minHeight:null,maxWidth:null,maxHeight:null};
/*쿠키유지일*/ xwzGadgetry.prototype.AvailableCookie=7;


/**
 * 각 객체들의 순서를 쿠키에서 가져오는 함수
 *@method loadSequel
 *@return {String} Return 쿠키값.
 *@access public
 */
xwzGadgetry.prototype.loadSequel=function(){ 	
	var offsetMin=0,offsetMax=0; 	
	if(document.cookie.length>0){
		/*쿠키가 존재하는 체크*/ 		
		offsetMin=document.cookie.indexOf(this.sName+'=');
		/*해당한 이름을 갖는 쿠키 정보 시작 위치*/ 		
		if(offsetMin!==-1){ 			
			offsetMin+=this.sName.length+1;offsetMax=document.cookie.indexOf(';',offsetMin); 			
			if(offsetMax===-1){offsetMax=document.cookie.length;}; 			
			return (unescape(document.cookie.substring(offsetMin,offsetMax))).toString().split(','); 		
		} 
	} 
	return false; 
}


/**
 * 각 레이어들의 순서를 쿠키로 저장하는 함수, 외부호출함수가 있을 경우 해당 함수 호출
 *@method saveSequel
 *@see #onArrival()
 *@access public
 */
xwzGadgetry.prototype.saveSequel=function(){ 	
	var dtExpire=null,sExpire='',S=[],domain=window.document.domain||window.location.hostname; 	
	for(var i=0,len=this.Nodes.length;i<len;i++){
		S[i]=this.Nodes[i].getAttribute('_GadgetIndex');
	} 	
	if(this.AvailableCookie*24>0){
		dtExpire=new Date((new Date()).getTime()+(this.AvailableCookie*24)*3600000);
		sExpire='; expires='+dtExpire.toGMTString();
	} 	
	
	document.cookie=(this.sName)+'='+escape(S.toString())+sExpire+'; path=/;'+(typeof(domain)=='string'&&domain!=''?'domain='+domain:'')+';'; 	
	var $A=[],nIndex; 	
	
	/** 	 * 레이어 위치가 변경되어 완료되었을 때 지정된 함수를 호출 	 
	*@method onArrival 	 
	*@see #saveSequel() 	 
	*/ 	
	if(typeof this.onArrival=='function'&&this.initialize==true){ 		
		for(var i=0,len=this.Nodes.length;i<len;i++){ 			
			nIndex=this.Nodes[i].getAttribute('_GadgetIndex'); 			
			$A[nIndex]={
				index:i,top:parseInt(this.Nodes[i].style.top),
				left:parseInt(this.Nodes[i].style.left),
				width:parseInt(this.Nodes[i].style.width),
				height:parseInt(this.Nodes[i].style.height),
				expanded:this.Nodes[i].gadget!=null?(this.Nodes[i].gadget.state=='collapse'?false:true):true
			}; 		
		} 		
		this.onArrival($A); 	
	} 
};


/**
 * 각 객체들의 고유 인덱스를 통해서 해당 객체의 배열에서의 위치를 찾는 함수
 *@method getIndex
 *@param {Number} _index 객체의 인덱스값
 *@return {Number} 대상 인덱스를 갖는 Nodes 배열 인덱스
 *@access public
 */
xwzGadgetry.prototype.getIndex=function(_index){for(var i in this.Nodes){if(this.Nodes[i].getAttribute('_GadgetIndex')==_index)return(i*1);}};
/**
 * 인덱스 또는 객체값을 통해 해당 객체의 배열 위치를 찾는 함수
 *@method searchGadget
 *@param {Object,Number,String} mixValue 특정 객체의 인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@return {Number} 대상 객체의 배열 Nodes 인덱스
 *@access public
 *@changelog 2007.05.04 파라미터에 id, name속성값에 대해서도 처리할 수 있도록 getElementById, getElementByName 추가
 */
xwzGadgetry.prototype.searchGadget=function(mixValue){var o=null;if(typeof mixValue=='object'){for(var i in this.Nodes)if(this.Nodes[i]==mixValue)return(i*1);}else if(typeof mixValue=='number'){for(var i in this.Nodes)if(i==mixValue)return(i*1);}else if(typeof mixValue=='string'){o=document.getElementById(mixValue)||document.getElementsByName(mixValue)[0]||null;for(var i in this.Nodes)if(this.Nodes[i]==mixValue)return(i*1);};return false};
/**
 * Column 갯수 변경 함수 또는 Column갯수 반환 함수
 *@method columns
 *@param {Number} nValue column 갯수
 *@param {Bool} isDirectly 변경 시 즉각 반영여부(true : 즉각 반영하지 않음, false : 즉각 반영)
 *@return {Number} Column 갯수
 *@access public
 */
xwzGadgetry.prototype.columns=function(nValue,isDirectly){this.nCells=(typeof nValue=="number"||typeof nValue=="string")?nValue*1:this.nCells;this._swapDepart(isDirectly);return this.nCells;};
/**
 * 레이어 이동 단계 갯수 변경 및 반환 함수
 *@method strata
 *@param {Number} nValue 변경하려는 값
 *@return {Number} 레이어 이동 단계 갯수 싸이클
 *@access public
 */
xwzGadgetry.prototype.strata=function(nValue){return this.nStrata=(typeof nValue=="number"||typeof nValue=="string")?nValue*1:this.nStrata;};
/**
 * 이동시 투명도 설정함수
 *@method opacity
 *@param {Number} nValue 변경하려는 값
 *@return {Number} 투명도 값
 *@access public
 */
xwzGadgetry.prototype.opacity=function(nValue){return this.nOpacity=(typeof nValue=="number"||typeof nValue=="string")?nValue*1:this.nOpacity;};
/**
 * 이동시 바로 적용할 지 여부
 *@method motion
 *@param {Bool} bValue 변경하려는 값
 *@return {Bool} true, false
 *@access public
 */
xwzGadgetry.prototype.motion=function(bValue){return this.isMotion=bValue;};
/**
 * 각 레이어들의 순위를 초기화 하는 함수
 *@method resetToggle
 *@see #reset()
 *@see addEventObserve()
 *@param {Object} 이벤트 발생 객체
 *@param {Bool} isDirectly 즉각 반영여부
 *@access public
 */
xwzGadgetry.prototype.resetToggle=function(objSrc,isDirectly){addEventObserve(objSrc,'click',this.reset.bindAsObject(this,isDirectly))};
/**
 * 이전 레이어와 위치 변경
 *@method prevToggle
 *@see #searchGadget()
 *@see #_stack()
 *@param {Object} 이벤트 발생 객체
 *@param {Object,Number,String} mixValue 특정 객체의 인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@access public
 */
xwzGadgetry.prototype.prevToggle=function(objSrc,mixValue){var _index=this.searchGadget(mixValue);if(_index!==false)addEventObserve(objSrc,'click',this._stack.bindAsObject(this,_index,-1));};
/**
 * 다음 레이어와 위치 변경
 *@method nextToggle
 *@see #searchGadget()
 *@see #_stack()
 *@param {Object} 이벤트 발생 객체
 *@param {Object,Number,String} [mixValue] 특정 객체의 인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@access public
 */
xwzGadgetry.prototype.nextToggle=function(objSrc,mixValue){var _index=this.searchGadget(mixValue);if(_index!==false)addEventObserve(objSrc,'click',this._stack.bindAsObject(this,_index,1));};
/**
 * 처음 위치로 이동
 *@method firstToggle
 *@see #searchGadget()
 *@see #_above()
 *@param {Object} 이벤트 발생 객체
 *@param {Object,Number,String} [mixValue] 특정 객체의 인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@access public
 */
xwzGadgetry.prototype.firstToggle=function(objSrc,mixValue){var _index=this.searchGadget(mixValue);if(_index!==false)addEventObserve(objSrc,'click',this._above.bindAsObject(this,_index));};
/**
 * 마지막 위치로 이동
 *@method lastToggle
 *@see #searchGadget()
 *@see #_below()
 *@param {Object} 이벤트 발생 객체
 *@param {Object,Number,String} [mixValue] 특정 객체의 인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@access public
 */
xwzGadgetry.prototype.lastToggle=function(objSrc,mixValue){var _index=this.searchGadget(mixValue);if(_index!==false)addEventObserve(objSrc,'click',this._below.bindAsObject(this,_index));};
/**
 * y 좌표 객체와 자리 변경(column 수 만큼 인덱스 감소)
 *@method upToggle
 *@see #searchGadget()
 *@see #_cross()
 *@param {Object} 이벤트 발생 객체
 *@param {Object,Number,String} [mixValue] 특정 객체의 인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@access public
 */
xwzGadgetry.prototype.upToggle=function(objSrc,mixValue){var _index=this.searchGadget(mixValue);if(_index!==false)addEventObserve(objSrc,'click',this._cross.bindAsObject(this,_index,'up'));};
/**
 * y 좌표 객체와 자리 변경(column 수 만큼 인덱스 증가)
 *@method downToggle
 *@see #searchGadget()
 *@see #_cross()
 *@param {Object} 이벤트 발생 객체
 *@param {Object,Number,String} [mixValue] 특정 객체의  인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@access public
 */
xwzGadgetry.prototype.downToggle=function(objSrc,mixValue){var _index=this.searchGadget(mixValue);if(_index!==false)addEventObserve(objSrc,'click',this._cross.bindAsObject(this,_index,'down'));};
/**
 * x 좌표 객체와 자리 변경(해당 가로 줄에서 첫번째로 이동)
 *@method leftToggle
 *@see #searchGadget()
 *@see #_cross()
 *@param {Object} 이벤트 발생 객체
 *@param {Object,Number,String} [mixValue] 특정 객체의  인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@access public
 */
xwzGadgetry.prototype.leftToggle=function(objSrc,mixValue){var _index=this.searchGadget(mixValue);if(_index!==false)addEventObserve(objSrc,'click',this._cross.bindAsObject(this,_index,'left'));};
/**
 * x 좌표 객체와 자리 변경(해당 가로 줄에서 마지막으로 이동)
 *@method rightToggle
 *@see #searchGadget()
 *@see #_cross()
 *@param {Object} 이벤트 발생 객체
 *@param {Object,Number,String} [mixValue] 특정 객체의  인덱스값, 또는 인스턴스 또는 ID, Name 속성 값
 *@access public
 */
xwzGadgetry.prototype.rightToggle=function(objSrc,mixValue){var _index=this.searchGadget(mixValue);if(_index!==false)addEventObserve(objSrc,'click',this._cross.bindAsObject(this,_index,'right'));};
/**
 * 환경값에 따라 초기화 하는 함수
 *@method load
 *@access public
 */
xwzGadgetry.prototype.load=function(){ 	
	if(this.initialize==true) return ; 	
	var nX=0,nY=0,v=[],h=[],nNodeIndex=0; 	
	var oNode=null,List=[],_t=[],_f=this.loadSequel(),a={},t=null; 	
	for(var i=0,iLen=this.Gadgetry.childNodes.length;i<iLen;i++){ 	
		oNode=this.Gadgetry.childNodes[i]; 				
		/*자식 객체가 주석,문자열 또는 object가 아닌 경우 */ 		
		if(oNode.nodeName.toLowerCase()=='#text' || oNode.nodeName.toLowerCase()=='font' ||oNode.nodeName.toLowerCase()=='#comment'||typeof(oNode)!='object') continue;
		oNode['gadget']={}; 		
		oNode['conf']={}; 		
		/*자식 객체에서 명령 기능을 갖는 SPAN 태그를 찾는다*/ 		
		oList=oNode.getElementsByTagName(this.tagName); 
		for(var j=0,jLen=oList.length;j<jLen;j++){ 						
			if(oList[j].getAttribute('toggle')==null)continue; 			
			//oList[j].style.cursor='pointer'; 				
			switch(oList[j].getAttribute('toggle').toLowerCase()){ 				
				case 'gadget': 					
					/*slide 효과를 주기위한 DIV 생성*/ 					
					t=document.createElement('DIV'); 					
					t.style.marginTop='-1px'; 					
					t.style.marginLeft='0px'; 					
					t.style.width=(parseInt(oList[j].style.width)||oList[j].offsetWidth)+'px'; 					
					t.style.height='1px'; 					
					t.style.overflow='hidden';/*IE Margin Bug -_-;;*/ 					
					oList[j].insertBefore(t,oList[j].childNodes[0]); 					
					oNode['gadget'].topgap=t.style.marginTop; 					
					/*영역 지정*/ 					
					oList[j].style.display='block'; 					
					oList[j].style.width=(parseInt(oList[j].style.width)||oList[j].offsetWidth)+'px'; 					
					oList[j].style.height=((parseInt(oList[j].style.height)||oList[j].offsetHeight)+1)+'px'; 					
					oList[j].style.overflow='hidden'; 					
					oList[j].style.visibility='visible'; 					
					oNode['gadget'].style=oList[j].style; 					
					oNode['gadget'].width=parseInt(oList[j].style.width); 					
					oNode['gadget'].height=parseInt(oList[j].style.height); 					
				break; 	
				case 'conf': 					
					/*slide 효과를 주기위한 DIV 생성*/ 					
					t=document.createElement('DIV'); 					
					t.style.marginTop='-1px'; 					
					t.style.marginLeft='0px'; 					
					t.style.width=(parseInt(oList[j].style.width)||oList[j].offsetWidth)+'px'; 					
					t.style.height='1px'; 					
					t.style.overflow='hidden';/*IE Margin Bug -_-;;*/ 					
					oList[j].insertBefore(t,oList[j].childNodes[0]); 					
					oNode['conf'].topgap=t.style.marginTop; 					
					/*영역 지정*/ 					
					oList[j].style.display='block'; 					
					oList[j].style.width=(parseInt(oList[j].style.width)||oList[j].offsetWidth)+'px'; 					
					oList[j].style.height=((parseInt(oList[j].style.height)||oList[j].offsetHeight)+1)+'px'; 					
					oList[j].style.overflow='hidden'; 					
					oList[j].style.visibility='visible';
					oNode['conf'].style=oList[j].style; 					
					oNode['conf'].width=parseInt(oList[j].style.width); 					
					oNode['conf'].height=parseInt(oList[j].style.height); 					
					oList[j].style.height='1px';					
				break; 			
				case 'expand': 										
					oNode['gadget'].onMinimized=new Function(oList[j].getAttribute('attr:min')).bindAsObject(oList[j]);
					/*최소화일때 사용자 정의 함수 실행*/ 					
					oNode['gadget'].onMaximized=new Function(oList[j].getAttribute('attr:max')).bindAsObject(oList[j]);
					/*최대화일때 사용자 정의 함수 실행*/ 					
					oNode['gadget'].strata=oList[j].getAttribute('attr:strata')||this.nStrata; 					
					oNode['gadget'].state=oList[j].getAttribute('attr:state')||'expand'; 					
					oNode['gadget'].animate=(oList[j].getAttribute('attr:animate')||'none').toLowerCase(); 					
					addEventObserve(oList[j],'click',this.expose.bindAsObject(this,nNodeIndex,'gadget'));			
				break; 
				case 'expand2': 										
					oNode['conf'].onMinimized=new Function(oList[j].getAttribute('attr:min')).bindAsObject(oList[j]);
					/*최소화일때 사용자 정의 함수 실행*/ 					
					oNode['conf'].onMaximized=new Function(oList[j].getAttribute('attr:max')).bindAsObject(oList[j]);
					/*최대화일때 사용자 정의 함수 실행*/ 					
					oNode['conf'].strata=oList[j].getAttribute('attr:strata')||this.nStrata; 					
					oNode['conf'].state=oList[j].getAttribute('attr:state')||'collapse'; 					
					oNode['conf'].animate=(oList[j].getAttribute('attr:animate')||'none').toLowerCase(); 					
					addEventObserve(oList[j],'click',this.expose.bindAsObject(this,nNodeIndex,'conf'));
				break; 
				case 'reset':	
					addEventObserve(oList[j],'click',this.reset.bindAsObject(this));										
				break; 				
				case 'up':		
					addEventObserve(oList[j],'click',this._cross.bindAsObject(this,nNodeIndex,'up'));			
				break; 				
				case 'down':	
					addEventObserve(oList[j],'click',this._cross.bindAsObject(this,nNodeIndex,'down'));		
				break; 				
				case 'left':	
					addEventObserve(oList[j],'click',this._cross.bindAsObject(this,nNodeIndex,'left'));			
				break; 				
				case 'right':
					addEventObserve(oList[j],'click',this._cross.bindAsObject(this,nNodeIndex,'right'));		
				break; 				
				case 'drag':						
					addEventObserve(oList[j],'mousedown',this.dragDepart.bindAsEvent(this,nNodeIndex)); 					
					oList[j].style.cursor='move'; 					
				break; 				
				case 'first':	
					addEventObserve(oList[j],'click',this._above.bindAsObject(this,nNodeIndex));				
				break; 				
				case 'last':	
					addEventObserve(oList[j],'click',this._below.bindAsObject(this,nNodeIndex));				
				break; 				
				case 'next':	
					addEventObserve(oList[j],'click',this._stack.bindAsObject(this,nNodeIndex,1));				
				break; 				
				case 'prev':	
					addEventObserve(oList[j],'click',this._stack.bindAsObject(this,nNodeIndex,-1)); 				
				default:break; 			
			}; 		
		};
		
		/*Resize*/ 		
		//addEventObserve(oNode,'mousedown',this.resize.bindAsEvent(this,nNodeIndex)); 		
		/*하위 객체의 너비,높이*/ 		
		nX=oNode.offsetWidth>parseInt(oNode.style.width||0)?oNode.offsetWidth:parseInt(oNode.style.width);
		/*Node Width*/ 		
		nY=oNode.offsetHeight>parseInt(oNode.style.height||0)?oNode.offsetHeight:parseInt(oNode.style.height);
		/*Node Height*/ 	
		/*각 하위객체에 Index값 설정*/ 					
		oNode.setAttribute('_GadgetIndex',nNodeIndex); 		
		/*각 하위객체의 위치에 대한 값을 갖음*/ 		
		this.Atlas[nNodeIndex]={X:0,Y:0,cX:nX,cY:nY,dX:0,dY:0}; 		
		this.Nodes[nNodeIndex]=oNode; 				
		/*하위 객체의 스타일 속성 변경*/ 		
		oNode.style.position='absolute'; 		
		oNode.style.width=nX+'px';oNode.style.height=nY+'px'; 		
		oNode.style.margin='0px'; 		
		oNode.style.display='block'; 			
		this.zIndex=this.zIndex+(this.Nodes.length*1000); 		
		nNodeIndex++; 	
	}; 	
	
	/*대상객체의 position값 변경*/ 	
	this.Gadgetry.style.position='relative'; 	
	if(_f!=false){/*sort*/ 		
		oList=this.Nodes;
		this.Nodes=[]; 						
		for(var i=0,len=oList.length;i<len;i++){
			if(typeof(_f[i])=='undefined') _f[i] = i;
			if(oList[_f[i]]==null)continue;
			_t[_t.length]=_f[i];
		}; 				
		for(var i=0,len=_t.length;i<len;i++){
			j=_t[i]==null?i:_t[i];this.Nodes[i]=oList[j];
		}; 
		oList=null;_f=null;_t=null; 	

	}; 
	

	
	/*드래그앤드랍,리사이즈에 대한 이동객체의 복사본(크기만 갖음)*/ 	
	this.keelblocks=document.createElement('DIV'); 	
	with(this.keelblocks.style){
		border=this.borderColor+' 1px dashed';position='absolute';display='none';cursor='move';
	}; 
	this.Gadgetry.appendChild(this.keelblocks); 	
	/*리사이즈*/ 	
	var cursors={lt:'se-resize',lm:'e-resize',lb:'ne-resize',ct:'s-resize',cb:'n-resize',rt:'sw-resize',rm:'w-resize',rb:'nw-resize'}; 	
	for(var nm in this.grips){ 		
		this.grips[nm]=document.createElement('DIV'); 		
		this.grips[nm].nm=nm; 		
		this.grips[nm].style.left ='0px';
		this.grips[nm].style.top='0px'; 		
		this.grips[nm].style.width=parseInt(this.grip.size)+'px';
		this.grips[nm].style.height=parseInt(this.grip.size)+'px'; 		
		this.grips[nm].style.margin='0px';
		this.grips[nm].style.padding='0px';
		this.grips[nm].style.border='none'; 		
		this.grips[nm].style.position='absolute'; 		
		this.grips[nm].style.display='block'; 		
		this.grips[nm].style.overflow='hidden'; 		
		this.grips[nm].style.visibility='hidden'; 		
		this.grips[nm].style.cursor=cursors[nm] ; 		
		this.grips[nm].style.zIndex=this.zIndex+100; 		
		this.grips[nm].style.backgroundColor=this.grip.color; 		
		addEventObserve(this.grips[nm],'mousedown',this._resizeDepart.bindAsEvent(this)); 		
		this.grips[nm].innerHTML="<img width='"+parseInt(this.grip.size)+"' height='"+parseInt(this.grip.size)+"' style='visibility:hidden;'>"; 		
		this.Gadgetry.appendChild(this.grips[nm]); 	
	} 	
	this.sizeinfo=document.createElement('DIV'); 	
	this.sizeinfo.style.fontSize='11px'; 	
	this.sizeinfo.style.fontFamily='Tahoma'; 	
	this.sizeinfo.style.color=this.grip.color; 	
	this.sizeinfo.style.position='absolute'; 	
	this.sizeinfo.style.visibility='hidden'; 	
	this.sizeinfo.style.cursor='default'; 	
	this.sizeinfo.style.left ='0px';
	this.sizeinfo.style.top='0px'; 	
	this.sizeinfo.style.margin='0px';
	this.sizeinfo.style.padding='0px'; 	
	this.sizeinfo.style.border='none';
	this.sizeinfo.style.zIndex=this.zIndex+1000; 	
	this.Gadgetry.appendChild(this.sizeinfo); 	
	this.compose(); 	
	this.initialize=true; 
};


/**
 * 레이어 순서를 초기화 하는 함수
 *@method reset
 *@param {Bool} isDirectly 즉시 반영 여부
 *@access public
 */
xwzGadgetry.prototype.reset=function(isDirectly){ 	
	var S=[],nIndex=0; 	
	for(var i=0,len=this.Nodes.length;i<len;i++){ 		
		nIndex=this.Nodes[i].getAttribute('_GadgetIndex')*1; 		
		S[nIndex]=this.Nodes[i]; 	
	}; 	
	this.Nodes=S; 	
	this._swapDepart(isDirectly); 
};


/**
 * Nodes 배열 인덱스 값에 따라 레이어 위치를 초기화하는 함수
 *@method compose
 *@see #_mapping()
 *@see #_molding()
 *@acess public
 */
xwzGadgetry.prototype.compose=function(){ 	
	var Rect=this._mapping(this.Nodes,this.nCells,this.nDistance); 	
	for(var i=0,len=this.Nodes.length;i<len;i++){ 		
		this.Atlas[i].X=Rect[i].X;this.Atlas[i].Y=Rect[i].Y; 		
		this.Atlas[i].cX=Rect[i].cX;this.Atlas[i].cY=Rect[i].cY; 		
		this.Nodes[i].style.left=Rect[i].X+'px';this.Nodes[i].style.top=Rect[i].Y+'px'; 		
		this.Nodes[i].style.zIndex=this.zIndex+i; 	
	}; 	
	this._molding(); 
};


/**
 * 마우스다운 이벤트가 발생하였을 때 drag&drop 이벤트 발생
 *@method _dragDepart
 *@param {Object} event 이벤트 핸들러
 *@param {Number} _index 대상 객체의 인덱스
 *@acess public
 */
xwzGadgetry.prototype.dragDepart=function(event,_index){ 	
	window.focus(); 	
	if(event._left!==true){this._dragArrival();return false;} 	
	/*Resize일때*/ 	
	if(this.talebearing=='resize')this._showGrips(); 	
	/*이벤트 상태값*/ 	
	this.talebearing='drag'; 	
	var s=this.getIndex(_index),z=0,node=this.Nodes[s];
	/*드래그앤드랍 대상 객체*/ 
	this.Queue=[]; 	
	/*이동 객체에 투명도를 줌*/ 	
	notifyOpacity(node,60); 	
	/*인덱스 저장*/ 	
	this._trace.nTarget=this._trace.nSource=s; 	
	/*z-index save*/ 	
	z=node.style.zIndex; 	
	this.keelblocks.style.zIndex=node.style.zIndex; 	
	node.style.zIndex=this.zIndex+(this.Nodes.length*500); 	
	/*레이어가 이동할 동안 레이어가 위치할 자리에 대신 위치할 객체 이동*/ 	
	this.keelblocks.style.left=node.style.left; 	
	this.keelblocks.style.top=node.style.top; 	
	this.keelblocks.style.width=node.style.width; 	
	this.keelblocks.style.height=node.style.height; 	
	/*현재 포인트 기록*/ 	
	this._trace.dX=event._x;this._trace.dY=event._y; 	
	/*이벤트 등록 */ 	
	this.Observers['mousemove']=this._dragAviate.bindAsEvent(this,node); 	
	this.Observers['mouseup']=this._dragArrival.bindAsObject(this,node); 	
	this.Observers['mousedown']=this._callbacks.bindAsEvent(this); 	
	addEventObserve(window.document,'mouseup',this.Observers['mouseup']); 	
	addEventObserve(window.document,'mousemove',this.Observers['mousemove']); 	
	addEventObserve(window.document,'mousedown',this.Observers['mousedown']); 	
	window.document.onselectstart=new Function('return false'); 	
	window.document.ondragstart = new Function ("return false"); 	
	this.Queue=this.Nodes;
};


/**
 * 정해진 영역 펼침 또는 숨김 함수 
 *@method expose
 *@acess Public
 *@param {String} _target 대상 객체의 인덱스명(gadget,conf)
 */ 
xwzGadgetry.prototype.expose=function(_index,_target){ 	
	/*이벤트 상태값*/ 			
	this.talebearing='expose'; 		
	var s=this.getIndex(_index),node=this.Nodes[s],node2=eval("node."+_target); 		
	var dest=0; 	
	if(node.pixWidth==null)node.pixWidth=parseInt(node.style.width); 	
	if(node.pixOheight==null)node.pixOheight=parseInt(node.style.height); 	
	
	if(_target=='conf') {
		if(node.gadget.state=='collapse') return false;
		if(node2.state=='collapse')	node.pixHeight=node.pixOheight+parseInt(node2.height); 	
		else node.pixHeight=parseInt(node.style.height); 	
	} else {
		if(node.conf.state=='expand') {
			if(node2.state=='collapse') node.pixHeight=node.pixOheight+parseInt(node.conf.height);	
			else node.pixHeight=node.pixOheight;
        } else node.pixHeight=node.pixOheight;
    }
	
	/*일반적인 숨김 또는 펼침 */ 	    
	if(node2.animate=='none'){this._expanded(node,node2);return;} 	
	if(node2.state=='collapse'){
		/*변경할 속성 값*/ 		
		dest=node2.animate=='slide'?-1:node.cont.height; 		
		node2.style.visibility='visible'; 	
	}else{
		/*변경할 속성 값*/ 		
		dest=node2.animate=='slide'?(node2.height+1)*-1:1; 	
	} 		
	this._expanding(node,dest,0,node2); 

};


/**
 * expand 설정값 변경 함수
 *@method expandAttrib
 *@param {object,number} _gadget 인덱스 또는 객체인스턴스
 *@param {string} _name 변경할 속성이름
 *@param {function,string,number} _value 속성 값
 *@param {object} _srcObj 최소, 최대화 함수의 참조 객체
 *@acess Public
 */
xwzGadgetry.prototype.expandAttrib=function(_gadget,_name,_value,_srcObj){ 	
	var node=this.searchGadget(_gadget); 	
	if(node==null)return false; 	
	switch(_name){ 				
		case 'min': /*최소화완료후실행할함수*/	
			node.gadget.onMinimized=typeof _srcObj!='object'?new Function(_value||''):new Function(_value).bindAsObject(_srcObj);
		break; 		
		case 'max': /*최대화완료후실행할함수*/
			node.gadget.onMaximized=typeof _srcObj!='object'?new Function(_value||''):new Function(_value).bindAsObject(_srcObj);	
		break; 		
		
		case 'strata': /*strata*/
			node.gadget.strata=parseInt(_value||node.gadget.strata);
		break; 		
		case 'animate': /*효과*/ 
			node.gadget.animate=parseInt(_value||node.gadget.animate);
		break; 	
		case 'min2': /*최소화완료후실행할함수*/	
			node.conf.onMinimized=typeof _srcObj!='object'?new Function(_value||''):new Function(_value).bindAsObject(_srcObj);
		break; 		
		case 'max2': /*최대화완료후실행할함수*/
			node.conf.onMaximized=typeof _srcObj!='object'?new Function(_value||''):new Function(_value).bindAsObject(_srcObj);	
		break; 		
		
		case 'strata2': /*strata*/
			node.conf.strata=parseInt(_value||node.conf.strata);
		break; 		
		case 'animate2': /*효과*/ 
			node.conf.animate=parseInt(_value||node.conf.animate);
		break; 	
	} 	
	return true; 
}


/**
 * 리사이즈 함수
 *@method resizeTo
 *@acess public
 */
xwzGadgetry.prototype.resize=function(event,_index){ 	if(this.noResize==true||event._left==false){return false;} 	var s=this.getIndex(_index); 	/*resize일때 중단*/ 	if((this.talebearing=='resize'&&s==this._trace.nSource)||(this.talebearing!='' &&this.talebearing!='resize')){ 		this.talebearing=this.talebearing!='resize'?this.talebearing:'';this._showGrips();return true; 	} 	this.talebearing='resize';/*이벤트 상태값 변경*/ 	this._trace.nSource=s; 	this._resizeArrange(this.Nodes[s]); 	this._showGrips(true); };
/**
 * 선택된 레이어를 첫번째 배열요소로 이동
 *@method _above
 *@param {Number}	_index 레이어 인덱스 번호
 *@param {Bool} isDirectly 즉시 반영 여부
 *@access private
 */
xwzGadgetry.prototype._above=function(_index){ 	var oList=[],s=this.getIndex(_index);if(s==0)return; 	oList[0]=this.Nodes[s]; 	for(var i=0,len=this.Nodes.length;i<len;i++){if(i!=s)oList[oList.length]=this.Nodes[i];}; 	this.Nodes=oList;this._swapDepart(); };
/**
 * 선택된 레이어를 마지막번째 배열요소로 이동
 *@method _below
 *@param {Number} _index 레이어 인덱스 번호
 *@access private
 */
xwzGadgetry.prototype._below=function(_index){ 	var oList=[],s=this.getIndex(_index);if(s==this.Nodes.length-1)return; 	for(var i=0,len=this.Nodes.length;i<len;i++){if(i!=s)oList[oList.length]=this.Nodes[i];}; 	oList[oList.length]=this.Nodes[s];this.Nodes=oList;this._swapDepart(); };
/**
 * 선택된 레이어를 정해진 step만큼 이동시키는 함수
 *@method _stack
 *@param {Number}_index 레이어 인덱스 번호
 *@param {Number} _step[:=decrease,increase]+1,-1,...
 *@access private
 */
xwzGadgetry.prototype._stack=function(_index,_step){ 	var s=this.getIndex(_index),d=s+_step,dist=0,oNodtmp=this.Nodes[s]; 	d=d<0?this.Nodes.length-1:d>this.Nodes.length-1?0:d; 	if(d==this.Nodes.length-1&&s==0){/*prev*/ 		oNodtmp=this.Nodes.shift();this.Nodes.push(oNodtmp); 	}else if(d==0&&s==this.Nodes.length-1){/*next*/ 		oNodtmp=this.Nodes.pop();this.Nodes.unshift(oNodtmp); 	}else{ 		this.Nodes[s]=this.Nodes[d];this.Nodes[d]=oNodtmp; 	}; 	this._swapDepart(); };
/**
 * 선택된 레이어를 정해진 Y,X 축으로 step만큼 이동시키는 함수
 *@method _cross
 *@param {Number}	_index 레이어 인덱스 번호
 *@param {String} _arrow[:=left,right,up,down]  이동 방향
 *@access private
 */
xwzGadgetry.prototype._cross=function(_index,_arrow){ 	_arrow=(_arrow).toString().toLowerCase()||(this.nCells>0?'up':'left'); 	var s=this.getIndex(_index),d=s; 	var iX=Math.floor(s%this.nCells),iY=Math.floor(s/this.nCells),mX=Math.floor(this.Nodes.length-1%this.nCells),mY=Math.floor(this.Nodes.length-1/this.nCells); 	if(_arrow=='up')iY=iY>0?iY-1:iY; 	else if(_arrow=='down')iY=iY<mY?iY+1:iY; 	else if(_arrow=='left')iX=iX>0?iX-1:iX; 	else if(_arrow=='right')iX=iX<mX?iX+1:iX; 	d=(iY*this.nCells)+iX; 	if(d==s||d<0||d>this.Nodes.length-1)return false; 	var oNodtmp=this.Nodes[s]; 	this.Nodes[s]=this.Nodes[d]; 	this.Nodes[d]=oNodtmp; 	this._swapDepart(); };
/**
 * Frame 객체의 너비와 높이 설정 함수
 *@method _molding
 *@see #_grid()
 *@acess private
 */
xwzGadgetry.prototype._molding=function(){ 	
	this.talebearing=''; 	
	var d=this._grid(this.Nodes,this.nCells),nX=Math.floor(0),nY=Math.floor(this.nDistance/2); 	
	for(var i=0,len=d.horizontal.length;i<len;i++){nX+=d.horizontal[i]+0;}; 	
	for(var i=0,len=d.vertical.length;i<len;i++){nY+=d.vertical[i]+this.nDistance;} 	
	this.Gadgetry.style.width=nX+'px';	
	this.Gadgetry.style.height=nY+'px'; 	
	this._showGrips();this.saveSequel(); 
};


/**
 * 각 레이어의 너비와 높이값을 배열화
 *@method _grid
 *@param {Object} Nodes 레이어 배열
 *@param {Number} nColumns column 수
 *@return {Object}
 *@acess private
 */
xwzGadgetry.prototype._grid=function(Nodes,nColumns){ 	var iX=0,iY=0,nX=0,nY=0,h=[],v=[]; 	for(var i=0,len=Nodes.length;i<len;i++){ 		nX=parseInt(Nodes[i].style.width); 		nY=parseInt(Nodes[i].style.height); 		iX=Math.floor(i%nColumns);iY=Math.floor(i/nColumns); 		v[iY]=(v[iY]||0)>nY?v[iY]:nY;h[iX]=(h[iX]||0)>nX?h[iX]:nX; 	}; 	return{vertical:v,horizontal:h}; };


/**
 * 레이어들에 대한 x,y, width, height 값 배열화 함수
 *@method _mapping
 *@see #_grid()
 *@param {Object} Nodes 레이어 배열
 *@param {Number} nColumns column 수
 *@return {Array} Array(x, y, width, height )
 *@acess private
 */
xwzGadgetry.prototype._mapping=function(Nodes,nColumns,Distance){ 	
	/*grid->_mapping->_molding*/ 	
	var iX=0,iY=0,nX=Math.abs(Distance/2),nY=Math.abs(Distance/2),Map=[],d=this._grid(Nodes,nColumns); 	
	for(var i=0,len=Nodes.length;i<len;i++){ 		
		iX=Math.floor(i%nColumns);iY=Math.floor(i/nColumns); 		
		if(iX==0){	nX=Math.abs(Distance/2);
		if(iY>0)nY+=(d.vertical[iY-1]+Distance);}; 		
		if(nColumns==1) nX = nX - Math.abs(Distance/2);
		Map[i]={X:nX,Y:nY,cX:parseInt(Nodes[i].style.width),cY:parseInt(Nodes[i].style.height)}; 		
		nX+=(d.horizontal[iX]+Distance); 	
	}; 	
	return Map; 
};

/**
 *변경된 배열의 인덱스에 따라 각 레이어가 이동할 위치 계산
 *@method _swapDepart
 *@see #_swapAviate()
 *@acess private
 */
xwzGadgetry.prototype._swapDepart=function(isDirectly){ 	
	/*이벤트 상태값*/ 	
	this.talebearing='position'; 	
	this.Queue=[]; 	
	var d=0,dX=0,dY=0,Rect=this._mapping(this.Nodes,this.nCells,this.nDistance); 	
	for(var i=0,len=this.Nodes.length;i<len;i++){ 		
		/*레이어 현재 위치와 이동할 위치 차이 계산*/ 		
		dX=Rect[i].X-parseInt(this.Nodes[i].style.left),dY=Rect[i].Y-parseInt(this.Nodes[i].style.top); 		
		d=len-1-i; 		
		this.Nodes[d].style.zIndex=(this.zIndex+len)-i; 		
		if(dX==0&&dY==0)continue; 		
		this.Atlas[i]=Rect[i]; 		
		/*각 속성값 배열화*/ 		
		this.Queue[this.Queue.length]={ 				
			style:this.Nodes[i].style, 				
			distX:dX,distY:dY, 				
			posX:parseInt(this.Nodes[i].style.left),
			posY:parseInt(this.Nodes[i].style.top), 				
			mvX:Rect[i].X,mvY:Rect[i].Y, 				
			scX:dX!=0?0:this.nStrata,
			scY:dY!=0?0:this.nStrata 			
		}; 		
		/*투명도 설정*/ 		
		notifyOpacity(this.Nodes[i],this.nOpacity); 	
	}; 	
	(this.isMotion==true||isDirectly==true)?this._swapAviate():this.compose(); 
};

/**
 * 레이어를 이동할 위치로 점차 이동시키는 함수
 *@method _swapAviate
 *@acess private 거리계산
 */
xwzGadgetry.prototype._swapAviate=function(){ 	
	clearTimeout(this.resTime);
	this.resTime=null; 	
	var o=null,Q=[]; 	
	var dY=0,dX=0; 
	
	for(var i=0,len=this.Queue.length;i<len;i++){ 		
		o=this.Queue[i];/*이동 객체*/ 		
		if(o.scX<this.nStrata){/*x축 이동*/ 			
			dX=Math.round(Math.sin(o.scX/this.nStrata*Math.PI/2)*o.distX); 			
			o.style.left=(o.posX+dX)+'px'; 			
			o.scX++; 		
		} 		
		if(o.scY<this.nStrata){/*y축 이동*/ 			
			dY=Math.round(Math.sin(o.scY/this.nStrata*Math.PI/2)*o.distY); 			
			o.style.top=(o.posY+dY)+'px'; 			
			o.scY++; 					
		} 				
		/*이동완료*/ 			
		if(o.scX>=this.nStrata&&o.scY>=this.nStrata){ 			
			notifyOpacity(o);o.style.left=(o.mvX)+'px';o.style.top=(o.mvY)+'px'; 		
		}else{/*재 배열화*/ 			
			Q[Q.length]=o; 		
		} 	
	}; 	
	
	if(Q.length==0){ 		
		this.Queue=[];this._molding(); 		
		return false; 	
	} 	
	this.Queue=Q; 	
	this.resTime=setTimeout(this._swapAviate.bindAsObject(this),0); 
};


/**
 * 마우스로 위치를 변경할 경우 입력된 객체의 위치를 마우스 포인터가 위치한 자리로 이동시킴
 *@method _dragArrange
 *@see #__dragAviate()
 *@see #__dragArrival()
 *@param {Object} element 대상 객체
 *@return {Object} 이동 객체와 각 인덱스에 따른 레이어 위치 값
 *@acess private
 */
xwzGadgetry.prototype._dragArrange=function(element){ 	
	var Queue=[],Rect=[]; 	
	/*마우스 이동 객체를 제외하고 재배열화*/ 	
	for(var i=0,len=this.Queue.length;i<len;i++){ 		
		if(i!=this._trace.nSource)Queue[Queue.length]=this.Queue[i]; 	
	}; 	
	/*대상객체와 객체가 이동할 위치에 있는 객체의 인덱스를 바꿔치기*/ 	
	Queue[Queue.length]=null; 	
	for(var i=Queue.length-1;i>this._trace.nTarget;i--)Queue[i]=Queue[i-1]; 	
	Queue[this._trace.nTarget]=this.Queue[this._trace.nSource]; 	
	/*레이어들에 대한 x,y,width,height 값 배열화*/ 	
	Rect=this._mapping(Queue,this.nCells,this.nDistance); 	
	for(var i=0,len=this.Queue.length;i<len;i++){ 		
		if(i!=this._trace.nTarget){ 			
			Queue[i].style.left=Rect[i].X+'px';Queue[i].style.top=Rect[i].Y+'px';Queue[i].style.zIndex=this.zIndex+i; 		
		}else if(element!=null){
			/*대상객체 위치 이동*/ 			
			element.style.left=Rect[i].X+'px';element.style.top=Rect[i].Y+'px';element.style.zIndex=this.zIndex+i; 		
		} 	
	}; 	
	return{Nodes:Queue,Rect:Rect}; 
};



xwzGadgetry.prototype._callbacks = function(event){ 
	if(event.preventDefault) {  
		event.preventDefault(); 
	} 
};

/**
 * drag&drop(마우스 이동)에 따른 처리함수
 *@method _dragAviate
 *@param {Object} event 이벤트 핸들러
 *@acess private
 */
xwzGadgetry.prototype._dragAviate=function(event,node){ 
	document.getElementById("IDS_DIV_FRAME1").style.zIndex = "88888";
	document.getElementById("IDS_DIV_FRAME2").style.zIndex = "88888";
	this.Gadgetry.style.zIndex = "88889";
	/*드래그앤드랍 대상이 없거나,왼쪽 마우스버튼을 누르지 않고 있는 경우 중단*/ 	
	if(node==null||event._left!==true){this._dragArrival(node);return false;} 	
	if(this.keelblocks.style.display!='block')this.keelblocks.style.display='block'; 	
	/*마우스 포인트 이동 값 측정*/ 	
	var dX=event._x-this._trace.dX,dY=event._y-this._trace.dY; 	
	/*마우스가 이동한 만큼 위치 이동*/ 
	var x=parseInt(node.style.left)+dX,y=parseInt(node.style.top)+dY; 	
	node.style.left=x+'px';node.style.top=y+'px'; 	
	/*현재 마우스 포인터에 따른 전체 영역에서 위치 계산*/ 	
	var iY=Math.floor(this._trace.nSource/this.nCells),cY=Math.abs(this.Atlas[this._trace.nSource].Y-y); 	
	var iX=Math.floor(this._trace.nSource%this.nCells),cX=Math.abs(this.Atlas[this._trace.nSource].X-x); 	
	for(var i=0,len=this.Atlas.length;i<len;i+=this.nCells){
		if(cY>Math.abs(this.Atlas[i].Y-y)){
			cY=Math.abs(this.Atlas[i].Y-y);
			iY=Math.floor(i/this.nCells);
		}
	}; 	
	for(var i=0;i<this.nCells;i++){
		if(cX>Math.abs(this.Atlas[i].X-x)){
			cX=Math.abs(this.Atlas[i].X-x);
			iX=Math.floor(i%this.nCells);
		}
	}; 	
	this._trace.nTarget=(iY*this.nCells)+iX; 	
	this._trace.nTarget=(this._trace.nTarget>this.Queue.length-1)?this.Queue.length-1:this._trace.nTarget; 	
	/*클론 레이어 이동*/ 	
	this._dragArrange(this.keelblocks); 	
	/*현재 포인트 기록*/ 	
	this._trace.dX=event._x;
	this._trace.dY=event._y; 
};
	

/**
 * drag&drop 완료에 따른 처리함수
 *@method _dragArrival
 *@acess private
 */
xwzGadgetry.prototype._dragArrival=function(node){ 	/*이벤트 해제*/ 	
	if(this.Observers['mouseup']!=null)freeEventObserve(window.document,'mouseup',this.Observers['mouseup']); 	
    if(this.Observers['mousemove']!=null)freeEventObserve(window.document,'mousemove',this.Observers['mousemove']); 	
	this.Observers['mouseup']=null;
	this.Observers['mousemove']=null; 	
	window.document.onselectstart=null;
	window.document.ondragstart = null; 	
	var oReturn=null,dX=dY=0; 	
	/**/ 	
	if(node!=null){ 		
		oReturn=this._dragArrange(); 		
		dX=parseInt(this.keelblocks.style.left)-parseInt(node.style.left); 		
		dY=parseInt(this.keelblocks.style.top)-parseInt(node.style.top); 		
		this.Queue=[{style:node.style,distX:dX,distY:dY,posX:parseInt(node.style.left),posY:parseInt(node.style.top),mvX:parseInt(this.keelblocks.style.left),mvY:parseInt(this.keelblocks.style.top),scX:0,scY:0}]; 		
		
		notifyOpacity(node,this.nOpacity); 		
	    this.isMotion==true?this._swapAviate():this.compose(); 		
		this.Nodes=oReturn.Nodes; 		
		this.Atlas=oReturn.Rect; 		
		node.style.zIndex=this.keelblocks.style.zIndex; 	
	} 	
	this.keelblocks.style.zIndex=-1;
	this.keelblocks.style.display='none'; 	
	this._trace.dX=0;this._trace.dY=0;this._trace.nTarget=null;this._trace.nSource=null; 	
	this.Observers=[]; 
};

/**
 * Slide, Bind 형태의 펼침 또는 숨김 모션 함수
 *@method _expanding
 *@param {Object} 대상 객체 영역을 감싸고 있는 객체
 *@param {Number} n Count
 *@param {Object} 대상 객체 (gadget,conf)
 *@acess private
 */
xwzGadgetry.prototype._expanding=function(node,dest,n,node2){ 		
	var curr=parseInt(node2.animate=='slide'?node2.topgap:node2.style.height);/*현재 속성*/ 		
	/*Time 인스턴스 제거*/ 
	clearTimeout(this.resTime);this.resTime=null; 			
	if(dest-curr==0||n>node2.strata){		
		/*Count 완료*/ 		
		if(node2.animate=='slide') node2.topgap=dest+'px'; 		
		else node2.style.height=dest+'px'; 		
		this._expanded(node,node2); 	
	}else{		
		/*ing*/ 		
		curr+=Math.round(Math.sin(n/node2.strata*Math.PI/2)*(dest-curr)); 				
		if(node2.animate=='slide'){  /*slide인 경우 marginTop 속성을 변경,height으로 나머지 영역을 감춤*/ 							
			node2.topgap=curr+'px'; 			
			node2.style.height=(node.conf.height+curr<=0?1:node.conf.height+curr)+'px'; 							
		}else{ /*slide가 아닌 경우 blind 로 처리하고 높이만 조정함*/ 			
			node2.style.height=(curr<=0?1:curr)+'px'; 			
		} 		
		/*대상 가젯의 높이 재 조정*/ 							
		node.style.height=(node.pixHeight-node2.height)+parseInt(node2.style.height)+'px'; 		
		this.resTime=setTimeout(this._expanding.bindAsObject(this,node,dest,++n,node2),1); 	
	} 
};

/**
 * 펼침 또는 닫힘 완료 함수
 *@method _expanded
 *@acess private
 *@param {Object} 대상 객체 영역을 감싸고 있는 객체 
 *@param {Object} 대상 객체 (gadget,conf)
 */
xwzGadgetry.prototype._expanded=function(node,node2){ 				
	if(node2.state=='expand'){	/*펼침 상태에서 완료된 경우*/ 		
		node2.style.visibility='hidden'; /*가젯을 숨김*/ 		
		node2.state='collapse';		
		/*상태값 변경*/ 		
		node2.style.height='1px'; 		
		node.style.height=(node.pixHeight-node2.height)+'px'; 		
		node2.onMinimized(); 	
	}else if(node2.state=='collapse'){ /*닫힘상태에서 완료된 경우*/ 				
		node2.style.visibility='visible';/*가젯 보임*/ 							 		
		if(node.gadget.state=='expand') {
			node.gadget.style.height = parseInt(node.gadget.style.height)+parseInt(node2.height)+'px';	         
			node2.style.height=node2.height+'px';
		}  else {
			if(node.conf.state=='expand') node2.style.height = parseInt(node2.height)+parseInt(node.conf.height)+'px';	         
			else node2.style.height = parseInt(node2.height)+'px';
        }
		node2.state='expand';/*상태값 변경*/ 	
		node.style.height=node.pixHeight+'px'; 						
		node2.onMaximized(); 
	} 	
	/*높이 변경에 따른 배열 위치 및 frame 높이 재 설정*/ 	
	this._swapDepart();
};

/**
 * resize grip view 상태 변경 함수
 *@method _showGrips
 *@param {bool} bValue [true, false]
 *@acess private
 */
xwzGadgetry.prototype._showGrips=function(bValue){for(var nm in this.grips)this.grips[nm].style.visibility=bValue==true?'visible':'hidden'; this.sizeinfo.style.visibility=bValue==true?'visible':'hidden';};
/**
 * resize grip/ border div 위치 설정 함수
 *@method _showGrips
 *@param {int} x x좌표값
 *@param {int} y y좌표값
 *@param {int} w 너비
 *@param {int} h 높이
 *@acess private
 */
xwzGadgetry.prototype._resizeArrange=function(src){ 	var g=parseInt(this.grip.size); 	var x=parseInt(src.offsetLeft||src.style.left)+1,y=parseInt(src.offsetTop||src.style.top)+1; 	var w=parseInt(src.offsetWidth||src.style.width)-1,h=parseInt(src.offsetHeight||src.style.height)-1; 	/*각 grip 위치 설정*/ 	for(var nm in this.grips){ 		if(nm.charAt(0)=='l')				this.grips[nm].style.left=x+'px'; 		else if(nm.charAt(0)=='c')		this.grips[nm].style.left=(x+(w/2-g/2))+'px'; 		else if(nm.charAt(0)=='r')		this.grips[nm].style.left=((x+w)-g)+'px'; 		if(nm.charAt(1)=='t')				this.grips[nm].style.top=y+'px'; 		else if(nm.charAt(1)=='m')	this.grips[nm].style.top=(y+(h/2-g/2))+'px'; 		else if(nm.charAt(1)=='b')	this.grips[nm].style.top=((y+h)-g)+'px'; 		this.grips[nm].style.visibility='visible'; 	}; };
/**
 * resize grip view 상태 변경 함수
 *@method _resizeDepart
 *@param {event object} event 마우스 이벤트
 *@acess private
 */
xwzGadgetry.prototype._resizeDepart=function(event,node){ 	window.focus(); 	var node=this.Nodes[this._trace.nSource]; 	if(node==null||event._left!==true){this._resizeArrival();return false;} 	this.keelblocks.style.display='block'; 	/*레이어가 이동할 동안 레이어가 위치할 자리에 대신 위치할 객체 이동*/ 	this.keelblocks.style.left=node.style.left; 	this.keelblocks.style.top=node.style.top; 	this.keelblocks.style.width=node.style.width; 	this.keelblocks.style.height=node.style.height; 	/*z-index save*/ 	var z=node.style.zIndex; 	this.keelblocks.style.zIndex=this.zIndex+(this.Nodes.length*500); 	this._trace.dX=event._x;this._trace.dY=event._y; 	/*이벤트 등록 */ 	this.Observers['mousemove']=this._resizeAviate.bindAsEvent(this,node,event.element.nm); 	this.Observers['mouseup']=this._resizeArrival.bindAsObject(this,node,event.element.nm); 	addEventObserve(window.document,'mouseup',this.Observers['mouseup']); 	addEventObserve(window.document,'mousemove',this.Observers['mousemove']); 	window.document.onselectstart=new Function('return false'); 	window.document.ondragstart = new Function ("return false"); 	this.keelblocks.style.cursor=event.element.style.cursor; };
/**
 * resizing 함수
 *@method _resizeAviate
 *@param {event object} event 마우스 이벤트
 *@acess private
 */
xwzGadgetry.prototype._resizeAviate=function(event,node,nm){ 	/*드래그앤드랍 대상이 없거나,왼쪽 마우스버튼을 누르지 않고 있는 경우 중단*/ 	if(node==null||event._left!==true){this._dragArrival(node);return false;} 	/*마우스 포인트 이동 값 측정*/ 	var dx =event._x-this._trace.dX,dy=event._y-this._trace.dY,m1=0,m2=0,d=0,g=0; 	var fX=nm.charAt(0),fY=nm.charAt(1); 	var l=parseInt(this.keelblocks.style.left),t=parseInt(this.keelblocks.style.top),w=parseInt(this.keelblocks.style.width),h=parseInt(this.keelblocks.style.height); 	/*left, width 설정*/ 	if(fX!='c'){ 		m1=parseInt(this.grip.minWidth||this.grip.size*3);m2=parseInt(this.grip.maxWidth||w*2); 		d=w+(fX=='l'?(dx*-1):dx);/*left 이동에 처리*/ 		if(d>m1&&d<m2){if(l+dx>0&&fX=='l'){this.keelblocks.style.left=(l+dx)+'px';w=d;}else if(fX!='l'){w=d;}} 		this.keelblocks.style.width=w+'px'; 	} 	/*top, height 설정*/ 	if(fY!='m'){ 		m1=parseInt(this.grip.minHeight||this.grip.size*3);m2=parseInt(this.grip.maxHeight||h*2); 		d=h+(fY=='t'?(dy*-1):dy);/*top 이동에 처리*/ 		if(d>m1&&d<m2){if(t+dy>0&&fY=='t'){this.keelblocks.style.top=(t+dy)+'px';h=d;}else if(fY!='t'){h=d;}} 		this.keelblocks.style.height=h+'px'; 	} 	/*각 grip 위치 설정*/ 	this._resizeArrange(this.keelblocks); 	this.sizeinfo.innerText=parseInt(this.keelblocks.style.width)+'x'+parseInt(this.keelblocks.style.height);	this.sizeinfo.style.left=(parseInt(this.grips.rb.style.left)-(this.sizeinfo.offsetWidth)+2)+'px';this.sizeinfo.style.top=(parseInt(this.grips.rb.style.top)-(this.sizeinfo.offsetHeight)+2)+'px'; 	/*현재 포인트 기록*/ 	this._trace.dX=event._x;this._trace.dY=event._y; };
/**
 * resize 완료함수
 *@method _resizeArrival
 *@param {event object} event 마우스 이벤트
 *@acess private
 */
xwzGadgetry.prototype._resizeArrival=function(node){ 	/*이벤트 해제*/ 	if(this.Observers['mouseup']!=null)freeEventObserve(window.document,'mouseup',this.Observers['mouseup']); 	if(this.Observers['mousemove']!=null)freeEventObserve(window.document,'mousemove',this.Observers['mousemove']); 	this.Observers['mouseup']=null;this.Observers['mousemove']=null; 	window.document.onselectstart=null;window.document.ondragstart = null; 	this._showGrips(); 	/*크기 재 설정*/ 	if(node!=null){	node.style.width=this.keelblocks.style.width;node.style.height=this.keelblocks.style.height;this._swapDepart();} 	this.keelblocks.style.cursor='auto';this.keelblocks.style.display='none'; 	this.sizeinfo.innerText=''; 	if(node.pixWidth!=null)node.pixWidth=parseInt(node.style.width);if(node.pixHeight!=null)node.pixHeight=parseInt(node.style.height);};