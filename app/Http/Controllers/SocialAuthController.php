<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Socialite;
use App\Profile;


class SocialAuthController extends Controller
{
    public function getSocialRedirect($account)
    {
        try {
            //return Socialite::with($account)->redirect();
            return Socialite::driver('facebook')->fields([
                'first_name', 'last_name', 'email', 'gender', 'birthday', 'location'
            ])->scopes([
                'email', 'user_birthday', 'user_location'
            ])->redirect();

        } catch (\InvalidArgumentException $e) {
            return redirect('/login');
        }
    }

    public function getSocialCallback($account)
    {
      /*
        Grabs the user who authenticated via social account.
         */

        //$socialUser = Socialite::with($account)->user();

        $socialUser = Socialite::driver('facebook')->fields([
            'first_name', 'last_name', 'email', 'gender', 'birthday', 'location'
        ])->user();

        dd($socialUser);

      /*
            Gets the user in our database where the provider ID
            returned matches a user we have stored.
         */
        $user = User::where('provider_id', '=', $socialUser->id)
            ->where('provider', '=', $account)
            ->first();

      /*
        Checks to see if a user exists. If not we need to create the
        user in the database before logging them in.
         */
        if ($user == null) {

            $newUser = new User();

            $newUser->name = $socialUser->getName() ? $socialUser->getName() : $socialDetailsUser['first_name'] . ' ' . $socialDetailsUser['last_name'];
            $newUser->email = $socialUser->getEmail() == '' ? '' : $socialUser->getEmail();
            $newUser->avatar = $socialUser->getAvatar();
            $newUser->password = '';
            $newUser->provider = $account;
            $newUser->provider_id = $socialUser->getId();

            $newUser->save();

            //save profile
            $profile = Profile::create([
                'user_id' => $newUser->id,
                'gender' => $socialDetailsUser['gender'] ? $socialDetailsUser['gender'] : '',
                'birthday' => $socialDetailsUser['birthday'],
                'location' => $socialDetailsUser['location']['name']
            ]);
            $newUser->profile()->save($profile);

            $user = $newUser;
        }

      /*
        Log in the user
         */
        Auth::login($user);

      /*
        Redirect to the app
         */
        return redirect('/');
    }

}
