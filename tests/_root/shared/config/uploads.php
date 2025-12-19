<?php

return [
    /**
     * Allowed mime types => extensions map for UploadedFile validation.
     */
    'allowed_mime_types' => [
        // Images
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],
        'image/svg+xml' => ['svg'],
        // Audio
        'audio/mpeg' => ['mp3'],
        'audio/wav' => ['wav'],
        'audio/ogg' => ['ogg'],
        'audio/webm' => ['weba'],
        // Video
        'video/mp4' => ['mp4'],
        'video/mpeg' => ['mpeg', 'mpg'],
        'video/quicktime' => ['mov'],
        'video/webm' => ['webm'],
        'video/x-msvideo' => ['avi'],
    ],
];