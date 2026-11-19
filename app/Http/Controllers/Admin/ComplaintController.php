<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Kelola laporan / pengaduan dari pengguna.
 */
class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        $reports = Report::query()
            ->with(['reporter', 'reportedUser', 'product'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.complaints.index', ['reports' => $reports]);
    }

    public function show(Report $complaint): View
    {
        $complaint->load(['reporter', 'reportedUser', 'product.store', 'handler']);

        return view('admin.complaints.show', ['report' => $complaint]);
    }

    public function update(Request $request, Report $complaint): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Report::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $complaint->update($data + ['handled_by' => $request->user()->id]);

        return back()->with('success', 'Pengaduan diperbarui.');
    }
}
