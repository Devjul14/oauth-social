<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function myposts()
    {
        $appId = config('services.facebook.client_id');
        $appSecret = config('services.facebook.client_secret');
        // dd($appId, $appSecret);
        // Inisialisasi HTTP client Guzzle
        $client = new Client();
        $tokenResponse = $client->get("https://graph.facebook.com/oauth/access_token?client_id={$appId}&client_secret={$appSecret}&grant_type=client_credentials");
        $accessToken = json_decode($tokenResponse->getBody())->access_token;

        $pageId = '100083551549379'; // Ganti dengan ID halaman Facebook Anda
        $postResponse = $client->get("https://graph.facebook.com/v12.0/{$pageId}/posts?access_token={$accessToken}");

        $posts = json_decode($postResponse->getBody())->data;
        dd($postResponse);

        return view('facebook-posts', ['posts' => $posts]);
    }
}
