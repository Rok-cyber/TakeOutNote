function checkCookieVar(){

	setCookie("mallUrl",window.location.href,3600,"/",domain=window.document.domain||window.location.hostname);
	if(getCookie("mallLimit")) {
		var lmt = getCookie("mallLimit");
		if(limitBoxs) limitBoxs.setNumCk(lmt);
		else if(typeof(document.limitForm.limit)!='undefined') document.limitForm.limit.value = lmt;
		getLists.cgLimit(lmt);
	}

	if(getCookie("mallOrder")) {
		getLists.cgOrder(getCookie("mallOrder"));
	}

	if(getCookie("mallBest")) {
		getLists.cgBest(getCookie("mallBest"));
	}

	if(getCookie("mallType")) {
		getLists.cgType(getCookie("mallType"));
	}

	if(getCookie("mallPage")) {		
		aPage.resultPage(getCookie("mallPage"),1);
	}
}

/************* 로그인 체크 ***************/
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

	if(document.getElementById('totals') || document.getElementById('prices')) {
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

		if(document.getElementById('prices')) {
			if(op_price>0) {
				if(f.p_sale.value>0) op_price2 = (op_price*100)/(100-eval(f.p_sale.value));
				else op_price2 = op_price;
				total = (eval(f.p_price.value) + eval(op_price2));								
			}
			else total = eval(f.p_price.value);	
			f.p_total.value = total + eval(f.p_carr.value);
			f.p_total2.value = total + eval(f.p_carr.value);
			document.getElementById('prices').innerHTML = number_format(total)+'원';
		}

		if(document.getElementById('prices2')) {
			if(op_price>0) total = (eval(f.p_price2.value) + eval(op_price));
			else total = eval(f.p_price2.value);	
			f.p_total.value = total + eval(f.p_carr.value);;
			f.p_total2.value = total + eval(f.p_carr.value);;
			document.getElementById('prices2').innerHTML = number_format(total)+'원';
		}

		if(document.getElementById('cp_prices')) {
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
			document.getElementById('cp_prices').innerHTML = number_format(total)+'원';				
		}
		ckTotal();
	}
}

function ckTotal(cnt){
	if(cnt) var f = eval("document.goodsForm"+cnt);
	else {
		cnt = '';
		var f = document.goodsForm;
	}

	if(document.getElementById('totals'+cnt)) {
		if(f.cp_type.value==0 && f.p_price3.value>0 && f.p_qty.value>1) {						
			total = eval(f.p_total.value) + eval(f.p_total2.value) * (eval(f.p_qty.value)-1);
		}
		else total = eval(f.p_total.value)* eval(f.p_qty.value);
		document.getElementById('totals'+cnt).innerHTML = number_format(total);
	}

	if(document.getElementById('reserves'+cnt)) {
		reserve = (eval(f.p_total.value) * eval(f.p_reserve.value)/100);
		document.getElementById('reserves'+cnt).innerHTML = number_format(reserve)+'원';		
	}
}	

function cgQty(type,cnt) {
	if(cnt) f = eval("document.goodsForm"+cnt);
	else f = document.goodsForm;
	if(type=='p') {		
		f.p_qty.value = eval(f.p_qty.value) + 1;
	}
	else if(f.p_qty.value>1) f.p_qty.value = eval(f.p_qty.value) - 1;
	ckTotal(cnt);
}	

