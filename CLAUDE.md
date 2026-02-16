# LaravelSkills — Project Intelligence

> A Laravel-branded AI agent skills directory + community social feed.
> Built on Laravel 12 (PHP 8.2+), deployed on Laravel Cloud.
> Upgrade to Laravel 13 the moment it's released (expected Q1 2026).
> **This is a POC to pitch to Taylor Otwell. Ship fast, ship clean, ship Laravel-native.**

---

## Project Overview

LaravelSkills is a two-pillar platform:

1. **Skills Directory** — A curated, searchable directory of AI agent skills focused on Laravel and PHP. Think skills.sh, but branded, quality-controlled, and Laravel-native. Developers discover, install, and contribute reusable agent skills (Markdown-based procedural knowledge for AI coding assistants like Claude Code, Cursor, etc.).

2. **Community Feed** — An aggregated social feed pulling from X, Bluesky, YouTube, and DEV.to. Filters for content tagged with Laravel, PHP, AI skills, and related topics. Celebrates developers building with Laravel and lowers the barrier to AI-powered Laravel development.

### Strategic Context

- This is a Laravel first-party product concept, not a third-party tool
- Positions Laravel as an "AI-first" ecosystem alongside Next.js/Vercel
- Community flywheel: celebrate builders → more building → more content → more discovery
- Dev Rel team activity: low maintenance, high community engagement
- Pre-populated with existing PHP/Laravel skills imported from skills.sh

### Guiding Principles

1. **Laravel-native only.** Every dependency must be a Laravel first-party or core-team package. No external services. This POC demonstrates the completeness of the Laravel ecosystem.
2. **Ship the POC.** This is a pitch concept, not a production app. Impressive but not over-engineered. If it's not needed to demo the vision, it's in the backlog.
3. **Always use latest.** Laravel 12 now, 13 the day it drops. Latest package versions. We're dogfooding. Bugs found = product insights.
4. **Log everything.** Unexpected behavior, workarounds, quirks — all go in `DEVLOG.md`. This project produces product insights, not just code.

---

## Tech Stack

| Layer | Technology | Notes |
|-------|-----------|-------|
| Framework | Laravel 12 | PHP 8.2+. Upgrade to 13 (PHP 8.3+) on release. |
| Frontend | Vue 3 + Inertia.js | SFCs with `<script setup>`. |
| Styling | Tailwind CSS 4 | Laravel default preset. |
| Database | PostgreSQL | Managed via Laravel Cloud. |
| Search | Laravel Scout (database driver) | Postgres full-text search. No external search service. |
| Cache | Redis | Laravel Cloud managed key-value store. |
| Queue | Laravel Cloud Queue Workers | Native Cloud workers for social feed jobs. |
| Storage | Laravel Cloud S3-compatible | Skill assets if needed. |
| Auth | Laravel Sanctum | API auth + SPA auth via Inertia. |
| Testing | Pest PHP | Unit + feature tests. |
| Code Style | Laravel Pint | Zero-config. Run before every commit. |
| Hosting | Laravel Cloud | Auto-scaling, push-to-deploy, managed infra. |

---

## Database Schema (PostgreSQL)

### skills
- `id` (ulid, primary)
- `name` (string, unique, indexed)
- `slug` (string, unique, indexed)
- `description` (text)
- `install_command` (string)
- `content` (text) — Full skill Markdown
- `author_id` (foreign → authors)
- `category_id` (foreign → categories, nullable)
- `source_url` (string, nullable)
- `is_official` (boolean, default false)
- `is_featured` (boolean, default false)
- `install_count` (integer, default 0)
- `compatible_agents` (json)
- `tags` (json)
- `created_at`, `updated_at`

### authors
- `id` (ulid, primary)
- `name` (string)
- `slug` (string, unique)
- `github_username` (string, nullable)
- `avatar_url` (string, nullable)
- `bio` (text, nullable)
- `user_id` (foreign → users, nullable)
- `created_at`, `updated_at`

