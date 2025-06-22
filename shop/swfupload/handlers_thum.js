function fileQueueError(fileObj, error_code, message) {
	try {
		var error_name = "";
		switch(error_code) {
			case SWFUpload.ERROR_CODE_QUEUE_LIMIT_EXCEEDED:
				error_name = "You have attempted to queue too many files.";
			break;
		}

		if (error_name !== "") {
			alert(error_name);
			return;
		}

		switch(error_code) {
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				image_name = "zerobyte.gif";
			break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				image_name = "toobig.gif";
			break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			default:
				alert(message);
				image_name = "error.gif";
			break;
		}

		AddImage("images/" + image_name);

	} catch (ex) { this.debug(ex); }

}

function fileDialogComplete(num_files_queued) {
	try {
		if (num_files_queued > 0) {
			this.startUpload();
		}
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadProgress(fileObj, bytesLoaded) {

	try {
		var percent = Math.ceil((bytesLoaded / fileObj.size) * 100)

		var progress = new FileProgress(fileObj,  this.customSettings.upload_target);
		progress.SetProgress(percent);
		if (percent === 100) {
			progress.SetStatus("Creating List...");
			progress.ToggleCancel(false);
			progress.ToggleCancel(true, this, fileObj.id);
		} else {
			progress.SetStatus("Uploading...");
			progress.ToggleCancel(true, this, fileObj.id);
		}
	} catch (ex) { this.debug(ex); }
}

function uploadSuccess(fileObj, server_data) {
	try {
		AddList(server_data);

		var progress = new FileProgress(fileObj,  this.customSettings.upload_target);

		progress.SetStatus("List Created.");
		progress.ToggleCancel(false);


	} catch (ex) { this.debug(ex); }
}

function uploadComplete(fileObj) {
	try {
		/*  I want the next upload to continue automatically so I'll call startUpload here */
		if (this.getStats().files_queued > 0) {
			this.startUpload();
		} else {
			var progress = new FileProgress(fileObj,  this.customSettings.upload_target);
			progress.SetComplete();
			progress.SetStatus("All Files received.");
			progress.ToggleCancel(false);
			setTimeout(function() { FadeOut('100');},1500);
		}
	} catch (ex) { this.debug(ex); }
}

function uploadError(fileObj, error_code, message) {
	var image_name =  "error.gif";
	try {
		switch(error_code) {
			case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
				try {
					var progress = new FileProgress(fileObj,  this.customSettings.upload_target);
					progress.SetCancelled();
					progress.SetStatus("Stopped");
					progress.ToggleCancel(true, this, fileObj.id);
				}
				catch (ex) { this.debug(ex); }
			case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
				image_name = "uploadlimit.gif";
			break;
			default:
				alert(message);
			break;
		}

		AddImage("images/" + image_name);

	} catch (ex) { this.debug(ex); }

}


/* ******************************************
 *	FileProgress Object
 *	Control object for displaying file info
 * ****************************************** */

function FileProgress(fileObj, target_id) {
	this.file_progress_id = "divFileProgress";

	this.fileProgressWrapper = document.getElementById(this.file_progress_id);
	if (!this.fileProgressWrapper) {
		this.fileProgressWrapper = document.createElement("div");
		this.fileProgressWrapper.className = "progressWrapper";
		this.fileProgressWrapper.id = this.file_progress_id;

		this.fileProgressElement = document.createElement("div");
		this.fileProgressElement.className = "progressContainer";

		var progressCancel = document.createElement("a");
		progressCancel.className = "progressCancel";
		progressCancel.href = "#";
		progressCancel.style.visibility = "hidden";
		progressCancel.appendChild(document.createTextNode(" "));

		var progressText = document.createElement("div");
		progressText.className = "progressName";
		progressText.appendChild(document.createTextNode(fileObj.name));

		var progressBar = document.createElement("div");
		progressBar.className = "progressBarInProgress";

		var progressStatus = document.createElement("div");
		progressStatus.className = "progressBarStatus";
		progressStatus.innerHTML = "&nbsp;";

		this.fileProgressElement.appendChild(progressCancel);
		this.fileProgressElement.appendChild(progressText);
		this.fileProgressElement.appendChild(progressStatus);
		this.fileProgressElement.appendChild(progressBar);

		this.fileProgressWrapper.appendChild(this.fileProgressElement);

		document.getElementById(target_id).appendChild(this.fileProgressWrapper);
		FadeIn(this.fileProgressWrapper, 0);

	} else {
		if (this.fileProgressWrapper.filters) {
			try {
				this.fileProgressWrapper.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 100;
			} catch (e) {
				this.fileProgressWrapper.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 100 + ')';
			}
		} else {
			this.fileProgressWrapper.style.opacity = 1;
		}

		this.fileProgressElement = this.fileProgressWrapper.firstChild;
		this.fileProgressElement.childNodes[1].firstChild.nodeValue = fileObj.name;
	}

	this.height = this.fileProgressWrapper.offsetHeight;

}
FileProgress.prototype.SetProgress = function(percentage) {
	this.fileProgressElement.className = "progressContainer greens";
	this.fileProgressElement.childNodes[3].className = "progressBarInProgress";
	this.fileProgressElement.childNodes[3].style.width = percentage + "%";
}
FileProgress.prototype.SetComplete = function() {
	this.fileProgressElement.className = "progressContainer grays";
	this.fileProgressElement.childNodes[3].className = "progressBarComplete";
	this.fileProgressElement.childNodes[3].style.width = "0px";
}
FileProgress.prototype.SetError = function() {
	this.fileProgressElement.className = "progressContainer reds";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "0px";

}
FileProgress.prototype.SetCancelled = function() {
	this.fileProgressElement.className = "progressContainer";
	this.fileProgressElement.childNodes[3].className = "progressBarError";
	this.fileProgressElement.childNodes[3].style.width = "0px";

}
FileProgress.prototype.SetStatus = function(status) {
	this.fileProgressElement.childNodes[2].innerHTML = status;
}

FileProgress.prototype.ToggleCancel = function(show, upload_obj, file_id) {
	this.fileProgressElement.childNodes[0].style.visibility = show ? "visible" : "hidden";
	if (upload_obj) {
		this.fileProgressElement.childNodes[0].onclick = function() { upload_obj.cancelUpload(); return false; };
	}
}

function AddList(src) {
	var new_list = document.createElement("div");
	
	r_data = src.split("|");

	if(!r_data[1]) {
		alert(src);		
		return false;;
	}

	FadeOut(100,document.getElementById('listDefine'));
	
	new_list.style.margin = "2px";
	new_list.id = "list_"+r_data[1];
	new_list.className = "thumList";
	new_list.innerHTML = "<div><img src='../../image/other_img/"+ori_dir+"/"+r_data[0]+"' align='absmiddle' class='thumImg'></div><div class='thumName'>"+urldecode(r_data[1])+"<br /><font class='thumName2'>"+r_data[2]+"</font> <a href='#' onclick='DelList(\""+r_data[1]+"\");return false;'><img src='../../swfupload/icon/del.gif' align='absmiddle' border='0' /></a></div>";
    
	document.getElementById("fileList").appendChild(new_list);
	
	if (new_list.filters) {
		try {
			new_list.filters.item("DXImageTransform.Microsoft.Alpha").opacity = 0;
		} catch (e) {			
			new_list.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + 0 + ')';
		}
	} else {
		new_list.style.opacity = 0;
	}    

	FadeIn(new_list, 0);

	fSizes =  Math.round(parseFloat(document.getElementById('fSize').innerHTML)*100 + (r_data[3]/10.24))/100;
	document.getElementById('fSize').innerHTML = fSizes;
     
	//if(parseInt(fSizes) > 5120) {
	//	alert('총용량이 5M를 넘었습니다');
	//	removeList(new_list,100,r_data[3]);	
	//}
}

function FadeIn(element, opacity) {
	var reduce_opacity_by = 15;
	var rate = 15;	// 15 fps

	if (opacity < 100) {
		opacity += reduce_opacity_by;
		if (opacity < 0) opacity = 100;

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity < 100) {		
		setTimeout(function() { FadeIn(element, opacity); }, rate);
	}
}

function FadeOut(opacity,element) {
	var reduce_opacity_by = 15;
	var rate = 15;	// 15 fps

    if(!element) element = document.getElementById('divFileProgress');	

	if (opacity > 0) {
		opacity -= reduce_opacity_by;
		if (opacity > 100) opacity = 0;

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}
	}

	if (opacity > 0) {
		setTimeout(function() { FadeOut(opacity,element); }, rate);
	} else {
		if(element==document.getElementById('listDefine')) element.style.display='none';		
	}
}

function removeList(element, opacity,size) {
	var reduce_opacity_by = 15;
	var rate = 30;	// 15 fps

	if (opacity > 0) {
		opacity -= reduce_opacity_by;
		if (opacity > 100) opacity = 0;

		if (element.filters) {
			try {
				element.filters.item("DXImageTransform.Microsoft.Alpha").opacity = opacity;
			} catch (e) {
				// If it is not set initially, the browser will throw an error.  This will set it if it is not set yet.
				element.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=' + opacity + ')';
			}
		} else {
			element.style.opacity = opacity / 100;
		}		
	}
	
	if (opacity > 0) {		
		setTimeout(function() { removeList(element, opacity,size); }, rate);
	} else {
		element.innerHtml ='';
		document.getElementById("fileList").removeChild(element);
		
		if(isMsie) var cnts=document.getElementById("fileList").childNodes.length;
		else var cnts=document.getElementById("fileList").childNodes.length -2;
        
		fSizes =  Math.round(parseFloat(document.getElementById('fSize').innerHTML)*100 - (size/10.24))/100;
		document.getElementById('fSize').innerHTML = fSizes;

		if(cnts==1) {			
			document.getElementById('listDefine').style.display='block';
			FadeIn(document.getElementById('listDefine'),0);			
		}		
	}
}


