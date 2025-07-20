<?php

if (!function_exists('get_file_icon')) {
    function get_file_icon($fileType)
    {
        switch (strtolower($fileType)) {
            case 'application/pdf':
                return 'fas fa-file-pdf text-danger';
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                return 'fas fa-file-word text-primary';
            case 'application/vnd.ms-powerpoint':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
                return 'fas fa-file-powerpoint text-warning';
            case 'text/plain':
                return 'fas fa-file-alt text-secondary';
            default:
                return 'fas fa-file text-muted';
        }
    }
}

if (!function_exists('format_file_size')) {
    function format_file_size($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
} 