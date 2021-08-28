<?php

return [
    // TABLE
    'table'     => [
        'view'              => 'livewire-table::table',
        'class'             => null,
        'attributes'        => [],
    ],

    // T-HEAD
    'head'      => [
        'class'             => null,
        'attributes'        => [],

        // T-HEAD ROW
        'row'   => [
            'class'         => null,
            'attributes'    => [],
        ],

        // ORDERING ARROWS
        'order' => [
            'asc'           => '&dtrif;',
            'desc'          => '&utrif;',
        ]
    ],

    // T-BODY
    'body'      => [
        'class'             => null,
        'attributes'        => [],
    ],

    // EVERY ROW IN T-BODY
    'row'       => [
        'component'         => 'livewire-row',
        'class'             => null,
        'attributes'        => []
    ],

    // PAGINATION
    'paginate'  => [
        'status'            => true,
        'length'            => 10,
        'current'           => 1,
        'theme'             => 'livewire::tailwind'
    ],

    // ORDERING
    'order'     => [
        'status'            => true,
        'column'            => null,
        'direction'         => null
    ],

    // SEARCHING
    'search'    => [
        'status'            => true,
        'value'             => ''
    ],

    // FILTERING
    'filter'    => [
        'status'            => true,
        'columns'           => []
    ]
];
