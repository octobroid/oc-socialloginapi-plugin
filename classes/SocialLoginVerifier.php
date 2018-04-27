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
}