<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DayOfWeek;
use App\Enums\UserRole;
use App\Enums\WorkshopImageType;
use App\Enums\WorkshopStatus;
use App\Http\Controllers\Controller;
use App\Models\CarBrand;
use App\Models\Service;
use App\Models\User;
use App\Models\Workshop;
use App\Models\WorkshopImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(Request $request): View
    {
        $workshops = Workshop::query()
            ->with('owner:id,name,phone')
            ->withCount(['services', 'brands'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('city'), fn ($q) => $q->where('city', $request->string('city')))
            ->when($request->filled('area'), fn ($q) => $q->where('area', $request->string('area')))
            ->when($request->filled('is_verified'), fn ($q) => $q->where('is_verified', $request->boolean('is_verified')))
            ->when($request->filled('accepts_booking'), fn ($q) => $q->where('accepts_booking', $request->boolean('accepts_booking')))
            ->when($request->filled('accepts_sos'), fn ($q) => $q->where('accepts_sos', $request->boolean('accepts_sos')))
            ->when($request->filled('subscription_status'), fn ($q) => $q->where('subscription_status', $request->string('subscription_status')))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.workshops.index', [
            'workshops' => $workshops,
            'statuses' => WorkshopStatus::cases(),
        ]);
    }

    public function create(): View
    {
        return view('admin.workshops.create', $this->formData(new Workshop()));
    }

    public function store(Request $request): RedirectResponse
    {
        $workshop = Workshop::query()->create($this->data($request));
        $this->syncRelations($request, $workshop);

        return redirect()->route('admin.workshops.show', $workshop)->with('success', 'Workshop created successfully.');
    }

    public function show(Workshop $workshop): View
    {
        $workshop->load(['owner', 'services.category', 'brands', 'workingHours', 'images', 'verifications']);

        return view('admin.workshops.show', compact('workshop'));
    }

    public function edit(Workshop $workshop): View
    {
        $workshop->load(['services', 'brands', 'workingHours', 'images']);

        return view('admin.workshops.edit', $this->formData($workshop));
    }

    public function update(Request $request, Workshop $workshop): RedirectResponse
    {
        $workshop->update($this->data($request));
        $this->syncRelations($request, $workshop);

        return redirect()->route('admin.workshops.show', $workshop)->with('success', 'Workshop updated successfully.');
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        $workshop->delete();

        return redirect()->route('admin.workshops.index')->with('success', 'Workshop deleted successfully.');
    }

    public function approve(Workshop $workshop): RedirectResponse { return $this->status($workshop, WorkshopStatus::Approved, 'Workshop approved.'); }
    public function reject(Workshop $workshop): RedirectResponse { return $this->status($workshop, WorkshopStatus::Rejected, 'Workshop rejected.'); }
    public function suspend(Workshop $workshop): RedirectResponse { return $this->status($workshop, WorkshopStatus::Suspended, 'Workshop suspended.'); }

    public function verify(Workshop $workshop): RedirectResponse
    {
        $workshop->update(['is_verified' => true]);

        return back()->with('success', 'Workshop verified.');
    }

    public function unverify(Workshop $workshop): RedirectResponse
    {
        $workshop->update(['is_verified' => false]);

        return back()->with('success', 'Workshop unverified.');
    }

    public function uploadImage(Request $request, Workshop $workshop): RedirectResponse
    {
        $data = $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'type' => ['required', new Enum(WorkshopImageType::class)],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $workshop->images()->create([
            'image_path' => $request->file('image')->store("workshops/{$workshop->id}", 'public'),
            'type' => $data['type'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return back()->with('success', 'Workshop image uploaded.');
    }

    public function deleteImage(WorkshopImage $image): RedirectResponse
    {
        $image->delete();

        return back()->with('success', 'Workshop image deleted.');
    }

    private function status(Workshop $workshop, WorkshopStatus $status, string $message): RedirectResponse
    {
        $workshop->update(['status' => $status]);

        return back()->with('success', $message);
    }

    private function formData(Workshop $workshop): array
    {
        return [
            'workshop' => $workshop,
            'owners' => User::query()->where('role', UserRole::WorkshopOwner->value)->orderBy('name')->get(),
            'services' => Service::query()->orderBy('name')->get(),
            'brands' => CarBrand::query()->orderBy('name')->get(),
            'statuses' => WorkshopStatus::cases(),
            'days' => DayOfWeek::cases(),
            'imageTypes' => WorkshopImageType::cases(),
        ];
    }

    private function data(Request $request): array
    {
        return $request->validate([
            'owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string'],
            'city' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'google_maps_url' => ['nullable', 'string'],
            'accepts_booking' => ['sometimes', 'boolean'],
            'accepts_sos' => ['sometimes', 'boolean'],
            'is_verified' => ['sometimes', 'boolean'],
            'status' => ['required', new Enum(WorkshopStatus::class)],
            'subscription_status' => ['required', 'in:free,active,expired,cancelled'],
        ]) + ['accepts_booking' => false, 'accepts_sos' => false, 'is_verified' => false];
    }

    private function syncRelations(Request $request, Workshop $workshop): void
    {
        $workshop->services()->sync($request->input('service_ids', []));
        $workshop->brands()->sync($request->input('brand_ids', []));

        foreach ($request->input('hours', []) as $day => $hour) {
            $workshop->workingHours()->updateOrCreate(
                ['day_of_week' => $day],
                [
                    'opens_at' => $hour['opens_at'] ?: null,
                    'closes_at' => $hour['closes_at'] ?: null,
                    'is_closed' => isset($hour['is_closed']),
                ]
            );
        }
    }
}
