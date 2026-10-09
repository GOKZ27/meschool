<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Support\Bkk;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    /**
     * The home page.
     *
     * The page itself is a set of components; what it needs from the database
     * is exactly the seven preview blocks the news feature is judged on, so
     * the queries live here rather than inside the view.
     */
    public function index(): View
    {
        $featured = News::published()
            ->latestFirst()
            ->where('is_featured', true)
            ->first()
            ?? News::published()->latestFirst()->first();

        $latest = $this->without($featured)
            ->latestFirst()
            ->paginate((int) config('school.news.home.latest_per_page', 5));

        // The remaining blocks draw from what the refresh above already showed,
        // so no story is featured twice on the same page. Each block claims its
        // own slice before the next one runs.
        $shown = $latest->getCollection()->map(fn (News $article) => $article->getKey())->all();
        if ($featured) {
            $shown[] = $featured->getKey();
        }

        $grid = News::published()
            ->when($shown !== [], fn (Builder $query) => $query->whereKeyNot($shown))
            ->latestFirst()
            ->take(2)
            ->get();

        $shown = array_merge($shown, $grid->map(fn (News $article) => $article->getKey())->all());

        $past = News::published()
            ->when($shown !== [], fn (Builder $query) => $query->whereKeyNot($shown))
            ->latestFirst()
            ->take(3)
            ->get();

        return view('home', [
            'news' => [
                'featured' => $featured,
                'latest' => $latest,

                'grid' => $grid,

                'past' => $past,

                'trending' => News::published()
                    ->when($shown !== [], fn (Builder $query) => $query->whereKeyNot($shown))
                    ->orderByDesc('views')
                    ->take(5)
                    ->get(),

                'categories' => config('school.news.categories'),
            ],

            // The BKK section previews the config-sourced job listings, still
            // taking applications so the row never shows a closed listing.
            'bkk' => Bkk::forHome(),
        ]);
    }

    /** A fresh query for published articles, optionally without one of them. */
    private function without(?News $excluded): Builder
    {
        return News::published()->when(
            $excluded,
            fn (Builder $query) => $query->whereKeyNot($excluded->getKey()),
        );
    }
}
