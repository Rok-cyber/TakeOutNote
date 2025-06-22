/**************************************************************************************
####	previl WebEditor (DHTML wysiwyg 웹에디터)
####	Beta 2007
####	http://dev.previl.net

####	참조 에디터 : easyeditor(http://cafe.daum.net/easyeditor), minieditor(), spaw
***************************************************************************************/

var isMsie = document.all ? true : false; 
if(isMsie) {
	var tmp_now = new Date();
	tmp_now = tmp_now.getYear();
	if(parseInt(tmp_now)<1000) isMsie = false;
}

var pEditorConf = {

    border	:"1px solid #dadada",	//기본 border
	bgcolor : "#fff",			//기본 bgcolor
	font	: "normal 10pt 굴림",	//기본 폰트 style(font-style, font-variant, font-weight)
	color	: "#000000",				//기본 폰트 컬러
	margin	: "5px",				//내부 margin
	divTool_bgcolor : "#fff",	
	btnList : {		
		cut : ["잘라내기","tb_cut.gif"], copy : ["복사하기","tb_copy.gif"], paste : ["붙여놓기","tb_paste.gif"], undo : ["실행취소","tb_undo.gif"], redo : ["다시실행","tb_redo.gif"],
 		hyperlink : ["하이퍼링크","tb_hyperlink.gif"], imageInsert : ["이미지삽입","tb_image.gif"], imageModify : ["이미지편집","tb_image_prop.gif"], inserthorizontalrule : ["수평라인","tb_inserthorizontalrule.gif"], 
        tableCreate : ["표그리기","tb_table_create.gif"],  tableModify : ["표편집","tb_table_prop.gif"],  tableCellModify : ["셀편집","tb_table_cell_prop.gif"],  
		tableRowInsert: ["가로행추가","tb_table_row_insert.gif"],  tableColInsert: ["세로행추가","tb_table_column_insert.gif"],	tableRowDelete : ["가로행삭제","tb_table_row_delete.gif"],	tableColDelete : ["세로행삭제","tb_table_column_delete.gif"], 
		tableRowMerge: ["가로셀병합","tb_table_cell_merge_right.gif"],  tableColMerge: ["세로셀병합","tb_table_cell_merge_down.gif"],	tableRowSplit : ["가로셀추가","tb_table_cell_split_horizontal.gif"],	tableColSplit : ["세로셀추가","tb_table_cell_split_vertical.gif"], 
        bold : ["굵게","tb_bold.gif"], italic : ["기울림꼴","tb_italic.gif"], underline : ["밑줄","tb_underline.gif"], strikethrough : ["취소선","tb_strikethrough.gif"],
        justifyleft : ["왼쪽맞춤","tb_justifyleft.gif"], justifycenter : ["가운데맞춤","tb_justifycenter.gif"], justifyright : ["오른쪽맞춤","tb_justifyright.gif"], justifyfull : ["혼합정렬","tb_justifyfull.gif"],
		indent : ["들여쓰기","tb_indent.gif"], outdent : ["내어쓰기","tb_outdent.gif"], color : ["글자색","tb_fore_color.gif"], hilite : ["글자 배경색","tb_bg_color.gif"],
        superscript : ["윗첨자","tb_superscript.gif"], subscript : ["아랫첨자","tb_subscript.gif"], insertorderedlist : ["번호달기","tb_insertorderedlist.gif"],  insertunorderedlist : ["기호달기","tb_insertunorderedlist.gif"],
        preview : ["미리보기","tb_preview.gif"], save : ["HTML저장","tb_save.gif"], cleanup : ["새로작성","tb_cleanup.gif"],
        font : ["글자체","tb_font.gif"], fontSize : ["글자크기","tb_font_size.gif"],
		space : ["공백","spacer.gif"]         
	},
    toolTpl : {
		all : 
			[ "cut","copy","paste","space","undo","redo","space",
			    "hyperlink","imageModify","inserthorizontalrule","space",
			    "tableCreate","tableModify",
			    //"tableCellModify","tableRowInsert","tableColInsert","tableRowDelete","tableColDelete",
		        //"tableRowMerge","tableColMerge","tableRowSplit","tableColSplit",
			    "space","preview","save","cleanup",
				"br",
			    "font","fontSize","space","bold","italic","underline","strikethrough","space",
				"justifyleft","justifycenter","justifyright","justifyfull","space",
				"indent","outdent","space","color","hilite","space","superscript","subscript","space","insertorderedlist","insertunorderedlist"			    
			   
		    ],			
		popup : 
			[   "space","font","space","fontSize","space","bold","italic","underline","strikethrough","subscript","color","hilite","space",
				"justifyleft","justifycenter","justifyright",
				"indent","outdent","insertorderedlist","insertunorderedlist","space",
			    "hyperlink","inserthorizontalrule","imageInsert","imageModify","tableCreate","tableModify"
		    ],
		basic : 
			[   "space","font","space","fontSize","space","bold","italic","underline","strikethrough","superscript","subscript","color","hilite","space",
				"justifyleft","justifycenter","justifyright","justifyfull",
				"indent","outdent","insertorderedlist","insertunorderedlist","space",
			    "hyperlink","inserthorizontalrule","imageInsert","imageModify","tableCreate","tableModify"
		    ],
		basic2 : 
			[   "space","font","space","fontSize","space","bold","italic","underline","strikethrough","superscript","subscript","color","hilite","space",
				"justifyleft","justifycenter","justifyright","justifyfull",
				"indent","outdent","insertorderedlist","insertunorderedlist","space",
			    "hyperlink","inserthorizontalrule","imageModify","tableCreate","tableModify"
		    ]
		
	},
    fontList : ["굴림체","궁서체","돋움체","바탕체","Arial","Helvetica","Tahoma","Verdana"],
	fontSize : [["8pt","1"],["10pt","2"],["12pt","3"],["14pt","4"],["18pt","5"],["24pt","6"],["32pt","7"]],
	htmlModeTool : ["cut","copy","paste","undo","redo","preview","save","cleanup"],
	setId : null
}

