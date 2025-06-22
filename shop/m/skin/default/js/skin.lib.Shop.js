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

function checkCookieVar(){

	setCookie("mallUrl",window.location.href,3600,"/",domain=window.document.domain||window.location.hostname);
	
	if(getCookie("mallOrder")) {
		getLists.cgOrder(getCookie("mallOrder"));
	}

	if(getCookie("mallBest")) {
		getLists.cgBest(getCookie("mallBest"));
	}

	if(getCookie("mallType")) {
		getLists.cgType(getCookie("mallType"));
	}	
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


function setCookie( name, value, expiredays ){ 
	var todayDate = new Date(); 
	todayDate.setDate(todayDate.getDate() + expiredays); 
	document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
} 

function delCookie(name) {
	setCookie(name,'',3600,"/",domain=window.document.domain||window.location.hostname);	
	setcookie(name,'',-999,"/",domain=window.document.domain||window.location.hostname);
}

/***************** 숫자 체크 스크립트 *********************/
function isNumber(name,idname) { 
    var  ckStr= eval("f." + name); 

    for(var i = 0; i < ckStr.value.length; i++) { 
        var chr = ckStr.value.substr(i,1); 
        if(chr < '0' || chr > '9') {    			 
            if(idname)  document.getElementById(idname).innerHTML= '숫자만 입력 가능합니다.';
			else alert('숫자만 입력 가능합니다.');
            eval("f."+name+".value = ''")
			eval("f."+name+".focus()")
			return 1; 
        } 
     } 
} 

/***************** 입력값 체크 스크립트 *********************/
function ck_value(name,msg) {	
    var ch=0;
	eval("if(!f."+name+".value) { ch = 1 }")
    if(ch==1) { 
	    alert(msg);
        eval("f."+name+".focus()")
		return 1;
    } 
}  

if(typeof(pops)=='undefined') {
	BaramangSwipe.template.mainBanner = function(obj, pagination, options) {
		var mainBanner = $(obj).baramangSwipe(options && options.childTag || "figure", $.extend({	
			elementCountPerGroup: 1,
			isLoop: true,
			isAutoScroll: true,
			autoScrollDirection: "right",
			autoScrollTime: 5000
		}, options));

		mainBanner.refreshPagination = function() {
			$(pagination).empty();
			
			for(var i = 0; i < mainBanner.maxElementGroup; i++) {
				$(pagination).append("<span>" + (i + 1) + "</span>\n");
			}
			
			mainBanner.success();
		};
		
		mainBanner.success = function() {
			$(pagination).children().removeClass("active").eq(mainBanner.currentPageNo).addClass("active");
		};	
		return mainBanner;
	};

	BaramangSwipe.template.otherImage = function(obj, pagination, options) {
		var mainBanner = $(obj).baramangSwipe(options && options.childTag || "figure", $.extend({	
			elementCountPerGroup: 1,
			isLoop: true,
			isAutoScroll: false,
		}, options));

		mainBanner.refreshPagination = function() {				
			mainBanner.success();
		};
		
		mainBanner.success = function() {
			$("#currentImg")[0].innerHTML = parseInt(mainBanner.currentPageNo)+1;
		};	
		return mainBanner;
	};



	BaramangSwipe.template.mainGoodsList = function(obj, pagination, options) {
		var goodsList = $(obj).baramangSwipe(options && options.childTag || "li", $.extend({
			elementWidth: 100,
			isLoop: true,
			isAutoScroll: true,
			autoScrollDirection: "right",
			autoScrollTime: 5000
		}, options));
		
		goodsList.refreshPagination = function() {
			$(pagination).empty();
			
			for(var i = 0; i < goodsList.maxElementGroup; i++) {
				$(pagination).append("<span>" + (i + 1) + "</span>\n");
			}
			
			goodsList.success();
		};
		
		goodsList.success = function() {
			$(pagination).children().removeClass("active").eq(goodsList.currentPageNo).addClass("active");
		};
		
		goodsList.onresize = function() {
			if(goodsList.currentWindowSize != $(window).width()) {
				goodsList.currentWindowSize = $(window).width();
				goodsList.reload(function() {
					goodsList.refreshPagination();
				});
			}
		};
		
		return goodsList;
	};
}

function gotoTop(){
	scrollTo(0, ($("#header").offset() || {}).top || 0);
}

function ckSearchInput(form){
	if(event.keyCode==13) {
		return ckSearch();
	}

	if(form.search.value) $("#reset").show();
	else $("#reset").hide();
}

function searchLayer(obj) {	
	$("#searchForm").toggle();

	if($(obj).hasClass("active")) {
		$(obj).removeClass("active");
	}
	else {
		$(obj).addClass('active')
	}
}

function searchCheck(form){
	form.search.value = jQuery.trim(form.search.value);
	if(form.search.value.length<2) {
		alert('검색어를 2자리이상 입력 하시기 바랍니다.');
		form.search.focus();
		return false;
	}	

	if(!form.search.value){
        alert('검색어를 입력 하시기 바랍니다.');
		form.search.focus();
		return false;
	}	
}

function ckSearch(){
	form = document.TsearchForm;
	return searchCheck(form);
}

function reSearch(channel,pname){
	form = document.TsearchForm;
	reForm = document.reSearchForm;
	obj = reForm.detail;
	
	if(obj.checked){
		form.detail.value = '';
		form.channel.value = 'search';
		$('#searchI').attr('placeholder','검색어 입력');		
	}
	else {
		$('#searchForm').show();
		$('#searchI').attr('placeholder',pname);		
		form.detail.value = obj.value;
		if(channel) form.channel.value = channel;		
	}
}

function ckoSearch(f){
	if(!f.name.value) {
		alert('주문자명을 입력 하세요!');
		f.name.focus();
		return false;
	}
	
	if(!f.order_num.value) {
		alert('주문번호를 입력 하세요!');
		f.order_num.focus();
		return false;
	}
}

function getList(obj,limit,type,order,total,Pstart,qstr,url){
    this.aObj = new AjaxObject; 
	this.obj = document.getElementById(obj);
	this.limit = limit;
	this.type = type;
	this.order = order;
	this.total = total;
	this.Pstart = Pstart;
	this.qstr = qstr;
	this.reFresh = 0;
	this.page = 1;
	if(url) this.url = url;
	else this.url = 'php/getList.php';

	this.display = function(re) {
		if(this.total==0) return false;
		if(re==1) this.reFresh = 1;
		var loading = document.getElementById("listLoading");
		var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);
		loading.style.display = 'block';
		loading.style.top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) +  (h-(loading.height||parseInt(loading.style.height)||loading.offsetHeight))/2) + 'px';
		loading.style.left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft)  + (w-(loading.width||parseInt(loading.style.width)||loading.offsetWidth))/2) + 'px';
		this.aObj.getHttpRequest(this.url+"?limit="+this.limit+"&order="+this.order+"&Pstart="+this.Pstart+this.qstr, "getLists.dispList","data"); 
		return false;		
	}

	this.dispList = function (data) {
		document.getElementById("listLoading").style.display = 'none';

		if(typeof(data['error'])!='undefined') {
			alert("리스트를 가져오는 중 에러가 발생했습니다.");
			return;
		}
				
		if(this.reFresh==1) {
			for (i=0,cnt=(this.obj.childNodes.length);i<cnt;i++) {				
				this.obj.removeChild(this.obj.firstChild);
			}							
			this.reFresh = 0;			
		}

		for(i=0;i<this.limit;i++) {
			if(typeof(data['item'][i])!='undefined') {
				rtnCell(this.obj,data['item'][i]['uid'],data['item'][i]['image'],data['item'][i]['link'],data['item'][i]['name'],data['item'][i]['icon'],data['item'][i]['price'],data['item'][i]['cprice'],data['item'][i]['rese'],data['item'][i]['ccnt'],data['item'][i]['sout'],data['item'][i]['cp_price'],data['item'][i]['cate'],data['item'][i]['event'],data['item'][i]['oprice']);
			}
		}		
		
		this.cgType(this.type);
		listResize(this); 
		
		if(Math.ceil(this.total/this.limit) == this.page) {
			document.getElementById("listGoodsMore").innerHTML = "&nbsp;";
		}
	}

	this.cgType = function (type,clEvt) {
		
		this.obj.className = type+"List";
		
		obj2 = document.getElementById("btnList");
		obj3 = document.getElementById("btnImg");
			
		if(type=="list") {
			obj2.className = obj2.className.replace("On","")
			obj2.className = obj2.className+'On';
			obj3.className = obj3.className.replace("On","");
			if(this.url.indexOf("getToday.php")!=-1) $(".todayDel").show();
			if(this.url.indexOf("getWish.php")!=-1) $(".wishDel").show();
		}
		else {
			obj3.className = obj3.className.replace("On","");
			obj3.className = obj3.className+'On';
			obj2.className = obj2.className.replace("On","");
			if(this.url.indexOf("getToday.php")!=-1) $(".todayDel").hide();
			if(this.url.indexOf("getWish.php")!=-1) $(".wishDel").hide();
		}
		
		this.type = type;

		listResize(this); 

		if(clEvt==1) setCookie("mallType",type,3600,"/",domain=window.document.domain||window.location.hostname);
		return false;
	}

	this.cgOrder = function (vls,clEvt) {
		this.order = vls;

		if(clEvt==1) setCookie("mallOrder",vls,3600,"/",domain=window.document.domain||window.location.hostname);
		return this.display(1);
	}

	this.cgBest = function (vls,clEvt) {
		
		this.order = vls;
		
		if(clEvt==1) setCookie("mallBest",vls,3600,"/",domain=window.document.domain||window.location.hostname);

		return this.display();
	}

	this.goodsMore = function () {
		this.Pstart = parseInt(this.Pstart) + parseInt(this.limit);
		this.page++;
		if($("#currentPage")) $("#currentPage")[0].innerHTML = this.page;
		this.display();
	}
}


