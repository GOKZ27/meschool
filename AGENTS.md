# meschool

Public website for **SMK Islamiyah Ciputat** (Indonesian vocational high school). Laravel **13**
(`laravel/laravel` skeleton, framework v13.34 installed) on PHP `^8.3`. Four controllers carry the
real logic: the home page (`HomeController`), the news archive `/berita` (`NewsController` +
`App\Models\News`), BKK (`BkkController` + `App\Support\Bkk`, config-driven) and the programme pages
(`ProgramController`, config-driven). Every navbar entry lands on a real page: the programme
dropdown runs one literal route per slug (see Routes), the flat sections (SPMB, Tenaga Kependidikan)
are copy-driven `Route::view`s; only the legacy child URLs (`bkk.lowongan`, `bkk.tracer-study`,
`spmb.tentang-kami`, `spmb.informasi-sekolah`) and `/pencarian` still render `placeholder`.

Local toolchain: PHP 8.3.35 ZTS (Laragon), Node 24, npm 11. This **is** a git repo (branch `main`)
with **no CI** and no quality tooling beyond what the composer scripts run. Tests use in-memory
SQLite; the local `.env` points at MySQL — see Environment.

## Read only these

Everything needed to work here is in the files below. Anything else is noise or bulk — reading it
costs context and returns nothing:

- `config/school.php` (all brand copy, programs, hero slides, the news **and BKK** catalogues),
  `routes/web.php`, `bootstrap/app.php`
- `resources/css/app.css`, `resources/js/app.js`, `resources/views/home.blade.php`
- the component you are editing in `resources/views/components/`
- the news feature, if touching it: `app/Http/Controllers/{Home,News}Controller.php`,
  `app/Models/News.php`, `resources/views/berita/`,
  `resources/views/components/news-preview.blade.php`, `database/seeders/NewsSeeder.php`
- the BKK feature, if touching it: `app/Http/Controllers/BkkController.php`,
  `app/Support/Bkk.php`, `resources/views/bkk/`,
  `resources/views/components/bkk/`
- `tests/Feature/HomePageTest.php` (1600+ lines) — grep it for the test name you care about, never
  read it whole

**Never read:** `vendor/`, `node_modules/`, `public/build/**` (~110 KB of minified CSS — grep it with
`Select-String` if you must), `storage/framework/views/**`, `resources/views/welcome.blade.php`
(72 KB of unused stock Laravel), `README.md`, `CLAUDE.md`.

Grep first (`Select-String`), then read only the matching file with `offset`/`limit`.

## Verify in this order

```sh
npm run build      # required first: ten tests read the compiled CSS
composer test      # expect {"tool":"phpunit","tests":122,...,"assertions":1210}
vendor/bin/pint    # the only quality gate; run it last, after tests pass
```

A change is done when the build succeeds, all 122 tests pass, and pint is clean — not before.
**As of this writing one BKK test fails on date drift** (see Tests); treat that one as known, not
as a regression you introduced.

## Commands

```sh
composer setup                     # install, .env, key:generate, migrate, npm install, npm run build
composer dev                       # php artisan dev → serve :8000 + queue:listen + vite, one process
composer test                      # config:clear, then artisan test
php artisan db:seed --class=NewsSeeder   # news content (upsert by slug); setup does NOT seed
php artisan test tests/Feature/HomePageTest.php      # single file
php artisan test --filter=test_the_navbar_hover_pulses_instead_of_underlining   # single test
vendor/bin/pint                    # format (no pint.json → default `laravel` preset)
vendor/bin/pint --test             # check only
php artisan dev:list               # the three processes `dev` runs (there is no dev:reload)
php artisan view:clear
```

- `vendor/bin/pint` is the **only** quality gate. No PHPStan, no Rector, no JS linter/typecheck.
- `.npmrc` sets `ignore-scripts=true`, so no lifecycle scripts run on install.
- Fonts are fetched from Bunny at build time by the `bunny()` call in `vite.config.js`
  (`Instrument Sans` 400/500/600). They land in `public/build/assets/*.woff2` + a
  `fonts-manifest.json`, **not** `public/fonts/`, so **`npm run build` needs network access**.
- Run `php artisan config:clear` after editing `.env` (`composer test` does it for you), or stale
  cached config silently wins.

### Artisan output is JSON inside agent sessions

