<?php

/*
| Valeurs par défaut des coordonnées et textes. Elles sont modifiables depuis
| l'administration (table « settings ») sans toucher au code.
*/
return [
    'notify_email' => env('VOKAMIN_NOTIFY_EMAIL', 'info@vokamin.com'),
    'ga_id' => env('VOKAMIN_GA_ID'),

    'admin' => [
        'name' => env('ADMIN_NAME', 'Administrateur VOKAMIN'),
        'email' => env('ADMIN_EMAIL', 'admin@vokamin.ci'),
        'password' => env('ADMIN_PASSWORD', 'ChangezMoi-2026!'),
    ],

    'defaults' => [
        'company_name' => 'VOKAMIN SARL',
        'brand' => "VOKAMIN Côte d'Ivoire",
        'tagline' => "Nous co-créons plus qu'un bâtiment, un avenir.",
        'hero_lead' => 'Concevoir. Bâtir. Sublimer. Entretenir. Une seule équipe, de la première esquisse à la remise des clés.',
        'tel1' => '+225 07 08 07 11 32',
        'tel2' => '+225 01 01 16 81 81',
        'fax' => '+225 07 08 07 11 32',
        'whatsapp' => '+225 07 08 07 11 32',
        'whatsapp_message' => 'Bonjour VOKAMIN, je souhaite obtenir des informations concernant un projet.',
        'email' => 'info@vokamin.com',
        'address' => 'Cocody Angré, Villa 801, Îlot G',
        'bp' => '18 BP 2476 Abidjan 18',
        'city' => "Abidjan, Côte d'Ivoire",
        'rccm' => 'CI-ABJ-03-2022-B13-04305',
        'cc' => '22429012R',
        'forme' => 'SARLU',
        'capital' => '5 000 000 FCFA',
        'creation' => '18 mai 2022',
        'gerant' => 'Kouassi Atoubou Étienne',
        'hours_week' => '08 h 00 – 18 h 00',
        'hours_sat' => '09 h 00 – 13 h 00',
        'hours_sun' => 'Fermé',
        'facebook' => '',
        'linkedin' => '',
        'instagram' => '',
        'youtube' => '',
        'brochure_path' => '',
        'host_name' => '[Nom de l’hébergeur à compléter]',
    ],

    'upload' => [
        'quote_mimes' => 'pdf,jpg,jpeg,png,dwg,doc,docx',
        'quote_max_kb' => 10240,
        'quote_max_files' => 5,
        'cv_mimes' => 'pdf,doc,docx',
        'cv_max_kb' => 5120,
        'image_mimes' => 'jpg,jpeg,png,webp',
        'image_max_kb' => 8192,
    ],
];
