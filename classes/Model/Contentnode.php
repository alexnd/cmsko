<?php defined('SYSPATH') or die('No direct script access.');

//this should be moved to ORM driver class

class Model_Contentnode extends ORM {

    var $_table_name = 'content_nodes';


	function get_pages ( $url, $lang ) {
        $res = null;
		//if( is_string($url) && strlen($lang) )
        if( is_scalar($url) && strlen($lang) ) {
			//$url = ltrim(preg_replace('![\/]{2,}!', '/', str_replace('\\', '/', $url)));
            $urlpath = explode('/', ltrim($url));
			$n = count($urlpath);
			if( $n ) {
				if( strlen($urlpath[0]) ) {
				    $q = 'SELECT cp0.id AS id0,cp0.type AS type0,cp0.nested AS nested0';
				    $qj = '';
                    for( $i=1; $i < $n; $i++ ) {
                        if( strlen($urlpath[$i]) )
                        {
                            $q .= ",cp{$i}.id AS id{$i},cp{$i}.nested AS nested{$i}";
                            #$q .= ",cp0.type AS type{$i}";
                            $qj .= " LEFT JOIN ".$this->_table_name.
                                " AS cp{$i} ON (cp{$i}.parent_id=cp".($i-1).".id AND cp{$i}.deleted<1 AND cp{$i}.url='".
                                    addslashes($urlpath[$i])."' AND cp{$i}.lang='{$lang}')";
                        }
                    }
                    $q .= " FROM {$this->_table_name} AS cp0".$qj.
                        " WHERE cp0.url='{$urlpath[0]}' AND cp0.lang='{$lang}' AND cp0.parent_id='0' AND cp0.deleted<1";
                    $r = DB::query(Database::SELECT, $q)->execute()->as_array();
                    if( count($r) ) {
                        $pipe = $r[0];
                        $ii = 0;
                        $qi = "";
                        for( $i=0; $i < $n; $i++ ) {
                            $id = ( array_key_exists( 'id'.$i, $pipe ) && $pipe['id'.$i] > 0 ) ? $pipe['id'.$i] : 0;
                            $nested = ( array_key_exists( 'nested'.$i, $pipe ) ) ? $pipe['nested'.$i] : 0;
                            #$type = ( array_key_exists( 'type'.$i, $pipe ) ) ? $pipe['type'.$i] : 'node';
                            if( $id > 0 )
                            {
                                if( $ii > 0 ) $qi .= ",";
                                $qi .= $id;
                                $ii++;
                            }
                            if( $nested ) break;
                        }
                        $q = "SELECT * FROM {$this->_table_name} WHERE id IN({$qi}) AND deleted<1 ORDER BY `level` ASC";
                        $res = DB::query(Database::SELECT, $q)->execute()->as_array();
                    }
				}
			}
		}
        return $res;
	}

	
	function get_page( $url, $lang, $parent_id = 0, $obtain_data = TRUE ) {
        $res = null;
		if( is_scalar($url) && strlen($lang) ) {
            $parent_id = (int)$parent_id;
            //$url = ltrim(preg_replace('![\/]{2,}!', '/', str_replace('\\', '/', $url)));
            $urlpath = explode('/', ltrim($url));
			$n = count($urlpath);
			if( $n ) {
				if( strlen($urlpath[0]) ) {
                    $q = 'SELECT cp0.id AS id0,cp0.type AS type0,cp0.nested AS nested0';
                    $qj = '';
                    for( $i=1; $i < $n; $i++ ) {
                        if( strlen($urlpath[$i]) ) {
                            $q .= ",cp{$i}.id AS id{$i},cp{$i}.nested AS nested{$i}";
                            #$q .= ",cp0.type AS type{$i}";
                            $qj .= " LEFT JOIN {$this->_table_name} AS cp{$i} ON (cp{$i}.parent_id=cp".
                                ($i-1).".id AND cp{$i}.deleted<1 AND cp{$i}.url='".
                                    addslashes($urlpath[$i])."' AND cp{$i}.lang='{$lang}')";
                        }
                    }
				    $q .= " FROM {$this->_table_name} AS cp0".$qj.
				        " WHERE cp0.url='".$urlpath[0]."' AND cp0.lang='{$lang}' AND cp0.parent_id='{$parent_id}' AND cp0.deleted<1";
                    $r = DB::query(Database::SELECT, $q)->execute()->as_array();
					if( count($r) )
					{
						$pipe = $r[0];
						$ii = 0;
						$qi = '';
						for( $i=0; $i < $n; $i++ )
						{
							$id = ( array_key_exists( 'id'.$i, $pipe ) && $pipe['id'.$i] > 0 ) ? $pipe['id'.$i] : 0;
							$nested = ( array_key_exists( 'nested'.$i, $pipe ) ) ? $pipe['nested'.$i] : 0;
							#$type = ( array_key_exists( 'type'.$i, $pipe ) ) ? $pipe['type'.$i] : 'node';
							if( $id > 0 )
							{
								if( $ii > 0 ) $qi .= ",";
								$qi .= $id;
								$ii++;
							}
							if( $nested ) break;
						}
						$q = "SELECT * FROM {$this->_table_name} WHERE id IN({$qi}) AND deleted<1 ORDER BY `level` ASC";
                        $res = DB::query(Database::SELECT, $q)->execute()->as_array();
					}
				}
			}
		}
		return $res;
	}

}
