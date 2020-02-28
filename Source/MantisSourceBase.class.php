<?php

# Copyright (c) 2017 Damien Regad
# Licensed under the MIT license

require_once( config_get_global( 'class_path' ) . 'MantisPlugin.class.php' );

/**
 * Class MantisSourceBase
 *
 * Base class for all Source Integration Plugin classes
 */
abstract class MantisSourceBase extends MantisPlugin
{
	/**
	 * Source Integration framework version.
	 *
	 * Numbering follows Semantic Versioning. Major version increments indicate
	 * a change in the minimum required MantisBT version: 0=1.2; 1=1.3, 2=2.x.
	 * The framework version is incremented when the plugin's core files change.
	 */
	const FRAMEWORK_VERSION = '2.4.0';

	/**
	 * Minimum required MantisBT version.
	 * Used to define the default MantisCore dependency for all child plugins;
     * VCS plugins may override this based on their individual requirements.
	 */
	const MANTIS_VERSION = '2.0.1';

	/**
	 * Returns the list of error constants defined in the given class.
	 *
	 * Uses PHP's Reflection API to retrieve all class constants, ignoring
	 * those that are not starting with `ERROR_`.
	 *
	 * @param string $p_class Name of class, if unspecified uses $this
	 *
	 * @return array (constant name => value)
	 */
	public function getErrorConstants( $p_class = null ) {
		$t_reflect = new ReflectionClass( $p_class ?: $this );
		$t_error_constants = $t_reflect->getConstants();
		foreach( $t_error_constants as $t_const => $t_value ) {
			# Ignore non-error constants
			if( strpos( $t_const, 'ERROR_' ) !== 0 ) {
				unset( $t_error_constants[$t_const] );
			}
		}
		return $t_error_constants;
	}

	/**
	 * Retrieves the error strings for the given plugin basename.
	 *
	 * @param array  $p_constants
	 * @param string $p_basename Name of plugin to retrieve strings from.
	 *
	 * @return array (error code => translated error message)
	 */
	public function getErrorStrings( array $p_constants, $p_basename = null ) {
		if( $p_basename == null ) {
			$p_basename = $this->basename;
		}

		$t_errors = array();

		foreach( $p_constants as $t_error ) {
			$t_errors[$t_error] = plugin_lang_get( 'error_' . $t_error, $p_basename );
		}

		return $t_errors;
	}

	/**
	 * Define VCS plugins' new error messages.
	 *
	 * @return array The error_name => error_message list to add
	 */
	public function errors() {
		$t_errors = $this->getErrorStrings( $this->getErrorConstants() );
		return array_merge( parent::errors(), $t_errors );
	}
}