### categories
- `id` (ulid, primary)
- `name` (string, unique)
- `slug` (string, unique)
- `description` (text, nullable)
- `sort_order` (integer, default 0)
- `created_at`, `updated_at`

### social_posts
- `id` (ulid, primary)
- `platform` (enum: x, bluesky, youtube, devto)
- `platform_id` (string)
- `author_name` (string)
- `author_handle` (string)
- `author_avatar_url` (string, nullable)
- `content` (text)
- `media_url` (string, nullable)
- `post_url` (string)
- `engagement_score` (integer, default 0)
- `is_featured` (boolean, default false)
- `is_hidden` (boolean, default false)
- `published_at` (timestamp)
- `fetched_at` (timestamp)
- `created_at`, `updated_at`

### Indexes
- Unique composite: `social_posts(platform, platform_id)`
- Index: `social_posts(published_at)`
- Index: `skills(install_count)`

### users
Standard Laravel auth with Sanctum. Simple `role` column (enum: `admin`, `contributor`).

---

## API Contracts

All routes prefixed `/api/v1/`.

**Public:**
- `GET /skills` — Paginated, searchable, filterable. Params: `search`, `category`, `agent`, `sort` (newest|installs), `per_page`
- `GET /skills/{slug}` — Skill detail
- `GET /categories` — All categories
- `GET /feed` — Paginated social feed. Params: `platform`, `per_page`

**Authenticated (Sanctum):**
- `POST /skills` — Submit a skill
- `PUT /skills/{slug}` — Edit own skill
- `DELETE /skills/{slug}` — Delete own skill

**Admin:**
- `PUT /skills/{slug}/feature` — Toggle featured
- `PUT /skills/{slug}/verify` — Mark as official
- `PUT /feed/{id}/feature` — Pin social post
- `PUT /feed/{id}/hide` — Hide social post

---

## Frontend Routes (Inertia)

- `/` — Home (featured skills + trending feed)
- `/skills` — Skills directory with search/filter
- `/skills/{slug}` — Skill detail
- `/feed` — Community feed
- `/submit` — Submit a skill (auth required)
- `/docs` — How to create/install skills
- `/login`, `/register` — Auth

---

## Coding Standards

### PHP / Laravel
- `declare(strict_types=1);` in every file
- Always declare return types
- Actions pattern for business logic
- Form Requests for validation
- API Resources for JSON responses
- PHP backed enums
- `HasUlids` on all models
- PSR-12. Run `./vendor/bin/pint` before commits.

### Vue / Frontend
- `<script setup>` only
- PascalCase components (`SkillCard.vue`, `FeedPost.vue`)
- Tailwind only, no custom CSS

### Testing
- Pest PHP for everything
- Feature tests for every API endpoint
- Factories for every model

### Git
- Branches: `feature/`, `fix/`, `chore/`
- Commits: `feat:`, `fix:`, `chore:`, `test:`, `docs:`

---

## Agent Instructions

### Backend Agent
- Own migrations, models, controllers, actions, form requests, resources
- Scout database driver only — no Meilisearch
- Build the skills.sh import command
- Queue jobs for social feed

### Frontend Agent
- Own Vue components, Inertia pages, layouts
- SkillCard, FeedPost, SearchBar, FilterSidebar
- Mobile-first responsive
- OG meta via Inertia `<Head>`

### Social Integration Agent
- Adapter pattern: `SocialPlatformInterface` → platform adapters
- Normalize to `social_posts` schema
- Keyword filtering for Laravel/PHP/AI
- Rate limit handling per platform

### QA Agent
- Pest suite + factories + seeder
- Feature tests for every endpoint
- Realistic seed data for demos

---

## Reminders

- **POC scope.** Backlog lives in `BUILD_PLAN.md`.
- **Laravel-native only.** No external services.
- **Log findings in `DEVLOG.md`.** Date, context, resolution.
- **Laravel Cloud only.** No Forge/Vapor references.
- **Impress Taylor.** First-party quality.
