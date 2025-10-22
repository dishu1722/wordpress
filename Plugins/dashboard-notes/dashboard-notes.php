<?php 
/*
Plugin Name: Dashboard Notes
Description: Adds custom notes to the WordPress dashboard.
Version: 1.1
Author: Diksha Sharma
*/

// Load Quill.js and its styles
function dn_enqueue_quill_assets() {
	wp_enqueue_script('quill-js', 'https://cdn.quilljs.com/1.3.6/quill.js', array(), null, true);
	wp_enqueue_style('quill-css', 'https://cdn.quilljs.com/1.3.6/quill.snow.css');
}
add_action('admin_enqueue_scripts', 'dn_enqueue_quill_assets');

// Show Welcome Message
function dn_add_shortcode_note(){
	echo '<div class="notice notice-info"><p><strong>Note:</strong> Welcome to your WordPress site, Diksha!</p></div>';
}
add_action('admin_notices', 'dn_add_shortcode_note');

// Dashboard Note Form with Quill Editor
function dn_dashboard_note_form(){
	?>
	<style>
		#dn-quill-editor {
			background: #fff;
			border: 1px solid #ccc;
			border-radius: 4px;
			padding: 10px;
			max-width: 600px;
			height: 150px;
			margin-bottom: 10px;
		}
		.dn-note-form .button {
			margin-right: 5px;
		}
	</style>
	<div class="notice notice-info dn-note-form">
		<form method="post">
			<p><strong>Add a Dashboard Note:</strong></p>
			<div id="dn-quill-editor"></div>
			<input type="hidden" name="dn_note" id="dn_note"><br>
			<input type="submit" name="dn_submit_note" value="Save Note" class="button button-primary">
			<input type="submit" name="dn_clear_note" value="Clear Note" class="button">
		</form>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const quill = new Quill('#dn-quill-editor', {
				theme: 'snow',
				modules: {
					toolbar: [['bold', 'italic'], [{ 'list': 'bullet' }]]
				}
			});

			const hiddenInput = document.getElementById('dn_note');
			const form = hiddenInput.closest('form');

			// Load saved content into editor
			const savedContent = <?php echo json_encode(get_option('dn_dashboard_note')); ?>;
			quill.root.innerHTML = savedContent;

			// On form submit, copy editor content to hidden input
			form.addEventListener('submit', function () {
				hiddenInput.value = quill.root.innerHTML;
			});
		});
	</script>
	<?php
}
add_action('admin_notices', 'dn_dashboard_note_form');

// Handle note submission
function dn_handle_note_submission(){
	if (isset($_POST['dn_submit_note']) && !empty($_POST['dn_note'])){
		$note = wp_kses_post($_POST['dn_note']);
		update_option('dn_dashboard_note', $note);
		update_option('dn_note_saved', true);
	}
}
add_action('admin_init', 'dn_handle_note_submission');

// Display saved note
function dn_display_saved_note() {
	$note = get_option('dn_dashboard_note');
	if($note){
		echo '<div class="notice notice-success"><p><strong>Your Note:</strong></p>' . wp_kses_post($note) . '</div>';
	}
}
add_action('admin_notices', 'dn_display_saved_note');

// Clear note
function dn_clear_note(){
	if (isset($_POST['dn_clear_note'])){
		delete_option('dn_dashboard_note');
	}
}
add_action('admin_init', 'dn_clear_note');

// Display success message
function dn_display_success_message(){
	if(get_option('dn_note_saved')){
		echo '<div class="notice notice-success is-dismissible"><p>✅ Note saved successfully!</p></div>';
		delete_option('dn_note_saved');
	}
}
add_action('admin_notices', 'dn_display_success_message');
?>