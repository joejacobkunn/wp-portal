<?php

return [
    'demo_file_path' => env('WARRANTY_FILE_PATH', 'assets/csv/warranty-registration-template-file.xlsx'),
    'upload_location' => env('WARRANTY_IMPORT_PATH', 'warranty-imports/uploaded-files'),
    'valid_file_location' => env('WARRANTY_VALID_PATH', 'warranty-imports/valid-records/'),
    'failed_file_location' => env('WARRANTY_FAILD_PATH', 'warranty-imports/failed-records/'),
    'record_path' => env('WARRANTY_RECORD_PATH', 'warranty-records/'),
];
