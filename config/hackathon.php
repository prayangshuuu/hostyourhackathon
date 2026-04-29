<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Submission Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for idea submissions — file upload constraints, allowed
    | file types, and maximum number of attachments per submission.
    |
    */

    'submission' => [

        // Maximum file size in kilobytes (default: 10 MB)
        'max_file_size_kb' => env('SUBMISSION_MAX_FILE_SIZE_KB', 10240),

        // Allowed file extensions
        'allowed_extensions' => ['pdf', 'ppt', 'pptx'],

        // Allowed MIME types
        'allowed_mimes' => [
            'application/pdf',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ],

        // Maximum number of files per submission
        'max_files' => env('SUBMISSION_MAX_FILES', 5),

    ],

];
