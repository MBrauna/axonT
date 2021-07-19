<?php

    namespace App\Http\Controllers\Page\Admin;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    use Auth;
    use Carbon\Carbon;

    class Company extends Controller
    {
        public function pageCompany(Request $request) {
            try {
                return view('page.admin.company');
            } // try { ... }
            catch(Exception $error) {
                return redirect()->route('performance.graph');
            } // catch(Exception $error) { ... }
        } // public function pageCompany(Request $request) { ... }
    }
