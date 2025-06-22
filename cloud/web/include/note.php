<div id="contentsView">
	<div class="cover"><img src="../data/<?=$_NOTE['note']?>"/></div>

	<div id="noteInfoTitle"><img src="imgs/icon/memo.png" height="25"/>Note Info</div>
	<table id="noteInfoList">
		<tr>	
			<th width="90"><?php echo $lng['filename']?>(<?php echo $lng['capacity']?>)</th>
			<td><?=urldecode($_NOTE['title'])?> (<?=round($_NOTE['volume']/1000)?>Kb)</td>
		</tr>
		<tr>
			<th><?php echo $lng['regdate']?> / <?php echo $lng['lastupdate']?></th>
			<td><?=date("Y-m-d H:i:s", $_NOTE['lastupdate'] / 1000)?> / <?=date("Y-m-d H:i:s", $_NOTE['lastupdate'] / 1000)?></td>
		</tr>
		<tr>
			<th><?php echo $lng['note']?>URL</th>
			<td>http://codexbridge.com/tons/web/note.php?noteid=<?=MD5($_NOTE['uniqueid'])?></td>
		</tr>
	</table>
<!--
	<? if($MID == $_NOTE['mid']){ ?>
		<div class="public"><img src="imgs/icon/lock_<?=$_NOTE['public']=="N"?"closed":"open"?>.png" style="height: 30px;" onClick="location.href='view.php?GID=<?=$GID?>&NID=<?=$NID?>&public=N|^<?=$_NOTE['public']=="N"?"Y":"N"?>';"/></div>
		<? if($_NOTE['public']=="Y"){ ?>
		<div class="public">공개노트 URL : http://codexbridge.com/tons/web/note.php?noteid=<?=MD5($_NOTE['uniqueid'])?></div>
		<? } ?>
	<? } ?>
	<div class="info">
		<div class="file"><?=end(explode("/",$_NOTE['note']))?></div>
		<div class="date">생성일:<?=date("Y-m-d H:i:s", $_NOTE['lastupdate'] / 1000)?>/수정일:<?=date("Y-m-d H:i:s", $_NOTE['lastupdate'] / 1000)?></div>
		<div class="volume"><?=round($_NOTE['volume']/1000)?>Kb</div>
	</div>
-->

	<? if(is_array($_EXTRA)): ?>
	<div id="extraTitle"><img src="imgs/icon/memo.png" height="25"/>History</div>
	<table id="extraList">
		<? foreach($_EXTRA as $key=>$row): ?>
		<tr>	
			<td width="18"><img src="imgs/icon/<?=$row['property']?>.png" class="icon"></td>
			<td <?if($row['property']=="record"){?>onClick="location.href='../data/<?=$row['extra']?>';"<?}?>><?=end(explode("/",$row['extra']))?></td>
			<td width="70" align="center"><?=date("Y-m-d H:i:s", $row['regdate'] / 1000)?></td>
		</tr>
		<? endforeach ?>
	</table>
	<? endif ?>
</div>

<div id="replyWrite" onSubmit="replyWrite();">
	<div class="title"><img src="imgs/icon/memo.png" height="25"/>Reply</div>
</div>

<ul id="replyList">
	<? if(is_array($_REPLY)): foreach($_REPLY as $key=>$row): ?>
	<li class="basic" rid="<?=$row['rid']?>">
		<div class="profile"><div class="icon"></div></div>
		<div class="reply">
			<div class="title">
				<strong><?=$row['mname']?></strong>(<?=$row['regdate']?>) 
				<!--<img src="imgs/icon/modify.png" width="15"/>-->
			</div>
			<div><?=$row['reply']?></div>
		</div>
	</li>
	<? endforeach; endif; ?>
</ul>

<script type="text/javascript">
$(document).ready(function(){

	//extraDelete
	$('#replyList').on('click','.delete',function(e){
		var rid = $(this).parents('li').attr('rid');

		if(rid && confirm('삭제하면 복구 하실수 없습니다!\r\n삭제 하시겠습니까?') ){
			var returnMessage = getJson("_ajax.php?act=extraDelete&rid="+rid);
			if(returnMessage.result == "OK"){
				$(this).parents('li').remove();
			} else {
				alert("댓글 삭제에 실패 했습니다!");
			}
		}
	});

});

function replyWrite(){
	var nid = <?=$NID?>;
	var mid = $('#extra_mid').val();
	var reply = encodeURIComponent($('#extra_reply').val());
	var mname = $('#extra_mname').val();

	var returnMessage = getJson("_ajax.php?act=replyWrite&nid="+nid+"&mid="+mid+"&mname="+mname+"&reply="+reply);

	if(returnMessage.result == "OK"){
		$('#replyList').html( $('#replyList').html() + returnMessage.data );
		$('#extra_reply').val("");
		$(document).scrollTop($(document).height());
	} else {
		alert('댓글 등록에 실패 했습니다!');
	}

	/*
	if(returnMessage == "OK"){
		var li = "<li>"+mname+":"+reply+"</li>";
		$('#replyList').html( $('#replyList').html() + li );
	}
	*/
}

function noteSetPublic(){
	var nid = <?=$NID?>;
	var returnMessage = getData("_ajax.php?act=notePublic&nid="+nid);

	if(returnMessage == "Y"){
		alert("노트가 [공개]로 설정 되었습니다.\n노트URL을 통해 비회원도 접근 가능 합니다.");
		$('#notePublicImg').attr('src','imgs/icon/lock_open.png');
	} else if(returnMessage == "N"){
		alert("노트가 [비공개]로 설정 되었습니다.\n노트URL 접근이 차단 됩니다.");
		$('#notePublicImg').attr('src','imgs/icon/lock_closed.png');
	} else {
		alert("노트 공개여부 설정 실패");
		alert(returnMessage);
	}
}

function extraSetPublic(eid){
	var returnMessage = getData("_ajax.php?act=extraPublic&eid="+eid);

	if(returnMessage == "Y"){
		alert("정보가 [공개]로 설정 되었습니다.\n노트URL에서 공개 됩니다.");
		$('#extraPublicImg_'+eid).attr('src','imgs/icon/lock_open.png');
	} else if(returnMessage == "N"){
		alert("정보가 [비공개]로 설정 되었습니다.\n노트URL에서 표시되지 않습니다.");
		$('#extraPublicImg_'+eid).attr('src','imgs/icon/lock_closed.png');
	} else {
		alert("정보 공개여부 설정 실패");
		alert(returnMessage);
	}
}
</script>