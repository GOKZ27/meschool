<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NewsController extends Controller
{
    /**
     * The archive: newest first, narrowed by search, category and year.
     *
     * The three legacy child URLs (berita.pengumuman and friends) call this
     * same action with their category handed over as a route parameter, so an
     * old bookmark opens the archive already filtered.
     */
    public function index(Request $request, ?string $category = null): View
    {
        $filters = $this->filters($request, $category);

        $news = News::published()
            ->latestFirst()
            ->when($filters['category'], fn ($query, $value) => $query->where('category', $value))
            ->when($filters['year'], fn ($query, $value) => $query->whereYear('published_at', $value))
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $needle = '%'.$filters['q'].'%';

                $query->where(function ($inner) use ($needle) {
                    $inner->where('title', 'like', $needle)
                        ->orWhere('excerpt', 'like', $needle)
                        ->orWhere('body', 'like', $needle);
                });
            })
            ->paginate((int) config('school.news.per_page', 12))
            ->withQueryString();

        $counts = News::published()
            ->toBase()
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return view('berita.index', [
            'news' => $news,
            'filters' => $filters,
            'categories' => collect(config('school.news.categories'))
                ->map(fn (array $info, string $slug) => $info + [
                    'slug' => $slug,
                    'count' => (int) $counts->get($slug, 0),
                ])
                ->values(),
            'years' => News::published()
                ->latest('published_at')
                ->pluck('published_at')
                ->map(fn ($date) => (int) Carbon::parse($date)->year)
                ->unique()
                ->values(),
            'popular' => News::published()->orderByDesc('views')->take(5)->get(),
        ]);
    }

    /**
     * One article. The view counter runs on every read, which is what makes
     * the "Trending" sidebar mean something rather than being a fixed list.
     */
    public function show(string $slug): View
    {
        $article = News::published()->where('slug', $slug)->firstOrFail();

        $article->increment('views');
        $article->refresh();

        return view('berita.show', [
            'article' => $article,
            'related' => News::published()
                ->latestFirst()
                ->where('category', $article->category)
                ->whereKeyNot($article->getKey())
                ->take(4)
                ->get(),
            'popular' => News::published()
                ->orderByDesc('views')
                ->whereKeyNot($article->getKey())
                ->take(5)
                ->get(),
        ]);
    }

    /**
     * Normalise the requested filters: an unknown category is dropped rather
     * than left to return an empty page, and the search term is trimmed.
     *
     * @return array{q: string, category: ?string, year: ?string}
     */
    private function filters(Request $request, ?string $category): array
    {
        $known = config('school.news.categories', []);
        $category = $category ?? $request->query('kategori');
        $category = is_string($category) && isset($known[$category]) ? $category : null;

        $year = $request->query('tahun');
        $year = is_string($year) && preg_match('/^\d{4}$/', $year) ? $year : null;

        $q = $request->query('q');

        return [
            'q' => is_string($q) ? trim($q) : '',
            'category' => $category,
            'year' => $year,
        ];
    }
}
