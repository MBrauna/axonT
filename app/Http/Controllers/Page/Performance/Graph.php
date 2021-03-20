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

    class Graph extends Controller {
        public function startPage(Request $request) {
            try {
                return view('page.performance.graph',[]);
            }
            catch(Exception $error) {
                return view('page.errorPage');
            }
        } // public function startPage(Request $request) { ... }
    }
