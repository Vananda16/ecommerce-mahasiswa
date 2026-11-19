<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::with('author')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());

        Announcement::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'is_active' => $request->boolean('is_active'),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Pengumuman dipublikasikan.');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $announcement->update([
            'title' => $data['title'],
            'body' => $data['body'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Pengumuman diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
