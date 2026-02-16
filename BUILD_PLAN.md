# LaravelSkills — Build Plan

## Multi-Agent Orchestration with Claude Code

---

## Scope

This is a **POC** to pitch to Taylor Otwell and the Laravel team advisor. The goal is to demonstrate the vision — not ship a production app. Every feature included must contribute to the demo. Everything else is in the [Backlog](#backlog).

**What the POC must show:**
1. A browsable, searchable skills directory pre-populated with real PHP/Laravel skills
2. A social feed aggregating real developer content about Laravel + AI
3. The whole thing running on Laravel Cloud, built entirely with Laravel-native packages
4. It looks and feels like a first-party Laravel product

---

## Agent Architecture

```
┌──────────────────────────────────────────┐
│           LEAD ORCHESTRATOR              │
│  Assigns tasks, coordinates merges,      │
│  validates integration between phases    │
├────────────┬──────────┬─────────┬────────┤
│  BACKEND   │ FRONTEND │ SOCIAL  │   QA   │
│  AGENT     │ AGENT    │ AGENT   │ AGENT  │
│            │          │         │        │
│ Models     │ Vue SFCs │ Adapters│ Pest   │
│ Migrations │ Inertia  │ Queue   │ Factry │
│ Controllers│ Tailwind │ Jobs    │ Seeder │
│ Actions    │ Layouts  │ Filters │ Tests  │
│ Scout      │ Search   │ Rate Lm │        │
└────────────┴──────────┴─────────┴────────┘
```

### Git Worktree Setup

```bash
git worktree add ../ls-backend feature/backend
git worktree add ../ls-frontend feature/frontend
git worktree add ../ls-social feature/social
git worktree add ../ls-tests feature/tests
```

---

## Phase 1: Foundation

**Goal:** Scaffold, database, components, and infrastructure. Nothing fancy — just solid bones.

**Estimated effort:** ~2 hours

### Backend Agent

| # | Task |
|---|------|
| 1.1 | Scaffold Laravel 12 project with Inertia + Vue + Tailwind starter kit |
| 1.2 | Create all migrations: `skills`, `authors`, `categories`, `social_posts`, `users` (with `role` column) |
| 1.3 | Create Eloquent models with `HasUlids`, casts, relationships, basic scopes (`featured()`, `ordered()`) |
| 1.4 | Create PHP backed enums: `Platform`, `Role`, `SkillSort` |
| 1.5 | Configure Scout with database driver |
| 1.6 | Configure Sanctum for SPA auth |

### Frontend Agent

| # | Task |
|---|------|
| 1.7 | Tailwind config with Laravel brand colors. `AppLayout.vue` with nav + footer. |
| 1.8 | Core components: `SkillCard.vue`, `FeedPost.vue`, `SearchBar.vue`, `CategoryFilter.vue`, `CopyButton.vue`, `Pagination.vue` |

### Social Integration Agent

| # | Task |
|---|------|
| 1.9 | Create `SocialPlatformInterface` with `fetch()`, `normalize()`, `filter()` methods |
| 1.10 | Create base `FetchSocialPostsJob` with retry logic and error handling |
| 1.11 | Create `ContentFilterService` — keyword matching for Laravel/PHP/AI terms |

### QA Agent

| # | Task |
|---|------|
| 1.12 | Configure Pest with test database (SQLite) |
| 1.13 | Create factories for all models with realistic data |
| 1.14 | Create `DatabaseSeeder`: 5 categories, 20 skills, 3 authors, 50 social posts, 1 admin |

### Phase 1 Exit Criteria

- [ ] `php artisan migrate` runs clean
- [ ] `php artisan db:seed` populates demo data
- [ ] Layout renders with nav
- [ ] Component stubs render
- [ ] `./vendor/bin/pint` passes

---

## Phase 2: Core Features

**Goal:** All primary pages and APIs working. This is the meat of the POC.

**Estimated effort:** ~4 hours

### Backend Agent

| # | Task |
|---|------|
| 2.1 | Skills API: index (paginated, searchable via Scout, filterable by category/agent), show, store, update, destroy |
| 2.2 | Form requests: `StoreSkillRequest`, `UpdateSkillRequest` |
| 2.3 | API resources: `SkillResource`, `AuthorResource`, `CategoryResource`, `SocialPostResource` |
| 2.4 | Feed API: paginated social posts, filterable by platform |
| 2.5 | Admin endpoints: feature/verify skills, pin/hide feed posts |
| 2.6 | Inertia controllers: pass data to Vue pages. Shared data: categories, auth user. |
| 2.7 | Skills.sh import command: `php artisan skills:import` — fetch PHP/Laravel skills, map to schema, idempotent |

### Frontend Agent

| # | Task |
|---|------|
| 2.8 | **Home page** — Hero with value prop + featured skills grid + recent feed posts |
| 2.9 | **Skills directory** — Search bar + category filter + paginated skill cards + sort toggle |
| 2.10 | **Skill detail** — Full info, install command with copy button, Markdown content rendered, author card |
| 2.11 | **Community feed** — Platform filter tabs + paginated post cards + links to originals |
| 2.12 | **Submit skill** — Auth-gated form: name, description, content (textarea), category, compatible agents, source URL |
| 2.13 | **Auth pages** — Login + register. Clean, minimal. |
| 2.14 | **Docs page** — Static Inertia page: "How to install skills" + "How to create a skill" |

### Social Integration Agent

| # | Task |
|---|------|
| 2.15 | **X adapter** — Search API for Laravel + AI/skills content. Normalize. Handle rate limits. |
| 2.16 | **Bluesky adapter** — AT Protocol search. Normalize. Handle auth/sessions. |
| 2.17 | **YouTube adapter** — Data API v3 search. Pull title, thumbnail, channel. Normalize. |
| 2.18 | **DEV.to adapter** — API search by tags. Pull title, excerpt, author. Normalize. |
| 2.19 | Schedule jobs: X/Bluesky every 15 min, YouTube/DEV.to hourly |
| 2.20 | Deduplication via `platform + platform_id` upsert pattern |

### QA Agent

| # | Task |
|---|------|
| 2.21 | Feature tests: Skills CRUD (list, search, filter, show, create, update, delete) |
| 2.22 | Feature tests: Feed (pagination, platform filter) |
| 2.23 | Feature tests: Auth (login, register, protected routes 401, ownership) |
| 2.24 | Feature tests: Admin routes require admin role |

### Phase 2 Exit Criteria

- [ ] All API endpoints return correct responses
- [ ] Skills search works via Scout database driver
- [ ] Social feed displays posts from at least 2 platforms
- [ ] Skill submission works end-to-end
- [ ] All Pest tests pass
- [ ] Import command populates real skills from skills.sh
- [ ] Pages responsive on mobile
- [ ] `./vendor/bin/pint` passes

---

## Phase 3: Polish + Deploy

**Goal:** Demo-ready. Deployed on Laravel Cloud. Looks first-party.

**Estimated effort:** ~2 hours

### Backend Agent

| # | Task |
|---|------|
| 3.1 | Redis caching: categories (24hr), featured skills (15min), feed (5min) |
| 3.2 | Laravel Cloud config: env vars, queue workers, database, Redis, storage |
| 3.3 | Run import command with real skills.sh data — verify quality |

### Frontend Agent

| # | Task |
|---|------|
| 3.4 | Home page polish: hero copy, spacing, visual hierarchy |
| 3.5 | Empty states: no search results, no feed posts, empty category |
| 3.6 | Loading states: skeleton loaders on cards during navigation |
| 3.7 | OG meta tags on all pages via Inertia `<Head>` |

### Social Integration Agent

| # | Task |
|---|------|
| 3.8 | Error resilience: graceful fallback when APIs are down (stale > empty) |
| 3.9 | Engagement score calc: likes × 1 + reposts × 2 + comments × 3 |

### QA Agent

| # | Task |
|---|------|
| 3.10 | Verify all seeded + imported data renders correctly |
| 3.11 | Performance sanity: homepage < 500ms, search < 500ms |
| 3.12 | Final Pint pass + test suite green |

### Phase 3 Exit Criteria

- [ ] Deployed to Laravel Cloud staging
- [ ] Push-to-deploy working from `staging` branch
- [ ] 50+ real skills imported from skills.sh
- [ ] Social feed pulling real content
- [ ] Cache layers active
- [ ] All pages polished with empty/loading states
- [ ] All tests pass
- [ ] DEVLOG.md captures all findings

---

## Running the Build

### Prerequisites

- Claude Code with Agent Teams enabled (`CLAUDE_CODE_EXPERIMENTAL_AGENT_TEAMS`)
- Claude Max subscription (recommended)
- PHP 8.2+, Composer, Node 20+, npm

### Quick Start

**1. Scaffold**
```bash
composer create-project laravel/laravel laravelskills
cd laravelskills
git init && git add . && git commit -m "chore: initial laravel 12 scaffold"
```

**2. Add project docs**
Copy `CLAUDE.md`, `BUILD_PLAN.md`, and `DEVLOG.md` to project root.

**3. Create worktrees**
```bash
git worktree add ../ls-backend feature/backend
git worktree add ../ls-frontend feature/frontend
git worktree add ../ls-social feature/social
git worktree add ../ls-tests feature/tests
```

**4. Start the lead**
```
You are the lead orchestrator for LaravelSkills. Read CLAUDE.md for context.
Spawn 4 teammates in their worktrees. Assign Phase 1 tasks from BUILD_PLAN.md.
Don't hover — set a timer, check results when done. Merge between phases.
Log anything unexpected in DEVLOG.md.
```

**5. Phase by phase**
Each phase: assign → build → merge → test → next phase.

### Simpler Alternatives

**Subagents:** Single Claude Code session, use `/subagent` for focused tasks. Less coordination overhead.

**Manual parallel:** 4 terminal tabs, each in a worktree. You assign tasks from this plan. Merge yourself.

---

## Backlog

These are features that would strengthen a production version but are **not needed for the POC demo**. Come back to these after the pitch lands.

### Immediate Follow-ups
| Feature | Notes |
|---------|-------|
| **Upgrade to Laravel 13** | Upgrade the moment L13 is tagged. Document the upgrade experience in DEVLOG.md — this is a valuable product insight for the team. Expected: minimal changes (PHP 8.3+ baseline, Symfony 8.0 support, cleanup). |

### Laravel Ecosystem Showcase
| Feature | Package | Why |
|---------|---------|-----|
| Queue monitoring dashboard | **Laravel Horizon** | Visual proof that the social feed workers are running. Impressive for a demo but not required to show the concept. |
| Application performance monitoring | **Laravel Pulse** | Shows request times, slow queries, throughput. Great for production. Overkill for POC. |
| Live form validation | **Laravel Precognition** | Real-time validation on skill submission. Cool UX but submission works fine without it. |
| Feature flags | **Laravel Pennant** | Gate features during rollout. No rollout in a POC. |
| Real-time notifications | **Laravel Reverb** | Notify when new skills are submitted. Test the L13 database driver. Not needed for demo. |

### Additional Features
| Feature | Notes |
|---------|-------|
| Dark mode | Tailwind supports it easily. Not needed for a pitch. |
| Trending algorithm | Weighted score (installs, recency, recent installs). For POC, just sort by `install_count`. |
| Browser E2E tests | Laravel Dusk. Feature tests cover the POC. Add Dusk for production. |
| Rate limiting | API throttle middleware. Not needed when the audience is Taylor, not the public. |
| Dynamic OG images | Auto-generated social share images per skill. Polish item. |
| Admin dashboard | Full admin panel for managing skills/feed. For POC, admin endpoints are enough. |
| Skill versioning | Track skill updates over time. Production feature. |
| Skill dependencies | Skills that require other skills. Production feature. |
| Community voting | Upvote/downvote skills. Production feature. |
| Comments on skills | Discussion threads on skills. Production feature. |
| Webhooks | Notify when skills are added/updated. Production feature. |
| CLI tool | `laravelskills install <skill>` branded CLI. Production feature. |

---

## Appendix: Content Filter Keywords

Match posts containing at least one from each group:

**Group A — Framework (one required):**
laravel, php, eloquent, artisan, blade, livewire, inertia, filament, nova, vapor, forge, laravel cloud, pest, sail

**Group B — AI/Skills (one required):**
ai, skill, skills, agent, claude, cursor, copilot, windsurf, chatgpt, llm, prompt, vibe coding, ai-first, coding agent, claude code

**Or:** Contains the project's branded hashtag.

---

## Appendix: skills.sh Import Strategy

1. Fetch skills.sh leaderboard filtered for `php`, `laravel` tags or known Laravel community authors
2. For each skill, pull Markdown content from GitHub source
3. Map: name, description, content, author, install_command, source_url
4. All imports: `is_official: false`
5. Pull `install_count` from skills.sh if available
6. Idempotent: match on `source_url`, safe to re-run

---

## Appendix: Laravel Cloud Deployment

- **Push-to-deploy:** `main` → production, `staging` → staging
- **Env vars:** Set in Cloud dashboard. Cloud auto-injects DB, Redis, storage creds.
- **Queue workers:** Configure in Cloud dashboard.
- **Postgres + Redis:** One-click provisioned.
- **Custom domain:** Configure in dashboard. SSL auto-provisioned.
- **Note:** If anything about Cloud doesn't work as expected, document it in `DEVLOG.md` — that's a product insight.
