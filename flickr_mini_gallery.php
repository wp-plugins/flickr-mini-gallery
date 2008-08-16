<?php
/*
Plugin Name: Flickr mini gallery
Plugin URI: http://www.felipesk.com/flickr-mini-gallery/
Description: Mini flickr gallery is a easy way to embed super flexible galeries from any flickr account or group, using different parameters to customise it. This plugin is a gallery generator / lightbox view combo. Very easy to add to your post or page. Type a little code like [miniflickr user="yourusercode" tags="tag1&tag2"] and done. You'll have a super flexible gallery on your post
Author: Felipe Skroski	
Licence:GPL 3
Version: 1.0
Author URI: www.felipesk.com
*/

/*  Copyright 2008  FELIPE SKROSKI  (email : felipeskroski[at.]gmail[dot.]com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/




//add jquery and lightbox
function jquery_lightbox_scripts(){
	$path = '/wp-content/plugins/flickr-mini-gallery/js';

	if (function_exists('wp_enqueue_script')) {
		wp_deregister_script('jquery');
		//wp_enqueue_script('jquery');
		$opts = mfg_get_options();
		$format = $opts['mfg_thumbformat'];
		wp_enqueue_script('jquery', $path.'/jquery-1.2.6.pack.js', false, '1.2.6');
		echo '<script type="text/javascript">
			var theblogurl ="'.get_bloginfo('url').'";
			var flickr_mini_gallery_img_format ="'.$format.'";
		</script>';
		wp_enqueue_script('jquerylightbox', $path.'/jquery.lightbox-0.5.js', array('jquery'),'0.5');
		wp_enqueue_script('miniflickr', $path.'/miniflickr.js', array('jquery'),'0.1');
	}
	
}

add_action('wp_head', 'jquery_lightbox_scripts',5);


//builds the gallery
function build_mini_gallery($atts, $content='Loading... mini-flickr-gallery by Felipe Skroski') {
	$opts = mfg_get_options();
	$usr = $opts['mfg_userid'];
	$lang = $opts['mfg_language'];
	extract(shortcode_atts(array(
		'lang' 				=>'',
		'user_id' 			=> $usr,
		'tags' 				=>'',
		'tag_mode'			=>'',
		'min_upload_date'	=>'',
		'max_upload_date'	=>'', 
		'min_taken_date'	=>'',
		'max_taken_date'	=>'',
		'sort'				=>'',
		'bbox'				=>'',
		'safe_search'		=>'',
		'content_type'		=>'',
		'group_id'			=>'',
		'lat'				=>'',
		'lon'				=>'',
		'radius_units'		=>'',
		'per_page'			=>'30',
		'content'			=>$content,
	), $atts));
	$lang = "{$lang}";
	if(function_exists(xlanguage_current_language_code)){
		$code = xlanguage_current_language_code();
	}else{
		$code = $lang;
	}
	
	if($code == $lang or $lang==''){
		$flickr_gal = "<div class=\"flickr-mini-gallery\" rel=\"user_id={$user_id}&tags={$tags}&min_upload_date={$min_upload_date}&max_upload_date={$max_upload_date}&min_taken_date={$min_taken_date}&max_taken_date={$max_taken_date}&sort={$sort}&bbox={$bbox}&safe_search={$safe_search}&content_type={$content_type}&group_id={$group_id}&lat={$lat}&lon={$lon}&radius_units={$radius_units}&per_page={$per_page}\">{$content}</div>";
	}else{
		$flickr_gal ="";
	}
	return $flickr_gal;
}
add_shortcode('miniflickr', 'build_mini_gallery');

//----------------------------------------------------//
//OPTIONS
//----------------------------------------------------//
function mfg_get_options() {
	$mfg_userid = get_option('mfg_userid');
	$mfg_thumbformat = get_option('mfg_thumbformat');

	
	// Extra paranoia:
	if(empty($mfg_userid))
		$mfg_userid = '';
	if(empty($mfg_thumbformat))
		$mfg_thumbformat = '_s';
		
	return array(
		'mfg_userid' => $mfg_userid,
		'mfg_thumbformat' => $mfg_thumbformat,
	);
}




//----------------------------------------------------//
//USER INTERFACE
//----------------------------------------------------//

// Options update page:
// action function for above hook
function mfg_add_pages() {
    // Add a new submenu under Options:
    add_options_page('Mini Flickr Gallery', 'Mini Flickr Gallery', 8, 'miniflickrgallery', 'mfg_options_page');
}
// mfg_options_page() displays the page content for the Options submenu
function mfg_options_page() {
	if($_POST['action'] == 'update'){
		update_option('mfg_userid', $_POST['mfg_userid'] );
		update_option('mfg_thumbformat', $_POST['mfg_thumbformat'] );
		?><div class="updated"><p><strong><?php _e('Options saved.', 'eg_trans_domain' ); ?></strong></p></div><?php
	};

    ?>
	<div class='wrap'>
		<h2>Mini Flickr Gallery Options</h2>
		<form method='post'>
			<?php wp_nonce_field('miniflickrgallery_options'); ?>
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="mfg_userid,mfg_thumbformat,mfg_language" />
			<table class="form-table">
				<tbody>
					<tr valign="top">
					<th scope="row"><?php _e("Default Flickr User ID:", 'eg_trans_domain' ); ?></th>
						<td>
						<input type="text" name="mfg_userid" value="<?php echo get_option('mfg_userid'); ?>" />
						<br/>
						<a href="http://idgettr.com/">Find your flickr id</a>
						</td>
					</tr>
					
					<tr valign="top">
					<th scope="row"><?php _e("Thumbnail Format:", 'eg_trans_domain' ); ?></th>
						<td>
							<p>
							<?php $img = get_option('mfg_thumbformat'); ?>
								<select name="mfg_thumbformat">
									<option value ="_s" <?php if($img == "_s")echo 'selected="selected"'; ?>>Square</option>
  									<option value ="_t" <?php if($img == "_t")echo 'selected="selected"'; ?>>Thumbnail</option>
								</select>
								<br/>
						Square is 75px x 75px and Thumbnail is 100px max						</p></td>
					</tr>
					
					
				</tbody>
			</table>
			
			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
<?php
}

add_action('admin_menu', 'mfg_add_pages');

?>