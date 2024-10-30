<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class HC4_Crud_WordPress_CustomPost
{
	protected static $wpCoreFields = array( 
		'ID',
		'post_content',
		'post_title',
		'post_excerpt',
		'post_status',
		'post_date',
		'post_parent',
		'post_name',
		'post_type'
		);

	public function __construct()
	{
		// $this->_registerWp(); 
	}

	protected function _registerWp( $wpPostType )
	{
		if( ! post_type_exists($wpPostType) ){
			// echo "REGISTERING POST TYPE '" . $this->wpPostType . "'<br>";
			register_post_type(
				$wpPostType,
				array(
					// 'public' => TRUE,
					'public' => FALSE,
					'publicly_queryable' => TRUE,
					'has_archive' => FALSE,
					'exclude_from_search' => TRUE,
					'show_in_menu' => FALSE,
					'show_in_nav_menus'	=> FALSE,
					'show_in_rest' => TRUE,
					// 'show_in_menu' => TRUE,
					// 'show_in_nav_menus'	=> TRUE,
					// 'show_in_rest' => TRUE,
					// 'show_ui'		=> TRUE,
					// 'menu_position'	=> 5,
					)
				);
		}
	}

	public function read( $wpPostType, HC4_Crud_Q $q, $withMeta, array $mapFields = array(), array $convertFieldsTo = array(), array $convertFieldsFrom = array() )
	{
		$this->_registerWp( $wpPostType );

		$return = array();

		$wpQ = array();

	// SEARCH
		$search = $q->getSearch();
		if( strlen($search) ){
			$wpQ['s'] = $search;
		}

	// WHERE
		$where = $q->getWhere();

	// convert each property to WP
		list( $whereCore, $whereMeta, $whereTax ) = $this->_convertWhereTo( $where, $mapFields, $convertFieldsTo );

		if( $whereTax ){
			$wpQ['tax_query'] = $whereTax;
		}

		$wpDateQuery = array();

		foreach( $whereCore as $w ){
			list( $k, $compare, $v ) = $w;

			if( is_array($v) && (count($v) == 1) ){
				$v = array_shift( $v );
			}

			switch( $k ){
				case 'ID':
					if( ! is_array($v) ){
						$v = (int) (string) $v;
					}
					switch( $compare ){
						case '=':
							if( is_array($v) ){
								$wpQ['post__in'] = $v;
							}
							else {
								$wpQ['p'] = $v;
							}
							break;

						case '<>':
							if( is_array($v) ){
								$wpQ['post__not_in'] = $v;
							}
							else {
								$wpQ['post__not_in'] = array($v);
							}
							break;

						default:
							echo "comparision '$compare' is not allowed for 'ID'";
							break;
					}
					break;

				case 'post_parent':
					if( ! is_array($v) ){
						$v = (int) (string) $v;
					}
					switch( $compare ){
						case '=':
							if( is_array($v) ){
								$wpQ['post_parent__in'] = $v;
							}
							else {
								$wpQ['post_parent'] = $v;
							}
							break;

						case '<>':
							if( is_array($v) ){
								$wpQ['post_parent__not_in'] = $v;
							}
							else {
								$wpQ['post_parent__not_in'] = array($v);
							}
							break;

						default:
							echo "comparision '$compare' is not allowed for 'post_parent'";
							break;
					}
					break;

				case 'post_title':
					if( ! is_array($v) ){
						$v = (string) $v;
					}
					switch( $compare ){
						case '=':
							$wpQ['title'] = $v;
							break;

						case 'LIKE':
							$wpQ['s'] = $v;
							break;

						default:
							echo "comparision '$compare' is not allowed for 'post_title'";
							break;
					}
					break;

				case 'post_date':
					if( ! is_array($v) ){
						$v = (string) $v;
					}

					switch( $compare ){
						case '>':
							$wpDateQuery[] = array( 'after' => $v );
							break;

						case '>=':
							$wpDateQuery[] = array( 'after' => $v, 'inclusive' => TRUE );
							break;

						case '<':
							$wpDateQuery[] = array( 'before' => $v );
							break;

						case '<=':
							$wpDateQuery[] = array( 'before' => $v, 'inclusive' => TRUE );
							break;

						// case '=':
						// 	$year = substr( $v, 0, 4 );
						// 	$month = substr( $v, 4, 2 );
						// 	$day = substr( $v, 6, 2 );

						// 	$wpDateQuery['year'] = $year;
						// 	$wpDateQuery['month'] = $month;
						// 	$wpDateQuery['day'] = $day;
						// 	break;

						default:
							echo "comparision '$compare' is not allowed for 'post_date'";
							break;
					}
					break;

				case 'post_status':
					if( ! is_array($v) ){
						$v = (string) $v;
					}
					switch( $compare ){
						case '=':
							$wpQ['post_status'] = $v;
							break;

						default:
							echo "comparision '$compare' is not allowed for 'post_status'";
							break;
					}
					break;
			}
		}

		if( $wpDateQuery ){
			$wpQ['date_query'] = $wpDateQuery;
		}

		$wpMetaQuery = array();

		foreach( $whereMeta as $w ){
			list( $k, $compare, $v ) = $w;

			if( ! is_array($v) ){
				$v = (string) $v;
			}

			if( is_array($v) && (count($v) == 1) ){
				$v = array_shift( $v );
			}

			if( is_array($v) ){
				if( '=' == $compare ){
					$compare = 'IN';
				}
				if( '<>' == $compare ){
					$compare = 'NOT IN';
				}
			}

			$wpMetaQuery[] = array(
				'key' => $k,
				'compare' => $compare,
				'value' => $v,
				);
		}

	// SORT
		$wpOrderBy = array();

	// SORT ASC
		$sort = $q->getSort();
		list( $sortCore, $sortMeta ) = $this->_convertSortTo( $sort, $mapFields, $convertFieldsTo );

		if( $sortCore ){
			$thisOrderBy = array();
			foreach( $sortCore as $s ){
				$thisOrderBy[$s] = 'ASC';
			}
			$wpOrderBy = array_merge( $wpOrderBy, $thisOrderBy );
		}

		if( $sortMeta ){
			$metaOrderBy = array();
			foreach( $sortMeta as $k ){
				$wpMetaQuery[ $k . '_clause' ] = array( 'key' => $k );
				$metaOrderBy[ $k . '_clause' ] = 'ASC';
			}
			$wpOrderBy = array_merge( $wpOrderBy, $metaOrderBy );
		}

	// SORT DESC
		$sort = $q->getSortDesc();
		list( $sortCore, $sortMeta ) = $this->_convertSortTo( $sort, $mapFields, $convertFieldsTo );

		if( $sortCore ){
			$thisOrderBy = array();
			foreach( $sortCore as $s ){
				$thisOrderBy[$s] = 'DESC';
			}
			$wpOrderBy = array_merge( $wpOrderBy, $thisOrderBy );
		}

		if( $sortMeta ){
			$metaOrderBy = array();
			foreach( $sortMeta as $k ){
				$wpMetaQuery[ $k . '_clause' ] = array( 'key' => $k );
				$metaOrderBy[ $k . '_clause' ] = 'DESC';
			}
			$wpOrderBy = array_merge( $wpOrderBy, $metaOrderBy );
		}

		if( $wpOrderBy ){
			$wpQ['orderby'] = $wpOrderBy;
		}

		if( $wpMetaQuery ){
			$wpMetaQuery['relation'] = 'AND';
		}

		if( $wpMetaQuery ){
			$wpQ['meta_query'] = $wpMetaQuery;
		}

	// LIMIT
		$limit = $q->getLimit();
		$offset = $q->getOffset();

		if( (NULL !== $limit) && (NULL !== $offset) ){
			$wpQ['posts_per_page'] = $limit;
			$wpQ['offset'] = $offset;
		}
		elseif( NULL !== $limit ){
			$wpQ['posts_per_page'] = $limit;
		}
		else {
			$wpQ['posts_per_page'] = -1;
		}

		$wpQueryQ = $this->_wpQueryQ( $wpPostType, $wpQ, $withMeta );

		$posts = get_posts( $wpQueryQ );

		if( $posts ){
			foreach( $posts as $p ){
				$thisReturn = get_object_vars( $p );

				if( $withMeta ){
					$meta = get_metadata( 'post', $p->ID );

					// $metaValues = array();
					// foreach( $meta as $m ){
					// }

					$metaValues = array_map( function($n){return $n[0];}, $meta );
					$thisReturn = array_merge( $metaValues, $thisReturn );
				}

				$thisReturn = $this->_convertFrom( $thisReturn, $mapFields, $convertFieldsFrom );
				$return[] = $thisReturn;
			}
		}

		return $return;
	}

	public function create( $wpPostType, array $array, array $mapFields = array(), array $convertFieldsTo = array() )
	{
		$this->_registerWp( $wpPostType );
		list( $core, $meta ) = $this->_convertTo( $array, $mapFields, $convertFieldsTo );

		$postArray = $core;

		$postArray['post_type'] = $wpPostType;
		if( ! array_key_exists('post_status', $postArray) ){
			$postArray['post_status'] = 'publish';
		}

		if( $meta ){
			$postArray['meta_input'] = $meta;
		}

		$id = wp_insert_post( $postArray, TRUE );

		if( is_wp_error($id) ){
			$error = '__Database Error__' . ': ' . $id->get_error_message();
			throw new Exception( $error );
		}

		return $id;
	}

	public function update( $wpPostType, $id, array $values, array $mapFields = array(), array $convertFieldsTo = array() )
	{
		$this->_registerWp( $wpPostType );
		// $values[$this->idField] = $id;

		unset( $values['id'] );
		list( $coreValues, $metaValues ) = $this->_convertTo( $values, $mapFields, $convertFieldsTo );

		if( $coreValues ){
			$coreValues['ID'] = $id;
			wp_update_post( $coreValues );
		}

		if( $metaValues ){
			foreach( $metaValues as $k => $v ){
				update_post_meta( $id, $k, $v );
			}
		}

		return $values;
	}

	public function delete( $id )
	{
		$return = FALSE;

		if( ! $id ){
			return $return;
		}

		$return = wp_delete_post( $id, TRUE );
		return $return;
	}

	public function deleteAll( $wpPostType )
	{
		$this->_registerWp( $wpPostType );
		global $wpdb;

		$sql = '
DELETE `posts`, `pm`
FROM `' . $wpdb->prefix . 'posts` AS `posts` 
LEFT JOIN `' . $wpdb->prefix . 'postmeta` AS `pm` ON `pm`.`post_id` = `posts`.`ID`
WHERE `posts`.`post_type` = \'' . $wpPostType . '\'';

		$result = $wpdb->query($sql);

		$return = TRUE;
		return $return;
	}

	protected function _wpQueryQ( $wpPostType, $q, $withMeta )
	{
		$q['post_type'] = $wpPostType;
		if( ! array_key_exists('post_status', $q) ){
			$q['post_status'] = array( 'any', 'trash' );
		}
		$q['perm'] = 'readable';

		if( ! array_key_exists('posts_per_page', $q) ){
			$q['posts_per_page'] = -1;
		}

		if( ! $withMeta ){
			$q['update_post_meta_cache'] = FALSE;
		}

		return $q;
	}

	protected function _convertFrom( array $post, array $mapFields = array(), array $convertFieldsFrom = array() )
	{
		$return = array();

		reset( $mapFields );
		foreach( $mapFields as $myField => $wpField ){
			$return[ $myField ] = array_key_exists( $wpField, $post ) ? $post[$wpField] : NULL;
		}

		reset( $convertFieldsFrom );
		foreach( $convertFieldsFrom as $myField => $func ){
			if( ! array_key_exists( $myField, $return ) ){
				continue;
			}
			$return[ $myField ] = call_user_func( $func, $return[$myField] );
		}

		return $return;
	}

	protected function _convertTo( $array, array $mapFields = array(), array $convertFieldsTo = array() )
	{
		$core = array();
		$meta = array();

// if( $this->convertFieldsTo ){
// 	_print_r( $array );
// }

		reset( $convertFieldsTo );
		foreach( $convertFieldsTo as $myField => $func ){
			if( ! isset($array[$myField]) ){
				continue;
			}

		// COMPARISION
			if( is_array($array[$myField]) ){
				$count = count( $array[$myField] );
				for( $ii = 0; $ii < $count; $ii++ ){
					$array[$myField][$ii][1] = call_user_func( $func, $array[$myField][$ii][1] );
				}
			}
			else {
				$array[ $myField ] = call_user_func( $func, $array[$myField] );
			}
		}

// if( $this->convertFieldsTo ){
// 	_print_r( $array );
// 	exit;
// }

		if( $mapFields ){
			$myFields = array_keys( $array );

			foreach( $myFields as $myField ){
				if( ! isset($mapFields[$myField]) ){
					continue;
				}

				$v = $array[ $myField ];
				$wpField = $mapFields[$myField];

				if( in_array($wpField, self::$wpCoreFields) ){
					$core[ $wpField ] = $v;
				}
				else {
					$meta[ $wpField ] = $v;
				}

				unset( $array[$myField] );
			}
		}

		if( $array ){
			$meta = $array;
		}

		$keys = array_keys( $core );
		foreach( $keys as $k ){
			if( is_object($core[$k]) ){
				$core[$k] = (string) $core[$k];
			}
		}

		$keys = array_keys( $meta );
		foreach( $keys as $k ){
			if( is_object($meta[$k]) ){
				$meta[$k] = (string) $meta[$k];
			}
		}

		$return = array( $core, $meta );
		return $return;
	}

	protected function _convertWhereTo( array $where, array $mapFields = array(), array $convertFieldsTo = array() )
	{
		$whereTax = array();

		$whereByKey = array();
		reset( $where );
		foreach( $where as $w ){
			list( $k, $compare, $v ) = $w;
			if( ! isset($whereByKey[$k]) ){
				$whereByKey[$k] = array();
			}
			$whereByKey[$k][] = array( $compare, $v );
		}

		if( isset($whereByKey['wp_tax_query']) ){
			$whereTax = array();
			reset( $whereByKey['wp_tax_query'] );
			foreach( $whereByKey['wp_tax_query'] as $w ){
				$whereTax[] = $w[1];
			}
			unset( $whereByKey['wp_tax_query'] );
		}

		list( $whereByKeyCore, $whereByKeyMeta ) = $this->_convertTo( $whereByKey, $mapFields, $convertFieldsTo );

		$whereCore = array();
		foreach( $whereByKeyCore as $k => $wheres ){
			foreach( $wheres as $w ){
				list( $compare, $v ) = $w;
				$whereCore[] = array( $k, $compare, $v );
			}
		}

		$whereMeta = array();
		foreach( $whereByKeyMeta as $k => $wheres ){
			foreach( $wheres as $w ){
				list( $compare, $v ) = $w;
				$whereMeta[] = array( $k, $compare, $v );
			}
		}

		$return = array( $whereCore, $whereMeta, $whereTax );
		return $return;
	}

	protected function _convertSortTo( array $sort, array $mapFields = array(), array $convertFieldsTo = array() )
	{
		$sortByKey = array();
		reset( $sort );
		foreach( $sort as $s ){
			$sortByKey[$s] = $s;
		}

		list( $sortCore, $sortMeta ) = $this->_convertTo( $sortByKey, $mapFields, $convertFieldsTo );

		$sortCore = array_keys( $sortCore );
		$sortMeta = array_keys( $sortMeta );

		$return = array( $sortCore, $sortMeta );
		return $return;
	}
}