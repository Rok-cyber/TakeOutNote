<ul id="contentsList">
<? foreach($list as $key=>$row): ?>

<? if(!$row['cnt']['note']){ ?>
	<li class="basic" onClick="alert('등록된 노트가 없습니다!');">
<? } else { ?>
	<li class="basic" onClick="go('./list.php?GID=<?=$row['gid']?>');">
<? } ?>

		<div class="thumb"><img src="../data/<?=$row['thumb']?>"/></div>
		<div class="contents">
			<div class="title"><?=$row['name']?></div>
			<div class="content"><?php echo $lng['regdate']?>:<?=date("Y-m-d H:i:s", $row['regdate'] / 1000)?> / <?php echo $lng['lastupdate']?>:<?=date("Y-m-d H:i:s", $row['lastupdate'] / 1000)?></div>
		</div>
	</li>
<? endforeach; ?>
</ul>