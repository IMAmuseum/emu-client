<?php

return [
    'start' => 0,
    'chunk' => 500,
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
    'routes_enabled' => true,
];