function delGoods(cate,number) {
	aObj = new AjaxObject;  
	aObj.getHttpRequest("php/todayDel.php?cate="+cate+"&number="+number, "delGoodsOk","data"); 
}

function delGoodsOk(data) {
	 if(data['item']=='true' && data['uid']) {	
		obj = document.getElementById('goodsList');
		obj.removeChild(document.getElementById("tGoods_"+data['uid']));						
	}
}

function delGoods2(number) {
	aObj = new AjaxObject;  
	aObj.getHttpRequest("php/wishDel.php?number="+number, "delGoodsOk2","data"); 
}

function delGoodsOk2(data) {
	 if(data['item']=='true' && data['uid']) {	
		obj = document.getElementById('goodsList');
		obj.removeChild(document.getElementById("tGoods_"+data['uid']));		
		if($("#totalCnt")) {
			$("#totalCnt")[0].innerHTML = $("#totalCnt")[0].innerHTML - 1;
		}
	}	
}

function listResize(objs){
	if(!objs) return;
	
	if(objs.type=='img') {			
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);		
		if(w>=600) objs.obj.className = "imgList2";
		else objs.obj.className = "imgList";
	}
}

function getMulti(obj,limit,total,Pstart,qstr,url,callBack){
    this.aObj	= new AjaxObject; 
	this.obj	= document.getElementById(obj);
	this.limit	= limit;
	this.total	= total;
	this.Pstart = Pstart;
	this.qstr	= qstr;
	this.order	= "uid";
	this.reFresh = 0;
	this.str1 = this.str2 = '';
	this.page = 1;
	if(!url) this.url = "php/getReview.php";
	else this.url = url;
	this.callBack = callBack;

	this.display = function() {
		var loading = document.getElementById("listLoading");
		var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);
		loading.style.display = 'block';
		loading.style.top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) +  (h-(loading.height||parseInt(loading.style.height)||loading.offsetHeight))/2) + 'px';
		loading.style.left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft)  + (w-(loading.width||parseInt(loading.style.width)||loading.offsetWidth))/2) + 'px';
		this.aObj.getHttpRequest(this.url+"?limit="+this.limit+"&order="+this.order+"&total="+this.total+"&Pstart="+this.Pstart+this.qstr+this.str1+this.str2, "getMultis.dispList","data"); 
		return false;
	}

	this.dispList = function (data) {
		document.getElementById("listLoading").style.display = 'none';
		
		if(typeof(data['error'])!='undefined') {
			alert("리스트를 가져오는 중 에러가 발생했습니다.");
			return;
		}
			
		if(this.reFresh==1) {
			for (i=0,cnt=(this.obj.childNodes.length);i<cnt;i++) {				
				this.obj.removeChild(this.obj.firstChild);
			}							
			this.reFresh = 0;	
			
			if(typeof(data['total'])!='undefined') {
				this.total = data['total'];
				if($("#totalCnt")) $("#totalCnt")[0].innerHTML = data['total'];
				if(data['total']<4) $("#contentMore").hide();
				else $("#contentMore").show();
				if(data['total']==0) { $("#noContent").show(); return }
				else $("#noContent").hide();	
				if(Math.ceil(this.total/this.limit) > this.page) {					
					document.getElementById("listGoodsMore").innerHTML = '목록 더보기 (<em id="currentPage">1</em>/<em>'+(Math.ceil(this.total/this.limit))+'</em>)';
				}

			}
		}	
		
		for(i=0;i<this.limit;i++) {
			if(typeof(data['item'][i])!='undefined') {
				if(this.url.indexOf("Review")!=-1) eval(this.callBack+"(this.obj,data['item'][i]['num'],data['item'][i]['point'],data['item'][i]['title'],data['item'][i]['name'],data['item'][i]['buy'],data['item'][i]['date'],data['item'][i]['content'],data['item'][i]['mod'],data['item'][i]['uid'],data['item'][i]['name2'],data['item'][i]['link'],data['item'][i]['image'])" );
				else if(this.url.indexOf("Reserve")!=-1) eval(this.callBack+"(this.obj,data['item'][i]['name'],data['item'][i]['reserve'],data['item'][i]['status'],data['item'][i]['date'])" );
				else if(this.url.indexOf("Coupon")!=-1) eval(this.callBack+"(this.obj,data['item'][i]['name'],data['item'][i]['sale'],data['item'][i]['lmt'],data['item'][i]['status'],data['item'][i]['date'])" );
				else if(this.url.indexOf("Order")!=-1) eval(this.callBack+"(this.obj,data['item'][i]['orderNum'],data['item'][i]['name'],data['item'][i]['price'],data['item'][i]['type'],data['item'][i]['status'],data['item'][i]['date'],data['item'][i]['clink'],data['item'][i]['cnum'])" );
				else eval(this.callBack+"(this.obj,data['item'][i]['num'],data['item'][i]['title'],data['item'][i]['name'],data['item'][i]['ans'],data['item'][i]['date'],data['item'][i]['content'],data['item'][i]['answer'],data['item'][i]['mod'],data['item'][i]['uid'],data['item'][i]['secret'],data['item'][i]['name2'],data['item'][i]['link'],data['item'][i]['image'])" );
			}
		}	
		
		if(Math.ceil(this.total/this.limit) == this.page) {
			document.getElementById("listGoodsMore").innerHTML = "&nbsp;";
		}

	}	

	this.cgLimit = function (vls) { //ajaxPaging()와 연동
		if(!vls) vls = 10;
		this.limit = vls;	
		aPage.page_record_num = vls;
		aPage.makePage();
		this.display();
		aPage.printPage(' ');	
	}

	this.cgOrder = function (vls) {
		this.order = vls;		
		this.reFresh = 1;
		return this.display();
	}

	this.cgStr1 = function (vls, vls2) {
		if(vls2) this.str1 = '&'+vls+'='+vls2;		
		else this.str1 = '';
		this.reFresh = 1;
		this.page = 1;
		return this.display();
	}

	this.cgStr2 = function (vls, vls2) {
		if(vls2) this.str2 = '&'+vls+'='+vls2;		
		else this.str2 = '';
		this.reFresh = 1;
		this.page = 1;
		return this.display();
	}

	this.listMore = function () {
		this.Pstart = parseInt(this.Pstart) + parseInt(this.limit);
		this.page++;
		if($("#currentPage")) $("#currentPage")[0].innerHTML = this.page;
		this.display();
	}
}

