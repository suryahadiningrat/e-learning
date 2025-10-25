<?php

use Config\MenuConfig;

if (!function_exists('render_menu')) {
    /**
     * Render menu based on user role
     */
    function render_menu($role)
    {
        $menus = MenuConfig::getMenuByRole($role);
        $html = '';

        foreach ($menus as $menu) {
            if (isset($menu['type']) && $menu['type'] === 'header') {
                $html .= '<li class="nav-item">';
                $html .= '<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-white-50">';
                $html .= '<span>' . $menu['title'] . '</span>';
                $html .= '</h6>';
                $html .= '</li>';
            } else {
                $isActive = (strpos(current_url(), $menu['active_pattern']) !== false) ? 'active' : '';
                $html .= '<li class="nav-item">';
                $html .= '<a class="nav-link ' . $isActive . '" href="' . base_url($menu['url']) . '">';
                $html .= '<i class="' . $menu['icon'] . '"></i> ' . $menu['title'];
                $html .= '</a>';
                $html .= '</li>';
            }
        }

        return $html;
    }
}

if (!function_exists('get_sidebar_color')) {
    /**
     * Get sidebar color based on user role
     */
    function get_sidebar_color($role)
    {
        return MenuConfig::getSidebarColorByRole($role);
    }
}

if (!function_exists('get_panel_title')) {
    /**
     * Get panel title based on user role
     */
    function get_panel_title($role)
    {
        return MenuConfig::getPanelTitleByRole($role);
    }
}

if (!function_exists('check_menu_access')) {
    /**
     * Check if user has access to specific menu
     */
    function check_menu_access($role, $url)
    {
        $menus = MenuConfig::getMenuByRole($role);
        
        foreach ($menus as $menu) {
            if (isset($menu['url']) && $menu['url'] === $url) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('get_breadcrumb')) {
    /**
     * Generate breadcrumb based on current URL and role
     */
    function get_breadcrumb($role)
    {
        $menus = MenuConfig::getMenuByRole($role);
        $currentUrl = current_url();
        $breadcrumb = [];
        
        // Add home/dashboard
        $breadcrumb[] = [
            'title' => 'Dashboard',
            'url' => base_url($role . '/dashboard'),
            'active' => false
        ];
        
        // Find current menu
        foreach ($menus as $menu) {
            if (isset($menu['url']) && strpos($currentUrl, $menu['url']) !== false) {
                $breadcrumb[] = [
                    'title' => $menu['title'],
                    'url' => base_url($menu['url']),
                    'active' => true
                ];
                break;
            }
        }
        
        return $breadcrumb;
    }
}