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

	public function get_voter_table()
	{
		return $this->_vote_table;
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
	public $blog_id = 0;
	public $user_id = 0;

	public function __construct()
	{
		global $blog_id;
		$this->blog_id = (int) $blog_id;
		$this->user_id = (int) get_current_user_id();
		$this->_model = new WP_Post_Object_Voter_Model;
	}

	/**
	 * 1 for thumbs-up; -1 for thumbs-down; false for no vote;
	 */
	protected function _get_existing_vote( $blog_id = 0, $object_id = 0, $user_id = 0 )
	{
		global $wpdb;

		$blog_id = (int) $blog_id;
		$object_id = (int) $object_id;
		$user_id = (int) $user_id;

		$table = $this->_model->get_voter_table();

		$results = $wpdb->get_results( "
			SELECT vote_id, vote 
				FROM {$table}
				WHERE 
					blog_id = {$blog_id} AND
					object_id = {$blog_id} AND
					user_id = {$user_id}
				LIMIT 1
			"
		);
		
		if ( is_array( $results ) ) {
			$result = array_shift( $results );
			if ( ! empty( $result->vote_id ) ) {
				return $result->vote;
			}
		}

		return false;
	}

	protected function _add_vote( $blog_id = 0, $object_id = 0, $user_id = 0, $vote = 0  )
	{
		global $wpdb;

		$blog_id = (int) $blog_id;
		$object_id = (int) $object_id;
		$user_id = (int) $user_id;
		$vote = (int) $vote;

		$table = $this->_model->get_voter_table();

		return $wpdb->query(
			"INSERT INTO {$table}
				( blog_id, object_id, user_id, vote ) 
				VALUES
				( {$blog_id}, {$object_id}, {$user_id}, {$vote} )
			"
		);
	}

	protected function _update_vote( $blog_id = 0, $object_id = 0, $user_id = 0, $vote = 0  )
	{
		global $wpdb;

		$blog_id = (int) $blog_id;
		$object_id = (int) $object_id;
		$user_id = (int) $user_id;
		$vote = (int) $vote;

		$table = $this->_model->get_voter_table();
		
		return $wpdb->query(
			"UPDATE {$table} 
				SET vote_id = {$vote_id} 
				WHERE
					blog_id = {$blog_id} AND
					object_id = {$object_id} AND
					user_id = {$user_id}
			"
		);
	}

	public function vote_thumbs_down( $object_id = 0 )
	{
		$object_id = (int) $object_id;

		if ( false === $this->_get_existing_vote( $this->blog_id, $object_id, $this->user_id ) ) {
			$this->_add_vote( $this->user_id, $object_id, $this->user_id, -1  )
		} else {
			$this->_update_vote( $this->user_id, $object_id, $this->user_id, -1  )
		}
	}


	public function vote_thumbs_up( $object_id = 0 )
	{
		$object_id = (int) $object_id;

		if ( false === $this->_get_existing_vote( $this->blog_id, $object_id, $this->user_id ) ) {
			$this->_add_vote( $this->user_id, $object_id, $this->user_id, 1  )
		} else {
			$this->_update_vote( $this->user_id, $object_id, $this->user_id, 1  )
		}
	}
}

function load_post_object_voter()
{
	global $post_object_voter;
	$post_object_voter = new WP_Post_Object_Voter;
}

add_action( 'plugins_loaded', 'load_post_object_voter', 20 );

}
