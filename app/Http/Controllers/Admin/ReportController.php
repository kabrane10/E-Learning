<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function exportPDF()
    {
        return back()->with('success', 'Export PDF en cours...');
    }

    public function exportExcel()
    {
        return back()->with('success', 'Export Excel en cours...');
    }
}