<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Socialite;
use App\Profile;
use App\User;
use Illuminate\Support\Facades\DB;


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

        $socialDetailsUser = $socialUser->user;
        //dd($socialDetailsUser);
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
            //$newUser->save();

            
            //save profile
            $profile = new Profile();
            $profile->gender = isset($socialDetailsUser['gender']) ? $socialDetailsUser['gender'] : '';
            $profile->birthday = DateTime::createFromFormat('m/d/Y', $socialDetailsUser['birthday'])->format('Y-m-d H:i:s');;
            $profile->location = $socialDetailsUser['location']['name'];


            //Transaction
            DB::transaction(function () use ($newUser, $profile) {
                $newUser->save();
                $profile->user_id = $newUser->id;
                User::find($newUser->id)->profile()->save($profile);
            });

            $user = $newUser;
        }

      /*
        Log in the user
         */
        Auth::login($user);

      /*
        Redirect to the app
         */
        return redirect('/home');
    }

}