function cgQty2(type,cnt) {
	f = document.cartForm;
	var tmps = eval("f.qty"+cnt);
	if(type=='p') {
		tmps.value = eval(tmps.value) + 1;
	}
	else if(tmps.value>1) tmps.value = eval(tmps.value) - 1;	
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

function ckTagForm(){
	f = document.tagForm;
	if(!f.tag.value) {
		alert('등록하실 태그를 입력 하세요!');
		f.tag.focus();
		return false;
	}
	
	aObj = new AjaxObject;             
	aObj.getHttpRequest("php/tagOk.php?uid="+f.uid.value+"&tag="+f.tag.value, "tagAdd","data"); 
}

function tagAdd(data) {
	rtn = data['item'];
	if(rtn=='false') {
		alert('태그 등록에 실패 했습니다. 다시 등록 하시기 바랍니다.');		
	}
	else if(rtn=='dupl') {
		alert('이미 등록된 태그입니다.');		
	}
	else if(rtn=='over') {
		alert('태그 등록 가능수를 초과 했습니다.');		
	}
	else if(rtn=='succ') {
		tag = document.createElement('A');		
		tag.innerHTML = document.tagForm.tag.value;
		tag.href = data['main']+'?channel=search&search='+document.tagForm.tag.value;
		tag.className = data['cname'];
		document.getElementById("tagList").appendChild(tag);	
	}	
	document.tagForm.tag.value = '';
}

function ckGoods(type,ck){	
	if(ck==1) {
		pLightBox.hide();
		f.ckLogin.value = 'Y';
	}

    if(ck_value('p_qty','수량을 입력하세요')==1) return false;
	isNumber('p_qty','');
	
	if(type=='cooper') {
		if(parseInt(f.defQty.value) > 0 && parseInt(f.p_qty.value) > parseInt(f.defQty.value)) {
			alert('수량은 최대 '+f.defQty.value+f.goodsUnit.value+'까지 신청가능 합니다.');
			f.p_qty.value = f.defQty.value;
			f.p_qty.focus();	
			ckTotal();
			return false;
		}	
	}
	else {
		if(parseInt(f.defQty.value) >0 && parseInt(f.p_qty.value) < parseInt(f.defQty.value)) {
			alert('수량은 최소 '+f.defQty.value+f.goodsUnit.value+'이상 구매하셔야 됩니다.');
			f.p_qty.value = f.defQty.value;
			f.p_qty.focus();	
			ckTotal();
			return false;
		}  
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
	if(type=='cooper') {		
		if(f.coop_pay.value=='Y') {
			f.direct.value = 'Y';
			if(f.ckLogin.value=='N') {			
				pLightBox.show(paths+'php/plogin.php?type=cooper','iframe',450,380,'■ 로그인','20');
			}
			else f.submit();
		}
		else {
			if(f.ckLogin.value=='N') {
				pLightBox.show(paths+'php/plogin.php?type=cooper','iframe',450,380,'■ 로그인','20');
			}
			else {
				pLightBox.show(paths+'php/pcooperate_view.php?qty='+f.p_qty.value+'&uid='+f.p_number.value+p_op_arr,'iframe',580,440,'■ 공동구매 신청','20');
			}
			return false;
		}
	} 
	else if(type=="order") {
		if(f.ckCart.value=='Y') {
			messageBox.show('장바구니에 상품이 존재 합니다. <br/>장바구니에 담긴 상품과 같이 구매 하시겠습니까?','280','120','바로구매 확인',rtnConfirmValue,'예,아니요');
			return false;
		}

		if(f.ckLogin.value=='N') {			
			pLightBox.show(paths+'php/plogin.php?type=vorder','iframe',450,380,'■ 로그인','20');
		}
		else f.submit();
		return false;
	}
	else {
		aObj = new AjaxObject;             
		aObj.getHttpRequest(paths+"php/cartOk.php?view=Y&p_qty="+f.p_qty.value+"&cate="+f.p_cate.value+'&number='+f.p_number.value+p_op_arr, "gToCart.cartOk","data"); 
	}
}

function rtnConfirmValue(){	
	if(formNums!=null) {
		f = eval("document.goodsForm"+formNums);
		pt_forms = '&num='+formNums;
	}	
	else {
		f = document.goodsForm;
		pt_forms = "";
	}
	
	if(messageBox.getValue()=='아니요') {
		f.direct.value = 'Y';
	}
	if(f.ckLogin.value=='N') {
		pLightBox.show(paths+'php/plogin.php?type=vorder'+pt_forms,'iframe',450,380,'■ 로그인','20');
	}
	else f.submit();
}

function rtnConfirm2Value(){	
	if(messageBox.getValue()=='확인') {
		if(paths=='../') parent.location.href = "../index.php?channel=cart";
		else window.location.href = "index.php?channel=cart";
	}	
}

var formNums = null;
function ckGoods2(type,num){	
	f = eval("document.goodsForm"+num);
    
	if(ck_value('p_qty','수량을 입력하세요')==1) return false;
	isNumber('p_qty','');
	
	if(parseInt(f.defQty.value) >0 && parseInt(f.p_qty.value) < parseInt(f.defQty.value)) {
        alert('수량은 최소 '+f.defQty.value+f.goodsUnit+'이상 구매하셔야 됩니다.');
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
			formNums = num;
			messageBox.show('장바구니에 상품이 존재 합니다. <br />장바구니에 담긴 상품과 같이 구매 하시겠습니까?','280','120','바로구매 확인',rtnConfirmValue,'예,아니요');
		}
		else if(f.ckLogin.value=='N') pLightBox.show('plogin.php?type=vorder&num='+num,'iframe',450,380,'■ 로그인','20');
		else f.submit();
		return false;
	}
	else {
		aObj = new AjaxObject;             
		aObj.getHttpRequest("cartOk.php?view=Y&p_qty="+f.p_qty.value+"&cate="+f.p_cate.value+'&number='+f.p_number.value+p_op_arr, "gToCart.cartOk","data"); 
	}
}

function wishAdd(cate,num,ckLog) {	
	if(ckLog=='N') {
		ckLogin('view');
		return false;
	}

	if(cate && num) {
		$("HFrm").src = "php/wish_ok.php?cate="+cate+"&number="+num;
	}
}

var tmpType = '';
function ckLogin(type){
	tmpType = type;
	messageBox.show('로그인 하셔야만 이용 하실 수 있습니다. <br />로그인 하시겠습니까?','280','120','로그인 확인',rtnConfirm3Value,'확인,취소');
}

function rtnConfirm3Value(){	
	if(messageBox.getValue()=='확인') {
		pLightBox.show('php/plogin.php?type='+tmpType,'iframe',450,380,'■ 로그인','20');
		tmpType = '';
	}	
}

function copyHtml(name,type){
	ck = 0;
	if(type=='html') {
		if (window.clipboardData) { 
			window.clipboardData.setData('Text', document.getElementById(name).innerHTML); 
			ck = 1;
		}		
	}
	else {
		txt = document.body.createTextRange();
		txt.moveToElementText(document.getElementById(name));
		txt.select();	
		txt.execCommand("copy");
		document.selection.empty();
		ck = 1;
	}	 

	if(ck==1) {
		parent.messageBox.show('상품이 클립보드에 복사되었습니다.!<br />해당 게시판에 붙여넣기(ctrl+v)를 하시면 됩니다.','280','120');
		parent.pLightBox.hide();
	}
}

function copyRss(rss,type){
	try{
		if (window.clipboardData) { 
			if(type=='html') window.clipboardData.setData('Text', document.getElementById(rss).innerHTML); 
			else window.clipboardData.setData('Text', rss); 
			ck = 1;
		}
		if(ck==1) alert("RSS주소가 클립보드에 복사 되었습니다.");
		else alert("해당 브라우저가 클립보드 복사 기능을 허용하지 않습니다. 직접 왼쪽 주소를 복사해서 사용하시기 바랍니다.");
	}  catch (ex) {
		alert("해당 브라우저가 클립보드 복사 기능을 허용하지 않습니다. 직접 왼쪽 주소를 복사해서 사용하시기 바랍니다.");
	}
}

function createRss(url) {
	f = document.dsearchForm;
	var addstr = '';
	f.comp.value = f.comp.value.trim();
	f.search.value = f.search.value.trim();
	if(!f.seccate.value && !f.comp.value && !f.search.value) {
		alert('검색 조건을 선택하거나 입력 하세요.');	
		return false;
	}

	if(f.seccate.value) {
		if(addstr!='') addstr += '&cate='+f.seccate.value;
		else addstr = '?cate='+f.seccate.value;
	}

	if(f.comp.value) {
		if(addstr!='') addstr += '&cate='+f.comp.value;
		else addstr = '?comp='+f.comp.value;
	}

	if(f.search.value) {
		if(addstr!='') addstr += '&cate='+f.search.value;
		else addstr = '?search='+f.search.value;
	}
	
	document.getElementById('rssCopy').style.display = 'block';
	document.getElementById('rssUrl').innerHTML = url+addstr;
}

function ckTagInput(obj) {
	var tagFilter   = "%+&?<>/\\,"; 
	var tagMaxLength= "20";
	var NewTag = "";
	var tag = obj.value;
	
	for(var i=0;i<tag.length;i++)
		if (tagFilter.indexOf(tag.charAt(i)) == -1) NewTag += tag.charAt(i);
	NewTag += tag.charAt(i);
	tag = NewTag;

	if (tag.length>tagMaxLength)
		tag = tag.substring(0, tagMaxLength);	
	obj.value = tag;
}

function ckPasswd(){	
	if(ck_value('orig_passwd','기존 비밀번호를 입력하세요!')==1) return false;
	if(ck_value('passwd','새로운 비밀번호를 입력하세요!')==1) return false;
	if(ck_value('repasswd','비밀번호 확인을 입력하세요!')==1) return false;
	document.joinForm.submit();
	return false;
}

function ckQuit(){	
	if(ck_value('passwd','비밀번호 확인을 입력하세요!')==1) return false;
	if(ck_value('reason','탈퇴사유를 선택 하세요!')==1) return false;
	document.joinForm.submit();
	return false;
}

function zipcode(cnt) {
	if(typeof(zipcodeWidth)=='undefined') zipcodeWidth = 420;
	if(typeof(zipcodeHeight)=='undefined') zipcodeHeight= 220;

	pLightBox.show('php/pzipcode.php?fname=join&ocnt='+cnt,'iframe',zipcodeWidth,zipcodeHeight,'■ 우편번호 찾기','20');
}		

function zipcode2() {
	if(typeof(zipcodeWidth)=='undefined') zipcodeWidth = 420;
	if(typeof(zipcodeHeight)=='undefined') zipcodeHeight= 220;

	pLightBox.show('php/pzipcode.php','iframe',zipcodeWidth,zipcodeHeight,'■ 우편번호 찾기','20');
}

function searchCarr(link,num) {
	if(!link || !num) {
		alert("배송정보가 입력되지 않았거나 잘못 되었습니다. 고객센테에 문의 하시기 바랍니다.");
		return false;
	}
	window.open(link+num,"");
}

function openView(url){
	if(!url) return;
	window.open(url,"openView");
}

function openQview(num) {
	pLightBox.show('php/pquick_view.php?uid='+num,'iframe','760','460','■ Quick View','20');	
}

function openBimg(num) {
	pLightBox.show('php/pbig_image.php?uid='+num,'iframe','730','580','■ 큰이미지 보기','20');	
}

function secCompare(){
	var form = document.listForm;
	var vls = "";
			
	for (i=ck=0,cnt=form.elements.length;i<cnt;i++) {
		if(form.elements[i].name == 'compare[]' && form.elements[i].checked == true) {			
			vls += form.elements[i].value+"|";
			ck++;
		}
	}
	if(ck==0) {
		alert("체크된 상품이 없습니다.");
		return false;
	} 
	if(ck==1) {
		alert("상품비교는 2~3개를 선택 하셔야 됩니다.");
		return false;
	}
	if(ck>3) {
		alert("상품비교는 3개 까지 가능 합니다.");
		return false;
	}
	pLightBox.show('php/pcompare.php?compare='+vls,'iframe','840','540','■ 상품비교하기','20');	
	
}

function searchCheck(form){

	form.search.value = form.search.value.trim();
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

function ckSearch2(){
	form = document.searchForm2;
	return searchCheck(form);
}

function ckdSearch(){
	form = document.dsearchForm;
	return searchCheck(form);
}


var ckMenuOver	= 999;
var ckImgOn		= new Array();
var ckImgOff	= new Array();
var stateShow	= 0;

function scateShow(type) {
	if(!type) type = 'block';	
	document.getElementById("scateBox").style.display = type;
	document.getElementById("scateImg").src = shop_skin + "img/common/cate_view_" + type + ".gif";	
}

function rtnValue(smoney,emoney) {
	f = document.moneySeatch;
	f.mo1.value = smoney;
	f.mo2.value = emoney;
	f.submit();
}


function openCM(num,img,cimg,show) {  	
	if(stateShow==1) return false;
	if(ckMenuOver!=num && ckMenuOver!=999) closeCM(ckMenuOver,1);
	
	if(document.getElementById("CM"+num)) {
		document.getElementById("CM"+num).style.display="block";	
		document.getElementById("AR"+num).style.display="block";		
	}	
	if(document.getElementById("font"+(parseInt(num)+1))) document.getElementById("font"+(parseInt(num)+1)).style.color = cateColor;
	
	if(cimg) {
		document.getElementById("cm_"+num).src = cimg;
		ckImgOn[num] = cimg;
	}

	if(img) {
		ckImgOff[num] = img;			
	}

	if(show) stateShow = 1;
	else stateShow = 0;

	ckMenuOver	= num;
} 

function closeCM(num,hide) { 		
	if(stateShow==1 && hide==1) return;
	var obj = document.getElementById("CM"+num);		
	if(!hide) {			
		obj.onmouseover = function() { clearTimeout(tHide); openCM(num,'','','1'); if(ckImgOn[num] && num==ckMenuOver) document.getElementById("cm_"+num).src=ckImgOn[num];}
		tHide = setTimeout("closeCM("+num+", true)",500);
		stateShow = 0;
		return;			
	}
	
	if(document.getElementById("CM"+num)) {
		document.getElementById("CM"+num).style.display="none";	
		document.getElementById("AR"+num).style.display="none";	
	}
	if(document.getElementById("font"+(parseInt(num)+1))) document.getElementById("font"+(parseInt(num)+1)).style.color = '';
	if(ckImgOff[num]) document.getElementById("cm_"+num).src = ckImgOff[num];	
	ckMenuOver = 999;		
} 

function swapImgRestore() { //v3.0 
	var i,x,a=document.sr; 
	for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc; 
} 

function chImage(Url){
   obj = document.getElementById("viewImage");
   if(obj) obj.src = Url;
}

function ViewRankBox(value,sec) {   
	if(value) {
        document.getElementById("top_rankLine").style.display = "none";
        document.getElementById("top_rankBox").style.display = "block";		
		
		if(typeof(sec)!='undefined') {
			if(sec>5) rankView('6');			
			else rankView('1');			
		}	
    } 
	else {
        document.getElementById("top_rankLine").style.display = "block";
        document.getElementById("top_rankBox").style.display = "none";
    }
}

function rankView(num2) { 	
	if(num2==1) document.getElementById("rankBox_1").style.display='none';
	else if(num2==6) document.getElementById("rankBox_1").style.display='block';

	if(document.getElementById("rankBox_1").style.display=='none') {
		start = 1;
		num = "6~10";
		img = shop_skin + "img/common/icon_select_down.gif";
	}
	else {
		start = 6;
		num = "1~5";
		img = shop_skin + "img/common/icon_select_up.gif";
	}
	
	for (i=1;i<11;i++)
		document.getElementById("rankBox_"+i).style.display="none";	

	for(i=start;i<start+5;i++)
		document.getElementById("rankBox_"+i).style.display="block";		

	document.getElementById("rankNum").innerHTML = num;
	document.getElementById("rankImg").src = img;
} 

function viewAfter(num) {
	if(afterViewNum) {		
		if(!document.getElementById("after_"+afterViewNum)) {
			afterViewNum = '';	
			return;
		}
		document.getElementById("after_"+afterViewNum).style.display = 'none';
	}
	if(afterViewNum==num) {
		afterViewNum = '';
		return;
	}		
	document.getElementById("after_"+num).style.display = 'block';
	afterViewNum = num;	
}

function viewQna(num) {
	if(qnaViewNum) {		
		if(!document.getElementById("qna_"+qnaViewNum)) {
			qnaViewNum = '';	
			return;
		}
		document.getElementById("qna_"+qnaViewNum).style.display = 'none';
	}
	if(qnaViewNum==num) {
		qnaViewNum = '';
		return;
	}		
	document.getElementById("qna_"+num).style.display = 'block';
	qnaViewNum = num;	
}

function viewQnaLock(uid, num) {
	if($("qna_content_"+uid)) {
		num2 = $("qna_content_"+uid).innerHTML;
		if(num==num2) pLightBox.show('php/qna_write.php?pmode=secret&uid='+uid,'iframe','400','180','■ 비밀번호 확인','20');	
		else viewQna(num);
	}
}

function viewQnaLock2(uid, content, answer) {
	if($("qna_content_"+uid)) {
		num = $("qna_content_"+uid).innerHTML;
		$("qna_content_"+uid).innerHTML = content;
		if(answer) {
			$("qna_answer_"+uid).innerHTML = answer;
		}
		viewQna(num);
	}
}


function viewWeek(num) {
	obj = document.getElementById("weekRank");
	if(num==1) num2 = 2;
	else num2 = 1;
		
	obj.style.background = "url("+shop_skin+"img/main/box_tab_bg"+num+".gif) repeat-x";
	document.getElementById("weekRank"+num).style.fontWeight = 'bold';
	document.getElementById("weekRank"+num2).style.fontWeight = '';
	document.getElementById("weekGoods"+num).style.display = 'block';
	document.getElementById("weekGoods"+num2).style.display = 'none';			
}

function rtnModify(str,qstr) {	
	if(str) {
		pLightBox.hide();
		pLightBox.show('php/after_write.php?pmode=modify&passwd='+str+qstr,'iframe','600','420','■ 이용후기 수정','20');
	}
	else {
		pLightBox.hide();
		pLightBox.show('php/after_write.php?pmode=confirm'+qstr,'iframe','400','180','■ 비밀번호 확인','20');		
	}
}

function rtnModify2(str,qstr) {	
	if(str) {
		pLightBox.hide();
		pLightBox.show('php/qna_write.php?pmode=modify&passwd='+str+qstr,'iframe','600','420','■ 상품Q&A 수정','20');
	}
	else {
		pLightBox.hide();
		pLightBox.show('php/qna_write.php?pmode=confirm'+qstr,'iframe','400','180','■ 비밀번호 확인','20');		
	}
}

var gToCart = {
	
	posX : 0,
	posY : 0,
	startX : 0,
	startY : 0,
    opacity	: 50,
	dragStatus : false,
	pBody : null,
	bgColor : '#3399ff',
	zoneArea : false,
	addDiv	: null,	
	clEvt	: true,		
	divHeight : 0,
	topMargin : 0,
	zoneWidth : 0,
	zoneHeight : 0,
		
	init : function (e,obj,cate,number) {
		if(!obj || !cate || !number) return;

		this.cate		= cate;
		this.number		= number;				
		
		if(!this.pBody) {
			try {
				tElement = document.getElementsByTagName("body")[0];  
				if (typeof(tElement) === "undefined" || tElement === null) {			
					alert('Could not find the BODY element.');
					return false;
				}		
			} catch (ex) {
				return false;
			}
			this.pBody = tElement;
		}	

		tag = document.createElement('IMG');
		tmps = obj.style.backgroundImage;
		tmps = tmps.split("(");
		tmps = tmps[1].split(")");
		tag.src = tmps[0];
		tag.style.position	= 'absolute';
		tag.style.zIndex	= '999';		
		tag.style.width		= '80px';
		tag.style.height	= '80px';	
		tag.style.border	= '1px solid #3C0';	

		this.pBody.appendChild(tag);	

		if (tag.filters) {	
			try {
				tag.filters.item("DXImageTransform.Microsoft.Alpha").opacity = this.opacity;
			} catch (e) {			
				tag.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + this.opacity + ')';
			}
		} else tag.style.opacity = this.opacity / 100;		

		obj.style.border = '2px solid '+this.bgColor;
		this.obj = tag;		
		this.target	= obj;
		this.obj = tag;		
		
		if(typeof(rBoxDiv)!='undefined'){
			if(rBoxDiv.snum==1) rBoxDiv.scroll('2');
			if(rCartBoxDiv.count <= rCartBoxDiv.tcnt-rCartBoxDiv.vcnt) {				
				tmpCnt = (rCartBoxDiv.tcnt-rCartBoxDiv.vcnt)-(rCartBoxDiv.count-1);
				rCartBoxDiv.count += (tmpCnt-1);
				rCartBoxDiv.scroll('down');			
			}

			document.getElementById('gdropArea').style.border = '1px dashed #3399ff';
			document.getElementById('gdropAreaHd').style.display = 'none';		

			var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
			var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);

			this.dropZone = new Array();

			this.dropZone[0] = w - ((w - this.divHeight)/2);
			this.dropZone[1] = this.dropZone[0] - this.zoneWidth;
			this.dropZone[2] = this.topMargin;		
			this.dropZone[3] = this.dropZone[2] + this.zoneHeight;		
			this.dragStart(e);		
		}
	},

	dragStart : function (event) {						
		this.dragStatus = true;      
		this.clEvt	= true;
		this.obj.style.cursor	= 'move';		
		var isMsie = document.all ? true : false; 
		if (isMsie) {
			var e = window.event;
			window.document.attachEvent('onmousemove', gToCart.draging);			
			window.document.attachEvent('onmouseup', gToCart.dragEnd);			
			window.document.attachEvent('onselectstart', function(e){ return false;});			
		}
		else {
			var e = event;
			window.document.addEventListener('mousemove', gToCart.draging, false);
			window.document.addEventListener('mouseup', gToCart.dragEnd, false);
			window.document.addEventListener('selectstart', function(e){ return false;},false);	
			window.document.addEventListener('mousedown', gToCart.callbacks, false);
			
		}

		this.posX = parseInt(e.clientX);
		this.posY = parseInt(e.clientY);		
       
		this.startX = (document.body.scrollLeft || document.documentElement.scrollLeft) + parseInt(this.posX);
		this.startY = (document.body.scrollTop || document.documentElement.scrollTop) + parseInt(this.posY);
		
		this.obj.style.left = this.startX + 1 + 'px';
		this.obj.style.top = this.startY + 1 + 'px';		

		document.getElementById('cartGuide').style.display = 'block';
	},	

	callbacks : function(e){ 
		if(e.preventDefault) {  
			e.preventDefault(); 
		} 
	},

	draging : function (e) {	
		if (gToCart.dragStatus) {
			if(e.clientX<gToCart.dropZone[0] && e.clientX>gToCart.dropZone[1] && e.clientY>gToCart.dropZone[2] && e.clientY<gToCart.dropZone[3]) {
				if(!gToCart.zoneArea) {
					document.getElementById('gdropArea').style.backgroundColor = '#EFEFEF';									
					gToCart.zoneArea = true;
				}
			}	
			else {
				if(gToCart.zoneArea) {
					document.getElementById('gdropArea').style.backgroundColor = '';				
					gToCart.zoneArea = false;
				}
			}

			gToCart.obj.style.left = gToCart.startX + (e.clientX - gToCart.posX) - 2 +'px';
			gToCart.obj.style.top = gToCart.startY + (e.clientY - gToCart.posY) - 2 + 'px';
			if((gToCart.startX - 2) != parseInt(gToCart.obj.style.left)) gToCart.clEvt = false;
		}	
		return false;
	},
	
	dragEnd : function (e) {			
		if(gToCart.clEvt) {
			window.location.href = "index.php?channel=view&uid="+gToCart.number+"&cate="+gToCart.cate;
		}
		if(!gToCart.dragStatus) return;
		
		if(gToCart.zoneArea) {
			aObj = new AjaxObject;             
			aObj.getHttpRequest("php/cartOk.php?cate="+gToCart.cate+'&number='+gToCart.number, "gToCart.cartOk","data"); 
		}

		gToCart.dragStatus = false;	
		gToCart.zoneArea = false;					
		gToCart.target.style.border = '2px solid #FFF';
		
		document.getElementById('gdropArea').style.backgroundColor = '';
		document.getElementById('gdropArea').style.border = '1px solid #D5D5D5';
		document.getElementById('gdropArea').style.borderTop = '0px';
		document.getElementById('gdropArea').style.borderBottom = '0px';
		document.getElementById('gdropAreaHd').style.display = 'block';
		document.getElementById('cartGuide').style.display = 'none';

		gToCart.target  = null;
		gToCart.posX	= 0;
		gToCart.posY	= 0;				

		gToCart.fadeOut(60);		
	},
	
	cartAdd : function(cate,number) {		
		aObj = new AjaxObject;             
		aObj.getHttpRequest("php/cartOk.php?cate="+cate+'&number='+number, "gToCart.cartOk","data"); 
	},

	cartOk : function(data) {		
		if(data['item']=='false') {
			alert('장바구니 담기에 실패 했습니다.');
		} 
		else if(data['item']=='true') {						
			if((typeof(rCartBoxDiv)=='undefined' && typeof(parent.rCartBoxDiv)=='undefined') || !data['name']) {			
				if(typeof(quickBarCartOk)=='function') {
					quickBarCartOk(data);
					if(data['view']!='Y') return;
				}

				if(data['view']=='Y') {					
					if(!data['name']) messageBox.show('장바구니에 동일한 상품이 담겨있어 장바구니 수량을 변경 하였습니다. <br/> 장바구니로 이동 하시겠습니까?','280','140','장바구니 확인',rtnConfirm2Value,'확인,취소');
					else messageBox.show('장바구니에 상품이 담겼습니다. <br/> 장바구니로 이동 하시겠습니까?','280','120','장바구니 확인',rtnConfirm2Value,'확인,취소');
					return;
				}				
			}

			var isMsie = document.all ? true : false; 
			if(isMsie) scnt = 0;
			else scnt = 1;
			
			if(paths=='../') {
				tmps = parent.gToCart.addDiv;
				obj = parent.document.getElementById('rCartBox');
				rCartBoxDiv = parent.rCartBoxDiv;
			}
			else {
				tmps = gToCart.addDiv;
				obj = document.getElementById('rCartBox');
			}

			tmps = str_replace("[IMAGE]",data['image'],tmps);
			tmps = str_replace("[LINK]",data['link'],tmps);
			tmps = str_replace("[NAME]",data['name'],tmps);
			tmps = str_replace("[UID]",data['uid'],tmps);
			tmps = str_replace("[PRICE]",data['price'],tmps);			
			
			if(rCartBoxDiv.tcnt==0) {
				obj.childNodes[scnt].id = "cGoods_"+data['uid'];
				obj.childNodes[scnt].style.height = "92px";		
				obj.childNodes[scnt].innerHTML = tmps;		
			}
			else {				
				tag = document.createElement('UL');
				tag.id = "cGoods_"+data['uid'];
				tag.innerHTML = tmps;
				obj.appendChild(tag);								
			}
			rCartBoxDiv.tcnt++;
			if(rCartBoxDiv.tcnt>rCartBoxDiv.vcnt) rCartBoxDiv.scroll('down');
			
			if(paths=='../') parent.document.getElementById('rCartBoxDivCnt').innerHTML = rCartBoxDiv.tcnt;
			else document.getElementById('rCartBoxDivCnt').innerHTML = rCartBoxDiv.tcnt;
			
			if(data['view']=='Y') {
				messageBox.show('장바구니에 상품이 담겼습니다. <br/> 장바구니로 이동 하시겠습니까?','280','120','장바구니 확인',rtnConfirm2Value,'확인,취소');
			}
		} 
		else {
			alert(data['item']);
		}

	},

	fadeOut : function(opacity) {
		if(!gToCart.obj) return;
		var reduce_opacity_by = 15;
		var rate = 5;	// 15 fps
        
		if (opacity > 0) {
			opacity -= reduce_opacity_by;
			if (opacity > 100) opacity = 0;

			if (gToCart.obj.filters) {
				try {
					gToCart.obj.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
				} catch (e) {					
					gToCart.obj.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
				}
			} else {
				gToCart.obj.style.opacity = opacity / 100;
			}

			cnt = 5- (opacity/reduce_opacity_by);
			gToCart.obj.style.width = (80 - (5*cnt))+"px";
			gToCart.obj.style.height = (80 - (5*cnt))+"px";
		}
		

		if (opacity > 0) {
			setTimeout(function() { gToCart.fadeOut(opacity); }, rate);
		} 
		else {
			gToCart.pBody.removeChild(gToCart.obj);
			gToCart.obj		= null;			
		}
	},
	
	cancel : function () {	
		if(!document.getElementById('gdropArea')) return false;
		
		this.dragStatus = false;	
		this.zoneArea = false;					
				
		document.getElementById('gdropArea').style.backgroundColor = '';
		document.getElementById('gdropArea').style.border = '1px solid #d5d5d5';
		document.getElementById('gdropArea').style.borderTop = '0px';
		document.getElementById('gdropArea').style.borderBottom = '0px';
		document.getElementById('gdropAreaHd').style.display = 'block';
		document.getElementById('cartGuide').style.display = 'none';

		this.target  = null;
		this.posX	= 0;
		this.posY	= 0;	
		if(this.pBody) this.pBody.removeChild(this.obj);
		this.obj		= null;		
	}
}

function viewGoods(url) {    
	if(!url) return;	

	gToCart.cancel();
	if(gToCart.clEvt) window.location.href=url;	
	gToCart.clEvt = true;
}

function delGoods(cate,number) {
	aObj = new AjaxObject;  
	if(number) aObj.getHttpRequest("php/todayDel.php?cate="+cate+"&number="+number, "delGoodsOk2","data"); 
	else aObj.getHttpRequest("php/cartOk.php?mode=del&uid="+cate, "delGoodsOk1","data"); 
}

function delGoodsOk1(data) {
	if(data['item']=='true' && data['uid']) {			

		tmps = window.location.href.split('channel=');
		if(tmps[1]=='cart' || tmps[1]=='order_form') { 
			window.location.reload();
			return;
		}

		obj = document.getElementById('rCartBox');
		obj.removeChild(document.getElementById("cGoods_"+data['uid']));		
		rCartBoxDiv.tcnt--;		
		if(rCartBoxDiv.tcnt==0) {
			tag = document.createElement('UL');
			tag.style.height = "200px";
			tag.innerHTML = '<li style="height:120px;"></li><li class="small gray">장바구니 상품이 <br>없습니다.</li>';
			obj.appendChild(tag);		
			rCartBoxDiv.count = 1;
			document.getElementById('rCartBoxDivCnt').innerHTML = "0";
		}
		else {
			if(rCartBoxDiv.count>1) rCartBoxDiv.scroll('up');
			else {
				rCartBoxDiv.count--;		
					
				if(rCartBoxDiv.count<=(rCartBoxDiv.tcnt-rCartBoxDiv.vcnt)) document.getElementById(rCartBoxDiv.name+'ImgNext').src = rCartBoxDiv.img2.src;
				else document.getElementById(rCartBoxDiv.name+'ImgNext').src = rCartBoxDiv.img4.src;
			}
			document.getElementById('rCartBoxDivCnt').innerHTML = rCartBoxDiv.tcnt;
		}
	 }
}

function delGoodsOk2(data) {
	 if(data['item']=='true' && data['cate'] && data['number']) {	
		obj = document.getElementById('rTodayBox');
		obj.removeChild(document.getElementById("tGoods_"+data['cate']+data['number']));		
		rTodayBoxDiv.tcnt--;
		if(rTodayBoxDiv.tcnt==0) {
			tag = document.createElement('UL');
			tag.style.height = "200px";
			tag.innerHTML = '<li style="height:120px;"></li><li class="small gray">오늘 본 상품이 <br>없습니다.</li>';
			obj.appendChild(tag);		
			rTodayBoxDiv.count = 1;
			if(document.getElementById('rTodayBoxDivCnt')) document.getElementById('rTodayBoxDivCnt').innerHTML = "0";
		}
		else {
			if(rTodayBoxDiv.count>1) rTodayBoxDiv.scroll('up');
			else {
				rTodayBoxDiv.count--;		
					
				if(rTodayBoxDiv.count<=(rTodayBoxDiv.tcnt-rTodayBoxDiv.vcnt)) document.getElementById(rTodayBoxDiv.name+'ImgNext').src = rTodayBoxDiv.img2.src;
				else document.getElementById(rTodayBoxDiv.name+'ImgNext').src = rTodayBoxDiv.img4.src;
			}
			if(document.getElementById('rTodayBoxDivCnt')) document.getElementById('rTodayBoxDivCnt').innerHTML = rTodayBoxDiv.tcnt;
		}
	}
	if($("qTodayBox") && data['callback']!='N'){
		data2 = new Array();
		data2['item'] = 'true';
		data2['type'] = 'Today';
		data2['uid'] = data['cate']+data['number'];
		data2['callback'] = 'N';
		quickBarDelGoodsOk(data2);
	}
}

function getAfter(limit,total,Pstart,qstr){
    this.aObj	= new AjaxObject; 
	this.limit	= limit;
	this.total	= total;
	this.Pstart = Pstart;
	this.qstr	= qstr;
	this.order	= "uid";
	
	this.display = function() {
		if(this.total==0) return false;
		var loading = document.getElementById("list_loading");
		var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);
		loading.style.display = 'block';
		loading.style.top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) +  (h-(loading.height||parseInt(loading.style.height)||loading.offsetHeight))/2) + 'px';
		loading.style.left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft)  + (w-(loading.width||parseInt(loading.style.width)||loading.offsetWidth))/2) + 'px';
		
		this.aObj.getHttpRequest("php/getAfter.php?limit="+this.limit+"&order="+this.order+"&total="+this.total+"&Pstart="+this.Pstart+this.qstr, "getAfters.dispList","data"); 
		return false;
	}

	this.dispList = function (data) {
		document.getElementById("list_loading").style.display = 'none';
		obj = document.getElementById("list_after");				

		if(typeof(data['error'])!='undefined') {
			alert("리스트를 가져오는 중 에러가 발생했씁니다.");
			return;
		}
			
		for (i=0,cnt=(obj.rows.length);i<cnt;i++) {				
		   obj.deleteRow(0);				
		}		
		start = 0;		
		
		for(i=start;i<this.limit;i++) {
			if(typeof(data['item'][i])!='undefined') {
				rtnRowAfter(obj,data['item'][i]['num'],data['item'][i]['point'],data['item'][i]['title'],data['item'][i]['name'],data['item'][i]['buy'],data['item'][i]['date'],data['item'][i]['content'],data['item'][i]['mod'],data['item'][i]['uid'],data['item'][i]['name2'],data['item'][i]['link'],data['item'][i]['image'],data['item'][i]['dragd']);								
			}
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
		if(document.getElementById("order_"+this.order).tagName=='IMG') {
			document.getElementById("order_"+this.order).src = str_replace("_on","_off",document.getElementById("order_"+this.order).src);
			document.getElementById("order_"+vls).src = str_replace("_off","_on",document.getElementById("order_"+vls).src);
		}
		else {
			document.getElementById("order_"+this.order).className = 'tab_off small';
			document.getElementById("order_"+vls).className = 'tab_on small';
		}
		this.order = vls;		
		return this.display();
	}
}

