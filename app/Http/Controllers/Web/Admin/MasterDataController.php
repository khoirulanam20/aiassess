<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Web\BaseController;
use Illuminate\View\View;

class MasterDataController extends BaseController
{
    public function index(): View
    {
        $this->authorize('viewAny', \App\Models\Department::class);

        return view('admin.master-data.index');
    }
}
