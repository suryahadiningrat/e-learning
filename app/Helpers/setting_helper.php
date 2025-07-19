<?php

if (!function_exists('get_setting')) {
    /**
     * Get setting value by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        $db = \Config\Database::connect();
        $setting = $db->table('settings')->where('key', $key)->get()->getRowArray();
        
        return $setting ? $setting['value'] : $default;
    }
}

if (!function_exists('get_logo_sekolah')) {
    /**
     * Get school logo URL
     * 
     * @return string
     */
    function get_logo_sekolah()
    {
        $logo = get_setting('logo_sekolah');
        if ($logo && file_exists(FCPATH . 'uploads/logo/' . $logo)) {
            return base_url('uploads/logo/' . $logo);
        }
        return base_url('assets/img/default-logo.png');
    }
}

if (!function_exists('get_background_sistem')) {
    /**
     * Get system background URL
     * 
     * @return string
     */
    function get_background_sistem()
    {
        $background = get_setting('background_sistem');
        if ($background && file_exists(FCPATH . 'uploads/background/' . $background)) {
            return base_url('uploads/background/' . $background);
        }
        return base_url('assets/img/default-background.jpg');
    }
}

if (!function_exists('get_tahun_ajaran')) {
    /**
     * Get current academic year
     * 
     * @return string
     */
    function get_tahun_ajaran()
    {
        return get_setting('tahun_ajaran', '2024/2025');
    }
} 