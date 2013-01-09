<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contentnode extends ORM {

    var $_table_name = 'content_nodes';
/*
TODO: implement this!

	function get_pages_by_url( $url, $lang, $obtain_data = TRUE )
	{
		if( $this->debug ) $this->core->log_file_dump( array('url'=>$url, 'lang'=>$lang), "get_pages_by_url: input params", $this->log_file );
		if( is_string($url) && $this->_validate_lang($lang) )
		{
			$url = $this->core->path_lstrip( $this->core->fix_path( $url ) );
			//$url = preg_replace("![/]{2,}!", "/", $url);
			//if( preg_match("!^/!", $url) ) $url = substr($url, 1);
			//if( $this->debug ) $this->core->log_file( $url, "get_pages_by_url: url filtered", $this->log_file );
			$urlpath = explode("/", $url);
			if( $this->debug ) $this->core->log_file_print_r( $urlpath, "get_pages_by_url: url parts array", $this->log_file );
			$n = count($urlpath);
			if( $n )
			{
				if( !$this->_validate_url($urlpath[0]) )
				{
					$this->log_error( IB_ERROR_INVALID_PARAM, __FILE__, __LINE__ );
					return NULL;
				}
				$q = "SELECT cp0.id AS id0,cp0.type AS type0,cp0.nested AS nested0";
				$qj = "";
				for( $i=1; $i < $n; $i++ )
				{
					if( !$this->_validate_url($urlpath[$i]) )
					{
						$this->log_error( IB_ERROR_INVALID_PARAM, __FILE__, __LINE__ );
						return NULL;
					}
					$q .= ",cp{$i}.id AS id{$i},cp{$i}.nested AS nested{$i}";
					#$q .= ",cp0.type AS type{$i}";
					$qj .= " LEFT JOIN ".$this->interface_db->db_table('content_pages').
					" AS cp{$i} ON (cp{$i}.parent_id=cp".($i-1).".id AND cp{$i}.deleted<1 AND cp{$i}.url='".
					addslashes($urlpath[$i])."' AND cp{$i}.lang='{$lang}')";
				}
				$q .= " FROM ".$this->interface_db->db_table('content_pages')." AS cp0".$qj.
				" WHERE cp0.url='".$urlpath[0]."' AND cp0.lang='{$lang}' AND cp0.parent_id='0' AND cp0.deleted<1";
				if( $this->debug ) $this->core->log_file( $q, "get_pages_by_url: sql for pages pipe", $this->log_file );
				if( $this->interface_db->query($q) )
				{
					if( $this->interface_db->num_rows() )
					{
						$pipe = $this->interface_db->fetch_row();
						if( $this->debug ) $this->core->log_file_print_r( $pipe, "get_pages_by_url: pages pipe array", $this->log_file );
						$ii = 0;
						$qi = "";
						for( $i=0; $i < $n; $i++ )
						{
							$id = ( array_key_exists( 'id'.$i, $pipe ) && $pipe['id'.$i] > 0 ) ? $pipe['id'.$i] : 0;
							$nested = ( array_key_exists( 'nested'.$i, $pipe ) ) ? $pipe['nested'.$i] : 0;
							#$type = ( array_key_exists( 'type'.$i, $pipe ) ) ? $pipe['type'.$i] : IB_CONTENT_PAGE_GENERIC;
							if( $id > 0 )
							{
								if( $ii > 0 ) $qi .= ",";
								$qi .= $id;
								$ii++;
							}
							if( $nested ) break;
						}
						$q = "SELECT * FROM ".$this->interface_db->db_table('content_pages')." WHERE id IN(".$qi.") AND deleted<1 ORDER BY `level` ASC";
						if( $this->debug ) $this->core->log_file( $q, "get_pages_by_url: sql for optain pages", $this->log_file );
						if( $this->interface_db->query($q)  )
						{
							if( $this->interface_db->num_rows() )
							{
								$res = $this->interface_db->fetch_array();
								if( is_array( $res ) && count( $res ) )
								{
									if( $obtain_data )
									{
										foreach( $res as $i => $r )
										{
											$res[$i]['data'] = $this->interface_nodes->get_content_nodes(
											array( 'class_id' => $this->content_class_id, 'lang' => $r['lang'], 'item_id' => $r['id'] ), TRUE );
										}
									}
									$this->core->log_file_print_r( $res, "get_pages_by_url: obtained data", $this->log_file );
									return $res;
								}
							}
						}
						else
						{
							$this->log_error( IB_ERROR_CANNOT_RUN_SQL, __FILE__, __LINE__ );
						}
					}
				}
				else
				{
					$this->log_error( IB_ERROR_CANNOT_RUN_SQL, __FILE__, __LINE__ );
				}
			}
		}
		else
		{
			$this->log_error( IB_ERROR_INVALID_PARAM, __FILE__, __LINE__ );
		}
		return NULL;
	}
	
	function get_page_by_url( $url, $lang,$parent_id = 0,$obtain_data = TRUE )
	{
		if( $this->debug ) $this->core->log_file_dump( array('url'=>$url, 'lang'=>$lang), "get_pages_by_url: input params", $this->log_file );
		if( is_string($url) && $this->_validate_lang($lang))
		{
			$url = $this->core->path_lstrip( $this->core->fix_path( $url ) );
			$parent_id = (int)$parent_id;
														
			$urlpath = explode("/", $url);
			if( $this->debug ) $this->core->log_file_print_r( $urlpath, "get_pages_by_url: url parts array", $this->log_file );
			$n = count($urlpath);
			if( $n )
			{
				if( !$this->_validate_url($urlpath[0]) )
				{
					$this->log_error( IB_ERROR_INVALID_PARAM, __FILE__, __LINE__ );
					return NULL;
				}
				$q = "SELECT cp0.id AS id0,cp0.type AS type0,cp0.nested AS nested0";
				$qj = "";
				for( $i=1; $i < $n; $i++ )
				{
					if( !$this->_validate_url($urlpath[$i]) )
					{
						$this->log_error( IB_ERROR_INVALID_PARAM, __FILE__, __LINE__ );
						return NULL;
					}
					$q .= ",cp{$i}.id AS id{$i},cp{$i}.nested AS nested{$i}";
					#$q .= ",cp0.type AS type{$i}";
					$qj .= " LEFT JOIN ".$this->interface_db->db_table('content_pages').
					" AS cp{$i} ON (cp{$i}.parent_id=cp".($i-1).".id AND cp{$i}.deleted<1 AND cp{$i}.url='".
					addslashes($urlpath[$i])."' AND cp{$i}.lang='{$lang}')";
				}
				$q .= " FROM ".$this->interface_db->db_table('content_pages')." AS cp0".$qj.
				" WHERE cp0.url='".$urlpath[0]."' AND cp0.lang='{$lang}' AND cp0.parent_id='{$parent_id}' AND cp0.deleted<1";
				if( $this->debug ) $this->core->log_file( $q, "get_pages_by_url: sql for pages pipe", $this->log_file );
				if( $this->interface_db->query($q) )
				{
					if( $this->interface_db->num_rows() )
					{
						$pipe = $this->interface_db->fetch_row();
						if( $this->debug ) $this->core->log_file_print_r( $pipe, "get_pages_by_url: pages pipe array", $this->log_file );
						$ii = 0;
						$qi = "";
						for( $i=0; $i < $n; $i++ )
						{
							$id = ( array_key_exists( 'id'.$i, $pipe ) && $pipe['id'.$i] > 0 ) ? $pipe['id'.$i] : 0;
							$nested = ( array_key_exists( 'nested'.$i, $pipe ) ) ? $pipe['nested'.$i] : 0;
							#$type = ( array_key_exists( 'type'.$i, $pipe ) ) ? $pipe['type'.$i] : IB_CONTENT_PAGE_GENERIC;
							if( $id > 0 )
							{
								if( $ii > 0 ) $qi .= ",";
								$qi .= $id;
								$ii++;
							}
							if( $nested ) break;
						}
						$q = "SELECT * FROM ".$this->interface_db->db_table('content_pages')." WHERE id IN(".$qi.") AND deleted<1 ORDER BY `level` ASC";
						if( $this->debug ) $this->core->log_file( $q, "get_pages_by_url: sql for optain pages", $this->log_file );
						if( $this->interface_db->query($q)  )
						{
							if( $this->interface_db->num_rows() )
							{
								$res = $this->interface_db->fetch_array();
								if( is_array( $res ) && count( $res ) )
								{
									if( $obtain_data )
									{
										foreach( $res as $i => $r )
										{
											$res[$i]['data'] = $this->interface_nodes->get_content_nodes(
											array( 'class_id' => $this->content_class_id, 'lang' => $r['lang'], 'item_id' => $r['id'] ), TRUE );
										}
									}
									$this->core->log_file_print_r( $res, "get_pages_by_url: obtained data", $this->log_file );
									return $res;
								}
							}
						}
						else
						{
							$this->log_error( IB_ERROR_CANNOT_RUN_SQL, __FILE__, __LINE__ );
						}
					}
				}
				else
				{
					$this->log_error( IB_ERROR_CANNOT_RUN_SQL, __FILE__, __LINE__ );
				}
			}
		}
		else
		{
			$this->log_error( IB_ERROR_INVALID_PARAM, __FILE__, __LINE__ );
		}
		return NULL;
	}

*/
}
