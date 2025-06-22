<?
function alert($msg="", $url=""){
	if($msg) echo "<script type='text/javascript'>alert('$msg');</script>";

	if($url) echo "<script type='text/javascript'>location.href='$url';</script>";
	else echo "<script type='text/javascript'>history.go(-1);</script>";

	exit;
}

// mysql_query 와 mysql_error 를 한꺼번에 처리
function sql_query($sql, $error=TRUE)
{
    if ($error)
        $result = @mysql_query($sql) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : $_SERVER[PHP_SELF]");
    else
        $result = @mysql_query($sql);
    return $result;
}


// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
function sql_fetch($sql, $error=TRUE)
{
    $result = sql_query($sql, $error);
    //$row = @sql_fetch_array($result) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : $_SERVER[PHP_SELF]");
    $row = sql_fetch_array($result);
    return $row;
}

// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
function sql_fetch_one($sql, $error=TRUE)
{
    $result = sql_query($sql, $error);
    //$row = @sql_fetch_array($result) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : $_SERVER[PHP_SELF]");
    $row = sql_fetch_array($result);


		if($row) return current($row);
		else return 0;
}

// 결과값에서 한행 연관배열(이름으로)로 얻는다.
function sql_fetch_array($result)
{
    $row = @mysql_fetch_assoc($result);
    return $row;
}

?>