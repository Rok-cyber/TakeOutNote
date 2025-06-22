function rtnCell(obj,uid,image,link,dragd,name,icon,loc,price,cprice,rese,ccnt,tag,sout,cp_price,rank,cooperate,cate) {		

	newRow = document.createElement('TR');	
	newCell = document.createElement('TD');	
	
	if(rank && rank <11) {
		rank = '<div style="position:absolute;top:4px;text-align:left;"><img src="'+shop_skin+'/img/shop/rank_num'+rank+'_s.png" alt="rank '+rank+'" class="png24" /></div>';
	}
	else rank = '';
	
	tmps =   '<div class="left" style="padding:8px;width:20px;">';
	if(cooperate!=1) tmps +=  '	<div style="padding-top:35px;"><input type="checkbox" name="compare[]" value="'+uid+'"></div>';
	tmps +=  '</div>';
	tmps +=  '<div class="left" style="padding:8px; padding-left:0px;position:relative;">';
	if(cooperate==1) {
		tmps +=  '	<div style="height:80px;width:80px;overflow:hidden;">';
		tmps +=	 '		<div><a href="'+link+'"><img src="'+image+'" alt="'+name+'" /></a></div>';				
	}
	else {
		tmps +=  '	<div style="height:80px;width:80px;overflow:hidden;"  onmouseOver="viewQuick(\'listBoxQucik'+uid+'\',\'on\')" onmouseOut="viewQuick(\'listBoxQucik'+uid+'\',\'\')">';		
		tmps +=	 '		<div><a href="'+link+'"><img src="'+image+'" alt="'+name+'" /></a></div>';
		tmps +=  '		<div class="goodsQuickInfo" style="overflow:hidden;" id="listBoxQucik'+uid+'">';
		tmps +=  '			<div class="goodsQuickDefault" id="listBoxQucik_zoom'+uid+'">';
		tmps +=  '				<img src="'+ shop_skin +'img/common/quick_zoom.gif" border="0" alt="zoom" title="zoom" class="hand" onmouseOver="cgImg(this,\'_on\',\'listBoxQucik_zoom'+uid+'\')"  onmouseOut="cgImg(this,\'\',\'listBoxQucik_zoom'+uid+'\')" onclick="openQview('+uid+');" />';
		tmps +=  '			</div>';
		tmps +=  '			<div class="goodsQuickDefault" id="listBoxQucik_popup'+uid+'">';
		tmps +=  '				<img src="'+ shop_skin +'img/common/quick_popup.gif" border="0"  alt="popup" title="popup" class="hand" onmouseOver="cgImg(this,\'_on\',\'listBoxQucik_popup'+uid+'\')"  onmouseOut="cgImg(this,\'\',\'listBoxQucik_popup'+uid+'\')" onclick="openView(\''+link+'\');" />';
		tmps +=  '			</div>';
		tmps +=  '			<div class="goodsQuickDefault" id="listBoxQucik_wish'+uid+'">';
		tmps +=  '				<img src="'+ shop_skin +'img/common/quick_wish.gif" border="0"  alt="wish" title="wish" class="hand" onmouseOver="cgImg(this,\'_on\',\'listBoxQucik_wish'+uid+'\')"  onmouseOut="cgImg(this,\'\',\'listBoxQucik_wish'+uid+'\')" onclick="quickBarCheckWishAdd(\''+cate+''+uid+'\');" />';
		tmps +=  '			</div>';
		tmps +=  '			<div class="goodsQuickDefault" id="listBoxQucik_cart'+uid+'">';
		tmps +=  '				<img src="'+ shop_skin +'img/common/quick_cart.gif" border="0"  alt="cart" title="cart" class="hand" onmouseOver="cgImg(this,\'_on\',\'listBoxQucik_cart'+uid+'\')"  onmouseOut="cgImg(this,\'\',\'listBoxQucik_cart'+uid+'\')" onclick="gToCart.cartAdd('+cate+','+uid+');" />';
		tmps +=  '			</div>';
		tmps +=  '		</div>';	
	}	
	tmps +=  rank;
	tmps +=  '	</div>';
	
	tmps +=  '</div>';
	tmps +=  '<div class="left" style="padding:8px;">';
	tmps +=  '	<div style="height:48px;width:340px" class="clear tleft"><a href="'+link+'" onfocus="this.blur()">'+name+'</a><br />'+icon+'</div>';
	
	if(tag) {
		tag = "<img src='" + shop_skin + "/img/common/icon_tag.gif' style='vertical-align:middle' /> " + tag;
	}

	tmps +=  '	<div style="height:18px;width:340px;overflow:hidden;" class="clear tleft">'+tag+'</div>';
	tmps +=  '	<div class="small tleft" style="margin-top:4px;">'+loc+'</div>';
	tmps +=  '</div>';

	newCell.innerHTML = tmps;
	newRow.appendChild(newCell);						
					
	newCell = document.createElement('TD');	
	newCell.style.textAlign = 'right';
	if(cp_price!=0) {
		coupon_price = "<br /><font class='small mColor'>쿠폰가&nbsp;&nbsp;:&nbsp;&nbsp; </font><font class='num mColor bold'>"+cp_price+"</font>";		
	}
	else coupon_price = '';
	newCell.innerHTML = '<font class="num bold">'+price+'</font>'+coupon_price+'<br/><font class="num">'+rese+'P</font>';
	if(sout==1) newCell.innerHTML += '<br/><font class="small mColor">[품절]</font>';
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

function rtnImg(obj,uid,image,link,dragd,name,icon,price,cprice,ccnt,sout,cp_price,rank,cooperate,cate){
	if(rank && rank <11) {
		rank = '<div style="position:absolute;top:1px;text-align:left;"><img src="'+shop_skin+'/img/shop/rank_num'+rank+'.png" alt="rank '+rank+'" class="png24" /></div>';
	}
	else rank = '';
	
	if(cp_price!=0) {
		coupon_price = '<div style="float:left;padding:2px 2px 0 0 ;"><img src="'+shop_skin+'img/common/icon_coupon.gif" alt="쿠폰가" title="쿠폰가" width="10" height="10" /></div>';
		price = cp_price;
	}
	else coupon_price = '';
	if(sout==1) price +='&nbsp;<font class="small mColor">[품절]</font>';	

	defList =  '<ul style="position:relative">';
	if(cooperate==1) {
		defList += '	<li style="height:150px;width:150px;">';
		defList += '			<div><a href="'+link+'"><img src="'+image+'" alt="'+name+'" /></a></div>';
		defList += rank;
	}
	else {
		defList += '	<li>';
		defList += '		<div style="height:150px;width:150px;" onmouseOver="viewQuick(\'imgBoxQucik'+uid+'\',\'on\')" onmouseOut="viewQuick(\'imgBoxQucik'+uid+'\',\'\')">';	
		defList += '			<div><a href="'+link+'"><img src="'+image+'" alt="'+name+'" /></a></div>';
		defList += '			<div class="goodsQuickInfo" style="overflow:hidden;" id="imgBoxQucik'+uid+'">';
		defList += '				<div class="goodsQuickDefault" id="imgBoxQucik_zoom'+uid+'">';
		defList += '					<img src="'+ shop_skin +'img/common/quick_zoom.gif" border="0" alt="zoom" title="zoom" class="hand" onmouseOver="cgImg(this,\'_on\',\'imgBoxQucik_zoom'+uid+'\')"  onmouseOut="cgImg(this,\'\',\'imgBoxQucik_zoom'+uid+'\')" onclick="openQview('+uid+');" />';
		defList += '				</div>';
		defList += '				<div class="goodsQuickDefault" id="imgBoxQucik_popup'+uid+'">';
		defList += '					<img src="'+ shop_skin +'img/common/quick_popup.gif" border="0"  alt="popup" title="popup" class="hand" onmouseOver="cgImg(this,\'_on\',\'imgBoxQucik_popup'+uid+'\')"  onmouseOut="cgImg(this,\'\',\'imgBoxQucik_popup'+uid+'\')" onclick="openView(\''+link+'\');" />';
		defList += '				</div>';
		defList += '				<div class="goodsQuickDefault" id="imgBoxQucik_wish'+uid+'">';
		defList += '					<img src="'+ shop_skin +'img/common/quick_wish.gif" border="0"  alt="wish" title="wish" class="hand" onmouseOver="cgImg(this,\'_on\',\'imgBoxQucik_wish'+uid+'\')"  onmouseOut="cgImg(this,\'\',\'imgBoxQucik_wish'+uid+'\')" onclick="quickBarCheckWishAdd(\''+cate+''+uid+'\');" />';
		defList += '				</div>';
		defList += '				<div class="goodsQuickDefault" id="imgBoxQucik_cart'+uid+'" style="width:24%">';
		defList += '					<img src="'+ shop_skin +'img/common/quick_cart.gif" border="0"  alt="cart" title="cart" class="hand" onmouseOver="cgImg(this,\'_on\',\'imgBoxQucik_cart'+uid+'\')"  onmouseOut="cgImg(this,\'\',\'imgBoxQucik_cart'+uid+'\')" onclick="gToCart.cartAdd('+cate+','+uid+');" />';
		defList += '				</div>';
		defList += '			</div>';
		defList += rank;
		defList += '		</div>';		
	}
	defList += '	</li>';		
	defList += '	<li style="padding-top:4px;height:48px;width:150px;line-height:120%;"><a href="'+link+'" onfocus="this.blur()">'+name+'</a><br />'+icon+'</li>';
	defList += '	<li class="num bold" style="width:150px;height:20px;overfolw:hidden">'+coupon_price+price+'</li>';
	defList += '	<li style="width:150px;height:20px;">';
	defList += '		<div class="small left" style="width:90px;">상품평 <font class="num bold underline">'+ccnt+'</font></div>';
	if(cooperate!=1) defList += '		<div class="right" style="width:20px;"><input type="checkbox" name="compare[]" value="'+uid+'" /></div>';
	defList += '	</li>';
	defList += '</ul>';

	newCell = document.createElement('DIV');	
	if(cnt%4==0) newCell.className = "list_img_box both";
	else newCell.className = "list_img_box";	
	newCell.innerHTML = defList;
	obj.appendChild(newCell);	
}

function rtnImgLine(obj,cnt) {
	var linePer = 4;
	var i=cnt%linePer;
	if(i!=0) {
		newCell = document.createElement('DIV');
		newCell.className = "list_img_box";
		newCell.style.width = (((linePer-i)/linePer)*100)+'%';				
		defList =  '<ul>';
		defList += '	<li style="height:150px;">&nbsp;</li>';			
		defList += '	<li style="height:48px;line-height:120%;margin-top:4px;">&nbsp;</li>';
		defList += '	<li style="height:20px;">&nbsp;</li>';
		defList += '	<li style="height:20px;"><div class="left"></div></li>';
		defList += '</ul>';
		newCell.innerHTML = defList;
		obj.appendChild(newCell);	
	}	
}