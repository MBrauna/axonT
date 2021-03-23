<?php

    namespace App\Http\Controllers\Page\Task;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;

    class ListSS extends Controller
    {
        public function listPage(Request $request) {
            return view('page.solicitation.list');
        }
    }
