<?php

    namespace App\Http\Controllers\Page\Performance;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use Carbon\Carbon;
    use DB;
    use App\Models\Chamado;
    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\OauthAccessTokens;
    use App\Models\User;

    class Graph extends Controller
    {
        public function startPage(Request $request) {
            try {
                $user   =   User::find(Auth::user()->id);

                $others =   OauthAccessTokens::where('user_id', Auth::user()->id)
                ->where(function($query){
                    $query->orWhere('expires_at','>=',Carbon::now());
                    $query->orWhere('revoked',true);
                }) // ->where(function($query){ ... })
                ->delete();

                $oauthAccess    =   OauthAccessTokens::where('user_id', Auth::user()->id)->count();

                if($oauthAccess <= 0) {
                    User::find(Auth::user()->id)
                    ->update([
                        'accessToken' => null,
                    ]);
                    $user   =   User::find(Auth::user()->id);
                } // if($oauthAccess <= 0) { ... }
                
                if(is_null($user->accessToken)) {
                    $token = $user->createToken('axonT')->accessToken;

                    User::find(Auth::user()->id)
                    ->update([
                        'accessToken'   =>  $token,
                    ]);
                }

                return view('page.performance.graph',[]);
            }
            catch(Exception $error) {
                return view('page.errorPage');
            }
        } // public function startPage(Request $request) { ... }
    }
