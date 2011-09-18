/*
Plugin Name: iFrame Images Gallery
Plugin URI: http://www.gopiplus.com
Description: iFrame Images Gallery
Author: Gopi.R
Version: 1.0
*/


function iframe_submit()
{
	if(document.iframe_form.iframe_path.value=="")
	{
		alert("Please enter the image path.")
		document.iframe_form.iframe_path.focus();
		return false;
	}
	if((document.iframe_form.iframe_order.value!="") && isNaN(document.iframe_form.iframe_order.value))
	{
		alert("Please enter the display order, only number.")
		document.iframe_form.iframe_order.focus();
		return false;
	}
	if(document.iframe_form.iframe_type.value=="")
	{
		alert("Please select the gallery group.")
		document.iframe_form.iframe_type.focus();
		return false;
	}
}

function iframe_delete(id)
{
	if(confirm("Do you want to delete this record?"))
	{
		document.frm_iframe_display.action="options-general.php?page=wp-iframe-images-gallery/wp-iframe-images-gallery.php&AC=DEL&DID="+id;
		document.frm_iframe_display.submit();
	}
}	

function iframe_redirect()
{
	window.location = "options-general.php?page=wp-iframe-images-gallery/wp-iframe-images-gallery.php";
}
