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

/***************** 전화번호 체크 스크립트 *********************/
function ck_tel(num1,num2) {
	var tel = "tel"+num1+num2;
	var tel_value = eval("f."+tel+".value")
	if(!tel_value) return false;
	if(isNumber(tel,"tels"+num1) == 1) return false;  
   
	if(num2==1) {  
		if(tel_value.length<2||tel_value.length>4) {
			document.getElementById("tels"+num1).innerHTML= '전화번호 앞자리를 다시 입력해 주세요.';		
			eval("f."+tel+".value = ''")
			eval("f."+tel+".focus()")		
			return false;
		} 
	} 
	else if(num2==2) {
		if(tel_value.length<3||tel_value.length>4) {
			document.getElementById("tels"+num1).innerHTML= '전화번호 가운데자리를 다시 입력해 주세요.';
			eval("f."+tel+".value = ''")
			eval("f."+tel+".focus()")		
			return false;
		} 
	} 
	else if(num2==3) {
		if(tel_value.length != 4) {
			document.getElementById("tels"+num1).innerHTML= '전화번호 뒷자리를 다시 입력해 주세요.';
			eval("f."+tel+".value = ''")
			eval("f."+tel+".focus()")		
			return false;
		} 
	} 
	document.getElementById("tels"+num1).innerHTML= '';
	return; 
}

/***************** 이동전화번호 체크 스크립트 *********************/
function ck_phone(num1,num2) {
	if(!f) f = document.joinForm;
	var phone = "phone"+num1+num2;
	var phone_value = eval("f."+phone+".value")
		
	if(!phone_value) return false;
	if(isNumber(phone,"phones"+num1) == 1) return false;  
   
	if(num2==1) {  
		if(phone_value.length<3||phone_value.length>4) {
			document.getElementById("phones"+num1).innerHTML= '이동전화번호 앞자리를 다시 입력해 주세요.';		
			eval("f."+phone+".value = ''")
			eval("f."+phone+".focus()")		
			return false;
		} 
	} 
	else if(num2==2) {
		if(phone_value.length<3||phone_value.length>4) {
			document.getElementById("phones"+num1).innerHTML= '이동전화번호 가운데자리를 다시 입력해 주세요.';
			eval("f."+phone+".value = ''")
			eval("f."+phone+".focus()")		
			return false;
		} 
	} 
	else if(num2==3) {
		if(phone_value.length != 4) {
			document.getElementById("phones"+num1).innerHTML= '이동전화번호 뒷자리를 다시 입력해 주세요.';
			eval("f."+phone+".value = ''")
			eval("f."+phone+".focus()")		
			return false;
		} 
	} 
	document.getElementById("phones"+num1).innerHTML= '';
	return; 
}

/***************** 주소 체크 스크립트 *********************/
function ck_zip(num,cnt,obj) {
	if(num==1) {  
		if(!obj.value) return false;
		if(isNumber('zip'+cnt+'1','zips'+cnt) == 1) return false;  
		if(obj.value.length<3||obj.value.length>4) {
			document.getElementById("zips"+cnt).innerHTML= "우편번호 앞자리를 다시 입력해 주세요.";
			obj.value = '';
			obj.focus();
			return false;
		} 
	} 
	else if(num==2) {
		if(!obj.value) return false;
		if(isNumber('zip'+cnt+'2','zips'+cnt) == 1) return false;  
		if(obj.value.length<3||obj.value.length>4) {
			document.getElementById("zips"+cnt).innerHTML = "우편번호 뒷자리를 다시 입력해 주세요.";
			obj.value='';
			obj.focus();
			return false;
		} 
	} 
	document.getElementById("zips"+cnt).innerHTML = "";
	return; 
}


/***************** 이메일 체크 스크립트 *********************/
function ck_email() {
	if(!f) f = document.joinForm;	
    if(!f.email1.value || !f.email2.value) return false;
	   
	function error(){
        document.getElementById("emails").innerHTML = "잘못된 이메일 주소입니다.";
        f.email1.value='';
		f.email2.value='';
		f.email.value='';
		f.email1.focus();
		return false;
	}

	f.email.value = f.email1.value+'@'+f.email2.value;
	
	var email = f.email.value;
	var pattern = /^(.+)@(.+)$/;
    var atom = "\[^\\s\\(\\)<>#@,;:!\\\\\\\"\\.\\[\\]\]+";
    var word="(" + atom + "|(\"[^\"]*\"))";
    var user_pattern = new RegExp("^" + word + "(\\." + word + ")*$");
    var ip_pattern = /^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
    var domain_pattern = new RegExp("^" + atom + "(\\." + atom +")*$");

    var ck_email = email.match(pattern);
    if (!ck_email || !ck_email[1].match(user_pattern))  return error();
    var ip = ck_email[2].match(ip_pattern);
    if (ip) {
        for (var i=1; i<5; i++) if (ip[i] > 255) return error();
    } 
	else {
        if (!ck_email[2].match(domain_pattern)) return error();
		var domain = ck_email[2].match(new RegExp(atom,"g"));
        if (domain.length<2) return error();
        if (domain[domain.length-1].length<2 || domain[domain.length-1].length>3) return error();
	}
    document.getElementById("emails").innerHTML = '';
	return; 
} 

