# URL Shortener with Analytics

A hands-on Docker and Kubernetes tutorial. We build a small URL shortener with click analytics, learning container and cluster concepts phase by phase.

## Stack

| Component | Technology |
|-----------|------------|
| API | Laravel 12 (PHP 8.2+) |
| Worker | Same Laravel image, `php artisan queue:work` |
| Frontend | Static HTML/JS + nginx |
| Database | PostgreSQL |
| Cache / queue | Redis |
| Local Kubernetes | k3d |

**Not used:** Laravel Sail (removed intentionally — we write our own Docker/K8s config).

## Project layout

```
.
├── api/                 # Laravel API (links, redirects, stats)
├── frontend/            # Static UI + nginx (Phase 4)
├── cron/                # Rollup script for CronJob (Phase 10)
├── docker-compose.yml   # Local multi-container stack (Phase 2)
└── k8s/base/            # Kubernetes manifests (Phase 5+)
```

The **worker** reuses the `api/` Docker image with a different start command — no separate worker folder.

## Prerequisites

- Docker
- kubectl
- k3d (local cluster — needed from Phase 5)
- k9s (optional, for watching the cluster)
- PHP 8.2+ and Composer (for local Laravel dev without Docker)

## Tutorial phases

| Phase | Topic | Status |
|-------|-------|--------|
| 0 | Project layout, prerequisites | **Done** |
| 1 | Laravel API in Docker (in-memory store) | Done |
| 2 | docker-compose: API + Postgres + Redis | Done |
| 3 | Queue worker (`queue:work`) | **Done** |
| 4 | Frontend (nginx + static HTML/JS) | Next |
| 5 | Kubernetes: Deployment + Service | |
| 6 | ConfigMap, Secret, probes | |
| 7 | Postgres + Redis in K8s (StatefulSet, PVC) | |
| 8 | Worker, Frontend, Ingress | |
| 9 | Job: database migrations | |
| 10 | CronJob: analytics rollup | |
| 11 | HPA, resource limits, rollouts | |

## k3d cluster (Phase 5)

When we reach Kubernetes, create a local cluster:

```bash
k3d cluster create url-shortener \
  --agents 1 \
  -p "8080:80@loadbalancer"

kubectl config use-context k3d-url-shortener
```

**Important:** Always verify your kubectl context before applying manifests. Do not deploy to production clusters.

## Local development (current)

Laravel app lives in `api/`:

```bash
cd api
cp .env.example .env   # if needed
php artisan key:generate
php artisan serve
```

Docker and compose workflows are added in Phases 1–2.
