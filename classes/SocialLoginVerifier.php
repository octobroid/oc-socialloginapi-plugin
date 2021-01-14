<?php namespace Octobro\SocialLoginAPI\Classes;

use Auth;
class SocialLoginVerifier
{
    public function verifyFacebook($user_id, $access_token)
    {
        try {
            
            $fields = 'id,name,first_name,last_name,link,website,gender,locale,about,email,hometown,location,birthday,picture';

            $client = new \GuzzleHttp\Client();
            $res = $client->get('https://graph.facebook.com/me', [
                'query' => compact('fields', 'user_id', 'access_token'),
            ]);

            $userProfile = json_decode($res->getBody());

            $photoURL = isset($userProfile->photo) && $userProfile->photo ? $userProfile->photo->data->url : null;

            $user = \Flynsarmy\SocialLogin\Classes\UserManager::instance()->find(
                [
                    'provider_id'    => 'Facebook',
                    'provider_token' => $userProfile->id,
                ], [
                    'token'           => $userProfile->id,
                    'email'           => $userProfile->email,
                    'username'        => $userProfile->email,
                    'name'            => $userProfile->name,
                    'gender'          => isset($userProfile->gender) ? $userProfile->gender : null,
                    'dob'             => isset($userProfile->birthday) ? $userProfile->birthday : null,
                    'avatar_original' => $photoURL,
                ]
            );

            return $user->id;
        }
        catch (\Exception $e) {
            throw $e;
            return false;
        }
    }

    public function verifyGoogle($email, $access_token)
    {
        try {
            $client = new \GuzzleHttp\Client();
            $res = $client->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                'query' => compact('access_token'),
            ]);

            $userProfile = json_decode($res->getBody());
            $photoURL = isset($userProfile->picture) && $userProfile->picture ? $userProfile->picture : null;
            $user = \Flynsarmy\SocialLogin\Classes\UserManager::instance()->find(
                [
                    'provider_id'    => 'Google',
                    'provider_token' => $userProfile->sub,
                ],
                [
                    'token'           => $userProfile->sub,
                    'email'           => $email,
                    'username'        => $email,
                    'name'            => $userProfile->name,
                    'avatar_original' => $photoURL,
                ]
            );

            return $user->id;
        } catch (\Exception $e) {
            throw $e;
            return false;
        }
    }

    public function verifyApple($email, $access_token)
    {
        try {
            if(env('Firebase_Login') != null){
                $client = new \GuzzleHttp\Client();
                $url = 'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . env('Firebase_Login');
    
                $res = $client->post($url, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode([
                        'idToken' => $access_token,
                    ])
                ]);
                
                $userProfile = json_decode($res->getBody())->users[0];
    
                $user = \Flynsarmy\SocialLogin\Classes\UserManager::instance()->find(
                    [
                        'provider_id'    => 'Apple',
                        'provider_token' => $userProfile->localId,
                    ],
                    [
                        'token'           => $userProfile->localId,
                        'email'           => $userProfile->email,
                        'username'        => $userProfile->email,
                        'name'            => $userProfile->displayName,
                    ]
                );
                return $user->id;
            }

        } catch (\Exception $e) {
            throw $e;
            return false;
        }
    }

    public function verifyPhone($email, $access_token)
    {
        try {
            if(env('Firebase_Login') != null){
                $client = new \GuzzleHttp\Client();
                $url = 'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . env('Firebase_Login');
    
                $res = $client->post($url, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode([
                        'idToken' => $access_token,
                    ])
                ]);
                
                $userProfile = json_decode($res->getBody())->users[0];
    
                $user = \Flynsarmy\SocialLogin\Classes\UserManager::instance()->find(
                    [
                        'provider_id'    => 'Phone',
                        'provider_token' => $userProfile->localId,
                    ],
                    [
                        'token'           => $userProfile->localId,
                        'email'           => $userProfile->email,
                        'username'        => $userProfile->email,
                        'name'            => $userProfile->displayName
                    ]
                );
                return $user->id;
            }

        } catch (\Exception $e) {
            throw $e;
            return false;
        }
    }
}