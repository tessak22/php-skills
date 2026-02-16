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

---

<!-- Add new entries above this line, newest first -->
