function go(url, msg){
	if(msg) alert(msg);
	if(url) location.href=url;
}

function getData(Url){
	var returnValue = "";

	$.ajax({
		type:"GET",
		async:false,
		url:Url,
		success : function(data) {
			returnValue = data;
		}
	});
	return returnValue;
}

function getJson(Url){
	var returnValue = "";

	$.ajax({
		type:"GET",
		dataType: "json", 
		async:false,
		url:Url,
		success : function(data) {
			returnValue = data;
		}
	});
	return returnValue;
}