<?php

include 'header.html';

?>
<div id="tabs">
  <ul>
    <li><a href="#job_table_wrapper">Job List</a></li>
    <li><a href="#user_stats_table_wrapper">User Statistics</a></li>
    <li><a href="#job_stats_table_wrapper">Job Statistics</a></li>
  </ul>
  <div id="job_table_wrapper"></div>
  <div id="user_stats_table_wrapper"></div>
  <div id="job_stats_table_wrapper"></div>
</div>
<SCRIPT LANGUAGE="JavaScript">
<!--
$(document).ready(function() {
  load_tables();
  $( "#tabs" ).tabs();
});
// -->
</SCRIPT>
<?php

include 'footer.html';

