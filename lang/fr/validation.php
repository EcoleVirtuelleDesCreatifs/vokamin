<?php

return [
    'accepted' => 'Votre accord est nécessaire pour envoyer le formulaire.',
    'array' => 'Le champ :attribute est invalide.',
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'email' => 'Indiquez un email valide, par exemple nom@domaine.ci.',
    'file' => 'Le fichier :attribute est invalide.',
    'image' => 'Le fichier :attribute doit être une image.',
    'in' => 'La valeur de :attribute est invalide.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'max' => [
        'array' => '5 fichiers maximum.',
        'file' => 'Le fichier dépasse la taille autorisée (:max Ko). Compressez-le puis réessayez.',
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'mimes' => 'Format non accepté. Formats autorisés : :values.',
    'min' => [
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'regex' => 'Le format de :attribute est invalide (chiffres, espaces et + uniquement).',
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être un texte.',
    'unique' => 'Cette valeur de :attribute est déjà utilisée.',
    'confirmed' => 'La confirmation de :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe actuel est incorrect.',
    'url' => 'Le champ :attribute doit être une adresse web valide.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'prohibited' => 'Le champ :attribute doit être vide.',

    'attributes' => [
        'prestation' => 'prestation', 'nature' => 'nature du bâtiment', 'localisation' => 'localisation', 'surface' => 'surface',
        'description' => 'description', 'nom' => 'nom', 'entreprise' => 'entreprise', 'tel' => 'téléphone', 'whatsapp' => 'WhatsApp',
        'email' => 'email', 'sujet' => 'sujet', 'message' => 'message', 'poste' => 'poste', 'cv' => 'CV', 'files.*' => 'pièce jointe',
        'name' => 'nom', 'title' => 'titre', 'password' => 'mot de passe', 'location' => 'localisation', 'category' => 'catégorie',
        'images.*' => 'image', 'excerpt' => 'résumé', 'body' => 'contenu',
    ],
];