function getQna(limit,total,Pstart,qstr){
    this.aObj = new AjaxObject; 
	this.limit = limit;
	this.total = total;
	this.Pstart = Pstart;
	this.qstr = qstr;
	
	this.display = function() {
		if(this.total==0) return false;
		var loading = document.getElementById("list_loading");
		var h = (window.innerHeight || self.innerHeight || document.documentElement.clientHeight || document.body.clientHeight);
		var w = (window.innerWidth || self.innerWidth || document.documentElement.clientWidth || document.body.clientWidth);
		loading.style.display = 'block';
		loading.style.top = ((window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop) +  (h-(loading.height||parseInt(loading.style.height)||loading.offsetHeight))/2) + 'px';
		loading.style.left = ((window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft)  + (w-(loading.width||parseInt(loading.style.width)||loading.offsetWidth))/2) + 'px';
	
		this.aObj.getHttpRequest("php/getQna.php?limit="+this.limit+"&total="+this.total+"&Pstart="+this.Pstart+this.qstr, "getQna.dispList","data"); 
		return false;
	}

	this.dispList = function (data) {	
		document.getElementById("list_loading").style.display = 'none';
		obj = document.getElementById("list_qna");				

		if(typeof(data['error'])!='undefined') {
			alert("리스트를 가져오는 중 에러가 발생했씁니다.");
			return;
		}
			
		for (i=0,cnt=(obj.rows.length);i<cnt;i++) {				
		   obj.deleteRow(0);				
		}		
		start = 0;		
		
		for(i=start;i<this.limit;i++) {
			if(typeof(data['item'][i])!='undefined') {
				rtnRowQna(obj,data['item'][i]['num'],data['item'][i]['title'],data['item'][i]['name'],data['item'][i]['ans'],data['item'][i]['date'],data['item'][i]['content'],data['item'][i]['answer'],data['item'][i]['mod'],data['item'][i]['uid'],data['item'][i]['name2'],data['item'][i]['link'],data['item'][i]['image'],data['item'][i]['dragd'],data['item'][i]['secret']);
			}
		}			
	}	

	this.cgLimit = function (vls) { //ajaxPaging()와 연동
		if(!vls) vls = 10;
		this.limit = vls;	
		qPage.page_record_num = vls;
		qPage.makePage();
		this.display();
		qPage.printPage(' ');	
	}
}


