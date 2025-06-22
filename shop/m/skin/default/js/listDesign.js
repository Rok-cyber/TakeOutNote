function rtnCell(obj,uid,image,link,name,icon,price,cprice,rese,ccnt,sout,cp_price,cate,event,oprice) {		
	var cp_img = evt_img = '';
	
	price = price.replace("원","");
	if(cp_price>0) cp_img = '<img src="'+shop_skin+'img/common/bult_coupon.png" alt="쿠폰" />';
	if(event>0) evt_img = '<img src="'+shop_skin+'img/common/bult_event.png" alt="이벤트" />';

	newLi = document.createElement('LI');	
	newLi.id = "tGoods_"+uid;
		
	div = '<div class="item_box">\
				<a href="'+link+'" title="상품상세보기">\
					<div class="thumbnail"><img src="'+image+'" alt="상품이미지" /></div>\
					<div class="item_info">\
						<p class="goods" title="상품명">'+name+'</p>\
						<p class="review" title="상품후기">상품후기 : '+ccnt+'개</p>\
						<p class="sales" title="할인정보">'+cp_img+' '+evt_img+'</p>\
						<p class="price" title="가격">\
			';
	if(cp_price>0 || event>0) {
		if(event>0) { cp_price = price; price = oprice; }
		div	+= '			<span class="selling" title="판매가"><s>'+price+'</s>원</span>\
							<span title="할인가"><strong>'+cp_price+'</strong>원</span>\
				';
	}
	else {
		div += '				<span title="판매가"><strong>'+price+'</strong>원</span>\ ';
	}
	div += '			</p>\
					</div>\
					<div class="todayDel"><a href="javascript:delGoods('+cate+','+uid+');" title="최근상품삭제">삭제</a></div>\
					<div class="wishDel"><a href="javascript:delGoods2('+uid+');" title="관심상품삭제">삭제</a></div>\
				</a>\
			</div>\
			';
	newLi.innerHTML = div;
	obj.appendChild(newLi);
	obj.className = "listList";
}