`laravel/pao` (dev dep) detects agent sessions and swaps in `PaoOutputStyle`, so `php artisan test`
emits a single JSON line instead of PHPUnit's report:

```json
{"tool":"phpunit","result":"failed","tests":109,"passed":108,"assertions":938,"failed":1,
 "failures":[{"test":"Tests\\Feature\\X::test_y","file":"...","line":11,"message":"..."}]}
```

`PAO_DISABLE=1` (PowerShell: `$env:PAO_DISABLE=1`) restores normal output; `PAO_FORCE=1` forces JSON
outside agent sessions. `vendor/bin/pint` does the same (`{"tool":"pint","result":"passed"}`).
A clean run is **122 tests / ~1210 assertions**.

### Tests need a CSS build first

`HomePageTest::builtCss()` (a private helper) reads the **compiled** stylesheet:

```php
glob(public_path('build/assets/app-*.css'))   // "Run `npm run build` before the suite"
```

Ten tests read it: the four navbar-animation guards, the reveal-cascade guard, the programs-heading
reveal, the staff-rail edge, the dialog a11y and viewport pair, and the staff hover lift. **Run
`npm run build` before `php artisan test`** after touching `resources/css/app.css` or any Blade
markup, or they fail on a stale/absent bundle. `node_modules/` and `public/build/` currently exist
locally (both gitignored).

If a run reports `ParseError` in `storage/framework/views/*.php`, that is a partially-written compiled
cache from a bulk recompile, not a broken view — `php artisan view:clear` and re-run.

## Architecture

- **Layout is `resources/views/components/layouts/app.blade.php`, used as `<x-layouts.app>`.**
  Anonymous components only resolve from `resources/views/components/`, so a file at
  `resources/views/layouts/app.blade.php` throws "Unable to locate a class or view for component".
- Every page is `<x-layouts.app>` + `<x-header-top>` + `<x-navbar>` + `<main>`. The
  `<x-contact-strip>` (the domain + phone line) is the **last block inside `<main>` on every page
  except the home one**, where it sits **between** the hero and the greeting — and it reads as the
  footer on those pages. It renders a plain `<div>` on purpose: a `<footer>` may not be a
  descendant of `<main>`, and the strip genuinely sits mid-page on the home page. Keep it that way —
  renaming it back to a footer is a breaking change for `HomePageTest` and `KontakPageTest`.
- **Home page section order**: hero → contact strip → principal greeting → programs → staff →
  **news preview** → **BKK** → **SPMB** → **kontak** → end, as laid out in `home.blade.php`. Tests
  pin hero < strip < greeting, programs < staff, news < bkk, bkk < spmb and spmb < kontak.
  **Section backgrounds alternate** `bg-white` / `bg-slate-50` (sambutan white, programs slate, staff
  white, berita slate, bkk white); the SPMB banner is a **full-bleed photo band** instead — keep that
  alternation and the banner when moving or adding blocks, or adjacent sections merge. The contact
  section is **white** because it sits directly below the dark banner (its panels are slate card
  ground), and the **footer is `school-950`**, so light → dark → light dark rhythm closes the page.
  (The doc comment at the top of `principal-greeting.blade.php` still claims the greeting sits
  *between* the hero and the strip — it is stale, trust `home.blade.php`.)
- **The staff section is a horizontal rail, not a grid.** (It is no longer the closing section of
  the page — news, BKK, SPMB, kontak come after it, per the order above.)
  `config('school.staff')` (`eyebrow`, `title`, `intro`, `items[]` of `name` / `position` / `photo` /
  `description` / `expertise`) feeds `<x-staff-section>`, which renders one `<ul data-staff-rail>` of
  cards and a **single** `<dialog data-staff-modal>` for the whole rail — the script fills the dialog
  from the `<script type="application/json" data-staff-data>` payload beside it, so there is only one
  place the copy lives. Photos resolve to `null` when the file is absent and the card shows a
  monogram from the name; the popup rebuilds it in JS. No teachers table exists yet, so the eight