var pEditor = function(id,path,width,height,uploadDir,toolTpl,heightBar) {	
	if(typeof(document.execCommand)=="undefined" || !id) return;	
	this.version='1.0'; 	
	this.id			= id;
	this.uploadDir	= uploadDir;
	this.conf		= pEditorConf;		
	this.mode		= "design";	
	this.order		= null;	
	this.selc		= null;
	this.rng		= null;	
	this.gHtml		= null;
	this.dragStatus = false;
	this.posY		= null;
	this._colorPicker= null;	
	this._fontList	= null;	
	this._fontSize	= null;	
	this._cw		= null;
	this._doc		= null;	
	pEditorConf.setId= id;
	this._textarea	= document.getElementById(id);
	this._div		= document.createElement("div");		//전체 div
	this._divTool	= document.createElement("div");		//버튼영역 div
	this._divMode	= document.createElement("div");		//버튼영역 div
	this._iframe	= document.createElement("iframe");		//iframe	
	this._div.id	= this.id+"_div";
	this._divTool.id= this.id+"_divTool";
	this._iframe.id	= this.id+"_iframe";
	
	this.path	= (path)?path:'.';
	this.width	= (width)?width:'600';
	this.height = (height)?height:'400';	
	this.toolTpl= (toolTpl)?toolTpl:'all';	
	this.heightBar	= (heightBar) ? heightBar:'show';	

	this.init();
}

pEditor.prototype.init = function(){	    	
	this._textarea.style.display	="none";	
	this._div.style.textAlign		= "left";
	this._div.style.border			= this.conf.border;
	this._div.style.width			= parseInt(this.width)+'px';    
	if(document.selection) this._divTool.style.width = parseInt(this.width)+'px';
	this._divTool.style.background	= "#efefef url("+this.path+"/img/toolbarBg.gif) ";
	this._divTool.style.paddingTop	= "4px";
	this._divTool.style.paddingBottom = "3px";
	this._divTool.style.borderBottom= this.conf.border;
	//this._divTool.className			= "toolbarBg";
	this._iframe.style.padding		= "5px";
	this._iframe.style.width		= parseInt(this.width)-10+'px';
	this._iframe.style.height		= parseInt(this.height)-10+'px';	
	this._iframe.frameBorder		= "no";    
	this._divMode.style.width		= (parseInt(this.width)+3)+'px';	
    this._textarea.parentNode.insertBefore(this._div, this._textarea);	
	this._div.appendChild(this._divTool);
	this._div.appendChild(this._iframe);	
	this._textarea.parentNode.insertBefore(this._divMode, this._textarea);	
	if(!this._textarea.value) this._textarea.value = "<br />";

	this.setTool();
	this.setMode();

	this._cw	= this._iframe.contentWindow;
   	this._doc	= this._cw.document;

	this._doc.designMode="on";
	//기본 css설정 
	var css  = "body {margin:0px;background-color:#fff;}";
	css		+= "body,table,td{ font-size: 12px; color:#333; font-style: normal; font-family:'굴림','gulim','verdana'; line-height:1.4; word-spacing:-1pt;";
	css		+= "scrollbar-3dlight-color:#595959;scrollbar-arrow-color:#fff;scrollbar-base-color:#cfcfcf;";
	css		+= "scrollbar-darkshadow-color:#fff;scrollbar-face-color:#cfcfcf;scrollbar-highlight-color:#fff;";
	css		+= "scrollbar-shadow-color:#595959;}";
	
	this._iframe.css = css;
    this._doc.open();
	this._doc.write('<html><head><style type="text/css">'+css+'</style></head><body>'+this._textarea.value+'</body></html>');
	this._doc.close();

	var self=this;

	this.addEvent(this._doc, "keydown", function(e) {
		self.divHide();
		var range=self._doc.selection.createRange();		
		if(e.keyCode==13 && range.parentElement().tagName!="LI") {			
			if(isMsie==false) return;
			e.cancelBubble=true; 
			e.returnValue=false;
			range.pasteHTML("<br />"); 
			range.select(); 
			return false;
		}
	});

	this.addEvent(this._doc, "click", function(e) { self.divHide(); });
	
}