function getBoard(obj,code,limit,Pstart,qstr,url,callBack){
    this.aObj	= new AjaxObject; 
	this.obj	= document.getElementById(obj);
	this.code	= code;
	this.limit	= limit;
	this.Pstart = Pstart;
	this.qstr	= qstr;
	this.reFresh = 0;
	this.page = 1;
	if(!url) this.url = "php/getBoard.php";
	else this.url = url;
	this.callBack = callBack;

	this.display = function() {
		if(this.total==0) return false;
		var loading = document.getElementById("listLoading");
		var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);
		loading.style.display = 'block';
		loading.style.top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) +  (h-(loading.height||parseInt(loading.style.height)||loading.offsetHeight))/2) + 'px';
		loading.style.left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft)  + (w-(loading.width||parseInt(loading.style.width)||loading.offsetWidth))/2) + 'px';
		this.aObj.getHttpRequest(this.url+"?code="+this.code+"&limit="+this.limit+"&Pstart="+this.Pstart+this.qstr, "getBoards.dispList","data"); 
		return false;
	}

	this.dispList = function (data) {
		document.getElementById("listLoading").style.display = 'none';
		
		if(typeof(data['error'])!='undefined') {
			alert("리스트를 가져오는 중 에러가 발생했습니다.");
			return;
		}
			
		if(this.reFresh==1) {
			for (i=0,cnt=(this.obj.childNodes.length);i<cnt;i++) {				
				this.obj.removeChild(this.obj.firstChild);
			}							
			this.reFresh = 0;			
		}	
	
		for(i=0;i<this.limit;i++) {
			if(typeof(data['item'][i])!='undefined') {
				eval(this.callBack+"(this.obj,data['item'][i]['num'],data['item'][i]['name'],data['item'][i]['subject'],data['item'][i]['email'],data['item'][i]['hit'],data['item'][i]['reco'],data['item'][i]['comment'],data['item'][i]['date'],data['item'][i]['memocnt'],data['item'][i]['notice'],data['item'][i]['reply'],data['item'][i]['lock'],data['item'][i]['news'],data['item'][i]['file'],data['item'][i]['f_name'],data['item'][i]['img'],data['item'][i]['no'])");
			}
		}	
		
		if(Math.ceil(this.total/this.limit) == this.page) {
			document.getElementById("listGoodsMore").innerHTML = "&nbsp;";
		}
	}	

	this.cgLimit = function (vls) { //ajaxPaging()와 연동
		if(!vls) vls = 10;
		this.limit = vls;	
		aPage.page_record_num = vls;
		aPage.makePage();
		this.display();
		aPage.printPage(' ');	
	}

	this.listMore = function () {
		if(this.page==total_page) return;
		this.Pstart = parseInt(this.Pstart) + parseInt(this.limit);
		this.page++;
		if($("#currentPage")) $("#currentPage")[0].innerHTML = this.page;
		this.display();
	}
}


