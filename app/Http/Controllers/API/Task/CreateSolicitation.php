<?php

    namespace App\Http\Controllers\API\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class CreateSolicitation extends Controller {
        public function setData(Request $request) {
            try {
                return response()->json($request,200);
            } // try { ... }
            catch(Exception $error) {
                return response()->json($request,200);
            } // catch(Exception $error) { ... }
        } // public function setData(Request $request) { ... }
    } // class CreateSolicitation extends Controller { ... }
