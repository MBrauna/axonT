<?php

    namespace App\Http\Controllers\API\Auth;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Validator;
    use Hash;
    use Carbon\Carbon;

    use App\Models\Users;

    class Login extends Controller
    {
        public function verifyAccess(Request $request) {
            try {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|string',
                    'password' => 'required|string',
                ]);

                if($validator->fails()) {
                    $vReturn    =   [
                        'success'   =>  false,
                        'message'   =>  'Dados necessários não foram preenchidos! Verifique.'.$request->email.'  ---  '.$request->password,
                    ];
    
                    return response()->json($vReturn,200);
                } // if($validator->fails()) { ... }

                $exists =   Users::where('email',$request->email)->count();

                if($exists <= 0) {
                    $vReturn    =   [
                        'success'   =>  false,
                        'message'   =>  'Usuário não identificado! Verifique.',
                    ];
    
                    return response()->json($vReturn,200);
                } // if($exists <= 0) { ... }

                $user   =   Users::where('email',$request->email)->first();

                if(Hash::check($request->password, $user->password)) {
                    Users::where('email',$request->email)
                    ->update([
                        'last_login'    =>  Carbon::now(),
                    ]);

                    $vReturn    =   [
                        'success'   =>  true,
                        'message'   =>  'Login válido! Verifique.',
                    ];
    
                    return response()->json($vReturn,200);
                } // if(Hash::check($request->password, $user->password)) { ... }
                else {
                    $vReturn    =   [
                        'success'   =>  false,
                        'message'   =>  'Senha incorreta! Verifique.',
                    ];
    
                    return response()->json($vReturn,200);
                }


            } // try { ... }
            catch(Exception $error) {
                $vReturn    =   [
                    'success'   =>  false,
                    'message'   =>  'Não foi possível validar os dados de login! Verifique.',
                ];

                return response()->json($vReturn,200);
            } // catch(Exception $error) { ... }
        }
    }
