<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
            'filters' => $request->only(['search', 'role', 'status']),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User(),
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['password'] = Hash::make($data['password']);

        User::query()->create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        $user->load([
            'vehicles.brand',
            'vehicles.model',
            'bookings.workshop',
            'diagnoses.affectedCategory',
            'sosRequests.serviceType',
            'reviews.workshop',
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validated($request, $user);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?User $user = null): array
    {
        $userId = $user?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone,' . $userId],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'role' => ['required', new Enum(UserRole::class)],
            'city' => ['nullable', 'string', 'max:100'],
            'area' => ['nullable', 'string', 'max:100'],
            'status' => ['required', new Enum(UserStatus::class)],
        ]);
    }
}