function ajaxPaging(name,id,rtnId,total_record,page_record_num,page_link_num,tFocus,obj1,obj2) {
	if(!id || !name || !total_record) return;
	this.obj				= document.getElementById(id); // 페이징 출력 객체 
	this.rtnId				= rtnId; // 페이징 결과 리턴 객체
	this.name				= name;	// 생성객체 명	
	this.page				= 1;	// 현재페이지
	this.total_record		= total_record;	// 총 레코드 수
	this.page_record_num	= 10;	// 한페이지에 보여줄 레코드 수
	if(page_record_num) this.page_record_num = page_record_num;
	this.page_link_num		= 10;	// 한페이지에 보여줄 페이지 수
	if(page_link_num) this.page_link_num = page_link_num;	
	this.total_page			= 0;	// 총 페이지 수
	this.total_block		= 0;	// 총 블럭 수
	this.block				= 1;	// 현재블럭
	this.page_start			= 0;	// 화면에 뿌려질 페이지 숫자의 첫 페이지 숫자
    this.page_end			= 0;	// 화면에 뿌려질 페이지 숫자의 마지막 페이지 숫자
	this.prev_page			= 0;	// 이전페이지
	this.next_page			= 0;	// 다음페이지
	this.url				= null;	// 문서정보
	this.qstr				= null;	// 파라미터 값	
	this.type				= null; //타입
	this.imgPath			= null; //이미지경로
	this.separator			= null; //구분자
	this.pt_fb				= null; //앞뒤 구분자 여부
	this.pageCurrent		= obj1;  //현재 페이지 값을 받을 객체
	this.pageTotal			= obj2;  //총페이지수 값을 받을 객체
	
	if(tFocus) this.tFocus	= tFocus; //포커스 받을 객체명
	else this.tFocus = "list";

	this.makePage = function () {
		this.total_page = Math.ceil(this.total_record/this.page_record_num);		
	    this.total_block = Math.ceil(this.total_page/this.page_link_num);
		if(this.page<1) this.page = 1;		
		if(this.page>this.total_page) this.page = this.total_page;
       	this.block = Math.ceil(this.page/this.page_link_num);        // 현재블럭 설정
        this.page_end = this.block*this.page_link_num;        // 페이지출력 종료루프 변수
		this.page_start = (this.page_end-this.page_link_num)+1;       // 페이지출력 시작루프 변수
       	this.prev_page = (this.block*this.page_link_num)-this.page_link_num; // 이전블럭 번호 설정
		this.next_block = this.block+1; // 다음블럭 번호 설정		
		if(this.pageTotal) document.getElementById(this.pageTotal).innerHTML = this.total_page;
	}

	this.resultPage = function (page,re) {
		this.page = page;
		this.rtnId.Pstart = this.page_record_num*(this.page-1);
		this.rtnId.display();
		if(re==1) this.makePage(); 
		this.printPage();
		if(this.pageCurrent) document.getElementById(this.pageCurrent).innerHTML = page;		
		if(document.getElementById(this.tFocus)) document.getElementById(this.tFocus).focus();
		setCookie("mallPage",page,3600,"/",domain=window.document.domain||window.location.hostname);			
		return false;		
	}

	this.printPage = function (type,imgPath,separ,pt_fb) {
		var paging = '';

		if(type) this.type = type;
		else type = this.type;
		if(imgPath) this.imgPath = imgPath;
		else imgPath = this.imgPath;
		if(separ) this.separator = separ;
		else separ = this.separator;
		if(pt_fb) this.pt_fb = pt_fb;
		else pt_fb = this.pt_fb;

		var ckBlock1 = ckBlock2 = 0;

		if(type=="box" && this.total_page>6 ) {
			this.page_start = parseInt(this.page) - 3;
			this.page_end = parseInt(this.page) + 3;			
			if(this.page<4) {
				this.page_start = 1;
				this.page_end = 7
			}
			if(this.page_end>this.total_page) {
				this.page_start = parseInt(this.total_page) - 6;
				this.page_end = this.total_page;								
			}
			if(this.page>4) ckBlock1 = 1;
			if(this.page_end==this.total_page) ckBlock2 = 1;
		}
			
		if(this.block > 1 || ckBlock1==1) {
			switch(type) {
				case "img" :
					paging	= '<a href="#" onclick="return '+this.name+'.resultPage(\'1\',1);"><img src="'+imgPath+'/btn_first.gif" border="0" style="vertical-align:middle;" alt="첫페이지" title="첫페이지" /></a>&nbsp;';
					paging += '<a href="#" onclick="return '+this.name+'.resultPage(\''+this.prev_page+'\',1);"><img src="'+imgPath+'/btn_prev.gif" border="0"  style="vertical-align:middle;" alt="이전 목록" title="이전 목록" /></a>&nbsp;';
				break;
				
				case "box" :
					paging += '<a href="#" onclick="return '+this.name+'.resultPage(\'1\',1);"><span class="num default" onmouseover="this.className=\'num defaultOver\'" onmouseout="this.className=\'num default\'">1</span></a><span class="num">...</span>';
				break;

				default :
					paging  = '<a href="#" onclick="return '+this.name+'.resultPage(\''+this.prev_page+'\',1);"><span id="prevPage">이전</span></a>';
					paging += '<span class="numbox">';
					paging += '<a href="#" onclick="return '+this.name+'.resultPage(\'1\',1);"><span class="num2">1</span></a><span class="num">...</span>';
			}		
		}
		else {
			switch(type) {
				case "img" : case "box" : break;
				default :			
					paging += '<span class="numbox">';
			}			
		}
		
		if(this.block >= 1) {				
			for(i=this.page_start;i<=this.page_end;i++) {
				if(i!=this.page_start || pt_fb=='Y') pseparator = this.separator;
				else pseparator = "";

				if(this.page==i) { 				
					switch(type) {
						case "img" :
							paging += pseparator+'<span class="selected inum">'+i+'</span>';				
						break;

						case "box" :
							paging += '<span class="selected num">'+i+'</span>';				
						break;

						default :
							paging += '<span class="selected num">'+i+'</span>';				
					}
				} 
				else {
					switch(type) {					
						case "img" :
							paging += pseparator+'<a href="#" onclick="return '+this.name+'.resultPage(\''+i+'\');"><span class="inum">'+i+'</span></a>';
						break;

						case "box" :
							paging += '<a href="#" onclick="return '+this.name+'.resultPage(\''+i+'\');"><span class="num default" onmouseover="this.className=\'num defaultOver\'" onmouseout="this.className=\'num default\'">'+i+'</span></a>';
						break;

						default :
							paging += '<a href="#" onclick="return '+this.name+'.resultPage(\''+i+'\');"><span class="num">'+i+'</span></a>';
					}
				}
				
				this.next_page = i + 1;			
				if(this.next_page==this.total_page + 1) {
					if(pt_fb=='Y') paging += pseparator;	
					break;				
				}
			}
		}
    
		switch(type) {
			case "img" : case "box" : break;
			default :
				paging += '</span>';
		}
		
		if(this.block<this.total_block && ckBlock2!=1) {
			switch(type) {
				case "img" :
					paging += '&nbsp;<a href="#" onclick="return '+this.name+'.resultPage(\''+this.next_page+'\',1);"><img src="'+this.imgPath+'/btn_next.gif" border="0" style="vertical-align:middle;" alt="다음 목록" title="다음 목록" /></a>&nbsp;';
				    paging += '<a href="#" onclick="return '+this.name+'.resultPage(\''+this.total_page+'\',1);"><img src="'+this.imgPath+'/btn_last.gif" border="0"  style="vertical-align:middle;" alt="마지막 페이지" title="마지막 페이지" /></a>';
				break;
				
				case "box" :
					paging += '<span class="num">...</span><a href="#" onclick="return '+this.name+'.resultPage(\''+this.total_page+'\',1);"><span class="num default" onmouseover="this.className=\'num defaultOver\'" onmouseout="this.className=\'num default\'">'+this.total_page+'</span></a>&nbsp;';					
				break;

				default :
					paging += '<span class="num2">...</span><a href="#" onclick="return '+this.name+'.resultPage(\''+this.total_page+'\',1);"><span class="num">'+this.total_page+'</span></a>&nbsp;';
					paging += '<a href="#" onclick="return '+this.name+'.resultPage(\''+this.next_page+'\',1);"><span id="nextPage">다음</span></a>';
			}			
		}			
	
		this.obj.innerHTML = paging;		
	}	
}


