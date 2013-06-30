<?php
// Form submitted, check the data
if (isset($_POST['frm_iframe_display']) && $_POST['frm_iframe_display'] == 'yes')
{
	$did = isset($_GET['did']) ? $_GET['did'] : '0';
	
	$iframe_success = '';
	$iframe_success_msg = FALSE;
	
	// First check if ID exist with requested ID
	$sSql = $wpdb->prepare(
		"SELECT COUNT(*) AS `count` FROM ".WP_iframe_TABLE."
		WHERE `iframe_id` = %d",
		array($did)
	);
	$result = '0';
	$result = $wpdb->get_var($sSql);
	
	if ($result != '1')
	{
		?><div class="error fade"><p><strong>Oops, selected details doesn't exist (1).</strong></p></div><?php
	}
	else
	{
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('iframe_form_show');
			
			//	Delete selected record from the table
			$sSql = $wpdb->prepare("DELETE FROM `".WP_iframe_TABLE."`
					WHERE `iframe_id` = %d
					LIMIT 1", $did);
			$wpdb->query($sSql);
			
			//	Set success message
			$iframe_success_msg = TRUE;
			$iframe_success = __('Selected record was successfully deleted.', WP_iframe_UNIQUE_NAME);
		}
	}
	
	if ($iframe_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $iframe_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-post"></div>
    <h2><?php echo WP_iframe_TITLE; ?><a class="add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/options-general.php?page=iframe-images-gallery&amp;ac=add">Add New</a></h2>
    <div class="tool-box">
	<?php
		$sSql = "SELECT * FROM `".WP_iframe_TABLE."` order by iframe_type, iframe_order";
		$myData = array();
		$myData = $wpdb->get_results($sSql, ARRAY_A);
		?>
		<script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-iframe-images-gallery/pages/setting.js"></script>
		<form name="frm_iframe_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th class="check-column" scope="row"><input type="checkbox" name="iframe_group_item[]" /></th>
			<th scope="col">Title (Alt Text)</th>
            <th scope="col">URL</th>
			<th scope="col">Type</th>
			<th scope="col">Target</th>
            <th scope="col">Order</th>
            <th scope="col">Display</th>
          </tr>
        </thead>
		<tfoot>
          <tr>
            <th class="check-column" scope="row"><input type="checkbox" name="iframe_group_item[]" /></th>
			<th scope="col">Title (Alt Text)</th>
			<th scope="col">Type</th>
            <th scope="col">URL</th>
			<th scope="col">Target</th>
            <th scope="col">Order</th>
            <th scope="col">Display</th>
          </tr>
        </tfoot>
		<tbody>
			<?php 
			$i = 0;
			$displayisthere = FALSE;
			foreach ($myData as $data)
			{
				if($data['iframe_status'] == 'YES') 
				{
					$displayisthere = TRUE; 
				}
				?>
				<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td align="left"><input type="checkbox" value="<?php echo $data['iframe_id']; ?>" name="iframe_group_item[]"></th>
					<td>
					<?php echo stripslashes($data['iframe_title']); ?>
					<div class="row-actions">
						<span class="edit"><a title="Edit" href="<?php echo get_option('siteurl'); ?>/wp-admin/options-general.php?page=iframe-images-gallery&amp;ac=edit&amp;did=<?php echo $data['iframe_id']; ?>">Edit</a> | </span>
						<span class="trash"><a onClick="javascript:iframe_delete('<?php echo $data['iframe_id']; ?>')" href="javascript:void(0);">Delete</a></span> 
					</div>
					</td>
					<td><a target="_blank" href="<?php echo $data['iframe_path']; ?>" target="_blank"><?php echo $data['iframe_path']; ?></a></td>
					<td><?php echo $data['iframe_type']; ?></td>
					<td><?php echo $data['iframe_target']; ?></td>
					<td><?php echo $data['iframe_order']; ?></td>
					<td><?php echo $data['iframe_status']; ?></td>
				</tr>
				<?php 
				$i = $i+1; 
				} 
			?>
			<?php 
			if ($displayisthere == FALSE) 
			{ 
				?><tr><td colspan="7" align="center">No records available with display status (YES).</td></tr><?php 
			} 
			?>
		</tbody>
        </table>
		<?php wp_nonce_field('iframe_form_show'); ?>
		<input type="hidden" name="frm_iframe_display" value="yes"/>
      </form>	
	  <div class="tablenav">
	  <h2>
	  <a class="button add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/options-general.php?page=iframe-images-gallery&amp;ac=add">Add New</a>
	  <!--<a class="button add-new-h2" href="<?php echo get_option('siteurl'); ?>/wp-admin/options-general.php?page=iframe-images-gallery&amp;ac=set">Widget setting</a>-->
	  <a class="button add-new-h2" target="_blank" href="<?php echo WP_iframe_FAV; ?>">Help</a>
	  </h2>
	  </div>
	  <div style="height:5px;"></div>
	<h3>Plugin configuration option</h3>
	<ol>
		<li>Add directly in to the theme using PHP code.</li>
		<li>Add the plugin in the posts or pages using short code.</li>
	</ol>
	  <p class="description"><?php echo WP_iframe_LINK; ?></p>
	</div>
</div>