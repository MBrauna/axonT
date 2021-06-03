<?php

    namespace App\Http\Controllers\API\Util;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Auth;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\UsuarioConfig;
    use App\Models\Perfil;

    class Company extends Controller {
        public function getCompany(Request $request) {
            try {
                return response()->json(getCompanyPermission(), 200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json([], 500);
            } // catch(Exception $error) { ... }
        } // public function filter(Request $request) { ... }
    } // class Company extends Controller { ... }
