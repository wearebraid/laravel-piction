<?php

return [
    /**
     * ------------------------------------------------------------------------
     * PICTION SERVER SETTINGS
     * ------------------------------------------------------------------------
     */

    'host' => env('PICTION_HOST', null),

    'user' => env('PICTION_USERNAME', null),

    'pass' => env('PICTION_PASSWORD', null),

    'endpoint' => env('PICTION_ENDPOINT', '!soap.jsonget'),

    /**
     * ------------------------------------------------------------------------
     * SCOUT
     * ------------------------------------------------------------------------
     * When not using Scout, you will want to use:
     * \Braid\Piction\Models\Record as your Record model
     * If you are using Scout you should use:
     * \Braid\Piction\Models\Scout\Record
     * as your Record model.
     */

    'use_scout' => false,

    /**
     * Number of seconds before timing out on a Piction request, 0 = never
     */

    'timeout' => 300,


    /**
     * ------------------------------------------------------------------------
     * Collection Options
     * ------------------------------------------------------------------------
     *
     * Options for retrieving paginated collection data
     *
     */

    'options' => [
        'perpage' => 50,

        /**
         * If no meta fields are supplied, retrieve ALL meta fields
         */
        'meta' => [
            'retrieve_all' => true,
            'ignore' => ['EXIF']
        ],

        /**
         * Record is published indicators
         */
        'check_published' => false,
        'published' => [
            'GENERAL.PUBLISHED' => 'Y',
        ],
    ],
];
