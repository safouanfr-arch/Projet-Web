<?php

function main_api_banner(): string
{
    $url = (string)getenv('PRESS_BANNER_URL');
    if (parse_url($url, PHP_URL_SCHEME) !== 'https') {
        return api_json_response(['success' => true, 'banner' => null]);
    }

    $banner = api_remote_json_get($url);
    if ($banner === null) {
        return api_json_response(['success' => false, 'error' => 'Impossible de charger la bannière'], 502);
    }

    return api_json_response([
        'success' => true,
        'banner' => api_normalize_banner_payload($banner),
        'banner_raw' => $banner,
    ]);
}
