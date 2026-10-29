<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BranchSelectionController extends Controller
{
    public function index()
    {

        $branches = Branch::all();

        return view('branch-select/branch-selection', compact('branches'));
    }

    public function select(Branch $branch): RedirectResponse
    {
        session(['branch_id' => $branch->id]);

        return redirect('/admin');
    }

}