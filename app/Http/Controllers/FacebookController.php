<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class FacebookController extends Controller
{
    public function getFacebookPosts()
    {
        dd('hai');
        $appId = config('services.facebook.app_id');
        $appSecret = config('services.facebook.app_secret');

        // Inisialisasi HTTP client Guzzle
        $client = new Client();

        // Lakukan permintaan untuk mendapatkan token akses dari Facebook
        $tokenResponse = $client->get("https://graph.facebook.com/oauth/access_token?client_id={$appId}&client_secret={$appSecret}&grant_type=client_credentials");

        // Ambil token akses dari respons
        $accessToken = json_decode($tokenResponse->getBody())->access_token;

        // Gunakan token akses untuk mengambil postingan dari halaman Facebook
        $pageId = 'your_page_id'; // Ganti dengan ID halaman Facebook Anda
        $postResponse = $client->get("https://graph.facebook.com/v12.0/{$pageId}/posts?access_token={$accessToken}");

        $posts = json_decode($postResponse->getBody())->data;

        return view('facebook-posts', ['posts' => $posts]);
    }
}
