<?php
/**
 * @version    $Id$
 * @package    WR_Library
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2012 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

if ( ! class_exists( 'WR_CF_Gadget_Base' ) ) :

/**
 * Base gadget class.
 *
 * @package  WR_Library
 * @since    1.0.0
 */
class WR_CF_Gadget_Base {
	/**
	 * An array to hold instantiated gadget object.
	 *
	 * @var  array
	 */
	private static $_instance = array();

	/**
	 * Gadget file name without extension.
	 *
	 * @var  string
	 */
	protected $gadget = 'base';

	/**
	 * Hook into WordPress system.
	 *
	 * @return  void
	 */
	public static function hook() {
		// Check if any gadget is requested
		if ( isset( $_REQUEST['wr-cf-gadget'] ) ) {
			// Prepare gadget action
			$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : null;

			if ( ! empty( $action ) ) {
				// Execute gadget action
				self::execute( $_REQUEST['wr-cf-gadget'], $action );
			}
		}
	}

	/**
	 * Execute an action of specified gadget.
	 *
	 * @param   string   $gadget  Gadget to search for action.
	 * @param   string   $action  Action to be executed.
	 *
	 * @return  string
	 */
	public static function execute( $gadget, $action = 'default' ) {
		// Generate appropriate method
		$method = str_replace( '-', '_', $action ) . '_action';

		// Instatiate gadget
		if ( $instance = self::get_instance( $gadget, $method ) ) {
			try {
				// Execute gadget action
				$response = call_user_func( array( $instance, $method ) );

				if ( $response ) {
					// Set response for lazy gadget
					$instance->set_response( 'success', $response );
				}
			} catch ( Exception $e ) {
				// Set response for lazy gadget
				$instance->set_response( 'failure', $e->getMessage() );
			}

			return $instance->render( $action );
		}
	}

	/**
	 * Get an instance of specified gadget class.
	 *
	 * @param   string  $gadget  Gadget to instantiate.
	 * @param   string  $method  Only instantiate gadget if this method is declared.
	 *
	 * @return  mixed  An object instance on success, or boolean FALSE on failure.
	 */
	protected static function get_instance( $gadget, $method = 'default_action' ) {
		// Instantiate gadget class only if not already instantiated
		if ( ! isset( self::$_instance[ $gadget ] ) ) {
			// Preset variable
			self::$_instance[$gadget] = false;

			// Check if gadget class exists
			$class = explode( '-', $gadget );
			$class = array_map( 'ucfirst', $class );
			$class = 'WR_CF_Gadget_' . implode( '_', $class );

			// Try to autoload gadget class
			if ( class_exists( $class, true ) && method_exists( $class, $method ) ) {
				self::$_instance[$gadget] = new $class();
			}
		}

		return self::$_instance[$gadget];
	}

	/**
	 * Store response.
	 *
	 * @param   string  $status  Status of gadget action execution.
	 * @param   mixed   $data    Data generated by gadget action.
	 *
	 * @return  void
	 */
	protected function set_response( $status, $data ) {
		$this->response = array(
			'status' => $status,
			'data'   => $data,
		);
	}

	/**
	 * Get response.
	 *
	 * @param   string  $status  Default status.
	 * @param   mixed   $data    Default data.
	 *
	 * @return  mixed
	 */
	protected function get_response( $status = 'success', $data = '' ) {
		if ( ! isset( $this->response ) ) {
			$this->set_response( $status, $data );
		}

		return $this->response;
	}

	/**
	 * Render the output.
	 *
	 * @param   string  $action  Gadget action to execute.
	 *
	 * @return  void
	 */
	protected function render( $action = 'default' ) {
		// Clean all buffered output
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		// Get response
		$response = $this->get_response();

		// Tell browser that JSON string is returned
		@header( 'Content-Type: application/json' );

		// Check if template file exists for requested gadget action
		$file = WR_CF_Loader::get_path( "gadget/tmpl/{$this->gadget}/{$action}.php" );

		if ( 'success' == $response['status'] && ! empty( $file ) ) {
			// Start output buffering
			ob_start();

			// Extract response array to variables: $status and $data
			extract( $response );

			// Load template file to render output
			include_once $file;

			// Get final response
			$response['data'] = ob_get_clean();

			if ( empty( $response['data'] ) && $response != $this->get_response() ) {
				$response = $this->get_response();
			}
		}

		// Print the JSON encoded response then xxit immediately to prevent WordPress from processing further
		exit( json_encode( $response ) );
	}
}

endif;