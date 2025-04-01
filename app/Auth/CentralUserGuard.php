<?php

namespace App\Auth;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class CentralUserGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $provider;
    protected $user;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $id = $this->request->session()->get($this->getName());

        if ($id) {
            $this->user = $this->provider->retrieveById($id);
        }

        return $this->user;
    }

    public function validate(array $credentials = [])
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user === null) {
            return false;
        }

        if (!$user->isCentralUser()) {
            return false;
        }

        return $this->provider->validateCredentials($user, $credentials);
    }

    public function setUser($user)
    {
        $this->user = $user;
        $this->request->session()->put($this->getName(), $user->getAuthIdentifier());
    }

    public function getName()
    {
        return 'central_user';
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
    }
}