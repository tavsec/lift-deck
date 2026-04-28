<?php

return [
    'login' => [
        'email' => 'E-mail',
        'password' => 'Lozinka',
        'remember_me' => 'Zapamti me',
        'forgot_password' => 'Zaboravili ste lozinku?',
        'button' => 'Prijava',
    ],

    'register' => [
        'name' => 'Ime',
        'email' => 'E-mail',
        'password' => 'Lozinka',
        'confirm_password' => 'Potvrdi lozinku',
        'already_registered' => 'Već ste registrirani?',
        'button' => 'Registracija',

        'panel' => [
            'heading' => 'Prestanite trenirati iz tablica. Sve vodite u LiftDecku.',
            'trial_note' => 'Basic plan uključuje 7-dnevno besplatno probno razdoblje.',
            'feature_1' => 'Programi i bilježenje treninga',
            'feature_1_sub' => 'Gradite planove, klijenti bilježe u stvarnom vremenu',
            'feature_2' => 'Prehrana i metrike',
            'feature_2_sub' => 'Makroi, tjelesne mjere, fotografije napretka',
            'feature_3' => 'Poruke s klijentima',
            'feature_3_sub' => 'Komunicirajte bez napuštanja aplikacije',
            'feature_4' => 'Lojalnost i gamifikacija',
            'feature_4_sub' => 'XP, razine i nagrade koje definira trener',
        ],

        'step1' => [
            'label' => 'Korak 1 od 3',
            'title' => 'Krenimo',
            'subtitle' => 'Unesite e-mail za kreiranje vašeg LiftDeck računa.',
            'email' => 'E-mail adresa',
            'email_ph' => 'vas@primjer.com',
        ],

        'step2' => [
            'label' => 'Korak 2 od 3',
            'title' => 'Koga trenirate?',
            'subtitle' => 'Odaberite opciju koja vam najviše odgovara — možete je kasnije promijeniti.',
            'solo' => 'Solo trener',
            'solo_sub' => 'Tek počinjete ili upravljate do 5 klijenata',
            'growing' => 'Rastući trener',
            'growing_sub' => 'Skalirate, upravljate 5–30 klijenata',
            'gym' => 'Teretana ili tim',
            'gym_sub' => 'Više trenera ili veća baza klijenata',
        ],

        'step3' => [
            'label' => 'Korak 3 od 3',
            'title' => 'Postavite lozinku',
            'subtitle' => 'Gotovo je — dodajte ime i odaberite lozinku.',
            'name' => 'Vaše ime',
            'optional' => 'neobavezno',
            'password' => 'Lozinka',
            'password_ph' => 'Min. 8 znakova',
            'confirm' => 'Potvrdi lozinku',
            'confirm_ph' => 'Ponovite lozinku',
            'submit' => 'Počni s treningom',
        ],

        'actions' => [
            'back' => '← Nazad',
            'continue' => 'Nastavi →',
            'signin' => 'Već imate račun?',
            'signin_link' => 'Prijavite se',
        ],
    ],

    'forgot_password' => [
        'description' => 'Zaboravili ste lozinku? Nema problema. Samo nam javite svoju e-mail adresu i poslat ćemo vam poveznicu za resetiranje lozinke kojom možete odabrati novu.',
        'email' => 'E-mail',
        'button' => 'Pošalji poveznicu za resetiranje lozinke',
    ],

    'reset_password' => [
        'email' => 'E-mail',
        'password' => 'Lozinka',
        'confirm_password' => 'Potvrdi lozinku',
        'button' => 'Resetiraj lozinku',
    ],

    'confirm_password' => [
        'description' => 'Ovo je sigurno područje aplikacije. Molimo potvrdite lozinku prije nastavka.',
        'password' => 'Lozinka',
        'button' => 'Potvrdi',
    ],

    'verify_email' => [
        'description' => 'Hvala na registraciji! Prije nego što počnete, možete li verificirati svoju e-mail adresu klikom na poveznicu koju smo vam upravo poslali? Ako niste primili e-mail, rado ćemo vam poslati novi.',
        'link_sent' => 'Nova verifikacijska poveznica poslana je na e-mail adresu navedenu pri registraciji.',
        'resend' => 'Ponovno pošalji verifikacijski e-mail',
        'logout' => 'Odjava',
    ],

    'join' => [
        'heading' => 'Pridružite se kao klijent',
        'description' => 'Unesite pozivni kod od svog trenera',
        'code_label' => 'Pozivni kod',
        'button' => 'Nastavi',
    ],

    'join_register' => [
        'heading' => 'Dovršite registraciju',
        'joining' => 'Pridružujete se :gym_name',
        'name' => 'Ime',
        'email' => 'E-mail',
        'password' => 'Lozinka',
        'confirm_password' => 'Potvrdi lozinku',
        'button' => 'Stvori račun',
        'use_different_code' => 'Koristite drugi kod',
    ],
];
