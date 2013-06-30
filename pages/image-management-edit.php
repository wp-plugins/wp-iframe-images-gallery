<div class="wrap">
<?php
$did = isset($_GET['did']) ? $_GET['did'] : '0';

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
	?><div class="error fade"><p><strong>Oops, selected details doesn't exist.</strong></p></div><?php
}
else
{
	$iframe_errors = array();
	$iframe_success = '';
	$iframe_error_found = FALSE;
	
	$sSql = $wpdb->prepare("
		SELECT *
		FROM `".WP_iframe_TABLE."`
		WHERE `iframe_id` = %d
		LIMIT 1
		",
		array($did)
	);
	$data = array();
	$data = $wpdb->get_row($sSql, ARRAY_A);
	
	// Preset the form fields
	$form = array(
		'iframe_path' => $data['iframe_path'],
		'iframe_link' => $data['iframe_link'],
		'iframe_target' => $data['iframe_target'],
		'iframe_title' => $data['iframe_title'],
		'iframe_order' => $data['iframe_order'],
		'iframe_status' => $data['iframe_status'],
		'iframe_type' => $data['iframe_type']
	);
}
// Form submitted, check the data
if (isset($_POST['iframe_form_submit']) && $_POST['iframe_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('iframe_form_edit');
	
	$form['iframe_path'] = isset($_POST['iframe_path']) ? $_POST['iframe_path'] : '';
	if ($form['iframe_path'] == '')
	{
		$iframe_errors[] = __('Please enter the image path.', WP_iframe_UNIQUE_NAME);
		$iframe_error_found = TRUE;
	}

	$form['iframe_link'] = isset($_POST['iframe_link']) ? $_POST['iframe_link'] : '';
	if ($form['iframe_link'] == '')
	{
		$iframe_errors[] = __('Please enter the target link.', WP_iframe_UNIQUE_NAME);
		$iframe_error_found = TRUE;
	}
	
	$form['iframe_target'] = isset($_POST['iframe_target']) ? $_POST['iframe_target'] : '';
	$form['iframe_title'] = isset($_POST['iframe_title']) ? $_POST['iframe_title'] : '';
	$form['iframe_order'] = isset($_POST['iframe_order']) ? $_POST['iframe_order'] : '';
	$form['iframe_status'] = isset($_POST['iframe_status']) ? $_POST['iframe_status'] : '';
	$form['iframe_type'] = isset($_POST['iframe_type']) ? $_POST['iframe_type'] : '';

	//	No errors found, we can add this Group to the table
	if ($iframe_error_found == FALSE)
	{	
		$sSql = $wpdb->prepare(
				"UPDATE `".WP_iframe_TABLE."`
				SET `iframe_path` = %s,
				`iframe_link` = %s,
				`iframe_target` = %s,
				`iframe_title` = %s,
				`iframe_order` = %d,
				`iframe_status` = %s,
				`iframe_type` = %s
				WHERE iframe_id = %d
				LIMIT 1",
				array($form['iframe_path'], $form['iframe_link'], $form['iframe_target'], $form['iframe_title'], $form['iframe_order'], $form['iframe_status'], $form['iframe_type'], $did)
			);
		$wpdb->query($sSql);
		
		$iframe_success = 'Image details was successfully updated.';
	}
}

if ($iframe_error_found == TRUE && isset($iframe_errors[0]) == TRUE)
{
?>
  <div class="error fade">
    <p><strong><?php echo $iframe_errors[0]; ?></strong></p>
  </div>
  <?php
}
if ($iframe_error_found == FALSE && strlen($iframe_success) > 0)
{
?>
  <div class="updated fade">
    <p><strong><?php echo $iframe_success; ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/options-general.php?page=iframe-images-gallery">Click here</a> to view the details</strong></p>
  </div>
  <?php
}
?>
<script language="JavaScript" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-iframe-images-gallery/pages/setting.js"></script>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php echo WP_iframe_TITLE; ?></h2>
	<form name="iframe_form" method="post" action="#" onsubmit="return iframe_submit()"  >
      <h3>Update image details</h3>
      <label for="tag-image">Enter image path</label>
      <input name="iframe_path" type="text" id="iframe_path" value="<?php echo $form['iframe_path']; ?>" size="125" />
      <p>Where is the picture located on the internet</p>
      <label for="tag-link">Enter target link</label>
      <input name="iframe_link" type="text" id="iframe_link" value="<?php echo $form['iframe_link']; ?>" size="125" />
      <p>When someone clicks on the picture, where do you want to send them</p>
      <label for="tag-target">Select target option</label>
      <select name="iframe_target" id="iframe_target">
        <option value='_blank' <?php if($form['iframe_target']=='_blank') { echo 'selected' ; } ?>>_blank</option>
        <option value='_parent' <?php if($form['iframe_target']=='_parent') { echo 'selected' ; } ?>>_parent</option>
        <option value='_self' <?php if($form['iframe_target']=='_self') { echo 'selected' ; } ?>>_self</option>
        <option value='_new' <?php if($form['iframe_target']=='_new') { echo 'selected' ; } ?>>_new</option>
      </select>
      <p>Do you want to open link in new window?</p>
      <label for="tag-title">Enter image reference</label>
      <input name="iframe_title" type="text" id="iframe_title" value="<?php echo esc_html(stripslashes($form['iframe_title'])); ?>" size="75" />
      <p>Enter image reference. This is only for reference.</p>
      <label for="tag-select-gallery-group">Select gallery type/group</label>
	  <select name="iframe_type" id="iframe_type">
		<?php
		$sSql = "SELECT distinct(iframe_type) as iframe_type FROM `".WP_iframe_TABLE."` order by iframe_type";
		$myDistinctData = array();
		$arrDistinctDatas = array();
		$myDistinctData = $wpdb->get_results($sSql, ARRAY_A);
		$i = 0;
		foreach ($myDistinctData as $DistinctData)
		{
			$arrDistinctData[$i]["iframe_type"] = strtoupper($DistinctData['iframe_type']);
			$i = $i+1;
		}
		for($j=$i; $j<$i+5; $j++)
		{
			$arrDistinctData[$j]["iframe_type"] = "GROUP" . $j;
		}
		$selected = "";
		$arrDistinctDatas = array_unique($arrDistinctData, SORT_REGULAR);
		foreach ($arrDistinctDatas as $arrDistinct)
		{
			if(strtoupper($form['iframe_type']) == strtoupper($arrDistinct["iframe_type"]) ) 
			{ 
				$selected = "selected"; 
			}
			?>
			<option value='<?php echo $arrDistinct["iframe_type"]; ?>' <?php echo $selected; ?>><?php echo strtoupper($arrDistinct["iframe_type"]); ?></option>
			<?php
			$selected = "";
		}
		?>
		</select>
      <p>This is to group the images. Select your slideshow group. </p>
      <label for="tag-display-status">Display status</label>
      <select name="iframe_status" id="iframe_status">
        <option value='YES' <?php if($form['iframe_status']=='YES') { echo 'selected' ; } ?>>Yes</option>
        <option value='NO' <?php if($form['iframe_status']=='NO') { echo 'selected' ; } ?>>No</option>
      </select>
      <p>Do you want the picture to show in your galler?</p>
      <label for="tag-display-order">Display order</label>
      <input name="iframe_order" type="text" id="iframe_order" size="10" value="<?php echo $form['iframe_order']; ?>" maxlength="3" />
      <p>What order should the picture be played in. should it come 1st, 2nd, 3rd, etc.</p>
      <input name="iframe_id" id="iframe_id" type="hidden" value="">
      <input type="hidden" name="iframe_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="Update Details" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="iframe_redirect()" value="Cancel" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="iframe_help()" value="Help" type="button" />
      </p>
	  <?php wp_nonce_field('iframe_form_edit'); ?>
    </form>
</div>
<p class="description"><?php echo WP_iframe_LINK; ?></p>
</div>