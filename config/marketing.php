<?php

return [
    'sms'=> [
        'demo_file_path' => env('SMS_FILE_PATH', 'assets/csv/sms-marketing-template-file.csv'),
        'upload_location' => env('SMS_IMPORT_PATH', 'sms-imports/uploaded-files'),
        'valid_file_location' => env('SMS_VALID_PATH', 'sms-imports/valid-records/'),
        'failed_file_location' => env('SMS_FAILD_PATH', 'sms-imports/failed-records/'),
    ]
];
