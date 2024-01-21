<?php

return [
    'mode'                  => 'utf-8',
    'format'                => 'A4',
    'default_font_size'     => '',
    'default_font'          => 'chevin',
    'margin_left'           => 10,
    'margin_right'          => 10,
    'margin_top'            => 10,
    'margin_bottom'         => 10,
    'margin_header'         => 5,
    'margin_footer'         => 5,
    'orientation'           => 'P',
    'title'                 => 'HMH Report',
    'author'                => '',
    'watermark'             => '',
    'show_watermark'        => false,
    'watermark_font'        => 'chevin',
    'display_mode'          => 'fullpage',
    'watermark_text_alpha'  => 0.1,
    'custom_font_path' => base_path('/public/fonts/'), // don't forget the trailing slash!
    'custom_font_data' => [
        'chevin' => [
            'R'  => 'ChevinLight.ttf',    // regular font
            'B'  => 'ChevinBold.ttf',     // optional: bold font
            'I'  => 'ChevinLight.ttf',    // optional: italic font
            'BI' => 'ChevinBold.ttf'      // optional: bold-italic font
        ],
        'dinnextltarabic' => [
            'R'  => 'DINNextLTArabic-Light.ttf',    // regular font
            'B'  => 'DINNextLTArabic.ttf',          // optional: bold font
            'I'  => 'DINNextLTArabic-Light.ttf',    // optional: italic font
            'BI' => 'DINNextLTArabic.ttf'           // optional: bold-italic font
        ]
    ]
];
