<ul id="contentsList">
<? foreach($list as $key=>$row): ?>
	<li class="basic" onClick="go('./view.php?GID=<?=$row['gid']?>&NID=<?=$row['nid']?>');">
		<div class="thumb"><img src="../data/<?=$row['note']?>"/></div>
		<div class="contents">
			<div class="title"><?=urldecode($row['title'])?></div>
			<div class="content"><?php echo $lng['regdate']?>:<?=date("Y-m-d H:i:s", $row['regdate'] / 1000)?> / <?php echo $lng['lastupdate']?>:<?=date("Y-m-d H:i:s", $row['lastupdate'] / 1000)?></div>
			<div class="content">
				<?if($row['cnt']['memo']){?><img src="imgs/icon/memo.png" class="icon"/><?}?>
				<?if($row['cnt']['alarm']){?><img src="imgs/icon/alarm.png" class="icon"/><?}?>
				<?if($row['cnt']['location']){?><img src="imgs/icon/location.png" class="icon"/><?}?>
				<?if($row['cnt']['record']){?><img src="imgs/icon/record.png" class="icon"/><?}?>
			<!--<div class="content"><?=$row['memo']?></div>-->
		</div>
	</li>
<? endforeach; ?>
</ul>