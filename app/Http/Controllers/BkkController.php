<?php

namespace App\Http\Controllers;

use App\Support\Bkk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BkkController extends Controller
{
    /**
     * The archive: newest first, narrowed by search, field, location and
     * status. The listings are a config collection, so the paginator is built
     * by hand instead of coming from an Eloquent query.
     */
    public function index(Request $request): View
    {
        $filters = $this->filters($request);

        $jobs = Bkk::jobs()
            ->when($filters['q'] !== '', function ($jobs) use ($filters) {
                $needle = mb_strtolower($filters['q']);

                return $jobs->filter(function (array $job) use ($needle) {
                    return str_contains(mb_strtolower($job['position']), $needle)
                        || str_contains(mb_strtolower($job['company']), $needle);
                });
            })
            ->when($filters['category'] !== null, fn ($jobs) => $jobs->where('category', $filters['category']))
            ->when($filters['location'] !== null, fn ($jobs) => $jobs->where('location', $filters['location']))
            ->when($filters['status'] !== null, fn ($jobs) => $jobs->where('status', $filters['status']))
            ->values();

        $perPage = (int) config('school.bkk.per_page', 12);
        $page = max((int) $request->query('page', 1), 1);

        $paginator = new LengthAwarePaginator(
            $jobs->forPage($page, $perPage)->values(),
            $jobs->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('bkk.index', [
            'jobs' => $paginator,
            'filters' => $filters,
            'categories' => Bkk::categories(),
            'locations' => Bkk::locations(),
            'statuses' => Bkk::statuses(),
        ]);
    }

    /**
     * One job listing. Missing slugs 404; the three statuses are derived, so
     * a closed listing is still readable but its apply button is disabled.
     */
    public function show(string $slug): View
    {
        $job = Bkk::find($slug);

        abort_unless($job !== null, 404);

        return view('bkk.show', [
            'job' => $job,
            'related' => Bkk::jobs()
                ->where('category', $job['category'])
                ->reject(fn (array $other) => $other['slug'] === $slug)
                ->take(3)
                ->values(),
        ]);
    }

    /**
     * Normalise the requested filters: an unknown category or status is
     * dropped rather than left to return an empty page, and the search term
     * is trimmed.
     *
     * @return array{q: string, category: ?string, location: ?string, status: ?string}
     */
    private function filters(Request $request): array
    {
        $q = $request->query('q');
        $category = $request->query('bidang');
        $location = $request->query('lokasi');
        $status = $request->query('status');

        return [
            'q' => is_string($q) ? trim($q) : '',
            'category' => is_string($category) && isset(config('school.bkk.categories')[$category]) ? $category : null,
            'location' => is_string($location) && $location !== '' ? $location : null,
            'status' => is_string($status) && in_array($status, ['open', 'closing', 'closed'], true) ? $status : null,
        ];
    }
}
