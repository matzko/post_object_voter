<?php

if ( ! class_exists( '' ) ) {

class WP_Post_Object_Voter
{
	public function __construct()
	{
		
	}
}

class WP_Post_Object_Voter_Model
{
	protected $_table_suffix = 'post_user_votes';
	protected $_vote_table = '';

	public function __construct()
	{
		global $wpdb;
		$this->_vote_table = $wpdb->base_prefix . $this->_table_suffix;
	}

	protected function _get_create_table_query()
	{
		global $wpdb;

		$charset_collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty($wpdb->charset) )
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty($wpdb->collate) )
				$charset_collate .= " COLLATE $wpdb->collate";
		}

		$query = "CREATE TABLE {$this->_vote_table} (
			vote_id BIGINT(20) UNSIGNED NOT NULL auto_increment,
			blog_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
			object_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
			user_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
			vote TINYINT UNSIGNED NOT NULL DEFAULT 0,
			PRIMARY KEY (vote_id),
			KEY blog_id (blog_id),
			KEY object_id (object_id),
			KEY user_id (user_id)
		) {$charset_collate}; ";

		return $query;
	}

	public function create_voter_table()
	{
		global $wpdb;
		$query = $this->_get_create_table_query();
		error_log('query is ' . $query );
		$wpdb->query( $query );
	}

	public function does_voter_table_exist()
	{
		global $wpdb;

		return (bool) ( $wpdb->get_var("SHOW TABLES LIKE '{$this->_vote_table}'") == $this->_vote_table );
	}
}

class WP_Post_Object_Vote
{
	// public function 
}

function load_post_object_voter()
{
	global $post_object_voter;
	$post_object_voter = new WP_Post_Object_Voter;
}

add_action( 'plugins_loaded', 'load_post_object_voter', 20 );

}