pEditor.prototype.cmd = function(btn, order,value) 
{
	var obj = this._cw;

    obj.focus();

	if(this.mode=='html') {
		var cks=0;
		for(k=0,cnt=this.conf.htmlModeTool.length;k<cnt;k++){
			if(order==this.conf.htmlModeTool[k]) { cks=1; break; }
		}      
		if(cks==0) { alert("Design Mode에서만 사용 가능 합니다."); return; }
	}

	switch(order) {		
		case "cut": case "copy": case "paste": case "undo": case "redo" : case "inserthorizontalrule":
        case "bold": case "italic": case "underline": case "strikethrough": 
		case "justifyleft": case "justifycenter": case "justifyright": case "justifyfull":
		case "indent": case "outdent": case "superscript": case "subscript":
        case "insertorderedlist": case "insertunorderedlist":						
			this._doc.execCommand(order, false, null);
			obj.focus();
		break;
		case "font":	
			if(this.selc) this.rng.select();	
			this._doc.execCommand('fontname', false, value); 
			obj.focus();
        break;
		case "fontSize":
			if(this.selc) this.rng.select();
		    for(var i=0,cnt=this.conf.fontSize.length; i<cnt; i++) {
				if(this.conf.fontSize[i][0]==value) value2 = this.conf.fontSize[i][1];
			}
			if(value2) {
				this._doc.execCommand('fontsize', false, value2);			
				obj.focus();
			}
		break;
		case "color": case "hilite":				
		    if(this.selc) this.rng.select();		    		    
			if(order=='color') this._doc.execCommand('forecolor', false, value); 
		    else {
				if(this.selc) this._doc.execCommand('backcolor', false, value); 
				else this._doc.execCommand('hilitecolor', false, value); 
		    }
			obj.focus();		
		break;	
        case "preview":
            var w=window.open("","preview","width=800,height=600,status=1,scrollbars=1,resizable=1");
	        w.document.open();
	        w.document.write("<style>"+this._iframe.css+"</style>"+this.getHtml());
			w.document.close();
        break;
		case "save":  	
			this._doc.execCommand("saveAs", false, "WE0001.html");
			obj.focus();	 
        break;
		case "cleanup":
            if(confirm("확인을 누르시면 지금까지 작업한 모든 결과물들이 삭제됩니다!")){
				this._doc.body.innerHTML = '';			
			}
        break;
		case "hyperlink":
            this.setSelection();
			pEditor.openDialog(this,this.path,'createLink.html','','commCallback','','540','520');						
		break;
		case "imageInsert":
			this.setSelection();
		    pEditor.openDialog(this,this.path,'insertImage.php?udir='+this.uploadDir,'','commCallback','','660','580');     		
		break;
		case "imageModify":			
			var im = this.getHtmlInfo("IMG");			
			
			if (im) {
				var iProps = {};
				iProps.src		= im.src;
				iProps.alt		= im.alt;
				iProps.style	= im.style;
				iProps.width	= (im.style.width)?im.style.width:im.width;
				iProps.height	= (im.style.height)?im.style.height:im.height;			
				iProps.align	= im.align;
				iProps.hspace	= im.hspace;
				iProps.vspace	= im.vspace;

				pEditor.openDialog(this,this.path,'modifyImage.html',iProps,'imageModifyCallback',im,'600','520');     		

			} else alert("수정할 그림먼저선택하세요");			
		break;
		case "tableCreate":
			this.setSelection();
		    pEditor.openDialog(this,this.path,'propTable.html','','tableCallback','','600','480');     				
		break;
		case "tableModify":
			
			var tTable = this.getHtmlInfo("TABLE");
							
			if(tTable) {
				var tProps = {};
				
				tProps.rows			= tTable.rows.length;
				tProps.cols			= parseInt(tTable.cells.length/tProps.rows);				
				tProps.width		= (tTable.style.width)?tTable.style.width:tTable.width;
				tProps.height		= (tTable.style.height)?tTable.style.height:tTable.height;
				tProps.border		= tTable.border;
				tProps.cellPadding	= tTable.cellPadding;
				tProps.cellSpacing	= tTable.cellSpacing;
				tProps.bgColor		= tTable.bgColor;
				tProps.borderColor	= tTable.borderColor;
				tProps.align		= tTable.align;			
				tProps.background	= tTable.background;
				
				pEditor.openDialog(this,this.path,'propTable.html',tProps,'tableCallback','','600','480');  

            } else alert("먼저 표를 선택하세요");
			
        break;
	}
};

