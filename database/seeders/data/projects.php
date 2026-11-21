<?php

// Données de départ (issues de la plaquette et du cahier des charges VOKAMIN)
return [
    [
        'slug' => 'villa-kema',
        'name' => 'Villa Kema',
        'location' => 'Bingerville',
        'category' => 'Construction',
        'tags' => 'construction',
        'description' => 'La Villa Kema, à Bingerville, fait partie des réalisations notables de VOKAMIN en construction. Les visuels présentent la perspective 3D du projet et l\'avancement du gros œuvre.',
        'programme' => null,
        'service_slugs' => [
            'conception',
            'construction',
        ],
        'cover' => 'images/kema-rendu.jpg',
        'is_featured' => true,
        'position' => 1,
        'images' => [
            [
                'path' => 'images/kema-rendu.jpg',
                'caption' => 'Perspective 3D',
            ],
            [
                'path' => 'images/kema-chantier-1.jpg',
                'caption' => 'Chantier — gros œuvre',
            ],
            [
                'path' => 'images/kema-chantier-2.jpg',
                'caption' => 'Chantier — structure',
            ],
        ],
        'ba_before' => 'images/kema-chantier-2.jpg',
        'ba_after' => 'images/kema-rendu.jpg',
        'ba_label_before' => 'Chantier',
        'ba_label_after' => 'Perspective 3D',
    ],
    [
        'slug' => 'villa-lirana',
        'name' => 'Villa Lirana',
        'location' => 'Riviera 3',
        'category' => 'Construction',
        'tags' => 'construction',
        'description' => 'La Villa Lirana, à Riviera 3, fait partie des réalisations notables de VOKAMIN en construction. Les visuels présentent la perspective 3D du projet et deux étapes du chantier.',
        'programme' => null,
        'service_slugs' => [
            'conception',
            'construction',
        ],
        'cover' => 'images/lirana-rendu.jpg',
        'is_featured' => true,
        'position' => 2,
        'images' => [
            [
                'path' => 'images/lirana-rendu.jpg',
                'caption' => 'Perspective 3D',
            ],
            [
                'path' => 'images/lirana-chantier-1.jpg',
                'caption' => 'Chantier — élévations',
            ],
            [
                'path' => 'images/lirana-chantier-2.jpg',
                'caption' => 'Chantier — bassin',
            ],
        ],
        'ba_before' => 'images/lirana-chantier-1.jpg',
        'ba_after' => 'images/lirana-rendu.jpg',
        'ba_label_before' => 'Chantier',
        'ba_label_after' => 'Perspective 3D',
    ],
    [
        'slug' => 'villa-maureen',
        'name' => 'Villa Maureen',
        'location' => 'Cité Synacass-ci 2',
        'category' => 'Construction',
        'tags' => 'construction',
        'description' => 'La Villa Maureen, à la Cité Synacass-ci 2, est présentée du gros œuvre aux finitions : coulage du béton, crépissage des façades, piscine et façade éclairée.',
        'programme' => null,
        'service_slugs' => [
            'construction',
            'renovation',
        ],
        'cover' => 'images/maureen-facade-nuit.jpg',
        'is_featured' => true,
        'position' => 3,
        'images' => [
            [
                'path' => 'images/maureen-facade-nuit.jpg',
                'caption' => 'Façade éclairée',
            ],
            [
                'path' => 'images/maureen-piscine-1.jpg',
                'caption' => 'Piscine',
            ],
            [
                'path' => 'images/maureen-piscine-2.jpg',
                'caption' => 'Piscine et plage',
            ],
            [
                'path' => 'images/maureen-gros-oeuvre.jpg',
                'caption' => 'Gros œuvre',
            ],
            [
                'path' => 'images/maureen-crepissage.jpg',
                'caption' => 'Crépissage des façades',
            ],
            [
                'path' => 'images/coulage-pompe.jpg',
                'caption' => 'Coulage du béton à la pompe',
            ],
            [
                'path' => 'images/coulage-toupie.jpg',
                'caption' => 'Camion de coulage',
            ],
        ],
        'ba_before' => 'images/maureen-gros-oeuvre.jpg',
        'ba_after' => 'images/maureen-facade-nuit.jpg',
        'ba_label_before' => 'Gros œuvre',
        'ba_label_after' => 'Finitions',
    ],
    [
        'slug' => 'residences-oasis',
        'name' => 'Résidences Oasis',
        'location' => 'Bonoua',
        'category' => 'Promotion immobilière',
        'tags' => 'construction promotion',
        'description' => 'Les Résidences Oasis à Bonoua sont un complexe résidentiel de 10 villas haut standing. Les images retracent le projet, du terrassement du site aux villas achevées.',
        'programme' => 'Complexe résidentiel de 10 villas haut standing',
        'service_slugs' => [
            'promotion',
            'construction',
        ],
        'cover' => 'images/oasis-rendu.jpg',
        'is_featured' => true,
        'position' => 4,
        'images' => [
            [
                'path' => 'images/oasis-rendu.jpg',
                'caption' => 'Perspective du complexe',
            ],
            [
                'path' => 'images/oasis-rendu-rue.jpg',
                'caption' => 'Perspective côté rue',
            ],
            [
                'path' => 'images/oasis-villa-piscine.jpg',
                'caption' => 'Villa avec piscine',
            ],
            [
                'path' => 'images/oasis-villa-livree.jpg',
                'caption' => 'Villa et jardin',
            ],
            [
                'path' => 'images/oasis-terrassement.jpg',
                'caption' => 'Terrassement',
            ],
            [
                'path' => 'images/oasis-fouilles.jpg',
                'caption' => 'Fouilles',
            ],
            [
                'path' => 'images/oasis-ferraillage.jpg',
                'caption' => 'Ferraillage des fondations',
            ],
            [
                'path' => 'images/oasis-elevation.jpg',
                'caption' => 'Élévation des murs',
            ],
            [
                'path' => 'images/oasis-dalle.jpg',
                'caption' => 'Dalle',
            ],
            [
                'path' => 'images/oasis-piscine-coffrage.jpg',
                'caption' => 'Coffrage de piscine',
            ],
            [
                'path' => 'images/oasis-gros-oeuvre.jpg',
                'caption' => 'Villas en gros œuvre',
            ],
            [
                'path' => 'images/oasis-remblai.jpg',
                'caption' => 'Livraison de matériaux',
            ],
        ],
        'ba_before' => 'images/oasis-gros-oeuvre.jpg',
        'ba_after' => 'images/oasis-villa-livree.jpg',
        'ba_label_before' => 'Gros œuvre',
        'ba_label_after' => 'Villa achevée',
    ],
    [
        'slug' => 'standard-chartered',
        'name' => 'Standard Chartered Bank Côte d\'Ivoire',
        'location' => 'Abidjan',
        'category' => 'Building Maintenance',
        'tags' => 'maintenance',
        'description' => 'Intervention de Building Maintenance pour Standard Chartered Bank Côte d\'Ivoire, l\'une des références de VOKAMIN.',
        'programme' => null,
        'service_slugs' => [
            'maintenance',
        ],
        'cover' => 'images/scb-desinfection-1.jpg',
        'is_featured' => true,
        'position' => 5,
        'images' => [
            [
                'path' => 'images/scb-desinfection-1.jpg',
                'caption' => 'Désinfection — entrée d\'agence',
            ],
            [
                'path' => 'images/scb-desinfection-2.jpg',
                'caption' => 'Désinfection — espace d\'accueil',
            ],
        ],
    ],
    [
        'slug' => 'clinique-siloe',
        'name' => 'Clinique Médicale Siloé',
        'location' => 'Grand-Bassam',
        'category' => 'Design & enseignes',
        'tags' => 'design',
        'description' => 'Enseigne lumineuse de la Clinique Médicale Siloé à Grand-Bassam, accompagnée d\'un totem signalétique (5 m × 90 cm).',
        'programme' => null,
        'service_slugs' => [
            'design',
        ],
        'cover' => 'images/siloe-enseigne.jpg',
        'is_featured' => true,
        'position' => 6,
        'images' => [
            [
                'path' => 'images/siloe-enseigne.jpg',
                'caption' => 'Enseigne lumineuse',
            ],
            [
                'path' => 'images/siloe-totem.jpg',
                'caption' => 'Plan du totem signalétique',
            ],
        ],
    ],
    [
        'slug' => 'entretien-traitements',
        'name' => 'Entretien & traitements',
        'location' => 'Côte d\'Ivoire',
        'category' => 'Building Maintenance',
        'tags' => 'maintenance',
        'description' => 'Prestations de Building Maintenance : nettoyage professionnel, dératisation, désinsectisation et entretien d\'espaces verts.',
        'programme' => null,
        'service_slugs' => [
            'maintenance',
        ],
        'cover' => 'images/entretien-espaces-verts.jpg',
        'is_featured' => false,
        'position' => 7,
        'images' => [
            [
                'path' => 'images/entretien-espaces-verts.jpg',
                'caption' => 'Traitement des espaces verts',
            ],
            [
                'path' => 'images/entretien-nettoyage.jpg',
                'caption' => 'Nettoyage professionnel',
            ],
        ],
    ],
    [
        'slug' => 'negoce-equipements',
        'name' => 'Équipements & finitions',
        'location' => 'Côte d\'Ivoire',
        'category' => 'Négoce de matériaux',
        'tags' => 'negoce',
        'description' => 'Équipements fournis par VOKAMIN dans le cadre de son activité de négoce : éclairage LED, portes, parements en pierre et faux plafond lumineux.',
        'programme' => null,
        'service_slugs' => [
            'negoce',
            'renovation',
        ],
        'cover' => 'images/deco-plafond-led.jpg',
        'is_featured' => false,
        'position' => 8,
        'images' => [
            [
                'path' => 'images/deco-plafond-led.jpg',
                'caption' => 'Faux plafond et éclairage',
            ],
            [
                'path' => 'images/negoce-spot-1.jpg',
                'caption' => 'Spot LED encastré',
            ],
            [
                'path' => 'images/negoce-spot-2.jpg',
                'caption' => 'Spot LED sur rail',
            ],
            [
                'path' => 'images/negoce-spot-3.jpg',
                'caption' => 'Double spot LED',
            ],
            [
                'path' => 'images/negoce-porte-1.jpg',
                'caption' => 'Porte',
            ],
            [
                'path' => 'images/negoce-porte-2.jpg',
                'caption' => 'Porte décor bois',
            ],
            [
                'path' => 'images/deco-pierre-1.jpg',
                'caption' => 'Parement pierre',
            ],
            [
                'path' => 'images/deco-pierre-2.jpg',
                'caption' => 'Pierre en bord de bassin',
            ],
        ],
    ],
];
