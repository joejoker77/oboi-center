<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FileManagerController extends Controller
{
    public function index() :View
    {
        return view('vendor.file-manager.ckeditor-inner');
    }
}
