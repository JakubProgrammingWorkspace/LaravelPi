# HR-Portal Implementation Plan

> Project: **HR-Portal** — A Polish HR management application (Users & flexible Roles, Companies, Employees, an occupational exposure factors catalogue, and standardized medical examination referrals with PDF generation).
> Tech stack: Symfony 6 + API Platform (PHP 8.5) backend + React + Tailwind CSS frontend, running via Docker Compose.
>
> **Note:** The UI labels, form fields, error messages, and entity properties are in **Polish**. Keep this in mind when building the frontend. All implementation instructions below remain in English.

---

## Phase 0 — Infrastructure

- [ ] **US-001:** Set up Docker Compose environment (`php`, `nginx`, `postgres`, `node` services), `.env.example`, and `README` instructions.

---

## Phase 1 — Backend Skeleton

- [ ] **US-002:** Scaffold Symfony 6.4 + API Platform (JSON format) under `backend/`, configure CORS, Doctrine ORM, health check (`GET /api/health`), and `composer install`.

---

## Phase 2 — Frontend Skeleton

- [ ] **US-003:** Scaffold React + Vite + TypeScript + Tailwind CSS under `frontend/`, configure React Router with placeholder routes (`/login`, `/dashboard`, `/users`, `/companies`, `/employees`, `/exposure-factors`, `/referrals`), and a shared layout (sidebar nav + top bar).

---

## Phase 3 — Users & Roles (Backend)

- [ ] **US-004:** Create `User` and `Role` entities with many-to-many relationship, implement `UserInterface`/`PasswordAuthenticatedUserInterface`, write `User::getRoles()`, generate and run Doctrine migration.

- [ ] **US-005:** Create seed fixtures: `ROLE_ADMIN` ("Administrator"), `ROLE_USER` ("Użytkownik"), and a default admin user (`admin@hr-portal.local`) with a properly hashed password.

- [ ] **US-006:** Implement JWT authentication via `lexik/jwt-authentication-bundle`: `POST /api/login` returning JWT, custom `UserChecker` rejecting inactive users, route protection, configurable token TTL.

- [ ] **US-007:** Build a `UserManagementVoter` (or equivalent) restricting `/api/users` and `/api/roles` operations to `ROLE_ADMIN` only.

- [ ] **US-009:** Build User CRUD API (`GET /api/users`, `POST`, `PATCH`, `DELETE`) with a custom state processor that hashes passwords, excludes password from output, and prevents admins from deleting/deactivating themselves (409/422 with Polish error).

- [ ] **US-010:** Build Role API (`GET /api/roles`, `POST`) with unique `code` validation (`ROLE_*` pattern), return 422 on violations.

---

## Phase 4 — Users & Roles (Frontend)

- [ ] **US-008:** Build Login page (`/login`): email + password fields, JWT storage, redirect to `/dashboard` on success, inline Polish error ("Nieprawidłowy e-mail lub hasło") on failure, centralized API client with `Authorization: Bearer`, "Wyloguj" button.