entries in config are placeholders. The section has **no standfirst**: heading left, arrows right,
   one flex row. The rail **sweeps sideways on its own as a marquee**: the script duplicates the
   cards once at runtime (`cloneNode(true)`) so the run can wrap to the front without dead-ending,
   and a `requestAnimationFrame` loop drives it by frame delta (~200 px/s). **The sweep only yields
   to a hand actually on a card.** Pointing at a card `hold()`s the sweep and
   opens that card's profile straight away; moving along the cards refills the same popup, and
   leaving the cards and the popup closes it and the sweep carries on. Also `hold()`: an active drag
   and a hidden tab. A cursor merely resting nearby must **not** stop it — that reads as a broken
   carousel, and a visitor looking at the section always has the pointer over it.
   `prefers-reduced-motion` disables it entirely, like the hero, leaving a still rail where the
   arrows step past the last card and wrap to the front.

**The hover popup is a `show()`, never a `showModal()`.** A modal dialog makes the rest of the page
  inert, so the pointer could never travel to the next card and the rail would be stuck on one
  profile. Non-modal still gets the top layer and Escape, and `.staff-modal:not(:modal)` (which must
  come **after** `.staff-modal[open]` — same specificity, so source order decides) shrinks the box to
  the panel and drops the backdrop. The script places it beside the card, flipping to the far side
  and then below; it is never allowed to cover the card, because that would end the `:hover` that
  opened it. The card button keeps the **modal** path for keyboard and touch, where focus must be
  trapped. `close()` queues its event, so the `close` listener must bail out when
  `staffModal.open` is already true again — otherwise reopening from a hover releases the hold and
  the rail drifts behind a profile that is still on screen.
- `resources/views/welcome.blade.php` is dead stock Laravel — no route points at it. It is **not**
  harmless: Tailwind scans it, so it drags ~200 dead utilities into the bundle (see below).
- No `app/Http/Kernel.php`, no `Http/Middleware/`, no `routes/api.php`. Middleware, routing and
  exception config all live in `bootstrap/app.php` (`withMiddleware` / `withExceptions`).
- Models use PHP attributes, not properties: `#[Fillable([...])]` / `#[Hidden([...])]` — see
  `app/Models/User.php`. Match that style.
- `resources/js/app.js` is the only runtime script and holds **exactly three features**: the hero
  slider, scroll reveals, and the staff rail/popup — plain `document` calls, no library, no Alpine.
  The news preview and archive are pure Blade; nothing was added to `app.js` for them.
  **There is no icon library either**; icons are inline SVG in `components/icon.blade.php`, split
  into `$outlined` (stroke) and `$branded` (filled) maps. An unknown `name` renders an empty box,
  and `HomePageTest` greps the component for `'name' =>` for every configured program icon.
- **The news feature is the only database-backed section.** `HomeController` (home page) and
  `NewsController` (`/berita`) read `App\Models\News` (`slug / title / excerpt / body / category /
  image / gallery / video / author / views / published_at / is_featured / is_picked`; migration
  `2026_10_06_000000_create_news_table.php`, factory `NewsFactory` with a `featured()` state).
  Categories are **not** a table: the catalogue is `config('school.news.categories')` (label / icon /
  tone), so renaming one is a config edit and never a migration. `NewsSeeder` is the working copy of
  the content — upsert on slug, so re-seeding edits in place; `composer setup` does **not** seed, and
  a fresh database simply renders the home page's empty state and an empty archive.
  `NewsController::show` increments `views` on every read, so GETs have a side effect.
- **BKK (Bursa Kerja Khusus) is config-driven — no DB, no migration, no model.**
  `config('school.bkk')` feeds `App\Support\Bkk` (`jobs()` / `active()` / `forHome()` /
  `find($slug)`, plus `categories()` / `locations()` / `statuses()` derived from the catalogue for
  the sidebar filters); the nine `jobs` entries in config are the working copy. Each job takes
  `slug / position / company / logo (nullable) / category (key into the categories map) / location /
  type / salary / deadline (Y-m-d) / published_at / description[] / requirements[] /
  qualifications[] / benefits[] / how_to_apply[] / apply` — `apply` is optional and falls back to a
  `mailto:` at `contact.email` with `'Lamaran '.$position` as the subject. **Status is derived from
  `deadline` at render time**: `open` / `closing` (Segera Berakhir, within `closing_days`) / `closed`,
  with `closed` excluded from the home block and the `statuses()` sidebar list.
  `BkkController@index` filters `q / bidang / lokasi / status` over the in-memory collection with
  `Illuminate\Support\Collection::when` and paginates with a `LengthAwarePaginator` (any real
  backend can later swap in a model without touching views). A missing `logo` falls back to a
  monogram of the company initials via the `<x-bkk.logo>` component; UI copy (page titles, empty
  states) lives in config; dates render through the same Indonesian `school.news.months` month names
  the berita feature uses. `BkkPageTest` recomputes its expectations from `Bkk::jobs()` so deadline
  drift never rots them; it uses `e()` when asserting rendered positions because `&` in titles is
  escaped.
