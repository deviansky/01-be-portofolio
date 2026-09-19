<?php

use Stevebauman\Purify\Definitions\Html5Definition;

return [

    'default' => 'project',

    'configs' => [

        'project' => [
            'Core.Encoding' => 'utf-8',
            'HTML.Doctype' => 'HTML 4.01 Transitional',
            'HTML.Allowed' => 'p,h2,h3,strong,em,u,s,a[href],ul,ol,li,blockquote,pre,code,img[src|alt],figure,figcaption,hr,br',
            'HTML.ForbiddenElements' => '',
            'CSS.AllowedProperties' => '',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty' => false,
            'HTML.TargetBlank' => true,
            'HTML.Nofollow' => true,
        ],

    ],

    'definitions' => Html5Definition::class,

    'css-definitions' => null,

    'serializer' => [
        'driver' => env('CACHE_STORE', env('CACHE_DRIVER', 'file')),
        'cache' => \Stevebauman\Purify\Cache\CacheDefinitionCache::class,
    ],

];
