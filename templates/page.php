<?php
if(!is_admin())exit();
$_SESSION['IS_ADMIN'] = is_admin();
$_SESSION['SECURE_TOKEN'] = uniqid('backupdb', true);
?>

<script>
	var siteUrl = "<?php echo get_site_url(); ?>";
	var securetoken = "<?php echo $_SESSION['SECURE_TOKEN']; ?>";
</script>

<div id="backend" class="wrap">
	<h2><?php echo BACKUPDB_TITLE; ?></h2>
    
    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
        	<div id="post-body-content">
            	 <table class="wp-list-table widefat fixed pages" id="backupdb-content">
                    <thead>
                    	<tr>
                            <th scope="col">Erstellt</th>
                            <th scope="col">Zuletzt geändert</th>
                            <th scope="col">Einspielen</th>
                        </tr>
                    </thead>
                    <tbody id="the-list"></tbody>
                    <tfoot>
                    	<tr>
                            <th scope="col">Erstellt</th>
                            <th scope="col">Zuletzt geändert</th>
                            <th scope="col">Einspielen</th>
                        </tr>
                    </tfoot>
                </table>
            </div><!--/#post-body-content-->
            <div id="postbox-container-1">
            	<div id="side-sortables" class="meta-box-sortables ui-sortable">
                	<div class="postbox">
                        <h3 class="hndle"><span>Aktionen</span></h3>
                        <div class="inside">
                        	<button type="submit" class="button-primary" id="backup_data">Jetzt sichern</button>
                            <button type="submit" class="button-secondary" id="restore_data">Jetzt einspielen</button>
                            <div class="loading">Loading&#8230;</div>
                        </div>
                    </div><!--/.postbox-->
                </div><!--/#side-sortables-->
            </div><!--/#bpostbox-container-1-->
        </div><!--/#post-body-->
    </div><!--/#poststuff-->
</div><!--/#backend-->