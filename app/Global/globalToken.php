<?php
    if(!function_exists('generateTokenOaut')){
        function generateTokenOaut(){
            try {
                $user   =   App\Models\User::find(Auth::user()->id);

                $others =   App\Models\OauthAccessTokens::where('user_id', Auth::user()->id)
                ->where(function($query){
                    $query->orWhere('expires_at','<=',Carbon\Carbon::now());
                    $query->orWhere('revoked',true);
                }) // ->where(function($query){ ... })
                ->get();

                $oauthAccess    =   App\Models\OauthAccessTokens::where('user_id', Auth::user()->id)->count();

                if($oauthAccess <= 0) {
                    $user->token    =   null;
                    $user->save();
                } // if($oauthAccess <= 0) { ... }
                
                if(is_null($user->token)) {
                    
                    $token = $user->createToken('axonT')->accessToken;

                    $user->token    =   $token;
                    $user->save();
                }

                return true;
            }
            catch(Exception $error) {
                return false;
            }
        }
    }