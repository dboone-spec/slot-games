<?php

defined('SYSPATH') OR die('No direct access allowed.');


return array
        (
        'default'   => array
                (
                'type'         => 'postgresql',
                'connection'   => array(
                        'hostname'   => 'postgres',
                        'database'   => 'app_db',
                        'username'   => 'app_user',
                        'password'   => 'app_password',
                        'persistent' => FALSE,
                ),
                'table_prefix' => '',
                'charset'      => 'utf8',
                'caching'      => FALSE,
                'profiling'    => TRUE,
                'primary_key'  => 'id',
        ),
	'test'   => array
                (
                'type'         => 'postgresql',
                'connection'   => array(
                        'hostname'   => 'postgres',
                        'database'   => 'app_db',
                        'username'   => 'app_user',
                        'password'   => 'app_password',
                        'persistent' => FALSE,
                ),
                'table_prefix' => '',
                'charset'      => 'utf8',
                'caching'      => FALSE,
                'profiling'    => TRUE,
                'primary_key'  => 'id',
        ),
		'clickhouse' => [
            'type'       => 'clickhouse',
            'connection' => [
                'host'     => 'clickhouse',
                'port'     => 8123,
                'database' => 'default',
                'username' => 'default',
                'password' => 'app_password',
            ],
        ],
        'games'   => array
                (
                'type'         => 'postgresql',
                'connection'   => array(
                        'hostname'   => 'postgres',
                        'database'   => 'app_db',
                        'username'   => 'app_user',
                        'password'   => 'app_password',
                        'persistent' => FALSE,
                ),
                'table_prefix' => '',
                'charset'      => 'utf8',
                'caching'      => FALSE,
                'profiling'    => TRUE,
                'primary_key'  => 'id',
        ),
);
