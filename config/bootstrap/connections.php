<?php

namespace app\config\bootstrap;

use lithium\data\Connections;

 Connections::add('default', array(
 	'type' => CONNECTION_TYPE,
 	'host' => array(CONNECTION,
		),
//	'replicaSet' => true,
 	'database' => CONNECTION_DB,
	'login' => CONNECTION_USER,
	'password' => CONNECTION_PASS,	
	//'classes' => array('server' => 'Mongo')
//	'setSlaveOkay' => true,
//	'readPreference' => Mongo::RP_NEAREST	
 ));