- **SPMB is a single config-driven banner — no controller, no route of its own.** `config('school.spmb')`
  (`bg_photo` / `overlay` / `eyebrow` / `title` / `subtitle` / `description` / `button_label` /
  `link_label`) feeds `<x-spmb-section>`, which closes the informational sections on the home page.
  It is a **full-width photo band**, not a white/slate block: the photo is a plain `bg-cover` (never
  painted into a brick fallback gradient), the dark `overlay` is a config class, and both links go to
  the `route('spmb')` landing page (see below). `bg_photo` reuses the hero's `images/hero/hero1.jpeg`
  because it is the only school photo that ships with the repo — swap the config value the day a
  dedicated upload lands. Copy cascades in through the shared `data-reveal-group`; `SpmbPageTest`
  pins the markup contract (`id="spmb"`, `data-spmb-bg`, `data-spmb-overlay`) and both missing/
  present branches of the photo.
- **The navbars' landing pages are all copy-driven too.** `config('school.spmb_page')` feeds
  `resources/views/spmb.blade.php` (a `Route::view('/spmb')`): the hero reuses the SPMB photo +
  overlay, then `steps` / `requirements` / `cta_*` lay out the registration flow; it falls back to
  the brick gradient exactly like the home banner. `config('school.staff')` feeds
  `resources/views/tenaga-kependidikan.blade.php` (also a `Route::view`) as a **quiet grid**, with
  the `expertise` chips the home rail hides in its popup shown on the card — the monogram / photo
  logic is duplicated from `<x-staff-section>`, so point both places at the same config. The
  programme pages are `config('school.program_catalog')` keyed by slug (name / icon / tagline /
  description / competencies[] / careers[]), served by `ProgramController@show` with a literal
  route per slug in `routes/web.php` (`->defaults('program', $slug)` — a missing slug is a 404 by
  construction). Each programme page is brick-green hero + pills linking the other programmes
  (`data-program-pills`, current one marked) + intro + twin competencies/careers cards + a dark CTA
  to `kontak` / `bkk` (`data-program-cta`). **Page blades must not `use App\…` inside `@php`** —
  page bodies compile inside the layout's `shouldRender(): ... endif` wrapper, so a `use` there is
  a PHP parse error; use fully-qualified names or compute in the controller (component blades like
  `<x-staff-section>` are exempt — their `@php` is at absolute top).
- **Kontak is `<x-contact-section>`, a copy-driven white block after SPMB, followed by the
  site-wide `<x-footer>`.** The section is animated by nothing but the shared reveal data attributes;
  its four channel cards (`whatsapp` / `phone` / `mail` / `map-pin`, configured link targets), a
  plain CSS `address`-free Google Maps `<iframe>` (`maps.google.com/maps?...&output=embed` — no API
  key) and the social list all reuse the kontak page's patterns on `bg-slate-50` card ground. The
  footer (`<x-footer>`) is dark `school-950` with brand + tagline + description, Menu / Program
  Keahlian / Kontak columns and a gold social cluster; it is emitted by the layout **after**
  `{{ $slot }}` so it is always a sibling of `<main>`, never a descendant — a `<footer>` may not
  live inside `<main>`. It must not resurrect the legacy dropdown labels: `HomePageTest` still
  forbids `Pengumuman` / `Kegiatan Sekolah` / `Prestasi` / `Tracer Study` / `Informasi Lowongan
  Pekerjaan` anywhere in home markup. `FooterTest` pins the spmb < kontak order, the channel + social
  targets, footer presence on every page and the footer-outside-main contract.

## Site conventions