/************* 장바구니 체크 ***************/

function modPost(num){
	var f = document.cartForm;
	var ckStr= eval("f.qty"+num) 		

    if(!ckStr.value || ckStr.value==0) {
        alert('수량을 입력하시기 바랍니다.');
		ckStr.focus();		
		return false;
    }

	if(!ckNum(ckStr)) {
		alert('숫자만 입력 가능 합니다.');
		ckStr.focus();		
		return false;
	}

	tmp_cnt = eval("f.p_op_cnt_"+num+".value");
	p_op_arr = '';
	for(i=1;i<=tmp_cnt;i++) {
		tmps = eval("f.p_op_"+num+"_"+i);
		if(typeof(tmps)!="undefined") {
			if(!tmps.value) {
				alert('옵션을 선택 하시기 바랍니다.');
				return false;
			}
			if(!p_op_arr) p_op_arr = tmps.value;
			else p_op_arr = p_op_arr + "|"+tmps.value;
		}
	}

	if(p_op_arr) {
		f.p_option.value = p_op_arr;
	}

	f.type.value = 'mod';
    f.p_uid.value = num;
	f.p_qty.value = ckStr.value;
	f.submit();
}

function delPost(num){
	var f = document.cartForm;
    f.type.value = 'del';
    f.p_uid.value = num;	  
	f.submit();
}

function cartSecPost(type,ckLog){
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
	if(type=='corder') {
		for (i=0;i<f.elements.length;i++) {
			if(f.elements[i].name.indexOf('p_op_') == 0 && !f.elements[i].value) {
				for(k=0;k<ckUid.length;k++) {
					if(f.elements[i].name.indexOf('p_op_'+ckUid[k]+'_')==0) {
						alert("옵션 선택을 하지 않은 상품이 존재 합니다. 옵션을 선택 하시고 수정 하시기 바랍니다.");	
						return false;
					}
				}
			}
		}

		if(ckLog=='N') {			
			pLightBox.show('php/plogin.php?type=direct','iframe',450,380,'■ 로그인','20');
			return false;
		}
	}
	
	f.submit();
}

function orderPost(ckLog,skin){

	var f = document.cartForm;
	
	for (i=0;i<f.elements.length;i++) {
		if(f.elements[i].name.indexOf('p_op_') == 0 && !f.elements[i].value) {
			alert("옵션 선택을 하지 않은 상품이 존재 합니다. 옵션을 선택 하시고 수정 하시기 바랍니다.");	
			return false;
		}
	}

	if(ckOrder==0) {
		alert('옵션 선택 후 수정하지 않은 상품이 존재 합니다. 수정버튼을 눌러 수정된 옵션을 적용 시키시기 바랍니다.');
		return false;
	}
	
	if(ckLog=="N") {
		pLightBox.show('php/plogin.php','iframe',450,380,'■ 로그인','20');
	}
	else window.location.href='?channel=order_form';
}

function ckAll(frm){
	if(frm) var f = eval("document."+frm+"Form");
	else var f = document.cartForm;
	
	for (i=cnt=0;i<f.elements.length;i++) {
		if(f.elements[i].name == 'item[]') {
			if(f.elements[i].checked==true) f.elements[i].checked = false;
			else f.elements[i].checked = true;
		}
	}
}

function addWish(ckLog){
	if(ckLog=='N') {
		ckLogin('cart');
		return false;
	}

	var f = document.cartForm;
	var vls = '';

	for (i=cnt=0;i<f.elements.length;i++) {
		if(f.elements[i].name == 'item[]' && f.elements[i].checked == true) {
			vls += f.elements[i].value+"|";
			cnt++;
			break;
		}
	}
	
	if ( cnt <= 0) {
		alert("선택한 항목이 없습니다.");
		return;
	} 
	
	$("HFrm").src = "php/wish_ok.php?type=adds&vls="+vls;
}


/************* 주문폼 체크 ***************/
function msInfo(obj,num) {
	if(obj.checked) {
		f.message.value = eval("f.Ms"+num+".value")
		f.ckMs1.checked = false;
		if(typeof(f.ckMs2)!='undefined') f.ckMs2.checked = false;
		if(typeof(f.ckMs3)!='undefined') f.ckMs3.checked = false;
		eval("f.ckMs"+num+".checked = true")
	}
	else f.message.value = '';
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

	if($("orderOther").style.display=="block") {
		if(ck_value('bank_name','입금은행을 선택하세요!')==1) return false;
		if(ck_value('pay_name','입금자 성명을 입력하세요!')==1) return false;
	}
	else {
		if(typeof(f.cash_type.value)=="undefined") {			
			for(i=ct_ckd=0;i<f.cash_type.length;i++){
				if(f.cash_type[i].checked) {
					ct_ckd = 1;
					break;
				}
			}

			if(ct_ckd==0) {
				alert("결제 수단을 선택 하시기 바랍니다");
				return false;
			}

			if(f.cash_type[0].checked && f.cash_type[0].value=='B') {
				 if(ck_value('bank_name','입금은행을 선택하세요!')==1) return false;
				 if(ck_value('pay_name','입금자 성명을 입력하세요!')==1) return false;
			}		
		} else {
			f.cash_type.checked = true;
			if(f.cash_type.value=='B') {
				 if(ck_value('bank_name','입금은행을 선택하세요!')==1) return false;
				 if(ck_value('pay_name','입금자 성명을 입력하세요!')==1) return false;
			}
		}
	}

	if(typeof(f.cash_ctype)!='undefined') {
		if(f.cash_ctype[0].checked) {
			if(f.pay_type[0].checked) {
				if(f.cell1.value && f.cell2.value && f.cell3.value) {
					if((f.cell1.value.length<3) || (f.cell2.value.length<3) || (f.cell3.value.length!=4)) {
						alert("핸드폰번호를 정확히 입력 하시기 바랍니다.");
						f.cell1.focus();
						return false;
					}
				}
			}
			else {
				if(f.jumin1.value && f.jumin2.value) {
					if((f.jumin1.value.length!=6) || (f.jumin2.value.length!=7)) { 
						alert("주민번호를 정확히 입력 하시기 바랍니다.");
						f.jumin1.focus();
						return false;
					}
				}
			}
		}
		else {
			if(f.cnum1.value && f.cnum2.value && f.cnum3.value) {
				if((f.cnum1.value.length!=3) || (f.cnum2.value.length!=2) || (f.cnum3.value.length!=5)) {
					alert("사업자번호를 정확히 입력 하시기 바랍니다.");
					f.cnum1.focus();
					return false;
				}
			}
		}
	}

    if(typeof(f.cards)!="undefined") {f.cards.value= '';}
	
	if(typeof(f.agree1)!='undefined') {
		if(f.agree1.checked!=true) {
			alert("개인정보 수집동의 하셔야만 상품을 구매하실 수 있습니다.");
			return false;
		}		
	}
	
	f.ckBtn.value = 1;
	f.submit();

}

function cashClick(type) {
	if(document.getElementById("bankInfo")) document.getElementById("bankInfo").style.display = type;
	if(type=='none') ckCard();   

	if(document.getElementById('cash_dc')) {
		cashTotal();
		document.getElementById('cash_money').innerHTML = number_format(f.cash_total.value)+"원";		
		if(typeof(f.cash_type[0])=='undefined') ckkk = f.cash_type;
		else ckkk = f.cash_type[0];
	
		if(ckkk.checked==true) {		
			document.getElementById('cash_dc').innerHTML = number_format(f.tmp_cashdc.value)+"원";		
		}
		else {
			document.getElementById('cash_dc').innerHTML = "0원";		
		}
	}
}

