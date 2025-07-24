<?php

return [
    'inertia' => env('SEO_TOOLS_INERTIA', false),
    'meta'    => [
        /*
         * The default configurations to be used by the meta generator.
         */
        'defaults'       => [
            'titleBefore'  => false,
            'description'  => '',
            'separator'    => ' - ',
            'keywords'     => [],
            'canonical'    => false,
            'robots'       => false,
        ],
        /*
         * Webmaster tags are always added.
         */
        'webmaster_tags' => [
            'google'    => null,
            'bing'      => null,
            'alexa'     => null,
            'pinterest' => null,
            'yandex'    => null,
            'norton'    => null,
        ],

        'add_notranslate_class' => false,
    ],
    'opengraph' => [
        /*
         * The default configurations to be used by the opengraph generator.
         */
        'defaults' => [
            'description' => '',
            'url'         => false,
            'type'        => false,
            'site_name'   => false,
            'images'      => [],
        ],
    ],
];