- **Brand values live in `config/school.php`** (name, tagline, contacts, socials, logo/hero paths,
  image folders, programs, the principal's greeting, the whole news catalogue (categories,
  Indonesian month names, archive/home headings, `per_page`) and the whole BKK catalogue (categories,
  statuses, the nine `jobs`, home/archive/detail copy)). Edit that instead of hard-coding
  strings in components. UI copy is Indonesian; code comments are English. Match both.
- **Never hard-code the `images/` or `videos/` prefix.** Use `App\Support\Media` through its two
  subclasses: `Images::url($path, $folder)`, `Videos::url(...)`, and `existingUrl(...)` (returns
  `null` when the file is absent so the caller can fall back), plus `tag(...)`. Both accept a bare
  filename, a base-prefixed path, or a full URL.
- **Optional imagery must degrade gracefully.** Hero, logo and the principal's portrait read config
  paths and fall back to `bg-brick-green` / a typographic placeholder when the file is missing.
  Tests assert both branches, so a missing upload must never leave a broken `<img>`.
- **The hero is a sequence of slides.** `school.hero.slides` is a list; each entry takes a **photo OR
  a clip, never both** (`photo` wins if both keys are present), plus `poster` (clips only), `duration`
  in ms (floored at 400) and `caption` — true for the single slide that carries the school name, slogan
  and buttons. `overlay` shades every slide. A slide whose file is missing still renders, as the brick
  texture, so a half-uploaded set keeps cycling on its timings. Clips live in `public/videos/hero/`,
  photos in `public/images/hero/` — see `public/videos/README.md` and `public/images/README.md` (both
  Indonesian; the videos README wrongly says video beats photo, the component says otherwise).
- Photos go in `public/images/<subfolder>/`, clips in `public/videos/<subfolder>/` — see
  `public/images/README.md` and `public/videos/README.md` (both Indonesian) for the folder lists and
  naming/size rules.
- **The navbar is zero-JS.** The two navbar toggles are `sr-only` checkboxes that are the first
  children of `<nav>`; `.nav-root:has(#nav-…)` rules in `resources/css/app.css` drive the drawer and
  search panels. Those rules are **unlayered on purpose** so they outrank Tailwind's `hidden`, and the
  drawer rules are wrapped in `@media (width < 64rem)` so they cannot override `lg:hidden`. Desktop
  dropdowns use `group-hover:` / `group-focus-within:` on the `<li>` that `nav-item` owns. The mobile
  drawer additionally uses per-item `peer-checked:` checkboxes, so `<x-nav-item>` takes a `uid` prop
  (it renders twice — desktop bar + drawer) and every `id="nav-*"` must stay unique.
- **The hero slider is pure `data-state` flips.** `resources/js/app.js` rotates
  `config('school.hero.slides')`,
  flipping only `data-state` — every transition lives in `.hero-slide` / `.hero-caption` in
  `resources/css/app.css` (`translateX(100%)` parked → `0` active → `-100%` replaced). No framework,
  no Alpine. The first slide ships `data-state="active"` so the banner is complete without JS, and the
  script honours `prefers-reduced-motion` by never auto-advancing (the dots still work).
- **Scroll reveals.** The reveal block observes `[data-reveal], [data-reveal-group]` with an
  `IntersectionObserver` (threshold 0.2, `rootMargin: 0px 0px -8% 0px`), sets `data-revealed` **once**,
  then unobserves — entrance animations must not replay on every re-entry. The hidden state in CSS is
  gated behind `.has-js`, which the layout's inline `<head>` script adds before first paint; **never
  hide `[data-reveal]` unconditionally**, or the copy is unreachable without JS. Attributes:
  `data-reveal-from="left|right|scale"` for the travel direction, `data-reveal-group` to cascade a
  block's children, `data-reveal-slow` for the longer per-card cascade (the programs grid). Both
  halves of a section reveal from **opposite** edges so they cross — that is asserted, so keep it.
- **The principal's greeting is copy-driven, not hard-coded.**
  `config('school.principal')` holds `name`, `role`, `photo`, `photo_side` and a `message` array (one
  paragraph per entry); `<x-principal-greeting>` renders it last, after the contact strip.
  `photo_side` (`left`/`right`) flips the portrait with `lg:order-*` — DOM order stays
  portrait-first — and each half reveals from the opposite edge. **`name` is `null` until the real
  principal is confirmed**: the signature then shows the role alone rather than crediting someone
  nobody has checked.