function imgSizeConv(obj,Limit,path){ 
	try {
		if(!Limit || !obj) return;
		 
		var imgs = obj.find('img');
		imgs.each(function (i) {
			$(this).attr('width', Limit);
		 });

		return;
	} 
	catch(e) { return }
}

function viewReview(num) {
	obj1 = $("#reviewContent_"+reviewViewNum);
	obj2 = $("#reviewContent_"+num);

	if(reviewViewNum && obj1) {		
		obj1.hide();	
		if(reviewViewNum==num) {
			reviewViewNum = '';
			return;
		}
		reviewViewNum = '';			
	}
	
	obj2.show();
	reviewViewNum = num;	
}

function viewQna(num,uid) {
	if(uid==0) return false;

	obj1 = $("#qnaContent_"+qnaViewNum);
	obj2 = $("#qnaContent_"+num);

	if(qnaViewNum && obj1) {		
		obj1.hide();	
		if(qnaViewNum==num) {
			qnaViewNum = '';
			return;
		}
		qnaViewNum = '';			
	}
	
	obj2.show();
	qnaViewNum = num;	
}

function detailView(obj, obj2){	
	if(obj2.css("display")=='none') {
		obj.css("background","url('"+shop_skin+"img/common/icon_up_down.png') no-repeat 98% -15px");
		obj2.show();
	}
	else {
		obj.css("background","url('"+shop_skin+"img/common/icon_up_down.png') no-repeat 98% 15px");
		obj2.hide();

	}
}