/***************** 아이디 체크 스크립트 *********************/
function ck_id() {
	if(!f.id.value) return false;
	if(f.id.value.length<4||f.id.value.length>12) {
		document.getElementById("ids").innerHTML="아이디는 4 ~ 12 자여야 합니다.";
		f.id.value = ""; 
		f.id.focus();
		return false;
	} 
	var valid = "abcdefghijklmnopqrstuvwxyz0123456789_"; 
	var startChar = "abcdefghijklmnopqrstuvwxyz"; 
	var temp; 
	f.id.value = f.id.value.toLowerCase(); 
	temp = f.id.value.substring(0,1); 
	if (startChar.indexOf(temp) == "-1") {
		document.getElementById("ids").innerHTML="아이디의 첫 글자는 영문이어야 합니다.";
		f.id.value = ""; 
		f.id.focus();
		return false;
	}
	for (var i=0; i<f.id.value.length; i++) { 
		temp = "" + f.id.value.substring(i, i+1); 
		if (valid.indexOf(temp) == "-1") { 
			document.getElementById("ids").innerHTML="아이디는 영문과 숫자, _ 로만 해야 합니다.";
			f.id.value = "";
			f.id.focus();
			return false;
		}
	} 

	document.getElementById("ids").innerHTML="아이디 중복 검사중입니다!";

	aObj = new AjaxObject;             
	if(typeof(shop_path)!='undefined') aObj.getHttpRequest("/"+shop_path+"php/checkId.php?sid="+f.id.value, "idCkOk","data"); 	
	else aObj.getHttpRequest("php/checkId.php?sid="+f.id.value, "idCkOk","data"); 	
}		

function idCkOk(data){
	if(data['item']=="true") {
		document.getElementById("ids").innerHTML = "사용할 수 있는 아이디 입니다.";
		f.passwd.focus();
	}
	else if(data['item']=="false") {
		document.getElementById("ids").innerHTML = "이미 사용중 입니다.";
		f.id.value = "";
		f.id.focus();
	}
	else document.getElementById("ids").innerHTML = "시스템 에러 입니다.";
}

/***************** 주민번호 체크 스크립트 *********************/
function ck_jumin1() {
    if(!f.jumin1.value) return false;
	var yy   = f.jumin1.value.substr(0,2);    // 년도
    var mm   = f.jumin1.value.substr(2,2);    // 월
    var dd   = f.jumin1.value.substr(4,2);   // 일
    
	if(isNumber('jumin1','jumins') == 1) return false;  
     
	if (f.jumin1.value.length != 6) {
		document.getElementById("jumins").innerHTML="주민번호 앞자리는 6자리여야 합니다.";
	    f.jumin1.value = '';
		f.jumin1.focus();
	    return false;
	} 
	if (yy < "00" || yy > "99" || mm < "01" || mm > "12" || dd < "01" || dd > "31") {
        document.getElementById("jumins").innerHTML="주민번호 앞자리를 다시 입력하세요.";
	    f.jumin1.value = '';
		f.jumin1.focus();
		return false;
    }
    document.getElementById("jumins").innerHTML="";
 	return;
}

function ck_jumin2() {
    if(!f.jumin2.value) return false;
	var genda  = f.jumin2.value.substr(0,1);    // 성별

	if(isNumber('jumin2','jumins') == 1) return false;  

    if (f.jumin2.value.length != 7) {
		document.getElementById("jumins").innerHTML="주민번호 뒷자리는 7자리여야 합니다.";
	    f.jumin2.value = '';
		f.jumin2.focus();
	    return false;
	} 
	if (genda < "1" || genda > "4") {
       document.getElementById("jumins").innerHTML="주민번호 뒷자리자리를 다시 입력하세요.";
	   f.jumin2.value = '';
	   f.jumin2.focus();
       return false;
    }
	if (!isSSN(f.jumin1.value, f.jumin2.value)) {
       document.getElementById("jumins").innerHTML="주민번호를 검토한 후, 다시 입력하세요.";
	   f.jumin1.value = '';
	   f.jumin2.value = '';
	   f.jumin1.focus();
       return false;
	 }
	 document.getElementById("jumins").innerHTML = "";

	if(typeof(f.bir1)!='undefined' && typeof(f.bir2)!='undefined' && typeof(f.bir3)!='undefined') {
		if(f.jumin1.value){
			if(f.jumin2.value.substr(0,1) =='1' || f.jumin2.value.substr(0,1) =='2') var yy1 = '19'+f.jumin1.value.substr(0,2);
			else if(f.jumin2.value.substr(0,1) =='3' || f.jumin2.value.substr(0,1) =='4') var yy1 = '20'+f.jumin1.value.substr(0,2);
			f.bir1.value = yy1;
			f.bir2.value = f.jumin1.value.substr(2,2);
			f.bir3.value = f.jumin1.value.substr(4,2);
		}
	}

	if(typeof(f.sex)!='undefined') {
		if(f.jumin1.value){
			if(f.jumin2.value.substr(0,1) =='1' || f.jumin2.value.substr(0,1) =='3') f.sex[0].checked= true;
			else if(f.jumin2.value.substr(0,1) =='2' || f.jumin2.value.substr(0,1) =='4') f.sex[1].checked= true;
		}	
	}

	return;
}