- **The programs section is copy-driven and mirrors the navbar.** `config('school.programs')` holds
  only heading copy (`eyebrow` / `title` / `intro`) plus `slugs`; `<x-programs-section>` intersects
  `program_catalog.items` with those slugs, so the home accordion shows the **same six** programmes
  as the dropdown (Kuliner, DKV, TKJ, AK, MP, BD), in catalogue order.
  `HomePageTest::test_the_home_page_offers_all_six_programs` pins them in order (do not reintroduce
  the old Animasi/DKV/TKJT/PPLG card list — it is gone).
- **`program_catalog` holds more pages than the six showcased** — it also carries `animasi`, `tkjt`
  and `pplg` routes that no navbar item or home card links to. **`MP` and `BD` stay deliberately
  generic** — do not guess their real names.
- Tailwind **v4** via `@tailwindcss/vite`: no `tailwind.config.js`, no `postcss.config.js`, no
  `@tailwind base/components/utilities` — only `@import 'tailwindcss'` in `resources/css/app.css`.
  Brand palettes (`school-50…950`, `gold-400…600`) are `@theme` CSS vars there; use those tokens
  rather than arbitrary hex. New custom CSS goes in `@utility` blocks in that same file.
- Custom `@utility` classes: `bg-brick-green`, `ring-school-focus`, `nav-link`. `nav-link` pulses its
  inner `> span` on hover (never the flex-1 box, which would shift the bar layout) and honours
  `prefers-reduced-motion`. The logo is a **portrait 219×274** mark: callers must pin `h-*` with
  `w-auto shrink-0`, never a square `size-*` box — `LogoTest` asserts all three.
- `@fonts` in the layout resolves through the Vite font manifest; `@vite` is guarded by
  `file_exists()` on `public/build/manifest.json` / `public/hot` because `@vite` **throws** without a
  manifest. Keep that guard.

### CSS is compiled, minified and rewritten — assert the built shape

Vite 8 (rolldown + **Lightning CSS**) rewrites what you wrote: `:nth-child(1)` → `:first-child`,
`> *` stays but nested `group-hover:` becomes `:is(:where(.group):hover *)`, `transform: translateX()`
→ `translate()`, `#ffffff88` → `#fff8`. `HomePageTest` matches the **compiled** strings, so when a
CSS assertion fails, grep `public/build/assets/app-*.css` for the actual selector before changing the
test or the CSS.

Tailwind's sources are `resources/` (auto-detected) **plus two explicit `@source` globs** in
`app.css`: `storage/framework/views/*.php` and Laravel's pagination views. The first one means
**stale compiled views from earlier designs leak dead utilities into the bundle** (`dark:*`,
`bg-neutral-50`, `scheme-light-dark`, `peer-checked:*`, `motion-safe:starting:*` are all in there
today from views that no longer exist in `resources/`). Ignore them; `php artisan view:clear` +
rebuild if the bundle needs trimming.

### A stale `public/hot` silently unstyles the site

`public/hot` (gitignored) makes `@vite` point at the Vite dev server instead of the built manifest.
One is currently present (`http://[::1]:5173`, PID owned by a running `npm run dev`). If that server
dies, delete `public/hot` — otherwise the browser loads **no CSS or JS at all** while the test suite
keeps passing, because the tests read `public/build/assets/` directly.

## Routes

`routes/web.php` has these kinds of entry:

- `/` → `HomeController@index`; `/kontak` → a `Route::view('kontak')`.
- **Berita**: `/berita` → `NewsController@index`; the three legacy children
  (`berita.pengumuman|kegiatan|prestasi`) call the same action with `->defaults('category', …)`,
  and `/berita/{slug}` is registered **last** so `/berita/pengumuman` reads as a category rather
  than a slug — keep that order.
- **BKK**: `/bkk` → `BkkController@index`, then `/bkk/{slug}` → `show` registered **last** (after
  the placeholder loop) with `->where('slug','[a-z0-9\-]+')` — the legacy `bkk.lowongan` /
  `bkk.tracer-study` stubs stay before it so `/bkk/lowongan` still resolves to the placeholder page.
- **Section landing pages (copy-driven, no controller)**: `/spmb` → `Route::view('spmb')`,
  `/tenaga-kependidikan` → `Route::view('tenaga-kependidikan')`, both reading their copy from
  `config('school.spmb_page')` / `config('school.staff')`.