function ckLoginForm(f){
	if(!f.id.value) {
		alert('아이디를 입력 하세요!');
		f.id.focus();
		return false;
	}
	
	if(!f.passwd.value) {
		alert('비밀번호를 입력 하세요!');
		f.passwd.focus();
		return false;
	}
}


function ckOption(obj) {
	var f = document.goodsForm;

	if(obj.options[obj.selectedIndex].text.indexOf("품절") != -1) {
		obj.selectedIndex = 0;
	}

	if($("#totalPrice")) {
		var op_price = 0;
		if(typeof(f.p_op_cnt)!='undefined') {
			if(f.p_op_cnt.value>0) {
				for(i=1;i<=f.p_op_cnt.value;i++) {
					if(typeof(eval("f.p_op"+i))!='undefined') {
						tmp = eval("f.p_op"+i+".options[f.p_op"+i+".selectedIndex].text");						
						tmp = tmp.split(' (+');
						if(tmp[1]) {
							tmp[1] = str_replace(",","",tmp[1]);
							tmp[1] = str_replace("원","",tmp[1]);
							tmp[1] = str_replace(")","",tmp[1]);
							if(tmp[1]>0) {
								op_price += eval(tmp[1]);
							}
						}
					}
				}
			}	
		}	
		
		f.p_op.value = op_price;

		if($('#prices')[0]) {
			if(op_price>0) {
				if(f.p_sale.value>0) op_price2 = (op_price*100)/(100-eval(f.p_sale.value));
				else op_price2 = op_price;
				total = (eval(f.p_price.value) + eval(op_price2));								
			}
			else total = eval(f.p_price.value);	
			f.p_total.value = total + eval(f.p_carr.value);
			f.p_total2.value = total + eval(f.p_carr.value);
			$('#prices')[0].innerHTML = number_format(total);
			if($('#prices1')) $('#prices1')[0].innerHTML = number_format(total);
		}

		if($('#prices2')[0]) {
			if(op_price>0) total = (eval(f.p_price2.value) + eval(op_price));
			else total = eval(f.p_price2.value);	
			f.p_total.value = total + eval(f.p_carr.value);;
			f.p_total2.value = total + eval(f.p_carr.value);;
			$('#prices2')[0].innerHTML = number_format(total);
		}

		if($('#prices3')[0]) {
			if(op_price>0) {
				if(f.cp_sale.value>0 && f.cp_sale_type.value=='P') {
					op_price =  Math.round(op_price - ((op_price * eval(f.cp_sale.value))/100)); 
					op_price = Math.round(op_price/10);
					op_price = Math.round(op_price*10);
				}
				
				total = (eval(f.p_price3.value) + eval(op_price));				
			}
			else total = eval(f.p_price3.value);	
			f.p_total.value = total + eval(f.p_carr.value);;
			$('#prices3')[0].innerHTML = number_format(total);				
		}
		ckTotal();
	}
}

function ckTotal(){
	var f = document.goodsForm;

	if($('#totalPrice')) {
		if(f.cp_type.value==0 && f.p_price3.value>0 && f.p_qty.value>1) {						
			total = eval(f.p_total.value) + eval(f.p_total2.value) * (eval(f.p_qty.value)-1);
		}
		else total = eval(f.p_total.value)* eval(f.p_qty.value);

		$('#totalPrice')[0].innerHTML = number_format(total);
	}
}	

