<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'remindermails' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => 'cronjob',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryCronJob',
                        'action' => 'index',
                    ),
                ),
            ),
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'GroceryCli\Controller\Factory\Console' => 'GroceryCli\Controller\Factory\ConsoleFactory',
        ),
    ),
    'bjyauthorize' => array(
        // 'unauthorized_strategy' => 'BjyAuthorize\View\RedirectionStrategy',
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'Grocery\Controller\GroceryCronJob', 'roles' => array('guest')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'cronjob', 'roles' => array('guest')),
            )
        )
    ),
);
