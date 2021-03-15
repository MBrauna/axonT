<?php

    namespace App\Http\Controllers\API\Tasks;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\User;
    use App\Models\TipoProcesso;
    use App\Models\Questao;


    class Search extends Controller {
        public function verifyPermissionTaskAutomatic(Request $request) {
            try {
                $userPerm   =   json_decode(Permission()->verifyPerm($request));

                dd($userPerm);

                return response()->json($userPerm,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json([],200);
            } // catch(Exception $error) { ... }
        } // public function verifyPermissionTaskAutomatic(Request $request) { ... }
    } // class Search extends Controller { ... }
