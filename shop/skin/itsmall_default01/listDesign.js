function rtnCell(obj,uid,image,link,dragd,name,icon,loc,price,cprice,rese,ccnt,tag,sout,cp_price,rank,cooperate) {		

	newRow = document.createElement('TR');	
	newCell = document.createElement('TD');	
	
	if(rank && rank <11) {
		rank = '<img src="'+shop_skin+'/img/shop/rank_num'+rank+'_s.png" alt="rank '+rank+'" class="png24" />';
	}
	else rank = '';

	tmps =   '<div class="left" style="padding:8px;width:20px;">';
	if(cooperate!=1) {
		tmps +=  '	<div><input type="checkbox" name="compare[]" value="'+uid+'"></div>';
		tmps +=  '	<div style="margin-top:4px;"><img src="'+shop_skin+'/img/common/icon_qview.gif" alt="퀵뷰" title="퀵뷰"  class="hand" onclick="openQview('+uid+');" /></div>';
	}
	tmps +=  '	<div style="margin-top:4px;"><img src="'+shop_skin+'/img/common/icon_nwin.gif" alt="새창보기" title="새창보기" class="hand" onclick="openView(\''+link+'\');" /></div>';
	tmps +=  '</div>';
	tmps +=  '<div class="left" style="padding:8px; padding-lfet:0px;">';
	tmps +=  '	<div style="cursor:move;text-align:left;border:2px solid #FFF; height:80px;width:80px;background:url('+image+') no-repeat;" onclick="viewGoods(\''+link+'\');" '+dragd+'>'+rank+'</div>';
	tmps +=  '</div>';
	tmps +=  '<div class="left" style="padding:8px;">';
	tmps +=  '	<div style="height:48px;width:516px" class="clear tleft"><a href="'+link+'" onfocus="this.blur()">'+name+'</a><br />'+icon+'</div>';
	
	if(tag) {
		tag = "<img src='" + shop_skin + "/img/common/icon_tag.gif' style='vertical-align:middle' /> " + tag;
	}

	tmps +=  '	<div style="height:18px;width:516px;overflow:hidden;" class="clear tleft">'+tag+'</div>';
	tmps +=  '	<div class="small tleft" style="margin-top:4px;">Home > '+loc+'</div>';
	tmps +=  '</div>';

	newCell.innerHTML = tmps;
	newRow.appendChild(newCell);						
					
	newCell = document.createElement('TD');	
	newCell.style.textAlign = 'right';
	if(cp_price!=0) {
		coupon_price = "<br /><font class='small orange'>쿠폰가&nbsp;&nbsp;:&nbsp;&nbsp; </font><font class='num orange'>"+cp_price+"</font>";		
	}
	else coupon_price = '';
	newCell.innerHTML = '<font class="num green">'+price+'</font>'+coupon_price+'<br/><font class="num">'+rese+'P</font>';
	if(sout==1) newCell.innerHTML += '<br/><font class="small orange">[품절]</font>';
	newRow.appendChild(newCell);	

	newCell = document.createElement('TD');	
	newCell.style.textAlign = 'center';
	newCell.innerHTML = '<font class="small">상품평</font><br /><font class="num bold underline">'+ccnt+'</font>';
	newRow.appendChild(newCell);	

	obj.appendChild(newRow);				

	newRow = document.createElement('TR');			
	newCell = document.createElement('TD');	
	newCell.colSpan = 3;
	newCell.style.height = 1;
	newCell.style.backgroundColor = "#efefef";
	newCell.innerHTML = "<img src='" + shop_skin + "/img/common/blank.gif' width=1 height=1 alt='blank' />";;
	newRow.appendChild(newCell);

	obj.appendChild(newRow);
}

function rtnImg(obj,uid,image,link,dragd,name,icon,price,cprice,ccnt,sout,cp_price,rank,cooperate){
	if(rank && rank <11) {
		rank = '<img src="'+shop_skin+'/img/shop/rank_num'+rank+'.png" alt="rank '+rank+'" class="png24" />';
	}
	else rank = '';
	
	if(cp_price!=0) {
		coupon_price = '<div style="float:left;padding:2px 2px 0 0 ;"><img src="'+shop_skin+'img/common/icon_coupon.gif" alt="쿠폰가" title="쿠폰가" width="10" height="10" /></div>';
		price = cp_price;
	}
	else coupon_price = '';
	if(sout==1) price +='&nbsp;<font class="small orange">[품절]</font>';	

	defList =  '<ul>';
	defList += '	<li style="cursor:move;text-align:left;border:2px solid #FFF; height:150px;width:150px;background:url('+image+') no-repeat;" onclick="viewGoods(\''+link+'\');" '+dragd+'>'+rank+'</li>';		
	defList += '	<li style="padding-top:4px;height:48px;width:150px;line-height:120%;"><a href="'+link+'" onfocus="this.blur()">'+name+'</a><br />'+icon+'</li>';
	defList += '	<li class="num green" style="width:150px;height:20px;overfolw:hidden">'+coupon_price+price+'</li>';
	defList += '	<li style="width:150px;height:20px;">';
	defList += '		<div class="small left" style="width:90px;">상품평 <font class="num bold underline">'+ccnt+'</font></div>';
	defList += '		<div class="left" style="width:40px;text-align:right">';
	if(cooperate!=1) defList += '<img src="'+shop_skin+'img/common/icon_qview.gif" alt="퀵뷰" title="퀵뷰" class="hand" onclick="openQview('+uid+');" />';
	defList += '		&nbsp;<img src="'+shop_skin+'img/common/icon_nwin.gif" alt="새창보기" title="새창보기" class="hand" onclick="openView(\''+link+'\');" />';
	defList += '		</div>';
	if(cooperate!=1) defList += '		<div class="left" style="width:20px;"><input type="checkbox" name="compare[]" value="'+uid+'" /></div>';
	defList += '	</li>';
	defList += '</ul>';

	newCell = document.createElement('DIV');	
	if(cnt%5==0) newCell.className = "list_img_box clear";
	else newCell.className = "list_img_box";	
	newCell.innerHTML = defList;
	obj.appendChild(newCell);	
}

function rtnImgLine(obj,cnt) {
	if(i=cnt%5!=0) {
		i=cnt%5;
		newCell = document.createElement('DIV');
		newCell.className = "list_img_box";
		newCell.style.width = (((5-i)/5)*100)+'%';
		defList =  '<ul>';
		defList += '	<li style="height:154px;">&nbsp;</li>';			
		defList += '	<li style="height:48px;line-height:120%;margin-top:4px;">&nbsp;</li>';
		defList += '	<li style="height:20px;">&nbsp;</li>';
		defList += '	<li style="height:20px;"><div class="left"></div></li>';
		defList += '</ul>';
		newCell.innerHTML = defList;
		obj.appendChild(newCell);	
	}	
}