- **Programme pages**: one **literal** route per slug in `config('school.program_catalog.items')`,
  `/program-keahlian/{slug}` → `ProgramController@show` with `->defaults('program', $slug)`, so a
  missing slug is a 404 by construction (there is no `{program}` param route to swallow it). The
  catalogue has nine entries: the six navbar/home programmes plus `animasi`, `tkjt` and `pplg`,
  which have pages but are linked from nowhere; `program.{{slug}}` is the name for all nine.
- The remaining `Route::view` stubs render `placeholder` with `$title` / `$description` for the
  legacy child URLs only (`bkk.lowongan` / `bkk.tracer-study` / `spmb.tentang-kami` /
  `spmb.informasi-sekolah`) plus `/pencarian` (`search`). `/maps` is a `Route::redirect` to
  `config('school.contact.maps_url')`.

The BKK / SPMB child pages are no longer linked from the bar but stay registered so old bookmarks
work — `HomePageTest` still asserts they resolve.

## Tests

Feature tests are **DOM/XPath assertions against rendered markup**, not snapshot tests. They pin
exact structure, so navbar edits break them by design:

- exactly **8** top-level `<li>` in the desktop bar, each with `group` + `relative` + `lg:flex` +
  `lg:flex-1` + `lg:justify-center`, and **no** `lg:flex-1` in the mobile drawer
- exactly **2** `a[@aria-haspopup="true"]` (desktop bar + drawer) — only *Program Keahlian* has a
  dropdown
- exactly **4** `a[@aria-current="page"]` when visiting a dropdown child
- **no** nested `<li>` (`nav-item` renders its own)
- every nav label wrapped in a `<span>` (the pulse target) and every nav `id="nav-*"` unique
  (`nav-item` renders twice, so pass a `uid`)
- the navbar and the `<header>` strip share one container class list
  (`mx-auto max-w-7xl px-4 sm:px-6 lg:px-8`)

`Tests\TestCase::parseHtml()` returns a `DOMDocument`/`DOMXPath` with libxml's HTML5 warnings
suppressed — use it for any new markup assertions. Tests that need a real image/video on disk write a
stand-in file into `public/` and delete it in `tearDown()` (`HomePageTest::fakeHeroPhoto()` and
friends) — do that rather than committing fixtures. `phpunit.xml` overrides to in-memory SQLite plus
array cache/session and `sync` queue, so no services are needed; the abstract `Tests\TestCase` uses
`RefreshDatabase`, so every run starts on an empty schema (the home page queries the `news` table
and would 500 otherwise — the commented-out `use` inside the stock `ExampleTest` is a leftover,
ignore it). The news feature itself — archive, article page, filters, the home blocks — has **no**
dedicated tests; only route resolution is pinned, so add coverage if you change it.

The other Feature suites are small and per-area: `BkkPageTest`, `SpmbPageTest`,
`KependidikanPageTest`, `ProgramPageTest`, `FooterTest`, `KontakPageTest`, `LogoTest`. Some use
PHPUnit `#[Test]` attributes with plain snake_case method names (no `test_` prefix), so
`--filter=test_…` silently matches nothing for those — filter on the method name instead.

There is one **date-drift trap** in `BkkPageTest`: it recomputes expectations from `Bkk::jobs()`,
but `test_a_closed_listing_does_not_offer_to_apply` asserts the closed listing's raw `apply_url` is
absent from the page. When a closed job's `apply` is the bare contact `mailto:`, that string is
identical to the footer's contact link, so the assertion fails as soon as such a job becomes the
first closed one. Re-derive expectations from the actual deadlines; do not trust a stale "all green"
claim.

