<?php

include 'db.php';

$db = get_db();

$where_clause = 'WHERE DATE_SUB(NOW(), INTERVAL "1" MONTH) < finish_time';

if ( ! $res = $db->query('SELECT id,REPLACE(REPLACE(cid,"hitorque.lpl.arizona.edu","HiNET"),"torqueserver.lpl.arizona.edu","PIRL") as cid,name,uid,q,used_cput,used_mem,used_vmem,used_walltime,host,finish_time FROM jobs ' . $where_clause) ) {
  print "Could not get job list.";
  print $db->error;
  return;
}

// $result_count = $res->fetch_all();

function sec2hms($seconds) {
  $s = $seconds % 60;
  $seconds -= $s;
  $seconds /= 60;
  $m = $seconds % 60;
  $seconds -= $m;
  $seconds /= 60;
  $h = $seconds;
  return sprintf("%03d:%02d:%02d", $h, $m, $s);
}

$rows = array();
while($r = $res->fetch_row()) {
	// Modify times to look like HH:MM:SS for readability
	$r[5] = sec2hms($r[5]);
	$r[8] = sec2hms($r[8]);
	$rows[] = $r;
}

print json_encode($rows);
