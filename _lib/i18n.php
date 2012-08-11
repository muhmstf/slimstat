<?php

/*
 * SlimStat: simple web analytics
 * Copyright (C) 2010 Pieces & Bits Limited
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

require_once( realpath( dirname( __FILE__ ) ).'/config.php' );

class SlimStatI18n {
	
	var $data;
	
	function SlimStatI18n() {
		$config =& SlimStatConfig::get_instance();
		$this->data = parse_ini_file( realpath( dirname( dirname( __FILE__ ) ) ).'/_i18n/'.preg_replace( "[^A-Za-z\-]", '', $config->language ).'.ini', true );
	}
	
	function &get_instance() {
		static $i18n_instance = array();
		if ( empty( $i18n_instance ) ) {
			// Assigning the return value of new by reference is deprecated in PHP 5.3
			if ( version_compare( PHP_VERSION, '5.3.0' ) >= 0 ) {
				$i18n_instance[] = new SlimStatI18n();
			} else {
				$i18n_instance[] =& new SlimStatI18n();
			}
		}
		return $i18n_instance[0];
	}
	
	function label( $_field, $_key ) {
		if ( ( $_field == 'prev_resource' || $_field == 'next_resource' ) && $_key == '' &&
		     array_key_exists( $_field.'.'.$_key, $this->data['labels'] ) ) {
			return $this->data['labels'][$_field.'.'.$_key];
		}
		
		if ( strstr( $_field, '_resource' ) ) {
			$_field = 'resource';
		}
		
		if ( array_key_exists( $_field.'.'.$_key, $this->data['labels'] ) ) {
			return $this->data['labels'][$_field.'.'.$_key];
		} elseif ( $_key == '' ) {
			return $this->data['core']['indeterminable'];
		} elseif ( $_field == 'language' && mb_strlen( $_key ) == 5 ) {
			$language = mb_strtolower( mb_substr( $_key, 0, 2 ) );
			$country = mb_strtoupper( mb_substr( $_key, 3, 2 ) );
			
			if ( array_key_exists( 'language.'.$language, $this->data['labels'] ) ) {
				if ( array_key_exists( 'country.'.$country, $this->data['labels'] ) ) {
					return sprintf(
						$this->data['core']['language_country'],
					    $this->data['labels']['language.'.$language],
					    $this->data['labels']['country.'.$country] );
				} else {
					return $this->data['labels']['language.'.$language];
				}
			} else {
				return $_key;
			}
		} else {
			return $_key;
		}
	}
	
	function _( $_category, $_field, $_str='' ) {
		if ( array_key_exists( $_category, $this->data ) &&
		     array_key_exists( $_field, $this->data[$_category] ) ) {
			if ( $_str != '' ) {
				return sprintf( $this->data[$_category][$_field], $_str );
			} else {
				return $this->data[$_category][$_field];
			}
		} else {
			return $_field;
		}
	}
	
	function hsc( $_category, $_field, $_str='' ) {
		return htmlspecialchars( $this->_( $_category, $_field, $_str ) );
	}
}
