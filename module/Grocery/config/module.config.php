<?php

namespace Grocery;

return array(
    'controllers' => array(
        'factories' => array(
            'Grocery\Controller\GroceryProduct' => 'Grocery\Factory\GroceryProductControllerFactory',
            'Grocery\Controller\GroceryProductGroup' => 'Grocery\Factory\GroceryProductGroupControllerFactory',
            'Grocery\Controller\GroceryProductList' => 'Grocery\Factory\GroceryProductListControllerFactory',
            'Grocery\Controller\GroceryProductListAjax' => 'Grocery\Factory\GroceryProductListAjaxControllerFactory',
            'Grocery\Controller\GroceryProductAjax' => 'Grocery\Factory\GroceryProductAjaxControllerFactory',
            'Grocery\Controller\GroceryProductFact' => 'Grocery\Factory\GroceryProductFactControllerFactory',
            'Grocery\Controller\GroceryDashboard' => 'Grocery\Factory\GroceryDashboardControllerFactory',
            'Grocery\Controller\GrocerySettings' => 'Grocery\Factory\GrocerySettingControllerFactory',
            'Grocery\Controller\GroceryCronJob' => 'Grocery\Factory\GroceryCronJobControllerFactory'
        )
    ),
    'service_manager' => array(
        'invokables' => array(
            'Grocery\Service\productServiceInterface' => 'Grocery\Service\productService',
            'Grocery\Service\productListServiceInterface' => 'Grocery\Service\productListService',
            'Grocery\Service\productImageServiceInterface' => 'Grocery\Service\productImageService',
            'Grocery\Service\groceryMailServiceInterface' => 'Grocery\Service\groceryMailService'
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'groceryProduct' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/product[/:action][/:id][/:approve]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                        'approve' => '[0-2]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryProduct',
                        'action' => 'index',
                    ),
                ),
            ),
            'groceryProductList' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/productList[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryProductList',
                        'action' => 'index',
                    ),
                ),
            ),
            'groceryProductListAjax' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/productListAjax[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryProductListAjax',
                    ),
                ),
            ),
            'groceryProductAjax' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/productAjax[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryProductAjax',
                    ),
                ),
            ),
            'groceryProductGroup' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/productgroup[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryProductGroup',
                        'action' => 'index',
                    ),
                ),
            ),
            'groceryProductFact' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/productFact[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryProductFact',
                        'action' => 'index',
                    ),
                ),
            ),
            'groceryDashboard' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/dashboard[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryDashboard',
                        'action' => 'index',
                    ),
                ),
            ),
            'grocerySettings' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/settings[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GrocerySettings',
                        'action' => 'index',
                    ),
                ),
            ),
            'groceryCronJob' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/cronjob[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'Grocery\Controller\GroceryCronJob',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'grocery' => __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
    'navigation' => array(
        'siteuser_backend' => array(
            array(
                'label' => 'Settings',
                'route' => 'grocerySettings',
                'action' => 'index',
            ),
            array(
                'label' => 'Poducts',
                'route' => 'groceryProduct',
                'pages' => array(
                    array(
                        'label' => 'Overview',
                        'route' => 'groceryProduct',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Add',
                        'route' => 'groceryProduct',
                        'action' => 'add',
                    ),
                    array(
                        'label' => 'Product suggestions',
                        'route' => 'groceryProduct',
                        'action' => 'productSuggestions',
                    ),
                ),
            ),
            array(
                'label' => 'Product groups',
                'route' => 'groceryProductGroup',
                'pages' => array(
                    array(
                        'label' => 'Overview',
                        'route' => 'groceryProductGroup',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Add',
                        'route' => 'groceryProductGroup',
                        'action' => 'add',
                    ),
                ),
            ),
            array(
                'label' => 'Product facts',
                'route' => 'groceryProductFact',
                'pages' => array(
                    array(
                        'label' => 'Overview',
                        'route' => 'groceryProductFact',
                        'action' => 'index',
                    ),
                    array(
                        'label' => 'Add',
                        'route' => 'groceryProductFact',
                        'action' => 'add',
                    ),
                ),
            ),
            array(
                'label' => 'Product lists',
                'route' => 'groceryProductList',
                'action' => 'index',
            ),
        ),
        'default' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'groceryDashboard',
                'action' => 'index',
            ),
        )
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'Grocery\Controller\GroceryProductList', 'roles' => array('user')),
                array('controller' => 'Grocery\Controller\GroceryProduct', 'roles' => array('user')),
                array('controller' => 'Grocery\Controller\GroceryProductGroup', 'roles' => array('admin')),
                array('controller' => 'Grocery\Controller\GroceryProductListAjax', 'roles' => array('user')),
                array('controller' => 'Grocery\Controller\GroceryProductAjax', 'roles' => array('user')),
                array('controller' => 'Grocery\Controller\GroceryProductFact', 'roles' => array('admin')),
                array('controller' => 'Grocery\Controller\GroceryDashboard', 'roles' => array('user')),
                array('controller' => 'Grocery\Controller\GrocerySettings', 'roles' => array('admin')),
                array('controller' => 'Grocery\Controller\GroceryCronJob', 'roles' => array('guest')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'groceryProduct', 'roles' => array('user')),
                array('route' => 'groceryProductList', 'roles' => array('user')),
                array('route' => 'groceryProductGroup', 'roles' => array('admin')),
                array('route' => 'groceryProductListAjax', 'roles' => array('user')),
                array('route' => 'groceryProductAjax', 'roles' => array('user')),
                array('route' => 'groceryProductFact', 'roles' => array('admin')),
                array('route' => 'groceryDashboard', 'roles' => array('user')),
                array('route' => 'grocerySettings', 'roles' => array('admin')),
                array('route' => 'groceryCronJob', 'roles' => array('guest')),
            )
        )
    ),
    'imageUploadSettings' => array(
        'uploadFolder' => 'img/userFiles/product/original/',
        'uploadeFileSize' => '5000000000000000',
        'allowedImageTypes' => array(
            'jpg',
            'png',
            'gif'
        )
    ),
    'gocerySettings' => array(
        'orderReminder' => array(
            'subject' => 'Please reset your password.',
            'mail_sender_email' => 'sander@verzeilberg.nl',
            'mail_sender_name' => 'Reports BV',
            'mail_reply_email' => 'sander@verzeilberg.nl',
            'mail_reply_name' => 'Boodschappen support',
            'reminder_order_mail_template' => 'templates/send_order_Reminder_email.phtml',
            'product_order_list_mail_template' => 'templates/send_order_List_email.phtml',
            'product_suggestion_template' => 'templates/send_suggestion_email.phtml',
        ),
    ),
);