pEditor.prototype.setTool = function() {
	var self = (this)?this:'';
	var arr=this.conf.toolTpl[this.toolTpl];
	var len=arr.length, order="", btn=tag=null;	

	for(var i=0;i<len;i++) {
		tmp = "";		
		order = this.trim(arr[i]);				
		btn = this.conf.btnList[order];
        
		switch(order) {
			case "space" :
				tag			= document.createElement("img");
				tag.src		= this.path+"/img/"+btn[1];
				tag.width	= 4;
				tag.height	= 26;
				tag.align	="absmiddle";
				this._divTool.appendChild(tag);
		    break; 
			case "br" :
				this._divTool.appendChild(document.createElement("br"));
			break;			
			default :
                if(!order || !btn) { alert("없는 버튼입니다 ("+order+")"); continue; }
			    if(!document.selection && (order=='copy' || order=='paste' || order=='cut')) continue;
				tag			= document.createElement("img");
				tag.id		= this.id+"_btn_"+order;
				tag.src		= this.path+"/img/"+btn[1];
				tag.align	="absmiddle";             
				tag.alt		= btn[0];
				tag.cmd		= order;
				if(order=='imageInsert') tag.style.width = '90px';
				tag.style.cursor="pointer";		
						
				if(order=='color' || order=='hilite') tag.onclick = function() { self.setSelection(); self.order=this.cmd; self.setColorPicker(this.cmd); self.divHide(); self.divShow(this,'colorPicker'); };
				else if(order=='font') tag.onclick = function() { self.setSelection(); self.order=this.cmd; self.setFontList(this.cmd); self.divHide(); self.divShow(this,'fontList'); };
				else if(order=='fontSize') tag.onclick = function() { self.setSelection(); self.order=this.cmd; self.setFontSize(this.cmd); self.divHide(); self.divShow(this,'fontSize'); };
				else tag.onclick		= function() { self.divHide(); self.cmd(this, this.cmd); };
				tag.onmouseover	= function() { this.src = this.src.substr(0, this.src.length-4) + "_over.gif"; };
				tag.onmouseout	= function() { this.src = this.src.substr(0, this.src.length-9) + ".gif"; };			 				
				this._divTool.appendChild(tag);			
			break;	 
		}

	}
}

pEditor.prototype.setMode = function() {
    var tag = null;
	var self = (this)?this:'';

	function changeMode(cMode,obj){		
		if(cMode!=self.mode) return;
		tobj = document.getElementById(self.id+'_btn_'+cMode+'Mode');
		obj.src = obj.src.substr(0, obj.src.length-4) + "_over.gif"
		tobj.src = tobj.src.substr(0, tobj.src.length-9) + ".gif";
        
		if(cMode == 'design' ) {			
			if(self._doc.selection)	self._doc.body.innerText = self._doc.body.innerHTML; 
			else self._doc.body.textContent = self._doc.body.innerHTML; 			
			self.mode="html"; 						
			self._iframe.contentWindow.focus();
        } else {
			if(self._doc.selection)	self._doc.body.innerHTML = self._doc.body.innerText; 			
			else self._doc.body.innerHTML = self._doc.body.textContent;
			self.mode="design";
			self._iframe.contentWindow.focus();
        }
	}
	
	if(this.heightBar=='show') {
		tag			= document.createElement("div");
		tag.id		= this.id+"_heightChange";
		tag.style.background	= "url("+this.path+"/img/btn_heightChange.gif) no-repeat center";
		tag.style.cursor		= "n-resize";	
		tag.style.width		=  (parseInt(this._iframe.style.width)+10)+'px';	
		tag.style.height	= '8px';		
		tag.style.overFlow	= "hidden";
		tag.align			="center";
		tag.style.border	= this.conf.border;
		tag.style.borderTop	= '';
		tag.onmousedown		= function (e) { self.dragStart(e,this); };
		
		this._divMode.appendChild(tag);
	}

	this._divMode.style.textAlign = "right";
	
	tag			= document.createElement("img");
	tag.id		= this.id+"_btn_htmlMode";
	tag.src		= this.path+"/img/tb_html.gif";
	tag.align	="right";
	tag.hspace	= 1;
	tag.alt		= "Html Mode";	
	tag.style.cursor="pointer";	
	tag.onclick = function() { changeMode('design',this); }
	this._divMode.appendChild(tag);

	tag			= document.createElement("img");
	tag.id		= this.id+"_btn_designMode";
	tag.src		= this.path+"/img/tb_design_over.gif";
	tag.align	="right";
	tag.hspace	= 1;
	tag.alt		= "Design Mode";
	tag.style.cursor="pointer";	
	tag.onclick = function() { changeMode('html',this); }
	this._divMode.appendChild(tag);
}

