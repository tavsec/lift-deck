<?php

return [
    'login' => [
        'email' => 'Email',
        'password' => 'Password',
        'remember_me' => 'Remember me',
        'forgot_password' => 'Forgot your password?',
        'button' => 'Log in',
    ],

    'register' => [
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'already_registered' => 'Already registered?',
        'button' => 'Register',

        'panel' => [
            'heading' => 'Stop coaching from spreadsheets. Run everything in LiftDeck.',
            'trial_note' => '7-day free trial · No credit card required',
            'feature_1' => 'Programs & workout logging',
            'feature_1_sub' => 'Build plans, clients log in real-time',
            'feature_2' => 'Nutrition & metrics',
            'feature_2_sub' => 'Macros, body measurements, progress photos',
            'feature_3' => 'Client messaging',
            'feature_3_sub' => 'Communicate without leaving the app',
            'feature_4' => 'Loyalty & gamification',
            'feature_4_sub' => 'XP, levels, and coach-defined rewards',
        ],

        'step1' => [
            'label' => 'Step 1 of 3',
            'title' => 'Who are you coaching?',
            'subtitle' => 'Pick the option that best fits you — you can change it later.',
            'solo' => 'Solo coach',
            'solo_sub' => 'Just starting out or managing up to 5 clients',
            'growing' => 'Growing coach',
            'growing_sub' => 'Scaling up, managing 5–30 clients',
            'gym' => 'Gym or team',
            'gym_sub' => 'Multiple coaches or a larger client base',
        ],

        'step2' => [
            'label' => 'Step 2 of 3',
            'title' => 'Tell us about yourself',
            'subtitle' => 'All fields are optional — you can fill these in later.',
            'name' => 'Your name',
            'gym_name' => 'Gym or business name',
            'gym_name_ph' => 'e.g. Iron Peak Fitness',
            'bio' => 'Coaching niche',
            'bio_ph' => 'e.g. strength, weight loss, rehab',
            'client_count' => 'Current number of clients',
            'tools' => 'Tools you currently use',
            'tool_sheets' => 'Google Sheets',
            'tool_excel' => 'Excel',
            'tool_whatsapp' => 'WhatsApp',
            'tool_other' => 'Other',
            'optional' => 'optional',
            'skip' => 'Skip this step',
        ],

        'step3' => [
            'label' => 'Step 3 of 3',
            'title' => 'Create your account',
            'subtitle' => 'Almost there — just your email and a password.',
            'email' => 'Email address',
            'email_ph' => 'you@example.com',
            'password' => 'Password',
            'password_ph' => 'Min. 8 characters',
            'confirm' => 'Confirm password',
            'confirm_ph' => 'Repeat your password',
            'submit' => 'Create account',
        ],

        'actions' => [
            'back' => '← Back',
            'continue' => 'Continue →',
            'signin' => 'Already have an account?',
            'signin_link' => 'Sign in',
        ],
    ],

    'forgot_password' => [
        'description' => 'Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.',
        'email' => 'Email',
        'button' => 'Email Password Reset Link',
    ],

    'reset_password' => [
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'button' => 'Reset Password',
    ],

    'confirm_password' => [
        'description' => 'This is a secure area of the application. Please confirm your password before continuing.',
        'password' => 'Password',
        'button' => 'Confirm',
    ],

    'verify_email' => [
        'description' => 'Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.',
        'link_sent' => 'A new verification link has been sent to the email address you provided during registration.',
        'resend' => 'Resend Verification Email',
        'logout' => 'Log Out',
    ],

    'join' => [
        'heading' => 'Join as a Client',
        'description' => 'Enter the invitation code from your coach',
        'code_label' => 'Invitation Code',
        'button' => 'Continue',
    ],

    'join_register' => [
        'heading' => 'Complete Your Registration',
        'joining' => "You're joining :gym_name",
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'button' => 'Create Account',
        'use_different_code' => 'Use a different code',
    ],
];
