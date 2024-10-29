'use strict';
(function ($) {
	
	var g	= {},
		win	= $(window),
		doc = $(document);
		
/************************************************************************
 * DOM ready
 **************************************************************************/
 
	$(document).ready(function(){
		bdb_init();
	});
	
	function bdb_init(){
		bdb_loadContent();
		$('form').on('submit',function(e){e.preventDefault();});
		$('#backup_data').on('click',function(){bdb_backupData();});
		$('#restore_data').on('click',function(){bdb_restoreData();});
	}
	
	function bdb_loadContent(){
		$.ajax({
			type		: 'POST',
			url			: siteUrl + ajaxurl,
			data		: {
				action		: 'bdb_get_files',
				token		: securetoken
			},
			beforeSend	: function(){
				$('div.loading').show();
			}
		}).done(function(erg){
			$('div.loading').hide();
			erg = $.parseJSON(erg);
			var a = '';
			if(erg == null || erg.files.length == 0){
				a += '<tr class="backupdb-empty">';
					a += '<td>';
						a += 'Keine Backups gefunden';
					a += '</td>';
				a += '</tr>';
			}else{
				$(erg.files).each(function(i,el){
					a += '<tr>';
						a += '<td style="display:none;">';
							a += el.name;
						a += '</td>';
						a += '<td>';
							a += el.ctime;
						a += '</td>';
						a += '<td>';
							a += el.mtime;
						a += '</td>';
						a += '<td scope="row">';
							a += '<input type="radio" value="'+el.name+'" class="backupdb-radio" name="backupdb-radio"/>';
						a += '</td>';
					a += '</tr>';
				});
			}
			$('#backupdb-content').find('tbody').html('').append($(a)).hide().fadeIn();
		}).error(function(erg){
			alert("Error\n\n"+erg);
		});
	};
	
	function bdb_backupData(e){
		$.ajax({
			type		: 'POST',
			url			: siteUrl + ajaxurl,
			data		: {
				action		: 'bdb_create_mysql_dump',
				token		: securetoken
			},
			beforeSend	: function(){
				$('div.loading').show();
			}
		}).done(function(erg){
			if(erg == ""){
				setTimeout(function(){bdb_loadContent();},1000);
			}else{
				alert("Error\n\n"+erg);
			}
		}).error(function(erg){
			alert("Error\n\n"+erg);
			$('div.loading').hide();
		});
	}
	
	function bdb_restoreData(){
		var val = $('.backupdb-radio:checked').val();
		if(typeof val === 'undefined' || val.length < 1){
			alert("Please select a file first.");
			return;
		}

		$.ajax({
			type		: 'POST',
			url			: siteUrl + ajaxurl,
			data		: {
				action		: 'bdb_load_mysql_dump',
				path		: val,
				token		: securetoken
			},
			beforeSend	: function(){
				$('div.loading').show();
			}
		}).done(function(erg){
			if(erg == ""){
				location.reload();
			}else{
				alert("Error\n\n"+erg);
			}
			$('div.loading').hide();
		}).error(function(erg){
			alert("Error\n\n"+erg);
			$('div.loading').hide();
		});
	}
/************************************************************************
 * Helper
 **************************************************************************/
	function trace(msg){try{console.log(msg);}catch(e){};}
	
})(jQuery);
