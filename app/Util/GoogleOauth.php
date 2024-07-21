<?php
namespace App\Util;

const oauthCheckUrl = "https://oauth2.googleapis.com/tokeninfo?id_token=";

class GoogleOauth {
    function get_google_check_url($token) {
        return oauthCheckUrl . $token;
    }
}