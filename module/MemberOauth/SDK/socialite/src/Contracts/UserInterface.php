<?php

namespace Overtrue\Socialite\Contracts;

interface UserInterface
{
    public function getId();

    public function getNickname();

    public function getName();

    public function getEmail();

    public function getAvatar();

    public function getAccessToken();

    public function getRefreshToken();

    public function getExpiresIn();
}
