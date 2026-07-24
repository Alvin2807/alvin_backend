<?php

return [
    'oracle' => [
        'driver'         => 'oracle',
        /* 'tns'            => env('DB_TNS', '(DESCRIPTION= (ADDRESS= (PROTOCOL=TCP)(HOST= 172.26.11.44)(PORT=1521))
		(CONNECT_DATA=
		(SERVER=DEDICATED)
		(SERVICE_NAME = desaprocudbpdb.procuraduria.local)
		))'), */
        'host'           => 'mp8dc01desa.procuraduria.local',
        'port'           => env('DB_PORT', '1521'),
        'database'       => 'INVADMIN',
        'username'       => 'INVADMIN',
        'password'       => 'ministerio',
        'charset'        => env('DB_CHARSET', 'UTF8'),
        'prefix'         => env('DB_PREFIX', ''),
        'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
        'edition'        => env('DB_EDITION', 'ora$base'),
        'service_name' => 'desaprocudbpdb.procuraduria.local',
        'hj' => 'local',
    ],
];
