<?php
  
// return [
// 	'mode'                  => 'utf-8',
// 	'format'                => 'A4',
// 	'author'                => '',
// 	'subject'               => '',
// 	'keywords'              => 'PDF, Laravel, Package, Peace',
// 	'creator'               => 'Laravel Pdf',
// 	'display_mode'          => 'fullpage',
// 	'tempDir'               =>   base_path('temp')
// ];

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	//'tempDir'               => base_path('../temp/'),
	'tempDir'               => base_path('temp'),  //for root temp folder
	//'tempDir'               => public_path('temp'),  //for public temp folder iitmuni.ttf

	'font_path' => public_path('fonts/'),
    'font_data' => [
        'kannad' => [
            'R'  => 'tunga.ttf',    // regular font for kannad
            'B'  => 'tunga.ttf',	//'Karma-Regular.ttf',       // optional: bold font for hindi
             // 'I'  => 'latha.ttf',     // optional: italic font
               //'BI' => 'latha.ttf', // optional: bold-italic font
            
        ],
       'telugu' => [
            'R'  => 'gautami.ttf',    // regular font for talugu
            'B'  => 'gautami.ttf',    //'Karma-Regular.ttf',       // optional: bold font for hindi
            'I'  => 'gautami.ttf',     // optional: italic font
             
        ],
       'manny' => [
            'R'  => 'FreeSerifItalic.ttf',          // FreeSerif font for all
            'B'  => 'FreeSerifItalic.ttf',       //'FreeSerif-Regular.ttf',       // optional: bold font for hindi
            'I'  => 'FreeSerifItalic.ttf',       // FreeSerif: italic font
            'BI' => 'FreeSerifBoldItalic.ttf',   // FreeSerif: bold-italic font
            
        ],
        'bangla' => [
            'R'  => 'mitra.ttf',          // FreeSerif font for all
            //'B'  => 'FreeSerifItalic.ttf',       //'FreeSerif-Regular.ttf',       // optional: bold font for hindi
           // 'I'  => 'FreeSerifItalic.ttf',       // FreeSerif: italic font
            //'BI' => 'FreeSerifBoldItalic.ttf',   // FreeSerif: bold-italic font
            
        ]
        // ...add as many as you want.
    ]

 
];

