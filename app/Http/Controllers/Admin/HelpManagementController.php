<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class HelpManagementController extends Controller
{
    /**
     * Display all help articles.
     */
    public function index(Request $request)
    {
        $query = HelpArticle::query()->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('summary', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where(
                'is_published',
                $request->status === 'published'
            );
        }

        $articles = $query
            ->orderBy('sort_order')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => HelpArticle::count(),
            'published' => HelpArticle::where(
                'is_published',
                true
            )->count(),
            'drafts' => HelpArticle::where(
                'is_published',
                false
            )->count(),
            'faqs' => HelpArticle::where(
                'type',
                'faq'
            )->count(),
        ];

        return view(
            'super-admin.help-management.index',
            compact('articles', 'stats')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('super-admin.help-management.create');
    }

    /**
     * Store a new help article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['article', 'faq'])],
            'summary' => ['nullable', 'string', 'max:2000'],
            'content' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $this->generateUniqueSlug(
            $validated['title']
        );

        $validated['is_published'] = $request->boolean(
            'is_published'
        );

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        HelpArticle::create($validated);

        return redirect()
            ->route('dashboard.help-management.index')
            ->with('success', 'হেল্প কনটেন্ট সফলভাবে তৈরি হয়েছে।');
    }

    /**
     * Show edit form.
     */
    public function edit(HelpArticle $helpArticle)
    {
        return view(
            'super-admin.help-management.edit',
            compact('helpArticle')
        );
    }

    /**
     * Update an existing help article.
     */
    public function update(
        Request $request,
        HelpArticle $helpArticle
    ) {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(['article', 'faq'])],
            'summary' => ['nullable', 'string', 'max:2000'],
            'content' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($helpArticle->title !== $validated['title']) {
            $validated['slug'] = $this->generateUniqueSlug(
                $validated['title'],
                $helpArticle->id
            );
        }

        $validated['is_published'] = $request->boolean(
            'is_published'
        );

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $helpArticle->update($validated);

        return redirect()
            ->route('dashboard.help-management.index')
            ->with('success', 'হেল্প কনটেন্ট সফলভাবে আপডেট হয়েছে।');
    }

    /**
     * Toggle publish status.
     */
    public function toggleStatus(HelpArticle $helpArticle)
    {
        $helpArticle->update([
            'is_published' => !$helpArticle->is_published,
        ]);

        return back()->with(
            'success',
            $helpArticle->is_published
                ? 'কনটেন্ট প্রকাশ করা হয়েছে।'
                : 'কনটেন্ট Unpublish করা হয়েছে।'
        );
    }

    /**
     * Delete an article.
     */
    public function destroy(HelpArticle $helpArticle)
    {
        $helpArticle->delete();

        return redirect()
            ->route('dashboard.help-management.index')
            ->with('success', 'হেল্প কনটেন্ট মুছে ফেলা হয়েছে।');
    }

    /**
     * Generate a unique URL slug.
     */
    private function generateUniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($title);

        // Bengali titles may produce an empty slug.
        if ($baseSlug === '') {
            $baseSlug = 'help-' . Str::lower(Str::random(8));
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            HelpArticle::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
