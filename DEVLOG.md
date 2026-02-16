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

### 2026-02-16 — Laravel Cloud First Deploy: Chain of Silent Failures

**Context:** First-time deployment to Laravel Cloud with a Postgres database and Redis store declared in `cloud.yaml`. Multiple issues cascaded into hours of troubleshooting.

**The full chain of problems:**

1. **Build "canceled" with no error** — Turns out billing wasn't set up. No error message, just "canceled." (Logged separately below.)

2. **Data disappeared after each deploy** — Ran `skills:import` successfully, but data vanished on next deploy. Root cause: `DB_CONNECTION` defaulted to `sqlite` (from `.env.example`). The import wrote to an ephemeral sqlite file on the container, which gets destroyed on redeploy. Fix: Set `DB_CONNECTION=pgsql` in Cloud env vars.

3. **`config:cache` in build step cached wrong env** — Build step doesn't have access to runtime env vars (DATABASE_URL, REDIS_URL). Caching config during build locked in defaults (sqlite, etc.). Fix: Move `config:cache` to deploy commands in the dashboard.

4. **Deploy commands vs build commands confusion** — `cloud.yaml` doesn't support a `deploy:` key. Deploy commands must be set in the Cloud dashboard UI. The only documentation of this distinction is buried. We initially put `migrate --force` in `cloud.yaml` under `deploy:`, which was silently ignored.

5. **`db:seed` "command canceled" in Cloud terminal** — Production artisan commands require `--force` flag. The Cloud terminal can't handle interactive confirmation prompts, so commands just fail with "canceled." No hint about adding `--force`.

6. **`fake()` not available in production** — `fakerphp/faker` is a dev dependency. Running a factory-based seeder on Cloud fails with `Call to undefined function fake()`. Obvious in hindsight, but easy to miss when you develop locally where dev deps are installed.

7. **500 error: Redis connection refused** — Declared Redis in `cloud.yaml` (`stores: redis: type: redis`), set `CACHE_STORE=redis` in env vars, but Redis wasn't actually connected/provisioned. The app 500'd on every page because `Cache::remember()` is called in the HomeController. The error was only visible in the Logs tab — the site just showed a generic 500. Fix: Set `CACHE_STORE=database` and `SESSION_DRIVER=database` until Redis is properly linked.

**Resolution:** All issues resolved by: using `database` drivers for cache/session/queue, setting `DB_CONNECTION=pgsql`, moving artisan commands to dashboard deploy commands, and using hardcoded seed data instead of factories.

**Severity:** Blocker (collectively — each issue individually was Notable, but the cascade was brutal)

**Product Insights:**
- **Cloud needs a "first deploy" checklist or wizard.** The gap between "connect repo" and "working app" has too many silent failure modes. A guided setup that validates: billing → env vars → database connection → Redis connection → build → deploy would save hours.
- **`cloud.yaml` should validate unknown keys.** Putting `deploy:` in the YAML was silently ignored. A warning like "unknown key 'deploy' — deploy commands are configured in the dashboard" would have saved 30 minutes.
- **The Cloud terminal should hint about `--force`.** When a command is "canceled" due to a confirmation prompt, the error should say "Add --force to run in non-interactive mode."
- **Resource health should be visible at a glance.** If `cloud.yaml` declares Redis but Redis isn't connected, the dashboard should show a warning, not let you discover it via a 500 error.
- **Default `.env.example` should match Cloud defaults.** The starter kit defaults to `sqlite`/`database` drivers. Cloud injects Postgres and Redis. The mismatch is a trap for every new Cloud user. Either Cloud should auto-set these env vars, or the docs should have a "Required env vars" section.
- **Build vs deploy documentation needs a dedicated page.** "What runs in build (no services) vs deploy (full services) vs runtime" is the single most important Cloud concept and it's not prominently documented.

### 2026-02-16 — Laravel Cloud Build Shows "Canceled" With No Error When No Billing

**Context:** Connected GitHub repo to Laravel Cloud and triggered a build. Build immediately shows status "canceled" with no error log output.
**Expected:** Either a successful build, or a clear error message explaining why the build was canceled.
**Actual:** The build status simply says "canceled." No error log, no build output, no indication of what went wrong. After investigation, the cause was the account having no credit card/payment method attached.
**Resolution:** Need to add a payment method to the Laravel Cloud account. The `cloud.yaml` config and codebase are fine — this is purely a billing gate.
**Severity:** Notable
**Product Insight:** When a Laravel Cloud build is canceled due to missing billing information, the UI should display a clear, actionable notice like "Build canceled: please add a payment method to enable deployments." The current UX of showing "canceled" with no explanation forces developers to troubleshoot their config files, Dockerfiles, and code when the issue is entirely on the billing side. This is a significant DX gap — especially painful for new Cloud users during onboarding.

### 2026-02-16 — cloud.yaml: migrate --force Should Not Be in Build Step

**Context:** While investigating the Cloud build issue, reviewed `cloud.yaml` and found `php artisan migrate --force` listed under `build:` commands.
**Expected:** Migrations run after the build, when the database is provisioned and accessible.
**Actual:** Having `migrate` in the build step means it would run during container image creation, before the database is available. This will fail on first deploy.
**Resolution:** Will move `migrate --force` from `build:` to a `deploy:` section once Cloud billing is resolved and we can test deployments. The build step should only contain asset compilation and config caching.
**Severity:** Notable
**Product Insight:** The Laravel Cloud docs could benefit from a "common mistakes" section highlighting that database-dependent commands (migrate, db:seed) should not be in the build step. Many developers coming from Forge/Vapor may not realize the distinction.

### 2026-02-16 — Pint Fixes in Starter Kit Test Files

**Context:** Ran `./vendor/bin/pint --parallel` immediately after scaffold.
**Expected:** Starter kit code passes Pint out of the box.
**Actual:** 13 test files needed `single_blank_line_at_eof` fixes. Some two-factor tests needed `unary_operator_spaces` and `not_operator_with_successor_space` fixes.
**Resolution:** Pint auto-fixed all issues. No manual intervention needed.
**Severity:** Minor
**Product Insight:** The Vue starter kit ships with code that doesn't pass Pint's default rules. Minor, but noticeable when the first thing you do is run the formatter.

---

<!-- Add new entries above this line, newest first -->