function ckGoods(type,ck){	
	f = document.goodsForm;

	if(ck==1) {
		pLightBox.hide();
		f.ckLogin.value = 'Y';
	}

    if(ck_value('p_qty','수량을 입력하세요')==1) return false;
	isNumber('p_qty','');
	
	if(parseInt(f.defQty.value) >0 && parseInt(f.p_qty.value) < parseInt(f.defQty.value)) {
		alert('수량은 최소 '+f.defQty.value+f.goodsUnit.value+'이상 구매하셔야 됩니다.');
		f.p_qty.value = f.defQty.value;
		f.p_qty.focus();	
		ckTotal();
		return false;
	}  
	
	if(parseInt(f.sQty.value)>0 && parseInt(f.p_qty.value) > parseInt(f.sQty.value)) {
        alert('재고수량('+f.sQty.value+')을 초과 했습니다.');
		ckStr.focus();		
		return false;
	}
	
	if(typeof(f.p_op_cnt)!='undefined') {
		if(f.p_op_cnt.value>0) {
			p_op_arr = '';
			for(i=1;i<=f.p_op_cnt.value;i++) {
				if(typeof(eval("f.p_op"+i))!='undefined') {
					tmp = eval("f.p_op_name"+i+".value");
					tmp2 = eval("f.p_op"+i+".value");
					if(ck_value('p_op'+i, tmp + '를(을) 선택하시기 바랍니다.')==1) return false;					
					if(!p_op_arr) p_op_arr = "&p_option="+tmp2;
					else p_op_arr = p_op_arr + "|"+tmp2;
				}
			}
			f.p_option.value = p_op_arr;
			f.p_option.value = str_replace("&p_option=","",f.p_option.value);
		}
		else p_op_arr = '';
	}
	else p_op_arr = '';

	f.type.value = type;
	if(type=="order") {
		if(f.ckCart.value=='Y') {
			messageBox.show('장바구니에 상품이 존재 합니다. <br/>장바구니에 담긴 상품과 같이 구매 하시겠습니까?','280','140','바로구매 확인',rtnConfirmValue,'예,아니요');
			return false;
		}

		f.direct.value = 'Y';
		f.submit();
		return false;
	}
	else {
		aObj = new AjaxObject;             
		aObj.getHttpRequest(paths+"php/cartOk.php?view=Y&p_qty="+f.p_qty.value+"&cate="+f.p_cate.value+'&number='+f.p_number.value+p_op_arr, "cartOk","data"); 
	}
}

function rtnConfirmValue(){	
	f = document.goodsForm;
	
	if(messageBox.getValue()=='아니요') {
		f.direct.value = 'Y';
	}

	f.submit();
}

function rtnConfirm2Value(){	
	if(messageBox.getValue()=='확인') {
		if(paths=='../') parent.location.href = "../index.php?channel=cart";
		else window.location.href = "index.php?channel=cart";
	}	
}

function cartOk(data){	
	if(data['item']=='false') {
		alert('장바구니 담기에 실패 했습니다.');
	} 
	else if(data['item']=='true') {						
		if(data['view']=='Y') {					
			if(!data['name']) messageBox.show('장바구니에 동일한 상품이 담겨있어 장바구니 수량을 변경 하였습니다. <br/> 장바구니로 이동 하시겠습니까?','280','150','장바구니 확인',rtnConfirm2Value,'확인,취소');
			else messageBox.show('장바구니에 상품이 담겼습니다. <br/> 장바구니로 이동 하시겠습니까?','280','140','장바구니 확인',rtnConfirm2Value,'확인,취소');
			return;
		}
	} 
	else {
		alert(data['item']);
	}
}

function changeOption(){	
	f = document.goodsForm;
    
	if(ck_value('p_qty','수량을 입력하세요')==1) return false;
	isNumber('p_qty','');

	if(parseInt(f.defQty.value) >0 && parseInt(f.p_qty.value) < parseInt(f.defQty.value)) {
		alert('수량은 최소 '+f.defQty.value+f.goodsUnit.value+'이상 구매하셔야 됩니다.');
		f.p_qty.value = f.defQty.value;
		f.p_qty.focus();	
		ckTotal();
		return false;
	}  

	if(typeof(f.p_op_cnt)!='undefined') {
		if(f.p_op_cnt.value>0) {
			p_op_arr = '';
			for(i=1;i<=f.p_op_cnt.value;i++) {
				if(typeof(eval("f.p_op"+i))!='undefined') {
					tmp = eval("f.p_op_name"+i+".value");
					tmp2 = eval("f.p_op"+i+".value");
					if(ck_value('p_op'+i, tmp + '를(을) 선택하시기 바랍니다.')==1) return false;					
					if(!p_op_arr) p_op_arr = "&p_option="+tmp2;
					else p_op_arr = p_op_arr + "|"+tmp2;
				}
			}
			f.p_option.value = p_op_arr;
			f.p_option.value = str_replace("&p_option=","",f.p_option.value);
		}
		else p_op_arr = '';
	}
	else p_op_arr = '';

	aObj = new AjaxObject;             
	aObj.getHttpRequest("php/cartOk.php?mode=option&uid="+f.uid.value+"&p_qty="+f.p_qty.value+p_op_arr, "changeOptionOk1","data"); 	
}

