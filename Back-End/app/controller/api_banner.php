<?php

function main_api_banner(): string
{
    $banner = api_remote_json_get('http://playground.burotix.be/adv/banner_for_isfce.json');
    if ($banner === null) {
        return api_json_response(['success' => false, 'error' => 'Impossible de charger la bannière'], 502);
    }

    return api_json_response([
        'success' => true,
        'banner' => api_normalize_banner_payload($banner),
        'banner_raw' => $banner,
    ]);
}
