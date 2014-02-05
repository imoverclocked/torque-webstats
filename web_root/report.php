<?php

header("Content-type: text/plain");

require 'db.php';

global $mult;
$mult = array();
$mult[''] = 1;
$mult['kb'] = 1024;
$mult['mb'] = 10485761;
$mult['gb'] = 1073741824;
$mult['tb'] = 1099511627776;
$mult['pb'] = 1125899906842624;

function g($k) {
	return $_GET[$k];
}

if( g('sec') != 'SECRET_KEY' ) {
	die('required security key not found');
}

function compare_res($k, $used, $req) {
  if(array_search($k, array('cput','walltime')) !== FALSE) {
	  compare_res_time($k, $used, $req);
  } else {
	  compare_res_mem($k, $used, $req);
  }
}

function time_to_seconds($t) {
  $s = explode(":", $t);
  return $s[2] + $s[1]*60 + $s[0]*3600;
}

function compare_res_time($k, $used, $req) {
  global $mult;
  $u = time_to_seconds($used);
  $r = time_to_seconds($req);
  printf("Used %5.2f%% of requested %s\n", $u*100/$r, $k);
}

function size_to_bytes($s) {
  global $mult;
  $n = substr($s, 0, strspn($s,"0123456789"));
  $suffix = str_replace($n, '', $s);
  return $n * $mult[strtolower($suffix)];
}

function compare_res_mem($k, $used, $req) {
  $u = size_to_bytes($used);
  $r = size_to_bytes($req);
  printf("Used %5.2f%% of requested %s\n", $u*100/$r, $k);
}

/*
 * CREATE TABLE jobs (
 * 	id BIGINT UNSIGNED,
 * 	cid ENUM('torqueserver.lpl.arizona.edu','hitorque.lpl.arizona.edu'),
 * 	name VARCHAR(255),
 * 	uid VARCHAR(255),
 * 	gid VARCHAR(255),
 * 	sid BIGINT UNSIGNED,
 * 	req_res VARCHAR(255),
 * 	q VARCHAR(255),
 * 	acct VARCHAR(255),
 * 	used_cput BIGINT,
 * 	used_mem BIGINT,
 * 	used_vmem BIGINT,
 * 	used_walltime BIGINT,
 * 	PRIMARY KEY (id,cid));
 * ALTER TABLE jobs ADD COLUMN host VARCHAR(127);
 * ALTER TABLE jobs ADD COLUMN finish_time TIMESTAMP;
 *
 */
function record_job($jid, $cid, $name, $uid, $gid, $sid, $res, $used_resources, $q, $acct, $host) {
  /* All of the above are strings except $used_resources which is an associative array */
  if( ! $db = get_db() ) {
	return;
  }
  if( !($initial_insert = $db->prepare("INSERT INTO jobs(id,cid,name,uid,gid,sid,req_res,q,acct,host,used_cput,used_walltime,used_vmem,used_mem) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)")) ) {
	echo "Prepare INSERT failed: (" . $db->errno . ") " . $db->error;
	return;
  }
  if( ! $initial_insert->bind_param("issssissssiiii",
	$jid,
	$cid,
	$name,
	$uid,
	$gid,
	$sid,
	$res,
	$q,
	$acct,
	$host,
	time_to_seconds($used_resources['cput']),
	time_to_seconds($used_resources['walltime']),
	size_to_bytes(  $used_resources['vmem']),
	size_to_bytes(  $used_resources['mem'])
  		) ||
  	! $initial_insert->execute()
  	) {
	echo "Failed to record job: (" . $initial_insert->errno . ") " . $initial_insert->error;
	return;
  }
}

/* Really simple manipulation of the submitted vars */
$id = explode(".", g('jid'), 2);
$jid = $id[0];
$cid = $id[1];
/* build associative array of resources used */
$used_resources = array();
foreach( explode(',', g('used_res')) as $k ) {
	$tmp = explode('=', $k, 2);
	$used_resources[$tmp[0]] = $tmp[1];
}
/* build associative array of resources requested */
$requested_resources = array();
foreach( explode(',', g('res')) as $k ) {
	$tmp = explode('=', $k, 2);
	$requested_resources[$tmp[0]] = $tmp[1];
}

/* Output a quick job status for the user */

?>
<?= 'Job ID:              ' . $jid . '/' . $cid ?>

<?= 'Job Name:            ' . g('name'); ?>

<?= 'User/Group:          ' . g('uid') . '/' . g('gid'); ?>

<?= 'Requested Resources: ' . g('res'); ?>

<?= 'Used Resources:      ' . g('used_res') ?>

<?php

foreach( $used_resources as $k => $v ) {
	if(array_key_exists($k,$requested_resources)) {
		compare_res($k,$used_resources[$k],$requested_resources[$k]);
	}
}

record_job($jid, $cid, g('name'), g('uid'), g('gid'), g('sid'), g('res'), $used_resources, g('q'), g('acct'), g('host'));
