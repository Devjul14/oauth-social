<?php

namespace App\Http\Controllers;

use Facebook\Facebook;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;



class FacebookController extends Controller
{
    public function getFacebookPosts()
    {
        // dd('hai');

        try {
            $appId = config('services.facebook.client_id');
            $appSecret = config('services.facebook.client_secret');
            $urlRedirect = config('services.facebook.redirect');
            $defaultSdk = config('services.facebook.default_graph_version');
            // dd($appId);
            $fb = new Facebook([
                'app_id' => $appId,
                'app_secret' => $appSecret,
                'default_graph_version' => $defaultSdk,
            ]);

            $helper = $fb->getRedirectLoginHelper();

            // Jika belum terotentikasi, arahkan pengguna ke halaman otorisasi
            if (!isset($_GET['code'])) {
                $loginUrl = $helper->getLoginUrl(route('user_posts'), ['email', 'user_posts']);

                // dd($loginUrl);
            }

            // Jika pengguna sudah terotentikasi, ambil access token
            $accessToken = $helper->getAccessToken();
            // dd($accessToken);
            // Pastikan access token ada sebelum melanjutkan
            if (!isset($accessToken)) {
                // Handle error jika access token tidak ditemukan
                echo "Gagal mendapatkan access token";
                exit;
            }

            $response = $fb->get('/me/posts', $accessToken);

            $userPosts = $response->getGraphEdge();

            return view('facebook-posts', ['userPosts' => $userPosts]);
        } catch (FacebookResponseException $e) {
            // Tangani kesalahan respons dari Facebook
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            // Tangani kesalahan SDK Facebook
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

    
        
    }

}