pEditor.prototype.dragStart = function (event,obj) {
		var self = (this)?this:'';		
		if(document.all) e = window.event;
		else var e = event; 

		this.dragStatus = true;
		this.posY = (e.clientY || e.pageY);

      	this.addEvent(document, "mouseup", this.dragEnd);		
		this.addEvent(document, "mousemove", this.draging);		
		this.addEvent(document, "selectstart", function(e){ return false;});
}

pEditor.prototype.draging=function(e){
	try {			
		obj = eval(pEditorConf.setId);		
		if (obj.dragStatus) {				
			moveY = (e.clientY || e.pageY) - obj.posY;				
			if(parseInt(obj._iframe.style.height)+ moveY>=obj.height) {
				obj._iframe.style.height = parseInt(obj._iframe.style.height) + moveY + 'px';		
				obj.posY = (e.clientY || e.pageY);			
			}
		}
	} catch(e) { }
}

pEditor.prototype.dragEnd=function(e){		
	try {
		obj = eval(pEditorConf.setId);		
		obj.dragStatus = false;		
		obj.removeEvent(document, "mouseup", this.dragEnd);		
		this.removeEvent(document, "mousemove", this.draging);		
	} catch(e) { }
}

pEditor.prototype.addEvent=function(obj, type, listener)	{		
	if(obj.addEventListener) { obj.addEventListener(type, listener, false); } 
	else if(obj.attachEvent) { obj.attachEvent("on"+type, listener); } 
}

pEditor.prototype.removeEvent=function(obj, type, listener)	{		
	if(obj.addEventListener) { obj.removeEventListener(type, listener, false); } 
	else if(obj.attachEvent) { obj.detachEvent("on"+type, listener); } 
}

