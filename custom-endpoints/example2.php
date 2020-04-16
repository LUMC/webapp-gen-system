<?php

use Directus\Application\Http\Request;
use Directus\Application\Http\Response;
use Directus\Services\ItemsService;
use Zend\Db\Sql\Expression;

return [
    'tissue-data' => [
        'method' => 'POST',
        'handler' => function (Request $request, Response $response) {
            $tissueID = $request->getParam('tissueId');
            $amount = $request->getParam('amount');
            if($tissueID < 1 or $amount < 1){
                return $response->isClientError();
            }
            $mysqli = new mysqli("localhost",
                "root",
                "password",
                "keygenestest");
            if ($mysqli -> connect_errno) {
                echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
                exit();
            }
            $result = $mysqli -> query("SELECT * FROM transcript ORDER BY count DESC LIMIT 100");

            $mysqli -> close();
            return $response->withJson([
                'counts'=> $result -> num_rows,
            ]);
        }
    ],
    '/datetime' => [
        'group' => true,
        'endpoints' => [
            '/date[/{when}]' => [
                'method' => 'GET',
                'handler' => function (Request $request, Response $response) {
                    $when = $request->getAttribute('when');

                    $datetime = new DateTime();
                    switch ($when) {
                        case 'yesterday':
                            $datetime->modify('-1 day');
                            break;
                        case 'tomorrow':
                            $datetime->modify('+1 day');
                            break;
                        default:
                            // When empty we fallback to 'today' option
                            if (!empty($when)) {
                                throw new \Directus\Exception\Exception(
                                    sprintf(
                                        'Unknown: %. Options available: %s',
                                        $when, implode(['today', 'yesterday', 'tomorrow'])
                                    )
                                );
                            }
                    }

                    return $response->withJson([
                        'data' => [
                            'date' => $result = $datetime->format('Y-m-d')
                        ]
                    ]);
                }
            ],
            '/time' => [
                'handler' => function ($request, $response) {
                    return $response->withJSON([
                        'data' => [
                            'time' => date('H:i:s', time())
                        ]
                    ]);
                }
            ]
        ]
    ]
];