function cashCgType(num) {
	if(num==1) {
		document.getElementById("pay1").style.display = 'block';
		document.getElementById("pay2").style.display = 'none';
		f.pay_type[0].checked = true;		
	}
	else {
		document.getElementById("pay2").style.display = 'block';
		document.getElementById("pay1").style.display = 'none';
		f.pay_type[2].checked = true;		
	}
}

function ckCashOk(){
	f = document.cashForm;

	if(typeof(f.cash_ctype)!='undefined') {
		if(f.cash_ctype[0].checked) {
			if(f.pay_type[0].checked) {
				if(!f.cell1.value || !f.cell2.value || !f.cell3.value) {
					alert("핸드폰번호를 입력 하시기 바랍니다.");
					f.cell1.focus();
					return false;					
				}
				if(f.cell1.value && f.cell2.value && f.cell3.value) {
					if((f.cell1.value.length<3) || (f.cell2.value.length<3) || (f.cell3.value.length!=4)) {
						alert("핸드폰번호를 정확히 입력 하시기 바랍니다.");
						f.cell1.focus();
						return false;
					}
				}
			}
			else {
				if(!f.jumin1.value || !f.jumin2.value) {
					alert("주민번호를 입력 하시기 바랍니다.");
					f.jumin1.focus();
					return false;					
				}

				if(f.jumin1.value && f.jumin2.value) {
					if((f.jumin1.value.length!=6) || (f.jumin2.value.length!=7)) { 
						alert("주민번호를 정확히 입력 하시기 바랍니다.");
						f.jumin1.focus();
						return false;
					}
				}
			}
		}
		else {
			if(!f.cnum1.value || !f.cnum2.value || !f.cnum3.value) {
				alert("사업자번호를 정확히 입력 하시기 바랍니다.");
				f.cnum1.focus();
				return false;				
			}

			if(f.cnum1.value && f.cnum2.value && f.cnum3.value) {
				if((f.cnum1.value.length!=3) || (f.cnum2.value.length!=2) || (f.cnum3.value.length!=5)) {
					alert("사업자번호를 정확히 입력 하시기 바랍니다.");
					f.cnum1.focus();
					return false;
				}
			}
		}
	}

	f.submit();
}
/************* 약관동의 체크 ***************/

function ckForm(){
	f = document.joinForm;
	if(f.agree1.checked!=true) {
		alert("이용약관에 동의 하셔야만 회원가입을 하실 수 있습니다.");
		return false;
	}
	if(f.agree2.checked!=true) {
		alert(" 개인정보취급방침에 동의 하셔야만 회원가입을 하실 수 있습니다.");
		return false;
	}
}

function ckForm2(){
	form = document.pageForm;
	if(form.agree1.checked!=true) {
		alert("이용약관에 동의 하셔야만 회원가입을 하실 수 있습니다.");
		return false;
	}
	if(form.agree2.checked!=true) {
		alert(" 개인정보취급방침에 동의 하셔야만 회원가입을 하실 수 있습니다.");
		return false;
	}
	if(!form.userNm.value) {
		alert("이름을 입력 하시기 바랍니다");
		form.userNm.focus();
		return false;
	}
	if(!form.userNo1.value) {
		alert("주민등록번호 앞자리를 입력 하시기 바랍니다");
		form.userNo1.focus();
		return false;
	}
	if(!form.userNo2.value) {
		alert("주민등록번호 뒷자리를 입력 하시기 바랍니다");
		form.userNo2.focus();
		return false;
	}
	
	if(!isSSN(form.userNo1.value, form.userNo2.value)) {
		alert("주민번호를 검토한 후, 다시 입력하세요.");
		form.userNo1.focus();
		form.userNo2.focus();
		return false;
	}

	form2 = document.inputForm;
	form2.name.value = form.userNm.value;
	form2.jumin1.value = form.userNo1.value;
	form2.jumin2.value = form.userNo2.value;
	form2.submit();

	return false;
}

/*************  관심상품목록 ***************/

function wishPost(num,type){
	var f = document.wishForm;
	
	f.type.value = type;
    f.uid.value = num;		
	f.submit();
}

function wishPost2(type){
	var f = document.wishForm;
	
	for (i=cnt=0;i<f.elements.length;i++) {
		if(f.elements[i].name == 'item[]' && f.elements[i].checked == true) {
			cnt++;
			break;
		}
	}
	
	if ( cnt <= 0) {
		alert("선택한 항목이 없습니다.");
		return;
	} 
	
	f.type.value = type;
	f.submit();
	return false;
}

/*************  주문/배송조회 ***************/
function putDay(date1,date2) {
	f = document.orderForm;
	if(!date1) {
		f.sdate1.value = '';
		f.sdate2.value = '';
	} 
	else {
		f.sdate1.value = date1;
		f.sdate2.value = date2;
	}
}

function ckOsearch(){
	f = document.orderForm;

	if(f.sdate1.value && !f.sdate2.value) {
		alert('주문일 조건을 입력 하세요!');
		f.sdate2.focus();
		return false;
	}

	if(!f.sdate1.value && f.sdate2.value) {
		alert('주문일 조건을 입력 하세요!');
		f.sdate1.focus();
		return false;
	}
		

	f.submit();
	return false;
}

function printClick() {

	if(document.getElementById("sBtn2")) document.getElementById("sBtn2").style.display = 'none';
	if(document.getElementById("sBtn3")) document.getElementById("sBtn3").style.display = 'none';
	var temp = document.getElementById("printArea").innerHTML;
	if (temp) {
		temp_css = "<link rel='StyleSheet' HREF='"+shop_skin+"style.css' type='text/css' title='style'>\n";
		temp_print = "\n<script>function sleep(){ window.close(); };window.print();setTimeout('sleep()', 1000);<\/script>";
		preWindow= open("", '','width=840,height=230');          
		preWindow.document.open();
		preWindow.document.write("<html><body>");
		preWindow.document.write(temp_css);
		preWindow.document.write(temp);
		preWindow.document.write(temp_print);
		preWindow.document.write("</body></html>");
		preWindow.document.close();		
    }
	if(document.getElementById("sBtn2")) document.getElementById("sBtn2").style.display = 'block';
	if(document.getElementById("sBtn3")) document.getElementById("sBtn3").style.display = 'block';
}


/********** 고객센터 ************/
function viewFaq(num) {
	if(!num || num==0 || viewFaqNum==num) return;
	
	document.getElementById("faq_"+num).style.background = "url("+shop_skin+"img/customer/icon_faq_q_on.gif) no-repeat 10px 0px"
	document.getElementById("faq_an_"+num).style.display = 'block';

	document.getElementById("faq_"+viewFaqNum).style.background = "url("+shop_skin+"img/customer/icon_faq_q.gif) no-repeat 10px 0px"
	document.getElementById("faq_an_"+viewFaqNum).style.display = 'none';

	viewFaqNum = num;

}

function ckFsearch(){	
	f = document.faqsForm;
	f.word.value = f.word.value.trim();
	if(!f.word.value) {
		alert("검색어를 입력 하시기 바랍니다.");
		f.word.focus();
		return false;
	}
}

function ckIdsearch(){
	f = document.idsearchForm;
	if(!f.name.value) {
		alert('이름을 입력 하시기 바랍니다.');
		f.name.focus();
		return false;
	}
	
	f.email.value = '';
	if(f.email1.value && f.email2.value) f.email.value = f.email1.value+'@'+f.email2.value;
	if(!f.email.value) {
		alert('이메일 주소을 입력 하시기 바랍니다.');
		f.email1.focus();
		return false;
	}
	f.submit();

}

function ckPwsearch(){
	f = document.pwsearchForm;

	if(!f.name.value) {
		alert('이름을 입력 하시기 바랍니다.');
		f.name.focus();
		return false;
	}

	if(!f.id.value) {
		alert('아이디를 입력 하시기 바랍니다.');
		f.id.focus();
		return false;
	}

	f.email.value = '';
	if(f.email1.value && f.email2.value) f.email.value = f.email1.value+'@'+f.email2.value;
	if(!f.email.value) {
		alert('이메일 주소을 입력 하시기 바랍니다.');
		f.email1.focus();
		return false;
	}
	f.submit();

}

function cartPost(type){
	var form = document.listForm;
	var vls = '';
	
	for (i=ck=0,cnt=form.elements.length;i<cnt;i++) {
		if(form.elements[i].name == 'compare[]' && form.elements[i].checked == true) {			
			vls += form.elements[i].value+"|";
			ck++;
		}
	}

	if ( ck <= 0) {
		alert("체크된 항목이 없습니다.");
		return;
	} 
	
	if(type=='wish') {
		if(form.ckLog.value=='N') {
			ckLogin('view');
			return false;
		}
		$("HFrm").src = "php/wish_ok.php?type=adds2&item="+vls;
	}
	else $("HFrm").src = "php/wish_ok.php?type=cart2&item="+vls;
	
	return false;
}


/********** 쿠폰관련 ************/
function cuponDown(num,gid) {
	if(!num) return;
	pLightBox.show('php/pcupon_down.php?num='+num+'&gid='+gid,'iframe','450','400','■ 할인쿠폰다운로드','20');
}