function changeOptionOk1(data){	
	if(data['item']=='true') {	
		window.location.href = "index.php?channel=cart";
	} 
	else {
		alert(data['item']);
	}
}

function ordPost(num) {
	var f = document.cartForm;
	
	for (i=cnt=0;i<f.elements.length;i++) {
		if(f.elements[i].name == 'item[]') {
			if(f.elements[i].value==num) f.elements[i].checked = true;
			else f.elements[i].checked = false;
		}
	}
	cartSecPost('corder');
}

function delPost(num){
	var f = document.cartForm;
    f.type.value = 'del';
    f.p_uid.value = num;	  
	f.submit();
}

function cartSecPost(type){
	var f = document.cartForm;
	var ckUid = new Array();

	for (i=cnt=0;i<f.elements.length;i++) {
		if(f.elements[i].name == 'item[]' && f.elements[i].checked == true) {
			ckUid[cnt] = f.elements[i].value; 
			cnt++;			
		}
	}
	
	if ( cnt <= 0) {
		alert("선택한 항목이 없습니다.");
		return;
	} 

	f.type.value = type;    
	f.submit();
}

function orderPost(){

	var f = document.cartForm;
	
	if(f.ckLogin.value=="N") {
		window.location.href = "?channel=login&type=cart";
	}
	else window.location.href = "?channel=order_form";
}

function ckAll(){
	var f = document.cartForm;
	
	for (i=cnt=0;i<f.elements.length;i++) {
		if(f.elements[i].name == 'item[]') {
			if(f.elements[i].checked==true) f.elements[i].checked = false;
			else f.elements[i].checked = true;
		}
	}
}

function zipcode() {
	pLightBox.show('php/pzipcode.php','iframe','100%','100','■ 우편번호 찾기','20');
}

function messageInput(vls) {
	var f = document.orderForm;

	f.message.value = vls;
}

function cashType(vls) {
	var f = document.orderForm;

	f.cash_type.value = vls;

	if(vls=='B') $(".bankSelect").show();
	else  $(".bankSelect").hide();

	var arr = new Array("B","C","R","V","H");
	for(i=0;i<arr.length;i++) {
		if($("#cashType"+arr[i])) $("#cashType"+arr[i]).css("background-color","#fff");
	}
	$("#cashType"+vls).css("background-color","#ccc");
}

function ckOrder() {      
   	
	if(f.ckBtn.value==1) {
		alert("처리 중 입니다. 버튼은 한번만 누르시기 바랍니다.");
		return false;
	}

	if(ck_value('name1','주문고객 성명을 입력하세요!')==1) return false;
	if((!f.tel12.value || !f.tel13.value) && (!f.phone12.value || !f.phone13.value)) {
		alert('주문고객 연락처 하나는 입력 하셔야 됩니다');
		f.phone12.focus();
		return false;	
	}
	if(ck_value('name2','수취인 성명을 입력하세요!')==1) return false;
	if((!f.tel22.value || !f.tel23.value) && (!f.phone22.value || !f.phone23.value)) {
		alert('수취인 연락처 하나는 입력 하셔야 됩니다');
		f.phone22.focus();
		return false;	
	}
	if(ck_value('zip1','배송지 우편번호를 입력하세요!')==1) return false;
	if(ck_value('zip2','배송지 우편번호를 입력하세요!')==1) return false;
	if(ck_value('addr','배송지 주소를 입력하세요!')==1) return false;

	if(!f.cash_type.value) {
		alert("결제 수단을 선택 하시기 바랍니다");
		return false;
	}

	if(f.cash_type.value=='B') {
		 if(ck_value('bank_name','입금은행을 선택하세요!')==1) return false;
		 if(ck_value('pay_name','입금자 성명을 입력하세요!')==1) return false;	
	} 

	f.ckBtn.value = 1;
	f.submit();
}

