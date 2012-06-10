<?php 
/**
 * PHP Version 5.3
 *
 * @package     Shmanic.Libraries
 * @subpackage  Ldap.Log
 * @author      Shaun Maunder <shaun@shmanic.com>
 *
 * @copyright   Copyright (C) 2011-2012 Shaun Maunder. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

// TODO: is still needed?
jimport('joomla.log.logger');

/**
 * Log helper class for LDAP.
 *
 * @package     Shmanic.Libraries
 * @subpackage  Ldap.Log
 * @since       2.0
 */
class SHLdapLogHelper
{

	protected static $added = false;

	public static function addLoggers($fileDebug = null, $screenDebug = null) 
	{

		// Make sure this is only run once
		if (self::$added)
		{
			return;
		}

		// Get the parameter level for screen debug logging
		$screenDebug = is_null($screenDebug) ?
			SHLdapHelper::getGlobalParam('screen_debug', 'error_only') :
			$screenDebug;

		/* Add on-screen based loggers based on formattedtext */
		switch($screenDebug) {
			
			case "full":
			
				/* Full error logging */
				self::addOnScreenLogger(JLog::INFO);
				//JFactory::getApplication()->enqueueMessage('file ' . $fileDebug . ' screen ' . $screenDebug, 'notice');
			case "error_only":
			
				/* Error only logging */
				self::addOnScreenLogger((JLog::EMERGENCY + JLog::ALERT + JLog::CRITICAL + JLog::ERROR + JLog::WARNING + JLog::NOTICE));
				break;
					
			
			case "info_only":
			
				/* Info only logging */
				self::addOnScreenLogger(JLog::INFO);
				break;
			
			
			case "none":
		
		}
		
		
		// Get the parameter level for file debug logging
		$fileDebug = is_null($fileDebug) ? LdapHelper::getGlobalParam('file_debug', 'error_only') : $fileDebug;
		
		/* Add file based loggers based on formattedtext */
		switch($fileDebug) {
				
			case "full":
		
				/* Full error logging using three files */
				self::addFileLogger(JLog::DEBUG, 'ldap.debug.log.php');
					
		
			case "error_info":
					
				/* Error and Info logging using two files */
				self::addFileLogger(JLog::INFO, 'ldap.info.log.php');
		
			case "error_only":
		
				/* Error only using one file */
				self::addFileLogger((JLog::EMERGENCY + JLog::ALERT + JLog::CRITICAL + JLog::ERROR + JLog::WARNING + JLog::NOTICE),
							'ldap.error.log.php');
		
				break;
					
		
			case "info_only":
		
				/* Info only using one file */
				self::addFileLogger(JLog::INFO, 'ldap.info.log.php');
				break;
		
		
			case "none":
					
		}
		
		$done = true;
		
	}
	
	protected static function addOnScreenLogger($level) 
	{
		JLog::addLogger(
			array('logger'=>'messagequeue'),
			$level,
			array('ldap')
		);
	}
	
	protected static function addFileLogger($level, $file) 
	{
		JLog::addLogger(
			array('logger'=>'formattedtext',
				'text_file'=>$file, 
				'text_entry_format'=>'{DATETIME} {PRIORITY} {CLASS} {MESSAGE} {LDAP}'
			),
			$level,
			array('ldap')
		);
		
	}
	
	
	public static function addDebugEntry($message, $class = null, $id = 0) {
		return self::addEntry($message, $class, JLog::DEBUG, $id);
		
	}
	
	public static function addErrorEntry($message, $class = null, $id = 0) { 
		return self::addEntry($message, $class, JLog::ERROR, $id);
	}
	
	
	public static function addInfoEntry($message, $class = null, $id = 0) { 
		return self::addEntry($message, $class, JLog::INFO, $id);
	}
	
	public static function addEntry($message, $class = null, $level = JLog::INFO, $id = 0) {

		if($id)	$message = JText::sprintf('LIB_JLDAP2_LOG_ERROR_WID', $message, $id);
		
		if($entry = new JLogEntryLdapEntry($message, $class, $level)) {
			JLog::add($entry);
			return true;
		}
	}
	
	
}