pEditor.prototype.setColorPicker =function(order){
	if(this._colorPicker==null) {
		var self = (this)?this:'';
		var col= new Array();
		col[0] = new Array("#FFEEEE","#FFCCCC","#FFAAAA","#FF8888","#FF6666","#FF4444","#FF2222","#FF0000","#EE0000","#CC0000","#AA0000","#880000","#770000","#660000","#550000","#440000","#330000");
		col[1] = new Array("#EEFFEE","#CCFFCC","#AAFFAA","#88FF88","#66FF66","#44FF44","#22FF22","#00FF00","#00EE00","#00CC00","#00AA00","#008800","#007700","#006600","#005500","#004400","#003300");
		col[2] = new Array("#EEEEFF","#CCCCFF","#AAAAFF","#8888FF","#6666FF","#4444FF","#2222FF","#0000FF","#0000EE","#0000CC","#0000AA","#000088","#000077","#000066","#000055","#000044","#000033");
		col[3] = new Array("#FFFFEE","#FFFFCC","#FFFFAA","#FFFF88","#FFFF66","#FFFF44","#FFFF22","#FFFF00","#EEEE00","#CCCC00","#AAAA00","#888800","#777700","#666600","#555500","#444400","#333300");
		col[4] = new Array("#FFEEFF","#FFCCFF","#FFAAFF","#FF88FF","#FF66FF","#FF44FF","#FF22FF","#FF00FF","#EE00EE","#CC00CC","#AA00AA","#880088","#770077","#660066","#550055","#440044","#330033");
		col[5] = new Array("#FFDDD0","#FFE0AA","#FFDD88","#FFCC77","#FFBB66","#FFAA55","#FFAA44","#FF9944","#FF8833","#FF7722","#FF6622","#EE5522","#DD4411","#CC3300","#AA2200","#882200","#662200");
		col[6] = new Array("#EEFFFF","#CCFFFF","#AAFFFF","#88FFFF","#66FFFF","#44FFFF","#22FFFF","#00FFFF","#00EEEE","#00CCCC","#00AAAA","#008888","#007777","#006666","#005555","#004444","#003333");
		col[7] = new Array("#FFFFFF","#EEEEEE","#DDDDDD","#CCCCCC","#BBBBBB","#AAAAAA","#A0A0A0","#999999","#888888","#777777","#666666","#555555","#444444","#333333","#222222","#111111","#000000");

		var div = document.createElement("div");
		div.id				= this.id+'_colorPicker';
		div.className		= "WebEditorDiv";
		div.style.display	= "none"
		div.style.position	= "absolute";
		div.style.backgroundColor = "#fafafa";
		div.style.border	= "1px solid #cccccc";
		div.style.padding	= "4px";
		document.body.appendChild(div);

		for(var i=0; i<8; i++) {
			for(var j=0; j<17; j++)	{
				color	= col[i][j];
				tag		= document.createElement("a");
				tag.style.backgroundColor = color;
				tag.title		= color;
				tag.innerHTML	= "<img src='"+this.path+"/img/spacer.gif' width='3' height='10' />";
				tag.style.cursor= "pointer";
				tag.onclick = function() { document.getElementById(self.id+'_secColor').style.backgroundColor=this.title; document.getElementById(self.id+'_secHex').value=this.title; };
				div.appendChild(tag);
			}
			div.appendChild(document.createElement("br"));
		}

		tag		= document.createElement("div");
		tag.innerHTML = "<div style='float:left; margin-top:5px; margin-right:4px; border:1px #dadada solid; width:80px; height:18px;'><img src='"+this.path+"/img/spacer.gif' width='76' height='14' style='background-color:#000000;border:2px #FFFFFF solid;' id='"+this.id+"_secColor' /></div>";
		tag.innerHTML += "<div style='float:left; margin-top:4px;'><input type='text' style='height:16px; border:1px #dadada solid; font-family:Tahoma;font-size:8pt;' id='"+this.id+"_secHex' size='8' value='#000000' onblur=\"try { if(this.value.length==7) document.getElementById('"+this.id+"_secColor').style.backgroundColor=this.value; } catch(e){ this.value = document.getElementById('"+this.id+"_secColor').style.backgroundColor; }\"></div>";

		tag2			= document.createElement("img");
		tag2.src		= this.path+"/img/btn_select.gif";
		tag2.style.margin	= "5px 0px 0px 4px";
		tag2.style.cursor	= "pointer";
		tag2.onclick	= function() { self.cmd(self,self.order,document.getElementById(self.id+'_secHex').value); div.style.display='none';}
		tag.appendChild(tag2);

		div.appendChild(tag);
		this._colorPicker = div;
	}
}

pEditor.prototype.setFontList =function(order){	
	if(this._fontList==null) { 
		var self = (this)?this:'';	
		var div = document.createElement("div");
		div.id				= this.id+'_fontList';
		div.className		= "WebEditorDiv";
		div.style.display	= "none"
		div.style.textAlign	= "left"
		div.style.position	= "absolute";
		div.style.backgroundColor = "#f5f5f5";			
		div.style.border	= "1px solid #cccccc";
		div.style.padding	= "4px";	
		div.style.width		= "248px";	
		document.body.appendChild(div);

		var pattern=/^[가-힣]+$/;

		for(var i=0,cnt=this.conf.fontList.length; i<cnt; i++) {
			tag		= document.createElement("a");			
			tag.style.fontSize	= "9pt";

			tag.style.fontColor	= "#000000";
			tag.style.cursor	= "pointer";	
			tag.style.fontFamily= this.conf.fontList[i];
			tag.onclick = function() { self.cmd(this,self.order,this.style.fontFamily);div.style.display='none' };			
			txt = (pattern.test(this.conf.fontList[i])) ? "가나다라마바사 12345678":"abcdefgh 12345678";
			tag.innerHTML = txt + "("+this.conf.fontList[i]+")";
			div.appendChild(tag);	
			div.appendChild(document.createElement("br"));
		}

		this._fontList = div;    
	}	
}

