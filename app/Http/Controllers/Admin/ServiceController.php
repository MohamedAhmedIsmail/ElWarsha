<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RecordStatus;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $services = Service::query()
            ->with('category')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.create', $this->formData(new Service()));
    }

    public function store(Request $request): RedirectResponse
    {
        Service::query()->create($this->data($request));

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }

    public function edit(Service $service): View
    {
        return view('admin.services.edit', $this->formData($service));
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $service->update($this->data($request, $service));

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Service $service): array
    {
        return [
            'service' => $service,
            'categories' => ServiceCategory::query()->orderBy('sort_order')->orderBy('name')->get(),
            'statuses' => RecordStatus::cases(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function data(Request $request, ?Service $service = null): array
    {
        $data = $request->validate([
            'service_category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('services', 'slug')->ignore($service?->id)],
            'description' => ['nullable', 'string'],
            'status' => ['required', new Enum(RecordStatus::class)],
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        return $data;
    }
}
