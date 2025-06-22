<div id="contentsView">
	<div class="cover" onClick="window.open('../data/<?=$_NOTE['note']?>')"><img src="../data/<?=$_NOTE['note']?>"/></div>

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
			<td><span id="noteUrl">http://cloud.takeoutnote.com/web/note.php?noteid=<?=MD5($_NOTE['uniqueid'])?></span> <input type="button" class="button basic" value="<?php echo $lng['urlcopy']?>" onClick="prompt('노트URL','http://cloud.takeoutnote.com/web/note.php?noteid=<?=MD5($_NOTE['uniqueid'])?>');"/></td>
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
			<? if($row['property']=="record"){ ?>
				<td onClick="location.href='../data/<?=$row['extra']?>';"><?=end(explode("/",$row['extra']))?> <img src="imgs/icon/save.png" style="height: 15px;margin-bottom: -3px;"/></td>
			<? } elseif($row['property']=="store") { ?>
				<td><?php echo $lng['storetime']?>(<?=date("Y-m-d H:i:s", $row['extra'] / 1000)?>)</td>
			<? } else { ?>
				<td><pre><?=$row['extra']?></pre></td>
			<? } ?>
			<td width="25"><img id="extraPublicImg_<?=$row['eid']?>" src="imgs/icon/lock_<?=$row['public']=="N"?"closed":"open"?>.png" width="25" onClick="extraSetPublic(<?=$row['eid']?>);"/></td>
			<td width="70" align="center"><?=date("Y-m-d H:i:s", $row['regdate'] / 1000)?></td>
		</tr>
		<? endforeach ?>
	</table>
	<? endif ?>
</div>

<div id="replyWrite" onSubmit="replyWrite();">
	<div class="title"><img src="imgs/icon/memo.png" height="25"/>Reply</div>

	<div class="form">
		<input type="hidden" name="mid" id="extra_mid" value="<?=$MID?>"/>
		<? if($MNAME): ?>
		<input type="hidden" name="mname" id="extra_mname" value="<?=$MNAME?>"/><strong><?=$MNAME?></strong>
		<? else: ?>
		<input type="text" name="mname" id="extra_mname" value=""/>
		<? endif ?>
		<input type="text" name="reply" id="extra_reply" class="text basic" value="" style="width: 250px;"/>
		<input type="button" value="<?php echo $lng['save']?>" class="button basic" onClick="replyWrite();"/>
	</div>
</div>

<ul id="replyList">
	<? if(is_array($_REPLY)): foreach($_REPLY as $key=>$row): ?>
	<li class="basic" rid="<?=$row['rid']?>">
		<div class="profile"><div class="icon"></div></div>
		<div class="reply">
			<div class="title">
				<strong><?=$row['mname']?></strong>(<?=$row['regdate']?>) 
				<!--<img src="imgs/icon/modify.png" width="15"/>-->
				<img src="imgs/icon/delete.png" class="delete" width="13"/>
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

		if(rid && confirm('<?php echo $lng['replydeleteconfirm']?>') ){
			var returnMessage = getJson("_ajax.php?act=extraDelete&rid="+rid);
			if(returnMessage.result == "OK"){
				$(this).parents('li').remove();
			} else {
				alert("<?php echo $lng['fail']?>");
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
		alert('<?php echo $lng['fail']?>');
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
		alert('<?php echo $lng['notesetpublic']?>');
		$('#notePublicImg').attr('src','imgs/icon/lock_open.png');
	} else if(returnMessage == "N"){
		alert('<?php echo $lng['notesetprivate']?>');
		$('#notePublicImg').attr('src','imgs/icon/lock_closed.png');
	} else {
		alert('<?php echo $lng['fail']?>');
		alert(returnMessage);
	}
}

function extraSetPublic(eid){
	var returnMessage = getData("_ajax.php?act=extraPublic&eid="+eid);

	if(returnMessage == "Y"){
		alert('<?php echo $lng['extrasetpublic']?>');
		$('#extraPublicImg_'+eid).attr('src','imgs/icon/lock_open.png');
	} else if(returnMessage == "N"){
		alert('<?php echo $lng['extrasetprivate']?>');
		$('#extraPublicImg_'+eid).attr('src','imgs/icon/lock_closed.png');
	} else {
		alert('<?php echo $lng['fail']?>');
		alert(returnMessage);
	}
}
</script>