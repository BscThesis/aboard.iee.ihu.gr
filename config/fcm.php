<?php

return [
    // Enable/disable Firebase Cloud Messaging
    'enabled' => env('FCM_ENABLED', false),

    // Google Cloud project ID, can also be found in the Firebase
    'project_id' => env('FCM_PROJECT_ID'),

    // Absolute path to the Google service account JSON file
    'service_account' => env('FCM_SERVICE_ACCOUNT', ''),

    // Prefix used when constructing topic names from Tag IDs
    'topic_prefix' => env('FCM_TOPIC_PREFIX', 'tag-'),
];
