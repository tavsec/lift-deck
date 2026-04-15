<?php

return [
    'login' => [
        'email' => 'E-pošta',
        'password' => 'Geslo',
        'remember_me' => 'Zapomni si me',
        'forgot_password' => 'Ste pozabili geslo?',
        'button' => 'Prijava',
    ],

    'register' => [
        'name' => 'Ime',
        'email' => 'E-pošta',
        'password' => 'Geslo',
        'confirm_password' => 'Potrdi geslo',
        'already_registered' => 'Že registrirani?',
        'button' => 'Registracija',

        'panel' => [
            'heading' => 'Nehajte trenirati iz tabel. Vse vodite v LiftDecku.',
            'trial_note' => '7 dni brezplačno · Kreditna kartica ni potrebna',
            'feature_1' => 'Programi in beleženje treningov',
            'feature_1_sub' => 'Gradite načrte, stranke beležijo v realnem času',
            'feature_2' => 'Prehrana in metrike',
            'feature_2_sub' => 'Makri, telesne mere, fotografije napredka',
            'feature_3' => 'Sporočila s strankami',
            'feature_3_sub' => 'Komunicirajte brez zapuščanja aplikacije',
            'feature_4' => 'Zvestoba in gamifikacija',
            'feature_4_sub' => 'XP, ravni in nagrade, ki jih določi trener',
        ],

        'step1' => [
            'label' => '1. korak od 3',
            'title' => 'Koga trenirate?',
            'subtitle' => 'Izberite možnost, ki vam najbolj ustreza — pozneje jo lahko spremenite.',
            'solo' => 'Solo trener',
            'solo_sub' => 'Šele začenjate ali upravljate do 5 strank',
            'growing' => 'Rastoči trener',
            'growing_sub' => 'Se širite, upravljate 5–30 strank',
            'gym' => 'Telovadnica ali ekipa',
            'gym_sub' => 'Več trenerjev ali večja baza strank',
        ],

        'step2' => [
            'label' => '2. korak od 3',
            'title' => 'Povejte nam o sebi',
            'subtitle' => 'Vsa polja so neobvezna — izpolnite jih lahko pozneje.',
            'name' => 'Vaše ime',
            'gym_name' => 'Ime telovadnice ali podjetja',
            'gym_name_ph' => 'npr. Iron Peak Fitness',
            'bio' => 'Niša treniranja',
            'bio_ph' => 'npr. moč, hujšanje, rehabilitacija',
            'client_count' => 'Trenutno število strank',
            'tools' => 'Orodja, ki jih trenutno uporabljate',
            'tool_sheets' => 'Google preglednice',
            'tool_excel' => 'Excel',
            'tool_whatsapp' => 'WhatsApp',
            'tool_other' => 'Drugo',
            'optional' => 'neobvezno',
            'skip' => 'Preskoči ta korak',
        ],

        'step3' => [
            'label' => '3. korak od 3',
            'title' => 'Ustvarite račun',
            'subtitle' => 'Skoraj ste — le vaša e-pošta in geslo.',
            'email' => 'E-poštni naslov',
            'email_ph' => 'vi@primer.com',
            'password' => 'Geslo',
            'password_ph' => 'Min. 8 znakov',
            'confirm' => 'Potrdi geslo',
            'confirm_ph' => 'Ponovite geslo',
            'submit' => 'Ustvari račun',
        ],

        'actions' => [
            'back' => '← Nazaj',
            'continue' => 'Nadaljuj →',
            'signin' => 'Že imate račun?',
            'signin_link' => 'Prijavite se',
        ],
    ],

    'forgot_password' => [
        'description' => 'Ste pozabili geslo? Ni problema. Sporočite nam svoj e-poštni naslov in poslali vam bomo povezavo za ponastavitev gesla, ki vam bo omogočila izbiro novega.',
        'email' => 'E-pošta',
        'button' => 'Pošlji povezavo za ponastavitev gesla',
    ],

    'reset_password' => [
        'email' => 'E-pošta',
        'password' => 'Geslo',
        'confirm_password' => 'Potrdi geslo',
        'button' => 'Ponastavi geslo',
    ],

    'confirm_password' => [
        'description' => 'To je varno območje aplikacije. Pred nadaljevanjem potrdite svoje geslo.',
        'password' => 'Geslo',
        'button' => 'Potrdi',
    ],

    'verify_email' => [
        'description' => 'Hvala za registracijo! Preden začnete, bi lahko potrdili svoj e-poštni naslov s klikom na povezavo, ki smo vam jo pravkar poslali? Če e-pošte niste prejeli, vam jo bomo z veseljem poslali znova.',
        'link_sent' => 'Nova potrditvena povezava je bila poslana na e-poštni naslov, ki ste ga navedli pri registraciji.',
        'resend' => 'Ponovno pošlji potrditveno e-pošto',
        'logout' => 'Odjava',
    ],

    'join' => [
        'heading' => 'Pridruži se kot stranka',
        'description' => 'Vnesite povabilno kodo vašega trenerja',
        'code_label' => 'Povabilna koda',
        'button' => 'Nadaljuj',
    ],

    'join_register' => [
        'heading' => 'Dokončaj registracijo',
        'joining' => 'Pridružujete se :gym_name',
        'name' => 'Ime',
        'email' => 'E-pošta',
        'password' => 'Geslo',
        'confirm_password' => 'Potrdi geslo',
        'button' => 'Ustvari račun',
        'use_different_code' => 'Uporabi drugo kodo',
    ],
];
