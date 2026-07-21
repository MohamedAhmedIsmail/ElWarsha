<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RecordStatus;
use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = ServiceCategory::query()
            ->withCount('services')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%' . $request->string('search') . '%'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.service-categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.service-categories.create', ['category' => new ServiceCategory(), 'statuses' => RecordStatus::cases()]);
    }

    public function store(Request $request): RedirectResponse
    {
        ServiceCategory::query()->create($this->data($request));

        return redirect()->route('admin.service-categories.index')->with('success', 'Service category created successfully.');
    }

    public function edit(ServiceCategory $category): View
    {
        return view('admin.service-categories.edit', ['category' => $category, 'statuses' => RecordStatus::cases()]);
    }

    public function update(Request $request, ServiceCategory $category): RedirectResponse
    {
        $category->update($this->data($request, $category));

        return redirect()->route('admin.service-categories.index')->with('success', 'Service category updated successfully.');
    }

    public function destroy(ServiceCategory $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.service-categories.index')->with('success', 'Service category deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function data(Request $request, ?ServiceCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('service_categories', 'slug')->ignore($category?->id)],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', new Enum(RecordStatus::class)],
        ]);
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        return $data;
    }
}
