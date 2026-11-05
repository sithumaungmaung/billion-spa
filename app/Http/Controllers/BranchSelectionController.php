<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BranchSelectionController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('adminBranches');

        $admin_branches = $user->adminBranches->pluck('id')->toArray();

        // dd($admin_branches->pluck('id'));

        $branches = Branch::all();

        return view('branch-select/branch-selection', compact('branches', 'admin_branches'));
    }

    public function select(Branch $branch): RedirectResponse
    {
        session(['branch_id' => $branch->id]);

        return redirect('/admin');
    }

}