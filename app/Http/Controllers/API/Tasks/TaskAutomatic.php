<?php

    namespace App\Http\Controllers\API\Tasks;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;

    use App\Models\Empresa;
    use App\Models\Processo;
    use App\Models\TipoProcesso;
    use App\Models\Questao;

    class TaskAutomatic extends Controller
    {
        public function verifyTaskAutomatic(Request $request) {
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
            
            return response()->json([
                'verificador'   =>  (($counter <= 0) ? false : true)
            ],200);

        } // public function verifyTaskAutomatic(Request $request) { ... }

        public function listData(Request $request) {
            try {
                $return     =   [];
                $tmpData    =   getCompanyPermission();

                foreach ($tmpData as $keyEmpresa => $valueEmpresa) {
                    // Se o usuário tem acesso a uma empresa, então poderá abrir a todos os processos existentes.
                } // foreach ($tmpData as $keyEmpresa => $valueEmpresa) { ... }
            } // try { ... }
            catch(Exception $error) {
                return response()->json([],200);
            } // catch(Exception $error) { ... }
        } // public function listData(Request $request) { ... }
    }
