<?php

namespace UploadImages;

return array(
    'controllers' => array(
        'factories' => array(
            'UploadImages\Controller\UploadImages' => 'UploadImages\Factory\UploadImagesControllerFactory',
        )
    ),
    'service_manager' => array(
        'invokables' => array(
            'UploadImages\Service\cropImageServiceInterface' => 'UploadImages\Service\cropImageService'
        ),
    ),
    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'images' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/image[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'UploadImages\Controller\UploadImages',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'uploadimages' => __DIR__ . '/../view',
        ),
    ),
    'bjyauthorize' => array(
        'guards' => array(
            'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'UploadImages\Controller\UploadImages', 'roles' => array('user')),
            ),
            'BjyAuthorize\Guard\Route' => array(
                array('route' => 'images', 'roles' => array('user')),
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
);
