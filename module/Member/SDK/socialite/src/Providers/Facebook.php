<?php

namespace Overtrue\Socialite\Providers;

use Overtrue\Socialite\User;


class Facebook extends Base
{
    const NAME = 'facebook';
    protected $graphUrl = 'https://graph.facebook.com';
    protected $version = 'v3.3';
    protected $fields = ['first_name', 'last_name', 'email', 'gender', 'verified'];
    protected $scopes = ['email'];
    protected $popup = false;

    protected function getAuthUrl()
    {
        return $this->buildAuthUrlFromBase('https://www.facebook.com/' . $this->version . '/dialog/oauth');
    }

    protected function getTokenUrl()
    {
        return $this->graphUrl . '/oauth/access_token';
    }

    
    public function tokenFromCode($code)
    {
        $response = $this->getHttpClient()->get(
            $this->getTokenUrl(),
            [
                'query' => $this->getTokenFields($code),
            ]
        );

        return $this->normalizeAccessTokenResponse($response->getBody());
    }

    
    protected function getUserByToken($token, $query = [])
    {
        $appSecretProof = hash_hmac('sha256', $token, $this->getConfig()->get('client_secret'));
        $endpoint = $this->graphUrl . '/' . $this->version . '/me?access_token=' . $token . '&appsecret_proof=' . $appSecretProof . '&fields=' .
            implode(',', $this->fields);

        $response = $this->getHttpClient()->get(
            $endpoint,
            [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]
        );

        return \json_decode($response->getBody(), true) ?? [];
    }

    protected function mapUserToObject($user)
    {
        $userId = $user['id'] ?? null;
        $avatarUrl = $this->graphUrl . '/' . $this->version . '/' . $userId . '/picture';

        $firstName = $user['first_name'] ?? null;
        $lastName = $user['last_name'] ?? null;

        return new User(
            [
                'id' => $user['id'] ?? null,
                'nickname' => null,
                'name' => $firstName . ' ' . $lastName,
                'email' => $user['email'] ?? null,
                'avatar' => $userId ? $avatarUrl . '?type=normal' : null,
                'avatar_original' => $userId ? $avatarUrl . '?width=1920' : null,
            ]
        );
    }

    protected function getCodeFields()
    {
        $fields = parent::getCodeFields();

        if ($this->popup) {
            $fields['display'] = 'popup';
        }

        return $fields;
    }

    
    public function fields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    
    public function asPopup()
    {
        $this->popup = true;

        return $this;
    }
}
