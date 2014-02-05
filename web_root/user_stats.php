<?php

include 'db.php';

$db = get_db();

$where_clause = 'WHERE DATE_SUB(NOW(), INTERVAL "1" MONTH) < finish_time';
$group_clause = 'GROUP BY uid';

$select_clause = 'select uid,SUM(used_cput) as cput,SUM(used_walltime) as walltime,COUNT(name) as job_count from jobs '. $where_clause . ' ' . $group_clause;

if ( ! $res = $db->query($select_clause) ) {
  print "Could not get job list.";
  print $db->error;
  return;
}

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
	$r[1] = sec2hms($r[1]);
	$r[2] = sec2hms($r[2]);
	$rows[] = $r;
}

print json_encode($rows);
