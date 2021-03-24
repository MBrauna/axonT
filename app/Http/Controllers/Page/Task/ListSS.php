<?php

    namespace App\Http\Controllers\Page\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;

    use App\Models\Empresa;
    use App\Models\Processo;

    class ListSS extends Controller
    {
        public function listPage(Request $request) {
            return view('page.solicitation.list');
        } // public function listPage(Request $request) { ... }

        public function listAutomatic(Request $request) {

            $counter    =   0;

            // Verifica se é administrador
            if(Auth::user()->administrador) {
                $counter += 1;
            } // if(Auth::user()->administrador) { ... }

            $counter    +=  Empresa::where('situacao',true)
                            ->where('id_usuario_responsavel',Auth::user()->id)
                            ->count();

            $counter    +=  Processo::where('situacao',true)
                            ->where('id_usuario_responsavel',Auth::user()->id)
                            ->count();

            if($counter <= 0) {
                return redirect()->route('raiz');
            } // if($counter <= 0) { ... }
            else {
                return view('page.solicitation.listAutomatic');
            } // else { ... }

        } // public function listAutomatic(Request $request) { ... }
    }
