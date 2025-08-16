<?php

if (!function_exists('setup')) {
    function setup($key, $default = null)
    {
        return \App\Models\Setting::getByKey($key, $default);
    }

}
if (!function_exists('getImage')) {
    function getImage($model, $collection = 'profile', $conversion = 'thumb', $defaultUrl = 'https://placehold.co/400')
    {
        $placeholderUrl = setup('placeHolder') != '' ? setup('placeHolder') : $defaultUrl;

        return $model->getFirstMediaUrl($collection, $conversion)
            ?: $placeholderUrl;
    }
}
if (!function_exists('getUserProfileImage')) {
    function getUserProfileImage($user, $collection = 'profile', $conversion = 'thumb')
    {
        return $user->getFirstMediaUrl($collection, $conversion)
            ?: 'https://ui-avatars.com/api/?name=' . urlencode($user->name);
    }
}
if (!function_exists('getSettingImage')) {
    function getSettingImage($key = 'iconImage', $collection = 'icon', $conversion = 'thumb', $defaultUrl = 'https://placehold.co/400')
    {
        // Use a static variable to store settings to prevent duplicate queries
        static $settings = [];

        // Check if the setting is already retrieved in this request
        if (!array_key_exists($key, $settings)) {
            $settings[$key] = \App\Models\Setting::where('key', $key)->first();
        }

        // Return the image URL or the default placeholder
        return $settings[$key]?->getFirstMediaUrl($collection, $conversion) ?? setup('placeHolder', $defaultUrl);
    }
}

if (!function_exists('getErrorImage')) {
    function getErrorImage($defaultUrl = 'https://placehold.co/400')
    {
        $placeholderUrl = setup('placeHolder') != '' ? setup('placeHolder') : $defaultUrl;
        return "this.onerror=null; this.src='{$placeholderUrl}';";

    }
}
if (!function_exists('getWebErrorImage')) {
    function getWebErrorImage($defaultUrl = 'https://placehold.co/400')
    {
        $placeholderUrl = setup('placeHolder') != '' ? setup('placeHolder') : $defaultUrl;
        return "this.onerror=null; this.src='{$placeholderUrl}';";

    }
}
if (!function_exists('getErrorProfile')) {

    function getErrorProfile($user, $defaultUrl = 'https://placehold.co/400')
    {
            $placeholderUrl = 'https://ui-avatars.com/api/?name=' . urlencode($user->name);
            return "this.onerror=null; this.src='{$placeholderUrl}';";

    }
}