**PHPUnit cannot test the three JS features.** Timing, auto-advance, wrap-around, the rail
arrows/drag threshold and the dialog are browser behaviour. They are checked by throwaway harnesses
in `%TEMP%\opencode` — `hero-slider.check.cjs` and `reveal-check.cjs` (hand-stubbed DOM via the `vm`
module, fake `IntersectionObserver` / `setTimeout`), `reveal-cdp.mjs` for real-browser checks, and
`staff-check.mjs` / `eval-cdp.mjs` which drive headless Chrome over the DevTools Protocol.
`staff-check.mjs` takes a url plus a width and height: `node staff-check.mjs http://127.0.0.1:8000/
390 844`, and `drift-check.mjs` does the same for the auto-sweep. Both need the Vite dev server,
because `:hover` cannot be forced from script - they move a real mouse over CDP and read the
computed styles and `scrollLeft` back. A single `requestAnimationFrame` tick cannot be sampled
reliably from outside, so the sweep speed and the wrap are polled from inside the page; keep that in
mind before rewriting a timing check.
`hover-check.mjs` owns the hover contract (the drift runs, a card stops it and opens, moving along
switches, leaving resumes). Two traps in all three, both of which produced phantom failures:
**the rail drifts between measuring and clicking, so "the first card" is regularly off-screen** —
always resolve a card that is actually inside the viewport — and **the rail must be held still
before any geometry assertion**, which `staff-check.mjs` does by faking a hidden tab
(`Object.defineProperty(document, 'hidden', …)` plus a `visibilitychange`), because opening the
dialog or holding a drag would each corrupt the checks that follow. Keep them outside the repo and
re-run them after editing `app.js`; PHPUnit only pins the **markup
contract** the script depends on
(`data-hero-slide`, `data-hero-duration`, `data-state`, `data-hero-dot`, `data-reveal`, absence of
`data-revealed`, `data-staff-rail`, `data-staff-prev`/`next`, `data-staff-open`, `data-staff-close`).

**Native `<dialog>` needs three CSS resets that are easy to miss** (all three bit the staff popup
once, and all three are now pinned by a test):
- `dialog:not([open]) { display: none }` is a **user-agent** rule, so an author `display: flex` on
  the dialog silently wins and the closed overlay stays in the flow — a whole screen of blank space
  at the bottom of the page. Reset it explicitly.
- `inset: 0` cannot stretch a `<dialog>`, because the user agent gives it `width: fit-content`.
  `position: fixed; inset: 0` alone leaves the overlay shrink-wrapped at its in-flow position; add
  `width: auto; height: auto`.
- Do not size the overlay in viewport units. `100vw` counts the classic scrollbar, so the overlay
  ends up narrower than the page it is supposed to cover.

**Anchors go in the markup, not the stylesheet.** The staff rail is a native horizontal scroller, so
its arrows are `<button>`s beside the heading and the script looks them up with
`document.querySelector`, never `rail.querySelector`. It also needs `scroll-pl-*` matching the
container's `px-*`: mandatory snapping measures from the scrollport edge, so without it the first
card sits at `scrollLeft 32` instead of 0 (the previous arrow looks enabled and does nothing) and
every snap point is one card-offset out of step, so `scrollBy(card + gap)` lands between two snap
positions and gets snapped straight back.

**Guard against duplicate `class` attributes.** `@class([...])` and
`$attributes->merge(['class' => …])` each emit their own `class="..."`; never put both on one tag —
the parser silently keeps the first and drops the second, taking the order/width classes with it
(this already blew the navbar out once at the logo's full 219×274 intrinsic size). The logo, the
hero dots and the greeting each have a raw-markup regression test; do the same for any new
conditional-class element.

## Environment

`.env` exists with `APP_KEY` set and `APP_URL=http://localhost:8000`. **It currently points at MySQL
(`DB_CONNECTION=mysql`, `DB_DATABASE=meschool`), even though `.env.example` and `phpunit.xml` use
SQLite.** `database/database.sqlite` (gitignored) is present and already holds 20 seeded news rows,
so switching `DB_CONNECTION` back to `sqlite` gives a populated local site; either way the test
suite overrides to in-memory SQLite with a fresh schema every run and never sees local data.
`SESSION_DRIVER`, `CACHE_STORE` and `QUEUE_CONNECTION` are all `database`, so
**session and cache hit the DB and queued jobs do not run inline** — a common reason a feature
appears to "do nothing" (no `queue:listen` running). Change `APP_URL` if you need correct `asset()`
URLs from a real domain.

## Laravel Boost is not installed

`CLAUDE.md` is a stale Boost bootstrap stub and `README.md` is stock Laravel — both are noise.
`php artisan boost:install` does not exist and Boost is absent from `composer.json`/`vendor`. Do not
install it: `boost:install` **overwrites `AGENTS.md`** with its own generated guidelines. Ignore
`CLAUDE.md` and follow this file.