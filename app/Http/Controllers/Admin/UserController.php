<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Kelola data mahasiswa (penjual & pembeli).
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->whereIn('role', [User::SELLER, User::BUYER])
            ->when($request->filled('q'), function ($query) use ($request) {
                $s = $request->input('q');
                $query->where(fn ($w) => $w->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('nim', 'like', "%{$s}%"));
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->input('role')))
            ->when($request->input('status') === 'blocked', fn ($q) => $q->where('is_blocked', true))
            ->when($request->input('status') === 'active', fn ($q) => $q->where('is_blocked', false))
            ->with('store')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['role' => User::BUYER])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules() + [
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = new User($data);
        $user->role = $data['role'];
        $user->save();

        $this->ensureStore($user);

        return redirect()->route('admin.users.index')->with('success', 'Data mahasiswa ditambahkan.');
    }

    public function edit(User $user): View
    {
        $this->ensureMahasiswa($user);

        return view('admin.users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureMahasiswa($user);

        $data = $request->validate($this->rules($user) + [
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->fill($data);
        $user->role = $data['role'];
        $user->save();

        $this->ensureStore($user);

        return redirect()->route('admin.users.index')->with('success', 'Data mahasiswa diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->ensureMahasiswa($user);

        // Bersihkan file milik penjual sebelum akun dihapus
        if ($user->store) {
            if ($user->store->logo) {
                Storage::disk('public')->delete($user->store->logo);
            }
            foreach ($user->store->products as $product) {
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
            }
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Akun dihapus.');
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        $this->ensureMahasiswa($user);

        $data = $request->validate([
            'blocked_reason' => ['required', 'string', 'max:255'],
        ]);

        $user->is_blocked = true;
        $user->blocked_reason = $data['blocked_reason'];
        $user->save();

        return back()->with('success', "Akun {$user->name} diblokir.");
    }

    public function unblock(User $user): RedirectResponse
    {
        $this->ensureMahasiswa($user);

        $user->is_blocked = false;
        $user->blocked_reason = null;
        $user->save();

        return back()->with('success', "Blokir akun {$user->name} dibuka.");
    }

    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user?->id)],
            'nim' => ['nullable', 'string', 'max:30'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'role' => ['required', Rule::in([User::SELLER, User::BUYER])],
        ];
    }

    /** Admin tidak dikelola lewat halaman ini. */
    private function ensureMahasiswa(User $user): void
    {
        abort_if($user->isAdmin(), 403, 'Akun admin tidak dapat dikelola di sini.');
    }

    /** Penjual wajib punya toko. */
    private function ensureStore(User $user): void
    {
        if ($user->isSeller() && ! $user->store()->exists()) {
            $user->store()->create(['name' => 'Toko '.$user->name]);
        }
    }
}