function isSSN(s1, s2) {
    n = 2;
    sum = 0;
    for (i=0; i<s1.length; i++)
        sum += parseInt(s1.substr(i, 1)) * n++;
    for (i=0; i<s2.length-1; i++) {
        sum += parseInt(s2.substr(i, 1)) * n++;
		if (n == 10) n = 2;
    }
    c = 11 - sum % 11;
    if (c == 11) c = 1;
    if (c == 10) c = 0;
    if (c != parseInt(s2.substr(6, 1))) return false;
    else return true;
}

/***************** 홈페이지 체크 스크립트 *********************/
function ck_home() {
    if(!f.homepage.value) return false;
	   
	function error(){
        document.getElementById("homes").innerHTML = "잘못된 홈페이지 주소입니다.";
        f.homepage.value='';
		f.homepage.focus();
		return false;
	}

	var home = f.homepage.value;
	home = home.replace('http://','');
	var atom = "\[^\\s\\(\\)<>#@,;:!\\\\\\\"\\.\\[\\]\]+";
    var ip_pattern = /^\[(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})\]$/;
    var domain_pattern = new RegExp("^" + atom + "(\\." + atom +")*$");

    var ip = home.match(ip_pattern);
    if (ip) {
        for (var i=1; i<5; i++) if (ip[i] > 255) return error();
    } 
	else {
        if (!home.match(domain_pattern)) return error();
        var domain = home.match(new RegExp(atom,"g"));
        if (domain.length<2) return error();
        if (domain[domain.length-1].length<2 || domain[domain.length-1].length>3) return error();
    }
    document.getElementById("homes").innerHTML = '';
	return; 
} 

/***************** 생년월일 체크 스크립트 *********************/
function ck_bir(num) {
	var now = new Date();
    var year = now.getFullYear();

	if(num==1) {  
		if(!f.bir1.value) return false;
		if(isNumber('bir1','birs') == 1) return false;  
		if(f.bir1.value<1900 || f.bir1.value>year) {
			document.getElementById("birs").innerHTML="년도를 다시 입력해주세요.";
			f.bir1.value='';
			f.bir1.focus();
			return false;
		 } 
	} 
	else if(num==2) {
		if(!f.bir2.value) return false;
		if(isNumber('bir2','birs') == 1) return false;  
		if(f.bir2.value<1 || f.bir2.value>12) {
			document.getElementById("birs").innerHTML="달을 다시 입력해주세요.";
			f.bir2.value='';
			f.bir2.focus();
			return false;
		 } 
	} 
	else if(num==3) {
		if(!f.bir3.value) return false;
		if(isNumber('bir3','birs') == 1) return false;  
		if(f.bir3.value<1 || f.bir3.value>31) {
			document.getElementById("birs").innerHTML="일을 다시 입력해주세요.";
			f.bir3.value='';
			f.bir3.focus();
			return false;
		 } 
	} 
    document.getElementById("birs").innerHTML = '';
	return; 
} 

/***************** 비밀번호 체크 스크립트 *********************/
function ck_pw1() {
	if(!f.passwd.value) return false;
	if(f.passwd.value.length<4||f.passwd.value.length>12) {
		document.getElementById("passwd1s").innerHTML="비밀번호는 4 ~ 12 자여야 합니다.";
		f.passwd.focus();
		return false;
	} 
	var valid = "abcdefghijklmnopqrstuvwxyz0123456789_"; 
	var temp; 
	f.passwd.value = f.passwd.value.toLowerCase(); 
	for (var i=0; i<f.passwd.value.length; i++) { 
		temp = "" + f.passwd.value.substring(i, i+1); 
		if (valid.indexOf(temp) == "-1") { 
			document.getElementById("passwd1s").innerHTML="비밀번호는 영문과 숫자, _ 로만 해야 합니다.";
			f.passwd.value = "";
			f.passwd.focus();
			return false;
		}
	} 
	document.getElementById("passwd1s").innerHTML = "";
	return;
}

function ck_pw2() {	
	if(!f.repasswd.value) return false;
	if(f.passwd.value != f.repasswd.value) { 
		document.getElementById("passwd2s").innerHTML="패스워드가 일치하지 않습니다.";
		f.passwd.value = '';
		f.repasswd.value = '';
		f.passwd.focus();
		return false; 
	}
    document.getElementById("passwd2s").innerHTML="";
	return;
}