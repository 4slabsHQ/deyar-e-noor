<?php

namespace App\Http\Responses;

use App\Support\HomeRoute;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $home = HomeRoute::for($request->user());

        return $request->wantsJson()
            ? new JsonResponse(['two_factor' => false, 'redirect' => $home], 200)
            : redirect()->intended($home);
    }
}
