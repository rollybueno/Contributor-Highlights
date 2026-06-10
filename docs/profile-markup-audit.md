# WordPress.org Profile Markup Audit

Audit of the redesigned [profiles.wordpress.org](https://profiles.wordpress.org) markup and recommendations for updating Contributor Highlights (v1.1.0+).

**Reference profiles:** [rollybueno](https://profiles.wordpress.org/rollybueno/), [wordpress](https://profiles.wordpress.org/wordpress/)

---

## Executive summary

The plugin was built around a DOM that no longer exists (`#content-about`, `#user-meta`, `#user-badges` + dashicons). The new profile is section-based: **Bio**, **Badges**, **Current Job**, **Recent impact**, **WordPress releases**, and **Contributions** (activity feed).

Badges fail for two reasons:

1. **Parser** — `//ul[@id="user-badges"]/li` returns nothing on the new page.
2. **Renderer** — output assumes old `div.badge` + `dashicons-*` classes that WordPress.org no longer provides.

---

## Audit: old vs new markup

The parser in `public/class-contributor-highlights-public.php` (`parse_profile_html()`) still targets the **pre-2026 profile layout**:

| Plugin expects | Selector / mechanism | New profile reality |
|---|---|---|
| Name | `//header//h2/a` | Likely still present, but header structure changed; keep fallback |
| Avatar | `//img[contains(@class, "avatar")]` | Probably still works |
| Bio | `#content-about .item-meta-about p` | **Removed** — now a **Bio** section |
| Weekly pledge | `#content-about .item-meta-contribution p` | **Removed** — replaced by **Recent impact** + activity feed |
| Meta (job, location, GitHub, etc.) | `#user-meta li#user-*` | **Removed** — job is now **Current Job**; social links under “Find me on” |
| Badges + icons | `#user-badges li` → `div.badge` + `dashicons-*` classes | **Removed** — badges are chips with labels like `Core AI Contributor '25`, no dashicons |

### Broken badge pipeline

Parser target:

```php
//ul[@id="user-badges"]/li
// .//div[contains(@class, "badge")]
```

Renderer (`display_contributor_profile()`):

```html
<span class="dashicons {badge-class}"></span>
<span class="badge-name">{badge-name}</span>
```

CSS in `public/css/contributor-highlights-public.css` is built around `.badge-{slug}` + dashicons — that icon pipeline is obsolete on the new page.

---

## New profile sections

| Section | Example content | Card fit |
|---|---|---|
| **Identity** | Name, @username, avatar, profile URL | Essential |
| **Bio** | Free-text bio (sometimes includes contribution summary) | Essential (full mode) |
| **Badges** | `Plugin Developer`, `Core AI Contributor '25`, grouped rows | Essential — core plugin value |
| **Current Job** | Title, company, “Present” | High — replaces old `#user-meta` job/company |
| **Recent impact** | 30 / 90 / 365-day contributions, high/medium/score | High — great “at a glance” stat |
| **WordPress releases** | `7.0`, `6.9`, `6.8`, `6.1` | Medium — strong for core contributors |
| **Contributions** | Full paginated activity timeline with filters | Low in-card — too heavy; use top N only |
| **Team focus** | 365-day team distribution | Low — needs chart UI |
| **Contributor tabs** | Plugins, themes, photos, courses, translations | Optional separate mode — portfolio, not profile card |

---

## 1. What sections are good to display

### Full card (default)

1. **Header** — avatar, display name, link to WordPress.org profile
2. **Current job** — role + company (+ “Present” if applicable)
3. **Bio** — first paragraph; truncate (~300 chars) with “Read more on WordPress.org”
4. **Recent impact** — one line, e.g. `191 contributions · score 389 (12 months)`
5. **Badges** — all badges, grouped visually if the source groups them
6. **WordPress releases** — pill list when present
7. **Recent activity** — latest 3–5 items only (date + title + impact badge)

### Compact card (`compact_version`)

1. Avatar
2. Name (currently hidden in compact — consider showing it)
3. Current job (one line)
4. Badges (no section heading)
5. Optional: 12-month impact score as a single stat

### Skip or defer

- Full contributions timeline
- Team focus chart
- Plugins/themes/photos/courses tabs (different block: “Contributor Portfolio”)
- Slack (unless still in “Find me on” and explicitly desired)

### Suggested toggle renames

| Old toggle | New meaning |
|---|---|
| `show_contributions` | `show_recent_activity` (3–5 items) or `show_impact` |
| `show_meta` | `show_current_job` + optional `show_social` |
| `show_badges` | unchanged |
| `show_bio` | unchanged |

---

## 2. How to implement

### A. Refactor the parser into section extractors

Replace the one-shot XPath list in `parse_profile_html()` with section extractors and a normalized schema:

```php
[
  'name'            => 'Rolly Bueno',
  'username'        => 'rollybueno',
  'avatar'          => '...',
  'profile_url'     => 'https://profiles.wordpress.org/rollybueno/',
  'bio_html'        => '...',
  'current_job'     => [ 'title' => '...', 'company' => '...', 'is_present' => true ],
  'social'          => [ 'website' => [...], 'github' => [...] ],
  'impact'          => [ '30d' => [...], '90d' => [...], '12m' => [...] ],
  'releases'        => [ '7.0', '6.9', ... ],
  'badges'          => [ [ 'slug' => 'core-ai-contributor', 'label' => 'Core AI Contributor', 'year' => '25' ] ],
  'recent_activity' => [ [ 'date' => '...', 'impact' => 'high', 'summary' => '...' ] ],
]
```

Keep raw HTML cached (`conthi_wp_data_*`); store parsed structure in `conthi_profile_data_*`. Bump the cache key (e.g. `conthi_profile_data_v2_*`) so old empty parses do not linger after a markup change.

### B. Badge strategy (highest priority)

**Do not depend on WordPress.org dashicons/classes anymore.**

Recommended pipeline:

1. **Parse badge nodes** from the new Badges section (inspect cached transient HTML for the real wrapper — likely a section + list/grid, not `#user-badges`).
2. For each badge, extract:
   - `label` — e.g. `Core AI Contributor '25`
   - `year` — regex `/'(\d{2})$/`
   - `slug` — prefer `data-badge`, `class*="badge-"`, or `href` if present; else map from label
3. **Slug map in plugin** — dictionary from [Meta handbook badge names](https://make.wordpress.org/meta/handbook/tutorials-guides/profile-badges/) → internal slug (`Core Contributor` → `code`, `Plugin Developer` → `plugins`, etc.)
4. **Render with plugin-owned assets** — extend existing SVG approach (`core-ai-contributor.svg`, etc.); fallback to colored initial or generic badge icon
5. **Unknown badges** — use `.badge-unknown` (already exists in CSS)

Display: `Core AI Contributor` + small `'25` year chip, not the raw scraped string.

### C. Section parsers (conceptual)

Confirm exact selectors against a saved transient HTML fixture. Likely patterns:

```php
// Bio — first content block under a "Bio" heading/landmark
// XPath idea: section containing h3 "Bio" → first rich text container

// Current Job
// section "Current Job" → title, company, status

// Recent impact
// parse labeled stats: contributions, high, medium, score per period

// Badges
// iterate badge chip elements; never assume ul#user-badges

// Releases
// section "WordPress releases" → list of version strings

// Recent activity
// contributions list → cap at 5 items; strip filters/pagination UI
```

Save one transient HTML file as a test fixture and iterate selectors against it — the redesign may continue to evolve.

### D. Rendering changes

**Badges** — replace dashicons output with:

```html
<div class="badge-item badge-item--core-ai-contributor">
  <span class="badge-icon" aria-hidden="true"></span>
  <span class="badge-name">Core AI Contributor</span>
  <span class="badge-year">'25</span>
</div>
```

CSS: `badge-item--{slug}` maps to colors/SVG (reuse most of the existing palette rules, renamed).

**Impact** — simple stat row:

```html
<div class="contributor-impact">
  <span>191 contributions</span>
  <span>Score 389</span>
  <span class="impact-period">Last 12 months</span>
</div>
```

**Recent activity** — compact list, not the full profile feed.

### E. Resilience over time

| Approach | Verdict |
|---|---|
| Keep HTML scraping | OK for now — no public profiles JSON API |
| Add parser version + cache key bump | Required on markup changes |
| HTML fixture tests per section | High value |
| Monitor [wporg-contributor-dashboard API issue](https://github.com/WordPress/wporg-contributor-dashboard/issues/13) | Long-term: switch when stable |
| Defensive empty states | If a section is missing, hide it; do not break the card |

### F. Suggested implementation order

1. Save transient HTML as fixture; document real selectors
2. Fix **badges** (parse + slug map + new renderer)
3. Fix **bio** + **current job**
4. Add **recent impact** + **releases**
5. Replace **contributions** with **recent activity** (limited)
6. Update block toggles + readme
7. Add regression tests against the fixture

---

## Files affected

| File | Changes needed |
|---|---|
| `public/class-contributor-highlights-public.php` | Parser refactor, new data schema, updated render template |
| `public/css/contributor-highlights-public.css` | Badge class rename (`badge-item--{slug}`), impact/activity styles |
| `public/images/` | Additional badge SVGs as needed |
| `includes/class-contributor-highlights-blocks.php` | New block attributes / toggles |
| `src/blocks/contributor-highlights/index.js` | Inspector control updates |
| `readme.txt` | Document new sections and toggle changes |

---

## Related links

- [Profile Badges handbook](https://make.wordpress.org/meta/handbook/tutorials-guides/profile-badges/)
- [Career functionality testing (2026)](https://make.wordpress.org/test/2026/05/28/help-test-new-career-functionality-on-wordpress-org/)
- [GitHub-style profile redesign discussion](https://github.com/WordPress/wordpress.org/issues/612)
- [Contributor Dashboard API discussion](https://github.com/WordPress/wporg-contributor-dashboard/issues/13)
