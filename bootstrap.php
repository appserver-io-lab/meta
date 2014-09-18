<?php

/**
 * bootstrap.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    Meta
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/meta
 * @link       http://www.appserver.io
 */

define('APPSERVER_BP', __DIR__);

// load composer namespaces and add them to the include path
$paths = array();
$namespaces = require APPSERVER_BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .
    'code' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'composer' .
    DIRECTORY_SEPARATOR . 'autoload_namespaces.php';
foreach ($namespaces as $namespace) {
    $paths = array_merge($paths, $namespace);
}

// add the default include paths
$paths[] = APPSERVER_BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'local';
$paths[] = APPSERVER_BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code' .
    DIRECTORY_SEPARATOR . 'community';
$paths[] = APPSERVER_BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'core';
$paths[] = APPSERVER_BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code' .
    DIRECTORY_SEPARATOR . 'lib'; // directory for PEAR libraries

// set the new include path
set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, $paths));

require APPSERVER_BP . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR .
    'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
