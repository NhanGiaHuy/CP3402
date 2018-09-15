<?php

/*

Copyright 2014 Dario Curvino (email : d.curvino@tiscali.it)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

function yasr_stats_tabs($active_tab) {

	?>

    <h2 class="nav-tab-wrapper yasr-no-underline">

        <a href="?page=yasr_stats_page&tab=logs" class="nav-tab <?php if ($active_tab === 'logs') echo 'nav-tab-active'; ?>" >
			<?php _e("Logs", 'yet-another-stars-rating'); ?>
        </a>
		<?php do_action( 'yasr_add_stats_tab', $active_tab ); ?>
        <a href="?page=yasr_settings_page-pricing" class="nav-tab">
            <?php _e("Upgrade", 'yet-another-stars-rating'); ?>
        </a>

    </h2>

	<?php

}

/**
 * Create a new table class that will extend the WP_List_Table
 */
class YASR_Stats_Log_List_Table extends YASR_WP_List_Table
{
	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items()
	{
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		//print bulk_Actions
		$this->get_bulk_actions();

		$this->process_bulk_action();



		global $wpdb;
		$data = $wpdb->get_results ("SELECT * FROM ". YASR_LOG_TABLE . " ORDER BY date ASC", ARRAY_A);


		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = 15;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;

	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns()
	{

	    if (YASR_ENABLE_IP === 'yes') {

		    $columns = array(
			    'cb' => '<input type="checkbox" />',
			    'id'          => 'ID',
			    'post_id'       => 'Title',
			    'multi_set_id' => 'Description',
			    'user_id'        => 'User ID',
			    'vote'    => 'Vote',
			    'date'      => 'Date',
			    'ip'  => 'IP',
		    );

        }

        else {

	        $columns = array(
		        'cb' => '<input type="checkbox" />',
		        'id'          => 'ID',
		        'post_id'       => 'Title',
		        'multi_set_id' => 'Description',
		        'user_id'        => 'User ID',
		        'vote'    => 'Vote',
		        'date'      => 'Date'
	        );

        }

		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns()
	{
		return array('id', 'multi_set_id');
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns()
	{
		return array(
			'user_id' => array('user_id', FALSE),
			'vote' =>  array('vote', FALSE),
			'date' =>  array('date', FALSE),
			'ip' =>  array('ip', FALSE)
		);
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	protected function column_default( $item, $column_name )
	{

		switch( $column_name ) {
			case 'post_id':

				$post_id = $item[$column_name];

				$title_post = get_the_title( $post_id );
				$link = get_permalink( $post_id );

				$yasr_title_link = '<a href="'. $link .'">' . $title_post .'</a>';

				return $yasr_title_link;

			case 'user_id':

				$user_id = $item[$column_name];

				$user = get_user_by( 'id', $user_id );

				//If ! user means that the vote are anonymous
				if ($user == FALSE) {

					$user = (object) array('user_login');
					$user->user_login = __('anonymous');

				}

				return $user->user_login;

			//All other columns must return their content
			case 'vote':
			case 'date':
			case 'ip':
				return $item[$column_name];

			//default:
			//return print_r($item) ;
		}

	}

	/**
	 * Allows you to sort the data by the variables set in the $_GET
	 *
	 * @return Mixed
	 */
	protected function sort_data( $a, $b ) {

		// Set defaults (just need to avoid undefined variable at first load,
		// it is already ordered with the query
		$orderby = 'date';
		$order = 'desc';

		// If orderby is set, use this as the sort column
		if(!empty($_GET['orderby']))
		{
			$orderby = $_GET['orderby'];
		}

		// If order is set use this as the order
		if(!empty($_GET['order']))
		{
			$order = $_GET['order'];
		}


		$result = strcmp( $a[$orderby], $b[$orderby] );

		if($order === 'asc')
		{
			return $result;
		}

		return -$result;
	}


	protected function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Delete'
		);
		return $actions;
	}

	protected function column_cb( $item ) {

		return sprintf(
			"<input type='checkbox' name='yasr_logs_votes_to_delete[]' id='{$item['id']}' value='{$item['id']}' />"
		);
	}

	//process bulk action
	protected function process_bulk_action() {

		if ($this->current_action() === 'delete') {

			check_admin_referer( 'yasr-delete-stats-logs', 'yasr-nonce-delete-stats-logs' );

			global $wpdb;

			foreach ($_POST['yasr_logs_votes_to_delete'] as $log_id) {

				$wpdb->delete(
					YASR_LOG_TABLE,
					array(
						'id' => $log_id
					),
					array('%d')
				);

			}
		}

	}

}