<div id="contentsView">
<form name="myForm" method="POST" onSubmit="return chkForm();">
	<ul id="memberInfo">
		<li class="head">기본정보</li>
		<li class="description">* E-Mail을 변경 하시면 새로 인증을 받으셔야 합니다.</li>
		<li>
			<div class="title">이름</div>
			<div class="contents"><?=$myinfo['name']?></div>
		</li>
		<li>
			<div class="title">E-Mail</div>
			<div class="contents"><input type="text" name="email" value="<?=$myinfo['email']?>"/></div>
		</li>
		<li class="head">비밀번호 변경</li>
		<li>
			<div class="title">변경할 비밀번호</div>
			<div class="contents"><input type="password" name="password" value=""/></div>
		</li>
		<li>
			<div class="title">비밀번호 재입력</div>
			<div class="contents"><input type="password" name="password_re" value=""/></div>
		</li>
	</ul>

	<div style="margin-top: 10px;text-align: center;"> 
		<input type="submit" value="저장하기" class="button basic"/> 
		<input type="button" value="취소하기" class="button basic" onClick="history.go(-1);"/>
	</div>
</form>
</div>

<script type="text/javascript">
	var pre_email = "<?=$myinfo['email']?>";

	function IsEmail(email) {
		var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		if(!regex.test(email)) {
		   return false;
		}else{
		   return true;
		}
	}

	function chkForm(){
		var f = document.myForm;

		if(f.email.value == ""){
			alert("E-Mail주소를 입력해 주세요!");
			return false;
		}

		if(f.email.value != pre_email){
			if(!confirm("E-Mail을 변경하시면 새로 인증을 받으셔야 합니다.\r진행 하시겠습니까?")){
				return false;
			}
		}

		if(IsEmail(f.email.value) == false){
			alert("E-mail주소가 올바르지 않습니다!");
			return false;
		}

		if(f.password.value != "" || f.password_re.value != ""){
			if(!f.password.value || !f.password_re.value){
				alert("변경하실 비밀번호를 입력해 주세요!");
				return false;
			}

			if(f.password.value != f.password_re.value){
				alert("입력하신 비밀번호가 동일하지 않습니다!");
				return false;
			}
		}

		return true;

	}
</script>