function cuponDown2(num,gid) {
	if(!num) return;
	window.open('../php/pcupon_down.php?num='+num+'&gid='+gid,'coupon','width=450,height=400');
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


/********** 주문상품 삭제/수량변경 관련 ************/
function modPost2(num){
	var f = document.changeForm;
	var ckStr= eval("f.qty"+num) 		

    if(!ckStr.value || ckStr.value==0) {
        alert('수량을 입력하시기 바랍니다.');
		ckStr.focus();		
		return false;
    }

	if(!ckNum(ckStr)) {
		alert('숫자만 입력 가능 합니다.');
		ckStr.focus();		
		return false;
	}

	f.type.value = 'mod';
    f.p_uid.value = num;
	f.p_qty.value = ckStr.value;
	f.submit();
}

function delPost2(num){
	var f = document.changeForm;
    f.type.value = 'del';
    f.p_uid.value = num;	  
	f.submit();
}

function imgSizeConv(objName,Limit,path){ 
	try {
		if(!Limit) return;
		Obj=document.getElementById(objName);	
		if(!Obj) return;
		if(!path) path = "../";
		var img = Obj.getElementsByTagName('IMG');
		for(k=0; k<img.length; k++) {	
			ckWidth = img[k].width ? parseInt(img[k].width) : parseInt(img[k].style.width); 
			ckHeight = img[k].height ? parseInt(img[k].height) : parseInt(img[k].style.height); 
			if (ckWidth > Limit) {
				height = (ckHeight*Limit)/ckWidth;
				img[k].style.width  = Limit+'px';
				img[k].style.height = height+'px';
				img[k].style.cursor = "pointer";
				img[k].onclick = function() { pLightBox.show(path+"php/photo_view.php?img="+this.src,"iframe","800","600","■ 이미지 보기");};
			}
		}	
		return;
	} catch(e) { return }

}

function openPopup(num, top, left, width, height) {
	popupBox = new pPopupBox('php/pop_up.php?uid='+num+'&type=2', top, left, width, height,num);
	popupBox.show();	
}

function setCookie( name, value, expiredays ){ 
	var todayDate = new Date(); 
	todayDate.setDate(todayDate.getDate() + expiredays); 
	document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
} 

function closeWin(pop_name){ 
	setCookie(pop_name, "1" , 1); 
	parent.pPopupBoxObj.hide();
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

	window.open(sendUrl,"sendSNSWin","width=1024, height=800");
}

/************* 공동구매 ***************/
function cooperateCount(){
	serTime++;	
	lastTime = cooperateTime - serTime;		

	if(lastTime>0) {
		day  = Math.floor(lastTime/86400);
		lastTime -=  day * 86400;
		hour = Math.floor(lastTime/3600);
		lastTime -=  hour * 3600;
		min  = Math.floor(lastTime/60);
		lastTime -=  min * 60;
		sec = Math.floor(lastTime); 
		day		= day>9 ? day : '0'+day;
		hour	= hour>9 ? hour : '0'+hour;
		min		= min>9 ? min : '0'+min;
		sec		= sec>9 ? sec : '0'+sec;

		$("countDay").innerHTML = day;
		$("countHour").innerHTML = hour;
		$("countMin").innerHTML = min;
		$("countSec").innerHTML = sec;
		
		timerID  = setTimeout(cooperateCount, 1000);
	}
	else if(typeof(timerID) != "undefined") clearTimeout(timerID);		
}

function ckCooper(){
	f = document.joinForm;
	if(ck_value('phone11','핸드폰번호를 입력하세요!')==1) return false;
	if(ck_value('phone12','핸드폰번호를 입력하세요!')==1) return false;
	if(ck_value('phone13','핸드폰번호를 입력하세요!')==1) return false;
	if(ck_value('email1','이메일을 입력하세요!')==1) return false;
	if(ck_value('email2','이메일을 입력하세요!')==1) return false;
	f.submit();
}
/************* 공동구매 ***************/

function cgImg(obj, on, id){
	if(!obj) return;
	if(on) {
		obj.src = obj.src.substr(0, obj.src.length-4) + "_on.gif";
		if(id) $(id).style.backgroundColor = '#333';
	}
	else {
		obj.src = obj.src.substr(0, obj.src.length-7) + ".gif";
		if(id) $(id).style.backgroundColor = '#5a5b5a';
	}
}

function viewQuick(id, on) {
	obj = $(id);
	if(obj) {
		if(on) obj.style.display = "block";
		else obj.style.display = "none";
	}

}

/*********************** QUICK BAR 관련 ******************************/
quickBarHeight = 0;
qucikBarOffset = 0;
quickBarSec = "Today";
quickBarTodayCheck = new Array();
quickBarWishCheck = new Array();
quickBarCartCheck = new Array();

function quickBarScroll(name,tcnt,vcnt,width) {	
	this.name = name+'Div';
	this.obj = $(name).style;
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
	this.img1.src = shop_skin+'img/bottom/icon_arrow_prev.gif';
	this.img2.src = shop_skin+'img/bottom/icon_arrow_next.gif';
	this.img3.src = shop_skin+'img/bottom/icon_arrow_prev_off.gif';
	this.img4.src = shop_skin+'img/bottom/icon_arrow_next_off.gif';

	if(this.tcnt>this.vcnt) $(this.name+'ImgNext').src = this.img2.src;
	
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
				if(this.count==1) {
					return;
				}
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
			
			if(this.count>1) $(this.name+'ImgPrev').src = this.img1.src;
			else $(this.name+'ImgPrev').src = this.img3.src;

			if(this.count<=(this.tcnt-this.vcnt)) $(this.name+'ImgNext').src = this.img2.src;
			else $(this.name+'ImgNext').src = this.img4.src;
		} 
		else window.setTimeout(this.name+".scroll()",this.speed);
    }        

}

function quickBarChange(obj) {
	if(obj.src.indexOf('_up')!=-1) {
		obj.src = obj.src.substr(0, obj.src.length-6) + "down.gif";
		obj2 = $("quickBar"+quickBarSec);
		tmps = obj2.style.backgroundImage.replace('url(','').replace(')','').replace('"','').replace('"',''); 
		obj2.style.backgroundImage = "url(" + tmps.substr(0, tmps.length-4) + "_on.gif)";
		$("quickBar"+quickBarSec+"Box").style.display = "block";
		$("quickBarTop").style.height = "32px";
		quickBarOpen();
	}
	else {
		obj.src = obj.src.substr(0, obj.src.length-8) + "up.gif";
		obj2 = $("quickBar"+quickBarSec);
		tmps = obj2.style.backgroundImage.replace('url(','').replace(')','').replace('"','').replace('"',''); 
		obj2.style.backgroundImage = "url(" + tmps.substr(0, tmps.length-7) + ".gif)";
		$("quickBar"+quickBarSec+"Box").style.display = "none";
		$("quickBarTop").style.height = "31px";
		quickBarClose();
	}
}

function quickBarChange2(id) {	
	if($("quickBarArrow").src.indexOf('_up')!=-1) {
		quickBarSec = id;
		quickBarChange($("quickBarArrow"));		
	}
	else {
		obj = $("quickBar"+quickBarSec);
		tmps = obj.style.backgroundImage.replace('url(','').replace(')','').replace('"','').replace('"',''); 
		obj.style.backgroundImage = "url(" + tmps.substr(0, tmps.length-7) + ".gif)";
		$("quickBar"+quickBarSec+"Box").style.display = "none";
		quickBarSec = id;
		obj = $("quickBar"+quickBarSec);
		tmps = obj.style.backgroundImage.replace('url(','').replace(')','').replace('"','').replace('"',''); 
		obj.style.backgroundImage = "url(" + tmps.substr(0, tmps.length-4) + "_on.gif)";
		$("quickBar"+quickBarSec+"Box").style.display = "block";
	}
}

function quickBarOpen() {
	obj = $("quickBarBody");
	if(quickBarHeight==0) {
		quickBarHeight = parseInt(obj.style.height);
		obj.style.display = "block";
	}
	
	if (qucikBarOffset < quickBarHeight) {
			qucikBarOffset += Math.ceil(Math.abs(quickBarHeight - parseInt(obj.style.height)) / 3);
			obj.style.height = qucikBarOffset+'px';
			Id2 = setTimeout(quickBarOpen,30); 
	}
	else {
		if(typeof(Id2) != "undefined") clearTimeout(Id2);	
		obj.style.height = quickBarHeight+'px';		
		qucikBarOffset = quickBarHeight;
	}	
}        

function quickBarClose() {
	obj = $("quickBarBody");	
	if (qucikBarOffset > 0) {
			qucikBarOffset -= Math.ceil(Math.abs(quickBarHeight - parseInt(obj.style.height)) / 3);
			qucikBarOffset -= 25;			
			if(qucikBarOffset<0) qucikBarOffset = 0;
			obj.style.height = qucikBarOffset+'px';
			Id2 = setTimeout(quickBarClose,30); 
	}
	else {
		if(typeof(Id2) != "undefined") clearTimeout(Id2);	
		obj.style.display = "none";
		obj.style.height = quickBarHeight+'px';		
		quickBarHeight = 0;
	}	
}        
		
function quickBarCheck(type, num) {
	if(!num || !type) return false;
	var tmp = eval("quickBar"+type+"Check");
	if(in_array(num,tmp)) {
		tmp = tmp.join(",");		
		tmp = str_replace(eval("'"+num+"'"),"",tmp);				
		tmp = str_replace(",,",",",tmp);
		if(tmp.substring(0,1)==',') tmp = tmp.substring(1,tmp.length)
		else if(tmp.substring(tmp.length-1,tmp.length)==',') tmp = tmp.substring(0,tmp.length-1);
		tmp = tmp.split(',');
		eval("quickBar"+type+"Check = tmp")
		$('quickBar'+type+'Check_'+num).src = shop_skin+'img/bottom/icon_check.gif';
	}
	else {	
		if(tmp=='') tmp[0] = num;
		else tmp[tmp.length] = num;
		$('quickBar'+type+'Check_'+num).src = shop_skin+'img/bottom/icon_check_on.gif';
	}
	if(type=='Cart') quickBarCartSum();
}

function quickBarCheckAll(type) {
	if(!type) return false;
	var tmp = eval("quickBar"+type+"Check");
	var obj = $('q'+type+'Box');	
	
	tmp2 = obj.getElementsByTagName('IMG');
	
	for(i=0;i<tmp2.length;i++) {
		if(tmp2[i].id.indexOf('Check_')!=-1) {
			tmp3 = str_replace('quickBar'+type+'Check_',"",tmp2[i].id);
			if(!in_array(tmp3,tmp) && tmp3!='') {	
				if(tmp=='') tmp[0] = tmp3;
				else tmp[tmp.length] = tmp3;				
				$('quickBar'+type+'Check_'+tmp3).src = shop_skin+'img/bottom/icon_check_on.gif';
			}	
		}		
	}		
	if(type=='Cart') quickBarCartSum();
}

function quickBarCheckDel(type) {
	if(!type) return false;
	var tmp = eval("quickBar"+type+"Check");
	tmp = tmp.join(",");	
	
	if(tmp=='' || !tmp) {
		alert("체크된 상품이 없습니다");
		return;
	}

	aObj = new AjaxObject;  
	if(type=='Today') {
		aObj.getHttpRequest("php/todayDel2.php?uid="+tmp, "quickBarDelGoodsOk","data"); 
	}
	else if(type=='Cart') {
		aObj.getHttpRequest("php/cartOk.php?mode=del&uid="+tmp, "quickBarDelGoodsOk","data"); 
	}
	else if(type=='Wish') {
		aObj.getHttpRequest("php/wishOk.php?mode=del&uid="+tmp, "quickBarDelGoodsOk","data"); 
	}	
}

function quickBarDelGoodsOk(data) {
	if(data['item']=='true' && data['uid']) {				
		var type = data['type'];
		var obj = $('q'+type+'Box');		
		var obj2 = eval('q'+type+'BoxDiv');
		var tmp = data['uid'].split(',');
		var tmp2 = eval("quickBar"+type+"Check");
		
		tmp2 = tmp2.join(",");		
		for(i=0;i<tmp.length;i++) {			
			obj.removeChild($('qBar'+type+'_'+tmp[i]));					
			obj2.tcnt--;
				
			tmp2 = str_replace(eval("'"+tmp[i]+"'"),"",tmp2);				
			tmp2 = str_replace(",,",",",tmp2);
			if(tmp2.substring(0,1)==',') tmp2 = tmp2.substring(1,tmp2.length)
			else if(tmp2.substring(tmp2.length-1,tmp2.length)==',') tmp2 = tmp2.substring(0,tmp2.length-1);			
		}
		tmp2 = tmp2.split(',');
		eval("quickBar"+type+"Check = tmp2")

		obj.style.width = parseInt(obj.style.width) - quickBarGoodsWidth + 'px';		
				
		if(obj2.tcnt==0) {
			$('qBar'+type+'_no').style.display = 'block';
			obj2.count = 1;
			$('quickBar'+type+'Cnt').innerHTML = "(0)";
		}
		else {
			if(obj2.count>1) obj2.scroll('left');
			else {
				obj2.count--;							
				if(obj2.count<=(obj2.tcnt-obj2.vcnt)) $(obj2.name+'ImgNext').src = obj2.img2.src;
				else $(obj2.name+'ImgNext').src = obj2.img4.src;
			}
			$('quickBar'+type+'Cnt').innerHTML = '('+obj2.tcnt+')';
		}
	}
	if(type=='Cart') {
		tmps = window.location.href.split('channel=');
		if(tmps[1]=='cart' || tmps[1]=='order_form') { 
			window.location.reload();
			return;
		}

		quickBarCartSum();
		quickBarCartSumAll();
	}
	else if(type=='Wish') {
		tmps = window.location.href.split('channel=');
		if(tmps[1]=='mypage' || tmps[1]=='wish') { 
			window.location.reload();
			return;
		}

		quickBarCartSum();
		quickBarCartSumAll();
	}
	if(type=='Today' && $("rTodayBox") && data['callback']!='N') {
		data2 = new Array();
		for(i=0;i<tmp.length;i++) {		
			data2['item'] = 'true';
			tmp2 = tmp[i]+'';
			data2['cate'] = tmp2.substring(0,12);
			data2['number'] = tmp2.substring(12,tmp2.length);	
			data2['callback'] = 'N';
			delGoodsOk2(data2);
		}
	}
}

