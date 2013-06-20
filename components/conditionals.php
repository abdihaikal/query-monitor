<?php

class QM_Component_Conditionals extends QM_Component {

	var $id = 'conditionals';

	function __construct() {
		parent::__construct();
		add_filter( 'query_monitor_menus', array( $this, 'admin_menu' ), 110 );
	}

	function admin_menu( array $menu ) {

		foreach ( $this->data['conds']['true'] as $cond ) {
			$menu[] = $this->menu( array(
				'title' => $cond . '()',
				'id'    => 'query-monitor-' . $cond,
				'meta'  => array( 'classname' => 'qm-true qm-ltr' )
			) );
		}

		return $menu;

	}

	function output( array $args, array $data ) {

		echo '<div class="qm" id="' . $args['id'] . '">';
		echo '<table cellspacing="0">';
		echo '<thead>';
		echo '<tr>';
		echo '<th colspan="2">' . __( 'Conditionals', 'query-monitor' ) . '</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';

		$first = true;

		foreach ( $data['conds']['true'] as $cond ) {
			if ( $first )
				echo '<tr>';
			echo '<td class="qm-ltr qm-true">' . $cond . '()</td>';
			if ( !$first )
				echo '</tr>';
			$first = !$first;
		}

		foreach ( $data['conds']['false'] as $cond ) {
			if ( $first )
				echo '<tr>';
			echo '<td class="qm-ltr qm-false">' . $cond . '()</td>';
			if ( !$first )
				echo '</tr>';
			$first = !$first;
		}

		if ( !$first ) {
			echo '<td>&nbsp;</td>';
			echo '</tr>';
		}

		echo '</tbody>';
		echo '</table>';
		echo '</div>';

	}

	function process() {

		$conds = apply_filters( 'qm_conditionals', array(
			'is_404', 'is_archive', 'is_admin', 'is_attachment', 'is_author', 'is_blog_admin', 'is_category', 'is_comments_popup', 'is_date',
			'is_day', 'is_feed', 'is_front_page', 'is_generic_request', 'is_home', 'is_main_site', 'is_month', 'is_multitax', 'is_network_admin',
			'is_page', 'is_page_template', 'is_paged', 'is_post_type_archive', 'is_preview', 'is_robots', 'is_rtl', 'is_search', 'is_single',
			'is_singular', 'is_ssl', 'is_sticky', 'is_tag', 'is_tax', 'is_time', 'is_trackback', 'is_year'
		) );	

		$true = $false = $na = array();

		foreach ( $conds as $cond ) {
			if ( function_exists( $cond ) ) {

				if ( ( 'is_sticky' == $cond ) and !get_post( $id = null ) ) {
					# Special case for is_sticky to prevent PHP notices
					$false[] = $cond;
				} else if ( ( 'is_main_site' == $cond ) and !is_multisite() ) {
					# Special case for is_main_site to prevent it from being annoying on single site installs
					$na[] = $cond;
				} else {
					if ( call_user_func( $cond ) )
						$true[] = $cond;
					else
						$false[] = $cond;
				}

			} else {
				$na[] = $cond;
			}
		}
		$this->data['conds'] = compact( 'true', 'false', 'na' );

	}

}

function register_qm_conditionals( array $qm ) {
	$qm['conditionals'] = new QM_Component_Conditionals;
	return $qm;
}

add_filter( 'query_monitor_components', 'register_qm_conditionals', 40 );

?>
