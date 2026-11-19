<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Pengaduan dari penjual / pembeli kepada admin.
 */
class ComplaintController extends Controller
{
    public function index(Request $request): View
    {
        return view('complaints.index', [
            'reports' => $request->user()->reports()->with(['reportedUser', 'product'])->latest()->paginate(10),
            'product' => $request->filled('product') ? Product::find($request->input('product')) : null,
            'reportedUser' => $request->filled('user') ? User::find($request->input('user')) : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $me = $request->user();

        $data = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:2000'],
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'reported_user_id' => ['nullable', 'integer', 'exists:users,id', 'different:'.$me->id],
        ]);

        Report::create([
            'reporter_id' => $me->id,
            'reported_user_id' => $data['reported_user_id'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'subject' => $data['subject'],
            'description' => $data['description'],
            'status' => Report::OPEN,
        ]);

        return redirect()->route('complaints.index')
            ->with('success', 'Pengaduan terkirim. Admin akan segera menindaklanjuti.');
    }
}