function urldecode (str) {
    // Decodes URL-encoded string  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/urldecode    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer
    // +      input by: Ratheous    // +   improved by: Orlando
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +      bugfixed by: Rob
    // +      input by: e-mike
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // %        note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // %        note 2: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on
    // %        note 2: pages served as UTF-8
    // *     example 1: urldecode('Kevin+van+Zonneveld%21');
    // *     returns 1: 'Kevin van Zonneveld!'    // *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
    // *     returns 2: 'http://kevin.vanzonneveld.net/'
    // *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
    // *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
    return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}

function initList(src) {
	for(i=0,cnts=src.length;i<cnts;i++) {
		var new_list = document.createElement("div");
		r_data = src[i].split("|");
		if(i==0) document.getElementById('listDefine').style.display='none';

		new_list.style.margin = "2px";
		new_list.id = "list_"+r_data[1];
		new_list.className = "thumList";
		new_list.innerHTML = "<div><img src='../../image/other_img/"+ori_dir+"/"+r_data[0]+"' align='absmiddle' class='thumImg'></div><div class='thumName'>"+urldecode(r_data[1])+"<br /><font class='thumName2'>"+r_data[2]+"</font> <a href='#' onclick='DelList(\""+r_data[1]+"\");return false;'><img src='../../swfupload/icon/del.gif' align='absmiddle' border='0' /></a></div>";
    
		document.getElementById("fileList").appendChild(new_list);
	
		fSizes =  Math.round(parseFloat(document.getElementById('fSize').innerHTML)*100 + (r_data[3]/10.24))/100;
		document.getElementById('fSize').innerHTML = fSizes;		
	}
}

/************************* USE Ajax Object **********************************/

var aObj = new AjaxObject;

function DelList(name) {
	if(!document.getElementById('list_'+name)) return;
	name = name.replace(/\+/g,"|*|");
	aObj.getHttpRequest("../../swfupload/swf_upload_thum.php?mode=del&file="+name+"&tmp_dir="+tmp_dir, "resultList","data");	
}

function resultList(data) {	
	id = (data['item'][0]['id']);
	size =  (data['item'][0]['size']);	
	rmList = document.getElementById("list_"+id);
    removeList(rmList,100,size);
}

var isMsie = document.all ? true : false; 
if(isMsie) {
	var tmp_now = new Date();
	tmp_now = tmp_now.getYear();
	if(parseInt(tmp_now)<1000) isMsie = false;
}


