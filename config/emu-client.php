<?php

return [
    'start' => 0,
    'chunk' => 500,
    'update_since' => 30, // number of days
    'host' => env('EMU_HOST'),
    'port' => env('EMU_PORT'),
    'fields' => [
        'CreCreatorRef_tab.(NamFullName)',
        'CreSubjectClassification_tab',
        'TitAccessionNo',
        'TitCollection',
        'TitMainTitle',
        'TitParentTitle',
        'TitAlternateTitles_tab',
        'MedMedium',
        'MedObjectType',
        'WorAcmCollectionName'
    ],
    'export_path' => base_path('resources/emu-export'),
    // enable Laravel Routes
    'routes_enabled' => true,
    // do transformation of data out of Emu
    'transform_data' => false,
    // namespace of trandformer class
    'field_transform_class' => null,
    // transforms data within a single field
    'field_transform' => [
    ],
    // adds a field based off of data in multiple fields
    'field_addition' => [
    ],
];
