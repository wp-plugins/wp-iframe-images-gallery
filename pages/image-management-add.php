<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$iframe_errors = array();
$iframe_success = '';
$iframe_error_found = FALSE;

// Preset the form fields
$form = array(
	'iframe_path' => '',
	'iframe_link' => '',
	'iframe_target' => '',
	'iframe_title' => '',
	'iframe_order' => '',
	'iframe_status' => '',
	'iframe_type' => ''
);

// Form submitted, check the data
if (isset($_POST['iframe_form_submit']) && $_POST['iframe_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('iframe_form_add');
	
	$form['iframe_path'] = isset($_POST['iframe_path']) ? $_POST['iframe_path'] : '';
	if ($form['iframe_path'] == '')
	{
		$iframe_errors[] = __('Please enter the image path.', 'iframe-images');
		$iframe_error_found = TRUE;
	}

	$form['iframe_link'] = isset($_POST['iframe_link']) ? $_POST['iframe_link'] : '';
	if ($form['iframe_link'] == '')
	{
		$iframe_errors[] = __('Please enter the target link.', 'iframe-images');
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
		$sql = $wpdb->prepare(
			"INSERT INTO `".WP_iframe_TABLE."`
			(`iframe_path`, `iframe_link`, `iframe_target`, `iframe_title`, `iframe_order`, `iframe_status`, `iframe_type`)
			VALUES(%s, %s, %s, %s, %d, %s, %s)",
			array($form['iframe_path'], $form['iframe_link'], $form['iframe_target'], 
						$form['iframe_title'], $form['iframe_order'], $form['iframe_status'], $form['iframe_type'])
		);
		$wpdb->query($sql);
		$iframe_success = __('New image details was successfully added.', 'iframe-images');
		
		// Reset the form fields
		$form = array(
			'iframe_path' => '',
			'iframe_link' => '',
			'iframe_target' => '',
			'iframe_title' => '',
			'iframe_order' => '',
			'iframe_status' => '',
			'iframe_type' => ''
		);
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
		<p><strong><?php echo $iframe_success; ?> 
		<a href="<?php echo WP_iframe_ADMIN_URL; ?>"><?php _e('Click here', 'iframe-images'); ?></a> <?php _e('to view the details', 'iframe-images'); ?></strong></p>
	</div>
	<?php
}
?>
<script language="JavaScript" src="<?php echo WP_iframe_PLUGIN_URL; ?>/pages/setting.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
			var img_imagetitle = uploaded_image.toJSON().title;
            // Let's assign the url value to the input field
            $('#iframe_path').val(img_imageurl);
			$('#iframe_title').val(img_imagetitle);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery'); // jQuery
wp_enqueue_media(); // This will enqueue the Media Uploader script
?>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('iFrame Images Gallery', 'iframe-images'); ?></h2>
	<form name="iframe_form" method="post" action="#" onsubmit="return iframe_submit()"  >
      <h3><?php _e('Add new image details', 'iframe-images'); ?></h3>
      <label for="tag-image"><?php _e('Enter image path (URL)', 'iframe-images'); ?></label>
      <input name="iframe_path" type="text" id="iframe_path" value="" size="80" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
      <p><?php _e('Where is the picture located on the internet', 'iframe-images'); ?> (ex: http://www.gopiplus.com/work/wp-content/uploads/pluginimages/250x167/250x167_2.jpg)</p>
      <label for="tag-link"><?php _e('Enter target link', 'iframe-images'); ?></label>
      <input name="iframe_link" type="text" id="iframe_link" value="#" size="80" />
      <p><?php _e('When someone clicks on the picture, where do you want to send them. If you dont have any link enter #.', 'iframe-images'); ?></p>
      <label for="tag-target"><?php _e('Select target option', 'iframe-images'); ?></label>
      <select name="iframe_target" id="iframe_target">
        <option value='_blank'>_blank</option>
        <option value='_parent'>_parent</option>
        <option value='_self'>_self</option>
        <option value='_new'>_new</option>
      </select>
      <p><?php _e('Do you want to open link in new window?', 'iframe-images'); ?></p>
      <label for="tag-title"><?php _e('Enter image title (Image alt text)', 'iframe-images'); ?></label>
      <input name="iframe_title" type="text" id="iframe_title" value="" size="80" />
      <p><?php _e('Enter image title, We are using this title for image alt text.', 'iframe-images'); ?></p>
      <label for="tag-select-gallery-group"><?php _e('Select gallery group', 'iframe-images'); ?></label>
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
		$arrDistinctDatas = array_unique($arrDistinctData, SORT_REGULAR);
		foreach ($arrDistinctDatas as $arrDistinct)
		{
			?><option value='<?php echo $arrDistinct["iframe_type"]; ?>'><?php echo $arrDistinct["iframe_type"]; ?></option><?php
		}
		?>
		</select>
      <p><?php _e('This is to group the images. Select your gallery group.', 'iframe-images'); ?></p>
      <label for="tag-display-status"><?php _e('Display status', 'iframe-images'); ?></label>
      <select name="iframe_status" id="iframe_status">
        <option value='YES' selected="selected">Yes</option>
        <option value='NO'>No</option>
      </select>
      <p><?php _e('Do you want the picture to show in your galler?', 'iframe-images'); ?></p>
      <label for="tag-display-order"><?php _e('Display order', 'iframe-images'); ?></label>
      <input name="iframe_order" type="text" id="iframe_order" size="10" value="1" maxlength="3" />
      <p><?php _e('What order should the picture be played in. should it come 1st, 2nd, 3rd, etc.', 'iframe-images'); ?></p>
      <input name="iframe_id" id="iframe_id" type="hidden" value="">
      <input type="hidden" name="iframe_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Insert Details', 'iframe-images'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="iframe_redirect()" value="<?php _e('Cancel', 'iframe-images'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="iframe_help()" value="<?php _e('Help', 'iframe-images'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('iframe_form_add'); ?>
    </form>
</div>
<p class="description">
	<?php _e('Check official website for more information', 'iframe-images'); ?>
	<a target="_blank" href="<?php echo WP_iframe_FAV; ?>"><?php _e('click here', 'iframe-images'); ?></a>
</p>
</div>