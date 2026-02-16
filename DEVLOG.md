# LaravelSkills — Dev Log

> Product insights, bugs, workarounds, and findings discovered while building the LaravelSkills POC.
> Every entry here is potential feedback for the Laravel team.

---

## How to Use This Log

When something unexpected happens — a package behaves differently than documented, a Cloud feature doesn't work as expected, a workaround is needed — log it here. Format:

```
### YYYY-MM-DD — Short Title

**Context:** What were you doing?
**Expected:** What should have happened?
**Actual:** What happened instead?
**Resolution:** How did you work around it? Or is it still open?
**Severity:** Minor | Notable | Blocker
**Product Insight:** What should the Laravel team know?
```

---

## Findings

### 2026-02-16 — Project Kickoff

**Context:** Initial project scoping and build plan creation.
**Decisions made:**
- Laravel 12 as the starting framework (13 not yet released as of today)
- Will upgrade to Laravel 13 the moment it's tagged — documenting that upgrade is itself a product insight
- Laravel Cloud as the only hosting platform
- Scout database driver instead of Meilisearch (Laravel-native only approach)
- Pest over PHPUnit, Pint for code style
- All dependencies must be Laravel first-party or core-team maintained
**Product Insight:** We're intentionally dogfooding Laravel 12 + Cloud together, with a planned upgrade to 13. The 12→13 upgrade experience on a real project will be useful feedback. Any friction during setup, deployment, or runtime is valuable data.

### 2026-02-16 — Laravel 13 Not Yet Available

**Context:** Initially planned to build on Laravel 13 (expected Q1 2026, first week of Feb). Checked official docs on Feb 16 — master branch still labeled "Upcoming," latest versioned docs are 12.x.
**Expected:** Laravel 13 released by mid-February 2026.
**Actual:** Still unreleased as of Feb 16. No official announcement found.
**Resolution:** Start on Laravel 12. L13 upgrade added to backlog as first priority. Upgrade will be documented here.
**Severity:** Minor
**Product Insight:** Third-party blogs were confidently citing "first week of February 2026" as the release date. The official Laravel site gave no specific date. Worth noting the gap between community speculation and actual release timing.

### 2026-02-16 — Vue Starter Kit Uses Fortify, Not Sanctum Directly

**Context:** Scaffolded Laravel 12 with `laravel new --vue --pest --database=pgsql`. Expected Sanctum to be pre-installed since the CLAUDE.md spec calls for Sanctum SPA auth.
**Expected:** Sanctum included with the Vue starter kit.
**Actual:** The Vue starter kit uses Laravel Fortify for authentication (session-based), not Sanctum. Sanctum must be separately installed for API token auth.
**Resolution:** Installed `laravel/sanctum` via Composer, published config/migrations, enabled `statefulApi()` middleware in `bootstrap/app.php`. Fortify handles SPA auth; Sanctum will handle API token auth.
**Severity:** Minor
**Product Insight:** The Vue starter kit's auth story is Fortify-centric. Developers wanting both SPA auth and API tokens need to manually add Sanctum. Could be confusing for those expecting Sanctum out of the box.

### 2026-02-16 — User Model Kept with Auto-Increment ID

**Context:** CLAUDE.md specifies `HasUlids` on all models. The Vue starter kit scaffolds User with standard auto-increment `$table->id()`.
**Expected:** Could switch User to ULIDs for consistency.
**Actual:** Kept User with auto-increment to avoid breaking Fortify's two-factor migration, session foreign keys, and the full auth scaffolding.
**Resolution:** All new models (Skill, Author, Category, SocialPost) use `HasUlids`. User keeps auto-increment integer ID. The `authors.user_id` foreign key uses `foreignId()` to match.
**Severity:** Minor
**Product Insight:** Switching an existing User model from auto-increment to ULIDs is non-trivial when auth packages (Fortify) are involved. A "ULID-first" starter kit option would be a nice addition.

### 2026-02-16 — Pint Fixes in Starter Kit Test Files

**Context:** Ran `./vendor/bin/pint --parallel` immediately after scaffold.
**Expected:** Starter kit code passes Pint out of the box.
**Actual:** 13 test files needed `single_blank_line_at_eof` fixes. Some two-factor tests needed `unary_operator_spaces` and `not_operator_with_successor_space` fixes.
**Resolution:** Pint auto-fixed all issues. No manual intervention needed.
**Severity:** Minor
**Product Insight:** The Vue starter kit ships with code that doesn't pass Pint's default rules. Minor, but noticeable when the first thing you do is run the formatter.

---

<!-- Add new entries above this line, newest first -->