/************* SHARE ***************/
function sendSNS(media,goods,url,tag,uid,width,height) {
	switch(media) {
		case "tw":
			sendUrl = "http://twitter.com/home?status="+goods+url;				
		break;
		case "me":
			sendUrl = "http://me2day.net/posts/new?new_post[body]=\""+goods+"\":"+url+"&new_post[tags]="+tag;
		break;		
		case "fa":
			sendUrl = "http://www.facebook.com/sharer.php?u="+url+"&t="+goods;
		break;
		case "yz":
			sendUrl = "http://yozm.daum.net/api/popup/prePost?sourceid=41&link="+url+"&prefix="+goods;
		break;
		case "cp":
			if(!width) width = 550;
			if(!height) height = 420;
			pLightBox.show('php/pgoods_copy.php?uid='+uid,'iframe',width,height,'■ 상품 퍼가기','20');
			return;
		break;	
		case "ml":
			if(!width) width = 530;
			if(!height) height = 540;
			pLightBox.show('php/pgoods_copy.php?uid='+uid+'&type=mail','iframe',width,height,'■ 메일 보내기','20');
			return;
		break;	
	}

	window.open(sendUrl,"sendSNSWin","");
}

function searchCarr(link,num) {
	if(!link || !num) {
		alert("배송정보가 입력되지 않았거나 잘못 되었습니다. 고객센테에 문의 하시기 바랍니다.");
		return false;
	}
	window.open(link+num,"");
}

var tmpNum = '';
function ckLogin(num){
	tmpNum = num;
	messageBox.show('로그인 하셔야만 이용 하실 수 있습니다. <br />로그인 하시겠습니까?','280','140','로그인 확인',rtnConfirm3Value,'확인,취소');
}

function rtnConfirm3Value(){	
	if(messageBox.getValue()=='확인') {
		window.location.href = '?channel=login&type=view&num='+tmpNum;
	}	
}

function wishAdd(cate,num,ckLog) {	
	if(ckLog=='N') {		
		ckLogin(num);
		return false;
	}

	if(cate && num) {		
		$("HFrm").src = "php/wish_ok.php?cate="+cate+"&number="+num;
	}
}

function cuponDown(num,gid,ckLog) {
	if(ckLog=='N') {
		ckLogin(gid);
		return false;
	}

	if(!num) return;
	pLightBox.show('php/pcupon_down.php?num='+num+'&gid='+gid,'iframe','100%','280','■ 할인쿠폰다운로드','20');
}

function couponUse(vls) {
	pLightBox.show('php/pcupon_use.php?direct='+vls,'iframe','100%','420','■ 쿠폰사용하기','20')
}

function ckCupons(num,ckFloat) {    
	if(!num) num = 0;

	f = document.dispForm;
	f.c_total.value = parseInt(f.g_total.value) + num;	
	tmps = f.total.value - f.c_total.value;
	
	if(tmps<0) {
		alert("상품총금액보다 쿠폰할인금액이 커 사용할 수 없습니다.");
		parent.pLightBox.hide();
	}

	document.getElementById('use_money').innerHTML = number_format(f.c_total.value,ckFloat);
	document.getElementById('cash_money').innerHTML = number_format(tmps,ckFloat);
	return;
}

function ckCupons2(num,ckFloat,obj) {    
	if(!num) num = 0;

	f = document.dispForm;
	
	if(obj.checked==false) {
		f.c_total.value = parseInt(f.c_total.value) - num;	
		f.g_total.value = parseInt(f.g_total.value) - num;	
	}
	else {
		f.c_total.value = parseInt(f.c_total.value) + num;	
		f.g_total.value = parseInt(f.g_total.value) + num;	
	}
	tmps = f.total.value - f.c_total.value;

	if(tmps<0) {
		alert("상품총금액보다 쿠폰할인금액이 커 사용할 수 없습니다.");
		parent.pLightBox.hide();
	}
	document.getElementById('use_money').innerHTML = number_format(f.c_total.value,ckFloat);
	document.getElementById('cash_money').innerHTML = number_format(tmps,ckFloat);
	return;
}

function cuponOk(){
	f = document.secForm;
	vls = use_cupon = '';
	
	if(typeof(f.secs)!='undefined') {
		if(typeof(f.secs.length)=='undefined') {
			if(f.secs.checked==true) {
				use_cupon = f.secs.value; 
				vls = 1;
			}
		}
		else {
			for (i=0,cnt=f.secs.length;i<cnt;i++) {
				if(f.secs[i].checked == true) {						
					use_cupon = f.secs[i].value; 
					vls = 1;
					break;
				}
			}
		}
	}

	for (i=0,cnt=f.elements.length;i<cnt;i++) {
		if(f.elements[i].name == 'secsg[]' && f.elements[i].checked == true) {
			if(use_cupon) use_cupon += ','+f.elements[i].value; 
			else use_cupon = f.elements[i].value; 
			vls = 1;
		}
	}
	
	if(!vls) {
		alert('사용하실 쿠폰을 선택 하시기 바랍니다.');
		return false;
	}
	
	f2 = parent.document.orderForm;
	f2.use_cupon.value = document.dispForm.c_total.value;
	f2.cupon.value = use_cupon;

	parent.ckCupon();
	parent.pLightBox.hide();
}

function closeWin(){
	if(typeof(parent.pLightBox)!='undefined') parent.pLightBox.hide();
	else self.close();
}