function quickBarCheckWish() {
	if(ckLogins=='N') {
		messageBox.show('먼저 로그인을 하시기 바랍니다.','280','120','로그인 확인');		
		return;
	}

	if(quickBarTodayCheck=='' || !quickBarTodayCheck) {
		alert("체크된 상품이 없습니다");
		return;
	}
		
	for(i=0;i<quickBarTodayCheck.length;i++) {
		if(quickBarTodayCheck[i]) quickBarCheckWishAdd(quickBarTodayCheck[i]);
	}
	
}

function quickBarCheckWishAdd(uid) {
	if(ckLogins=='N') {
		messageBox.show('먼저 로그인을 하시기 바랍니다.','280','120','로그인 확인');	
		return;
	}
	aObj = new AjaxObject;  
	aObj.getHttpRequest("php/wishOk.php?uid="+uid, "quickBarWishOk","data"); 	
}


function quickBarWishOk(data){
	if(data['item']=='false') {
		alert('관심상품 담기에 실패 했습니다.');
	} 
	else if(data['item']=='true') {			
			
		obj = $('qWishBox');
		tmps = quickBarGoodsWishAdd;

		tmps = str_replace("[IMAGE]",data['image'],tmps);
		tmps = str_replace("[TYPE]",'Wish',tmps);
		tmps = str_replace("[LINK]",data['link'],tmps);
		tmps = str_replace("[NAME]",data['name'],tmps);
		tmps = str_replace("[UID]",data['uid'],tmps);
		tmps = str_replace("[PRICE]",data['price'],tmps);			
		tmps = str_replace("[PRICE2]",data['price2'],tmps);	
			
		if(qWishBoxDiv.tcnt==0) {
			$(qBarWish_no).style.display = "none";
		}
		
		tag = document.createElement('DIV');
		tag.id = "qBarWish_"+data['uid'];
		tag.className = "quickBarGoodsBox";
		tag.innerHTML = tmps;
		obj.appendChild(tag);								
		
		obj.style.width = parseInt(obj.style.width) + quickBarGoodsWidth + 'px';		
		qWishBoxDiv.tcnt++;
		if(qWishBoxDiv.tcnt>qWishBoxDiv.vcnt) qWishBoxDiv.scroll('right');			
		$('quickBarWishCnt').innerHTML = '('+qWishBoxDiv.tcnt+')';
		quickBarChange2('Wish');
	} 
	else {
		alert(data['item']);
	}
}

function quickBarCheckCartAdd(uid) {
	aObj = new AjaxObject;  
	aObj.getHttpRequest("php/wishOk.php?mode=info&uid="+uid, "quickBarWishInfo","data"); 	
}

function quickBarWishInfo(data){
	if(data['item']=='false') {
		alert('장바구니 담기에 실패 했습니다.');
	} 
	else if(data['item']=='true') {						
		gToCart.cartAdd(data['cate'],data['number']);
	} 
}


function quickBarCheckCart(type) {
	var tmp = eval("quickBar"+type+"Check");

	if(tmp=='' || !tmp) {
		alert("체크된 상품이 없습니다");
		return;
	}
	
	for(i=0;i<tmp.length;i++) {
		if(tmp[i]) {
			if(type=='Today') {				
				tmp2 = tmp[i]+'';								
				cate = tmp2.substring(0,12);
				number = tmp2.substring(12,tmp2.length);			
				gToCart.cartAdd(cate,number);
			}
			else {					
				quickBarCheckCartAdd(tmp[i]);
			}
		}			
	}	
}

function quickBarCartOk(data){
	if(data['item']=='false') {
		alert('장바구니 담기에 실패 했습니다.');
	} 
	else if(data['item']=='true') {						
		if(!data['name']) {
			messageBox.show('장바구니에 동일한 상품이 담겨있어 장바구니 수량을 변경 하였습니다.','280','120','장바구니 확인');
			tmp = eval("document.qCartForm.qty_"+data['uid'])				
			tmp.value = data['qty'];
			$("qucilBarCartQty_"+data['uid']).innerHTML = data['qty'];
			quickBarChange2('Cart');	
			quickBarCartSum();
			quickBarCartSumAll();
			return;
		}
		
		obj = $('qCartBox');
		tmps = quickBarGoodsCartAdd;

		tmps = str_replace("[IMAGE]",data['image'],tmps);
		tmps = str_replace("[TYPE]",'Cart',tmps);
		tmps = str_replace("[LINK]",data['link'],tmps);
		tmps = str_replace("[NAME]",data['name'],tmps);
		tmps = str_replace("[UID]",data['uid'],tmps);
		tmps = str_replace("[PRICE]",data['price'],tmps);			
		tmps = str_replace("[PRICE2]",data['price2'],tmps);					
		tmps = str_replace("[QTY]",data['qty'],tmps);	
		tmps = str_replace("[OPRICE]",data['oprice'],tmps);			
		tmps = str_replace("[QTY2]",data['qty'],tmps);		
		tmps = str_replace("[OPTION]",data['option'],tmps);	
				
		if(qCartBoxDiv.tcnt==0) {			
			document.getElementById('qBarCart_no').style.display = "none";
		}

		tag = document.createElement('DIV');
		tag.id = "qBarCart_"+data['uid'];
		tag.className = "quickBarGoodsBox";
		tag.innerHTML = tmps;
		obj.appendChild(tag);								
		
		if(data['option']=='N') {			
			$("quickBarOptionImg2_"+data['uid']).style.display = 'block';
		}

		obj.style.width = parseInt(obj.style.width) + quickBarGoodsWidth + 'px';		
		qCartBoxDiv.tcnt++;
		if(qCartBoxDiv.tcnt>qCartBoxDiv.vcnt) qCartBoxDiv.scroll('right');			
		$('quickBarCartCnt').innerHTML = '('+qCartBoxDiv.tcnt+')';
		quickBarChange2('Cart');
		quickBarCartSumAll();
	} 
	else {
		alert(data['item']);
	}
}

function quickBarCartSum() {
	var tmp = quickBarCartCheck;	
	var sum = 0;
	var form = document.qCartForm;
	for(i=0;i<tmp.length;i++) {	
		if(tmp[i]>0) {
			sum += eval("form.price_"+tmp[i]+".value") * eval("form.qty_"+tmp[i]+".value");
		}
	}
	$("quickBarCartSum1").innerHTML = number_format(sum)+'원';	
}

function quickBarCartSumAll() {
	var sum = 0;
	var form = document.qCartForm;
	
	for (j=0;j<form.elements.length;j++) {
		if(form.elements[j].name.indexOf('price_')!=-1) { 
			tmp = form.elements[j].name.substring(6,form.elements[j].name.length);
			sum += eval("form.price_"+tmp+".value") * eval("form.qty_"+tmp+".value");
		}
	}
	$("quickBarCartSum2").innerHTML = number_format(sum)+'원';
}

function quickBarOrder(){
	var form = document.qCartForm;
	for (j=0;j<form.elements.length;j++) {
		if(form.elements[j].name.indexOf('option_')!=-1) { 
			if(form.elements[j].value=='N') {
				alert("옵션 선택을 하지 않은 상품이 존재 합니다. 옵션을 선택 하시기 바랍니다.");	
				return false;	
			}
		}
	}

	if(ckLogins=="N") {		
		pLightBox.show('php/plogin.php','iframe',450,380,'■ 로그인','20');
	}
	else window.location.href='?channel=order_form';
}

function quickBarOrderSec() {
	var form = document.qCartForm;
	if(quickBarCartCheck=='' || !quickBarCartCheck) {
		alert("체크된 상품이 없습니다");
		return;
	}
	
	form.action = "php/cart_ok.php?type=qorder";
	form.uid.value = quickBarCartCheck.join(",");

	if(ckLogins=="N") {		
		pLightBox.show('php/plogin.php?type=direct2','iframe',450,380,'■ 로그인','20');
		return false;
	}
	else form.submit();
}

function quickBarChangeOption(){	
	f = document.goodsForm;
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
	aObj.getHttpRequest("../php/cartOk.php?mode=option&uid="+f.uid.value+p_op_arr, "quickBarChangeOptionOk1","data"); 	
}

function quickBarChangeOptionOk1(data){	
	if(data['item']=='true') {		
		parent.quickBarChangeOptionOk2(data);
		parent.pLightBox.hide();
	} 
	else {
		alert(data['item']);
	}
}

function quickBarChangeOptionOk2(data){	
	if(data['item']=='true') {	
		$("quickBarCartPrice_"+data['uid']).innerHTML = number_format(data['price']);
		tmp = eval("document.qCartForm.price_"+data['uid'])				
		tmp.value = data['price'];
		tmp = eval("document.qCartForm.option_"+data['uid'])				
		tmp.value = 'Y';
		$("quickBarOptionImg1_"+data['uid']).style.display = "block";
		$("quickBarOptionImg2_"+data['uid']).style.display = "none";
		quickBarCartSum();
		quickBarCartSumAll();
	} 
}

/*********************** @QUICK BAR 관련 ******************************/


function itemScrollBest(name,tcnt,vcnt,height) {	
	this.name = name+'Div';
	this.oname = name;
	this.obj = $(name).style;
    this.count = 1;
	this.tcnt = parseInt(tcnt);  
	this.vcnt = parseInt(vcnt);  
	this.destination = '';
	this.height = parseInt(height);  
	this.dirt	= null;
	this.speed = 10; // 간격 조정(속도)
	
	this.scroll = function(direct,cnt) {        		
		if(this.tcnt<this.vcnt) return;		
		if(direct) this.dirt = direct;
		var xOffset;

		if(!this.destination) {			
			if(this.dirt=='down'){
				if(cnt>1) this.count += (cnt-1);
				if(this.count>(this.tcnt-this.vcnt)) return;
				this.destination = -(this.count*this.height);								
			}
			else {				
				if(cnt>1) this.count -= (cnt-1);
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
			

			for(i=1;i<=this.tcnt;i++) {
				$("tab"+this.oname+i).src = str_replace("_on","",$("tab"+this.oname+i).src);
			}
			$("tab"+this.oname+this.count).src = $("tab"+this.oname+this.count).src.substr(0, $("tab"+this.oname+this.count).src.length-4) + "_on.gif";			
		} 
		else window.setTimeout(this.name+".scroll()",this.speed);
    }  
	
	this.move = function(ns) {	
		if(ns>this.count) {			
			this.scroll('down',ns-this.count);	
		}
		else {
			this.scroll('up',this.count-ns);	
		}		
	}
}