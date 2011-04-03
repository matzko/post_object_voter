<?php
/*
Plugin Name: Post Voter
Plugin URI:
Description: A WordPress plugin that allows users to vote on any post-type object.
Author: Austin Matzko
Author URI: http://austinmatzko.com
Version: 1.0
*/


if ( version_compare( PHP_VERSION, '5.2.0') >= 0 ) {

	require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'core.php';
	
} else {
	
	function post_voter_php_version_message()
	{
		?>
		<div id="post-voter-warning" class="updated fade error">
			<p>
				<?php 
				printf(
					__('<strong>ERROR</strong>: Your WordPress site is using an outdated version of PHP, %s.  Version 5.2 of PHP is required to use the Post Voter plugin. Please ask your host to update.', 'post-voter'),
					PHP_VERSION
				);
				?>
			</p>
		</div>
		<?php
	}

	add_action('admin_notices', 'post_voter_php_version_message');
}

function post_voter_init_event()
{
	load_plugin_textdomain('post-voter', null, dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'l10n');
}

add_action('init', 'post_voter_init_event');
