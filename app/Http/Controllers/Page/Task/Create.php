<?php

    namespace App\Http\Controllers\Page\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Auth;

    class Create extends Controller
    {
        public function startPage(Request $request) {
            try {

                return view('page.task.create',[
                    
                ]);
            }
            catch(Exception $error) {
                return view('page.errorPage');
            }
        }
    }
