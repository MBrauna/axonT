<?php

    namespace App\Http\Controllers\Page\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use DB;
    use Carbon\Carbon;

    use App\Models\Chamado;

    class ShowTask extends Controller {
        public function getID(Request $request) {
            if(is_null($request->idTask) || !is_numeric($request->idTask)) {
                return redirect()->route('task.list');
            } // if(is_null($request->idTask) || !is_numeric($request->idTask)) { ... }

            $listPermission =   [];

            foreach (getAccess() as $keyAccess => $valueAccess) {
                foreach ($valueAccess->proccessData as $keyProccess => $valueProccess) {
                    if(!in_array($valueProccess->id_processo, $listPermission)) {
                        array_push($listPermission,$valueProccess->id_processo);
                    } // if(!in_array($valueProccess, $listPermission)) { ... }
                } // foreach ($valueAccess->proccessData as $keyProccess => $valueProccess) { ... }
            } // foreach (getAccess() as $keyAccess => $valueAccess) { ... }

            $chamado = Chamado::where('id_chamado',intval($request->idTask))->first();

            if(is_null($chamado) || !isset($chamado->id_chamado) || !in_array($chamado->id_processo, $listPermission)) {
                return redirect()->route('task.list');
            } // if(is_null($chamado) || !isset($chamado->id_chamado) || !in_array($chamado->id_processo, $listPermission)) { ... }

             return view('page.solicitation.task',[
                'idTask'    =>  intval($request->idTask),
             ]);
        } // public function getID(Request $request) { ... }
    } // class ShowTask extends Controller { ... }