- [ ] **US-011:** Build User management panel (`/users`): list users (email, name, roles, active status), "Add user" form/modal (email, password, first/last name, multi-select roles from `/api/roles"), "Remove"/"Deactivate" with confirmation, role editing per row, "Użytkownicy" nav item visible only for `ROLE_ADMIN`, redirect non-admins to `/dashboard`).

---

## Phase 5 — Companies

- [ ] **US-012:** Create `Company` entity (`id`, `name`, `nip` — 10-digit Polish NIP validation, `street`, `city`, `postalCode`, `createdAt`), expose full CRUD API (`GET`, `POST`, `PATCH`, `DELETE`) for authenticated users, business logic in `CompanyService`.

- [ ] **US-013:** Build Company management panel (`/companies`): list companies (name, NIP, address), "Add company" form (name, NIP, street, city, postal code) with client-side validation mirroring backend constraints, "Remove" with confirmation, Polish validation/error messages.

---

## Phase 6 — Employees

- [ ] **US-014:** Create `Employee` entity (`id`, `firstName`, `lastName`, `pesel` — 11-digit Polish PESEL validation, `phone`, `email`, `address`, `company` — ManyToOne, `createdBy` — ManyToOne to User, `createdAt`), expose full CRUD API, `createdBy` set via state processor, `SearchFilter` on `firstName`, `lastName`, `company`.

- [ ] **US-015:** Build Employee management panel (`/employees`): list (name, phone, email, address, company name) with search and filter by company, "Add employee" form (Imię, Nazwisko, PESEL, Telefon, E-mail, Adres, Company dropdown), client-side PESED/email validation with Polish messages, "Remove" with confirmation.

---

## Phase 7 — Exposure Factors (Categories + Factors)

- [ ] **US-016:** Create `ExposureCategory` (`id`, `code` I-V, name, `sortOrder`) and `ExposureFactor` (`id`, `name`, `category` — ManyToOne, `description`) entities, generate and run Doctrine migration.

- [ ] **US-017:** Create seed fixtures for exactly 5 `ExposureCategory` records matching Annex 3 of *Rozporządzenie Ministra Zdrowia i Opieki Społecznej z dnia 30 maja 1996 r.*:
  - [ ] Category I: Czynniki fizyczne (with factors: Hałas, Hałas ultradźwiękowy, Wibracje ogólne/miejscowe, Mikroklimat gorący/zimny, Promieniowanie optyczne, Promieniowanie laserowe, Promieniowanie jonizujące, Pole elektromagnetyczne, Praca przy monitorach >4h)
  - [ ] Category II: Pyły (with factors: azbest, krzemionka, drewna, rud metali, węgla, mączne, bawełny, zwierzęce/roślinne)
  - [ ] Category III: Czynniki chemiczne (with factors: toksyczne, drażniące, uczulające, rakotwórcze/mutagenne, szkodliwe na rozrodczość, rozpuszczalniki, metale, pestycydy, tlenku węgla/azotu)
  - [ ] Category IV: Czynniki biologiczne (with factors: Wirusy, Bakterie, Grzyby, Pasożyty, Materiał biologiczny ludzki, Mikroorganizmy rolnicze/leśne, Kontakt ze zwierzętami)
  - [ ] Category V: Inne czynniki, w tym niebezpieczne (with factors: Praca na wysokości, Obsługa maszyn, Wymuszona pozycja, Obciążenie statyczne/dynamiczne, Praca nocna/zmianowa, Obciążenie psychiczne, Praca przy urządzeniach pod napięciem, Widoczność, Ryzyko poślizgnięcia)

- [ ] **US-018:** Build Exposure factor API: `GET /api/exposure-categories` (read-only, ordered by `sortOrder`), `GET/POST/DELETE /api/exposure-factors` with `409` guard via `ExposureFactorService` when factor is referenced.

---

## Phase 8 — Exposure Factors (Frontend)

- [ ] **US-019:** Build "Czynniki narażeń" panel (`/exposure-factors`): render 5 categories (I–V) as sections/cards listing factors, "Add factor" inline form per category (name + optional description), "Remove" with confirmation + Polish 409 error if in use, category headers not editable/removable.

---

## Phase 9 — Referrals (Backend)

- [ ] **US-020:** Create `Referral` entity (`id`, `employee` — ManyToOne, `examinationType` enum: wstępne/okresowe/kontrolne, `jobPosition`, `jobDescription`, `issuePlace`, `issueDate`, `createdBy` — ManyToOne to User, `pdfPath`, `pdfGeneratedAt`, `createdAt`) and `ReferralExposureFactor` join entity (`id`, `referral`, `factor`, `exposureDetails`). Generate migration. Update `ExposureFactorService::delete` to check `ReferralExposureFactor` references.

- [ ] **US-021:** Build Referral API: `GET /api/referrals` (paginated, ordered by `issueDate` desc), `GET/POST/DELETE /api/referrals/{id}`. `POST` accepts employee IRI, exam type, job position, job description, issue place, issue date, and array of `{factor: IRI, exposureDetails: string}`; creates `Referral` + `ReferralExposureFactor` rows in one transaction via `ReferralService`. `createdBy` auto-set via state processor. Validation on `examinationType` enum and existing employee.

- [ ] **US-023:** Build PDF generation service: install `dompdf/dompdf`, create Twig template `referral_pdf.html.twig` matching official "Skierowanie na badania lekarskie" layout (employer designation + place/date header, title "SKIEROWANIE NA BADANIA LEKARSKIE (wstępne/okresowe/kontrolne)", employee name + PESEL + address, job position/description, sections I–V listing selected factors with exposure details, total factor count, signature line). `ReferralPdfGeneratorService::generate(Referral): string` writes PDF to configurable storage path.

- [ ] **US-024:** Build PDF endpoints: `POST /api/referrals/{id}/generate-pdf` (custom operation → `ReferralPdfGeneratorService`), sets `pdfPath` and `pdfGeneratedAt`. `GET /api/referrals/{id}/pdf` streams file, returns 404 if no PDF generated.

---

## Phase 10 — Referrals (Frontend)

- [ ] **US-022:** Build Referral creation form at `/referrals`: fields (Pracownik select/search, Typ badania wstępne/okresowe/kontrolne, Stanowisko pracy, Opis warunków pracy/tekst, Miejsce wystawienia, Data wystawienia defaulting to today), 5 exposure categories with checkboxes per factor (checking reveals "Wielkość narażenia / wyniki pomiarów" text input), calls `POST /api/referrals`, required-field validation with Polish errors, form resets on success.

- [ ] **US-025:** Build Referral list panel (`/referrals`): shows creation form (from US-022) at top, table listing (Pracownik, Typ badania, Stanowisko, Data wystawienia, Status PDF: Wygenerowano/Nie wygenerowano), "Generuj PDF" button per row → "Pobierz PDF" link, paginated list matching API Platform page size.

---

## Phase 11 — App Shell & Dashboard

- [ ] **US-026:** Build app shell, navigation, and route guards: sidebar links (Dashboard, Użytkownicy admin-only, Firmy, Pracownicy, Czynniki narażeń, Skierowania), top bar with logged-in user's name/email and "Wyloguj" button, all panel routes wrapped in auth guard redirecting to `/login`.

- [ ] **US-027:** Build Dashboard landing page (`/dashboard`): show counts (total Companies, total Employees, total Referrals) fetched from existing collection endpoints via API Platform's `totalItems`.

---

## Summary

| Phase | User Stories | Status |
|-------|-------------|--------|
| 0 — Infrastructure | US-001 | ☐ |
| 1 — Backend Skeleton | US-002 | ☐ |
| 2 — Frontend Skeleton | US-003 | ☐ |
| 3 — Users & Roles (Backend) | US-004, US-005, US-006, US-007, US-009, US-010 | ☐ |
| 4 — Users & Roles (Frontend) | US-008, US-011 | ☐ |
| 5 — Companies | US-012, US-013 | ☐ |
| 6 — Employees | US-014, US-015 | ☐ |
| 7 — Exposure Factors (Backend) | US-016, US-017, US-018 | ☐ |
| 8 — Exposure Factors (Frontend) | US-019 | ☐ |
| 9 — Referrals (Backend) | US-020, US-021, US-023, US-024 | ☐ |
| 10 — Referrals (Frontend) | US-022, US-025 | ☐ |
| 11 — App Shell & Dashboard | US-026, US-027 | ☐ |

Total: **27 user stories**, ordered by priority (1–27).
