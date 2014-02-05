function load_tables() {
	$("#job_table_wrapper").html( '<table class="display" id="job_table"></table>' );
	$("#user_stats_table_wrapper").html( '<table class="display" id="user_stats_table"></table>' );
	$("#job_stats_table_wrapper").html( '<table class="display" id="job_stats_table"></table>' );

	$.ajax({url: 'job_list.php', dataType: "json",
		success: function(data, textStatus, jqHXR) {
			table = $('#job_table').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bProcessing": true,
				"aaData": data,
				//   SELECT jid,cid,name,uid,q,used_cput,used_mem,used_vmem,used_walltime,host,finish_time FROM jobs
				//
				"aoColumns": [
				{ "sTitle": "Job ID" },
				{ "sTitle": "Cluster" },
				{ "sTitle": "Job Name" },
				{ "sTitle": "User" },
				{ "sTitle": "Queue" },
				{ "sTitle": "CPU Time" },
				{ "sTitle": "Mem" },
				{ "sTitle": "VMem" },
				{ "sTitle": "Wall Time" },
				{ "sTitle": "Host" },
				{ "sTitle": "Finished" }
				]
			} );
			table.fnSort( [[10, 'desc']] );
		}});

	$.ajax({url: 'user_stats.php', dataType: "json",
		success: function(data, textStatus, jqHXR) {
			table = $('#user_stats_table').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bProcessing": true,
				"aaData": data,
				//   select uid,cput,walltime,job_count
				//
				"aoColumns": [
				{ "sTitle": "User" },
				{ "sTitle": "CPU Time" },
				{ "sTitle": "Wall Time" },
				{ "sTitle": "Job Count" }
				]
			} );
			table.fnSort( [[1, 'desc']] );
		}});

	$.ajax({url: 'job_stats.php', dataType: "json",
		success: function(data, textStatus, jqHXR) {
			table = $('#job_stats_table').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bProcessing": true,
				"aaData": data,
				//   select uid,cput,walltime,job_count
				//
				"aoColumns": [
				{ "sTitle": "User" },
				{ "sTitle": "Job Name" },
				{ "sTitle": "CPU Time" },
				{ "sTitle": "Wall Time" },
				{ "sTitle": "Job Count" }
				]
			} );
			table.fnSort( [[2, 'desc']] );
		}});

}