pEditor.prototype.setFontSize =function(order){	
	if(this._fontSize==null) { 
		var self = (this)?this:'';	
		var div = document.createElement("div");
		div.id				= this.id+'_fontSize';
		div.className		= "WebEditorDiv";
		div.style.display	= "none"
		div.style.textAlign	= "left"
		div.style.position	= "absolute";
		div.style.backgroundColor = "#f5f5f5";			
		div.style.border	= "1px solid #cccccc";
		div.style.padding	= "4px";	
		div.style.width		= "300px";	
		document.body.appendChild(div);
        
		for(var i=0,len=5,cnt=this.conf.fontSize.length; i<cnt; i++) {
			tag		= document.createElement("a");			
			tag.style.fontColor	= "#000000";			
			tag.style.cursor	= "pointer";	
			tag.style.fontFamily = this.conf.fontList[0];
			tag.style.fontSize	= this.conf.fontSize[i][0];
			tag.onclick = function() { self.cmd(this,self.order,this.style.fontSize);div.style.display='none' };			
			txt = "가나다라마바사";
			
			if(parseInt(tag.style.fontSize)>17) {
				if(parseInt(tag.style.fontSize)!=18) len=3;
				for(k=0,t='';k<len;k++){
					t += txt.charAt(k);
				}
				txt=t;
			}
			
			tag.innerHTML = txt + "("+this.conf.fontSize[i][0]+")";
			div.appendChild(tag);	
			div.appendChild(document.createElement("br"));
		}

		this._fontSize = div;    
	}	
}

pEditor.prototype.setSelection = function() {
	if(this._doc.selection)	{
		this.selc	= this._doc.selection;
		this.rng	= this.selc.createRange();		
		this.gHtml	= this.rng.htmlText;
	} else if(this._cw.getSelection) {
		this.selc	=this._cw.getSelection();
		this.rng	=this.selc.getRangeAt(0);		
		this.gHtml	=this.selc;		
		this.selc='';
	}	
}

pEditor.prototype.getHtmlInfo = function(tagName) {
	this.setSelection();
	if(this.selc.type == "Control") {
		elm = this.rng(0);
		while (elm && elm.tagName && elm.tagName.toLowerCase() != tagName.toLowerCase() && elm.tagName.toLowerCase() != 'body')
			elm = elm.parentNode;
			
		if (elm && elm.tagName && elm.tagName.toLowerCase() != 'body')
			return elm;		
    } else {						
		if (this.gHtml && this.gHtml.rangeCount>0) {											
			var container = this.rng.commonAncestorContainer;						
			if (container.nodeType == 3) elm =  container.parentNode;
			else if (this.rng.startContainer.nodeType == 1 && this.rng.startContainer == this.rng.endContainer && (this.rng.endOffset-this.rng.startOffset)<=1) {
				elm = this.rng.startContainer.childNodes[this.rng.startOffset];
			}				
			
			while (elm && elm.tagName && elm.tagName.toLowerCase() != tagName.toLowerCase() && elm.tagName.toLowerCase() != 'body')
				elm = elm.parentNode;
				
			if (elm && elm.tagName && elm.tagName.toLowerCase() != 'body')
				return elm;		
		} 

	}	
	return null;
}

pEditor.prototype.divShow = function(obj,name){
	tmpDiv = eval("this._"+name)
	tmpDiv.style.top= this.curTop(obj) + obj.offsetHeight + 12 + "px";			
	tmpDiv.style.left = this.curLeft(obj) + "px";			
	if(parseInt(tmpDiv.style.width)+parseInt(tmpDiv.style.left)>this.width){
		tmpDiv.style.left = this.width - parseInt(tmpDiv.style.width);
	} 
	tmpDiv.style.zIndex = "999";
	tmpDiv.style.display="";	
}

pEditor.prototype.divHide = function(){
	if(document.getElementById(this.id+'_colorPicker')) document.getElementById(this.id+'_colorPicker').style.display="none";	
	if(document.getElementById(this.id+'_fontList')) document.getElementById(this.id+'_fontList').style.display="none";	
	if(document.getElementById(this.id+'_fontSize')) document.getElementById(this.id+'_fontSize').style.display="none";	
}

pEditor.prototype.trim=function(s) {return s.replace(/^\s+|\s+$/g,'');} 

pEditor.prototype.curTop=function(obj) {
	var top		= obj.offsetTop;
	var parent	= obj.offsetParent;
	while(parent) {	top += parent.offsetTop; parent = parent.offsetParent; }
	return top;
}

pEditor.prototype.curLeft=function(obj) {
	var left = obj.offsetLeft;
	var parent = obj.offsetParent;
	while(parent) {	left += parent.offsetLeft; parent = parent.offsetParent; }
	return left;
}

pEditor.prototype.focus = function() {
	this._cw.focus();
}

pEditor.prototype.getHtml = function() {
	if(this.mode=="design") return this._doc.body.innerHTML;
	else {
		if(this._doc.selection)	return this._doc.body.innerText; 			
		else return this._doc.body.textContent;
	}
}



pEditor.openDialog = function(editor,path,dialog,arguments,callback,obj,width,height) {
    var posX = screen.availWidth/2 - 275;
    var posY = screen.availHeight/2 - 250;
    var durl = path + '/dialogs/'+dialog;
    var args = new Object();  
    args.Editor	= editor;  
    args.arguments= arguments;  
    args.callback	= 'pEditor.'+callback; 
    args.obj		= obj;
    var wnd = window.open(durl, '', 'status=no,resizable=yes,width='+width+',height='+height+',left='+posX+',top='+posY);
    window.dialogArguments = args;
    wnd.focus();   
    return wnd;
}

