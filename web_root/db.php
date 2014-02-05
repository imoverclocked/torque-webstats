<?php

function get_db() {
  $db = new mysqli('HOSTNAME','USERNAME','PASSWORD','DBNAME');
  if( $db->connect_errno ) {
	echo 'Could not record resource usage in DB';
	return FALSE;
  }
  return $db;
}