pEditor.commCallback = function(editor, result){	
	if(editor.selc) {
		editor.rng.pasteHTML(result);
	} 
	else {
		if(document.getSelection){
			sel = editor._doc.getSelection();
			
			var range = editor._doc.getSelection().getRangeAt(0);
			var nnode = editor._doc.createElement("span");
				range.surroundContents(nnode);
				nnode.innerHTML = result;
		}
		else editor._doc.execCommand("inserthtml", false, result);	
	}
}

pEditor.imageModifyCallback = function(editor, result, obj){	
	if(result) var niProps = result
	else return;

	if (niProps) {
		obj.src = (niProps.src)?niProps.src:'';
		if (niProps.alt) obj.alt = niProps.alt;
		else obj.removeAttribute("alt");
						
		obj.align		= (niProps.align)?niProps.align:'';
		obj.width		= (niProps.width)?niProps.width:'';
		obj.height	= (niProps.height)?niProps.height:'';
		obj.style.width		= (niProps.width)?niProps.width:'';
		obj.style.height	= (niProps.height)?niProps.height:'';
		obj.style.border = (niProps.sborder)?niProps.sborder:'';
				
		if (niProps.hspace) obj.hspace = niProps.hspace;
		else obj.removeAttribute("hspace");
		if (niProps.vspace) obj.vspace = niProps.vspace;
		else obj.removeAttribute("vspace");				
	}   
	
}

pEditor.tableCallback = function(editor, result, obj){
	if(result) var ntProps = result
	else return;
		
	if (ntProps) {
		if(!obj) var obj = document.createElement('TABLE');
     
		if(ntProps.width) obj.width		= ntProps.width;
		if(ntProps.height) obj.height	= ntProps.height;
		if(ntProps.cellPadding) obj.cellPadding = ntProps.cellPadding;
		if(ntProps.cellSpacing) obj.cellSpacing = ntProps.cellSpacing;		
		if(ntProps.bgColor) obj.bgColor	= ntProps.bgColor;
		if(ntProps.border) obj.border	= ntProps.border;		
		if(ntProps.borderColor) obj.borderColor	= ntProps.borderColor;
        if(ntProps.align) obj.align	= ntProps.align;
		if(ntProps.background) obj.background =ntProps.background;
            
		// create rows
		for (i=0;i<parseInt(ntProps.rows);i++) {
			var newrow = document.createElement('TR');					
			for (j=0; j<parseInt(ntProps.cols); j++) {
				var newcell = document.createElement('TD');
				newrow.appendChild(newcell);
			}
			obj.appendChild(newrow);
		}
        
		if(editor.selc) {
			editor.rng.pasteHTML(obj.outerHTML);
		} else {
			HTMLElement.prototype.__defineGetter__('outerHTML', function () { 
				var _emptyTags = {'IMG':true,  'BR':true,  'INPUT':true,  'META':true, 'LINK':true, 'PARAM':true, 'HR':true}; 
				var attrs = this.attributes; 
				var str = '<' + this.tagName; 
				for (var i = 0; i < attrs.length; i++) str += ' ' + attrs[i].name + '="' + attrs[i].value + '"'; 
				if (_emptyTags[this.tagName]) return str + '>'; 
				return str + '>' + this.innerHTML + '</' + this.tagName + '>'; 
			}); 			
			
			if(document.getSelection){
				sel = editor._doc.getSelection();
				
				var range = editor._doc.getSelection().getRangeAt(0);
				var nnode = editor._doc.createElement("span");
					range.surroundContents(nnode);
					nnode.innerHTML = obj.outerHTML;
			}
			else editor._doc.execCommand("inserthtml", false, obj.outerHTML);				
		}
	} 
}

pEditor.prototype.scaleMode = function(mode){
	if(mode=='up') {
		this._div.style.width = (parseInt(this.width)+198)+'px';
		this._iframe.style.width = (parseInt(this.width)+198)+'px';
		this._divTool.style.width = (parseInt(this.width)+198)+'px';
		this._divMode.style.width = (parseInt(this.width)+201)+'px';
	}
	else {
		this._div.style.width = (parseInt(this.width))+'px';
		this._iframe.style.width = (parseInt(this.width))+'px';
		this._divTool.style.width = (parseInt(this.width))+'px';
		this._divMode.style.width = (parseInt(this.width)+3)+'px';
	}
}
