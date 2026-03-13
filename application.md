# 🎓 Architecture — Application de Gestion d'Événements Universitaires
> **Stack :** Laravel 11 · Bootstrap 5 · MySQL · Queue/Scheduler · Email · WhatsApp

---

## 📁 Structure Racine du Projet

```
gestion-evenements-faculte/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
├── .env.example
├── artisan
├── composer.json
└── package.json
```

---

## 📂 app/ — Cœur de l'Application

```
app/
│
├── Console/
│   ├── Kernel.php                          # Enregistrement des tâches schedulées
│   └── Commands/
│       └── SendPendingAlerts.php           # php artisan alerts:send-pending
│
├── Exceptions/
│   └── Handler.php
│
├── Http/
│   ├── Kernel.php                          # Enregistrement des middlewares
│   │
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php         # Login / Logout
│   │   │   ├── ForgotPasswordController.php
│   │   │   └── ResetPasswordController.php
│   │   │
│   │   ├── DashboardController.php         # Page d'accueil manager
│   │   ├── EventController.php             # CRUD Événements
│   │   ├── AlertController.php             # Envoi et gestion des alertes
│   │   ├── UserController.php              # Gestion des utilisateurs
│   │   ├── RoleController.php              # Gestion des rôles & permissions
│   │   ├── ProfileController.php           # Profil utilisateur (7 onglets)
│   │   ├── TeamController.php              # Annuaire + Organigramme + Groupes
│   │   ├── LeaveController.php             # Congés & absences
│   │   ├── EvaluationController.php        # Évaluations des membres
│   │   └── DocumentController.php          # Upload & gestion documents RH
│   │
│   └── Middleware/
│       ├── Authenticate.php
│       ├── CheckRole.php                   # Vérifie le rôle de l'utilisateur
│       └── CheckPermission.php             # Vérifie les permissions granulaires
│
├── Jobs/
│   ├── SendEmailAlertJob.php               # Job d'envoi email en queue
│   └── SendWhatsAppAlertJob.php            # Job d'envoi WhatsApp en queue
│
├── Mail/
│   └── EventAlertMail.php                  # Mailable pour les alertes email
│
├── Models/
│   ├── User.php                            # Relations: profile, role, events, leaves
│   ├── Role.php                            # Relations: permissions, users
│   ├── Permission.php                      # Relations: roles
│   ├── Department.php                      # Relations: users, events
│   │
│   ├── Event.php                           # Relations: assignments, alerts, creator
│   ├── EventAssignment.php                 # Pivot: event <-> user avec rôle
│   │
│   ├── Alert.php                           # Relations: event, logs
│   ├── AlertLog.php                        # Historique d'envoi par canal
│   │
│   ├── Profile.php                         # Infos personnelles étendues
│   ├── AcademicInfo.php                    # Grade, spécialité, bureau
│   ├── Skill.php                           # Compétences disponibles
│   ├── Experience.php                      # Expériences professionnelles
│   ├── Document.php                        # Documents RH uploadés
│   ├── Leave.php                           # Demandes de congés
│   ├── Evaluation.php                      # Évaluations périodiques
│   └── TeamGroup.php                       # Groupes/sous-équipes
│
├── Notifications/
│   └── EventReminderNotification.php       # Notification Laravel native
│
├── Policies/
│   ├── EventPolicy.php
│   ├── UserPolicy.php
│   └── LeavePolicy.php
│
└── Services/
    ├── AlertService.php                    # Orchestration des envois
    └── WhatsAppService.php                 # Intégration Twilio / CallMeBot
```

---

## 📂 database/ — Base de Données

```
database/
│
├── migrations/
│   ├── 2024_01_01_create_roles_table.php
│   ├── 2024_01_02_create_permissions_table.php
│   ├── 2024_01_03_create_role_permission_table.php
│   ├── 2024_01_04_create_departments_table.php
│   ├── 2024_01_05_create_users_table.php
│   ├── 2024_01_06_create_events_table.php
│   ├── 2024_01_07_create_event_assignments_table.php
│   ├── 2024_01_08_create_alerts_table.php
│   ├── 2024_01_09_create_alert_logs_table.php
│   ├── 2024_01_10_create_profiles_table.php
│   ├── 2024_01_11_create_academic_infos_table.php
│   ├── 2024_01_12_create_skills_table.php
│   ├── 2024_01_13_create_user_skills_table.php
│   ├── 2024_01_14_create_experiences_table.php
│   ├── 2024_01_15_create_documents_table.php
│   ├── 2024_01_16_create_leaves_table.php
│   ├── 2024_01_17_create_evaluations_table.php
│   ├── 2024_01_18_create_team_groups_table.php
│   ├── 2024_01_19_create_team_group_members_table.php
│   └── 2024_01_20_create_notifications_table.php
│
├── seeders/
│   ├── DatabaseSeeder.php
│   ├── RoleSeeder.php                      # 5 rôles + permissions
│   ├── DepartmentSeeder.php                # 3 départements
│   ├── UserSeeder.php                      # Admin + 5 membres test
│   ├── ProfileSeeder.php                   # Profils complets pour chaque user
│   ├── SkillSeeder.php                     # Compétences types
│   ├── EventSeeder.php                     # 10 événements variés
│   └── AlertSeeder.php                     # Alertes en attente
│
└── factories/
    ├── UserFactory.php
    ├── EventFactory.php
    └── ProfileFactory.php
```

---

## 📂 resources/ — Vues & Assets

```
resources/
│
├── views/
│   │
│   ├── layouts/
│   │   ├── app.blade.php                   # Layout principal (sidebar + navbar)
│   │   ├── auth.blade.php                  # Layout pages d'auth (centré)
│   │   └── partials/
│   │       ├── sidebar.blade.php           # Menu latéral avec rôles
│   │       ├── navbar.blade.php            # Barre top + badge notifs
│   │       ├── footer.blade.php
│   │       └── alerts-flash.blade.php      # Messages succès/erreur
│   │
│   ├── auth/
│   │   ├── login.blade.php
│   │   ├── forgot-password.blade.php
│   │   └── reset-password.blade.php
│   │
│   ├── dashboard/
│   │   └── index.blade.php                 # Dashboard manager complet
│   │
│   ├── events/
│   │   ├── index.blade.php                 # Liste + filtres + vue calendrier
│   │   ├── create.blade.php                # Wizard 4 étapes
│   │   ├── edit.blade.php
│   │   └── show.blade.php                  # Détail + timeline + alertes
│   │
│   ├── alerts/
│   │   ├── index.blade.php                 # Liste des alertes programmées
│   │   ├── create.blade.php                # Formulaire envoi manuel
│   │   └── logs.blade.php                  # Historique complet des envois
│   │
│   ├── users/
│   │   ├── index.blade.php                 # Liste + filtres + statut
│   │   ├── create.blade.php
│   │   └── edit.blade.php
│   │
│   ├── roles/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   └── edit.blade.php                  # Permissions par checkbox
│   │
│   ├── profile/
│   │   ├── show.blade.php                  # 7 onglets Bootstrap
│   │   ├── edit.blade.php
│   │   └── partials/
│   │       ├── tab-personal.blade.php      # Onglet 1 : Infos personnelles
│   │       ├── tab-academic.blade.php      # Onglet 2 : Profil académique
│   │       ├── tab-skills.blade.php        # Onglet 3 : Compétences
│   │       ├── tab-experiences.blade.php   # Onglet 4 : Expériences (timeline)
│   │       ├── tab-documents.blade.php     # Onglet 5 : Documents RH
│   │       ├── tab-leaves.blade.php        # Onglet 6 : Congés
│   │       └── tab-evaluations.blade.php   # Onglet 7 : Évaluations (radar)
│   │
│   ├── team/
│   │   ├── index.blade.php                 # Annuaire (cards / tableau)
│   │   ├── orgchart.blade.php              # Organigramme hiérarchique
│   │   └── groups.blade.php                # Gestion sous-équipes
│   │
│   ├── leaves/
│   │   ├── index.blade.php                 # Liste + approbation manager
│   │   └── create.blade.php
│   │
│   ├── evaluations/
│   │   ├── index.blade.php
│   │   └── create.blade.php
│   │
│   └── emails/
│       └── alert-template.blade.php        # Template HTML email stylisé
│
├── css/
│   └── app.css                             # Styles custom + variables couleurs
│
└── js/
    ├── app.js                              # Entry point JS
    ├── calendar.js                         # FullCalendar init
    ├── charts.js                           # Chart.js (Bar, Donut, Radar)
    ├── orgchart.js                         # Organigramme
    └── notifications.js                    # Polling AJAX notifications
```

---

## 📂 routes/ — Définition des Routes

```
routes/
│
├── web.php
│   ├── Auth routes          → /login, /logout, /forgot-password, /reset-password
│   ├── Dashboard            → GET  /dashboard
│   │
│   ├── Events               → /events (index, create, store, show, edit, update, destroy)
│   ├── Alerts               → /alerts (index, create, store, logs)
│   │
│   ├── Users                → /users (CRUD complet)
│   ├── Roles                → /roles (CRUD + permissions)
│   │
│   ├── Profile              → /profile (show, edit, update, upload-avatar)
│   ├── Team                 → /team (index, orgchart, groups CRUD)
│   ├── Leaves               → /leaves (index, create, store, approve, reject)
│   ├── Evaluations          → /evaluations (index, create, store, show)
│   └── Documents            → /documents (index, store, download, destroy)
│
└── api.php
    ├── GET  /api/events         → JSON pour FullCalendar
    ├── GET  /api/notifications  → Polling notifications
    ├── POST /api/skills         → Ajout compétence AJAX
    └── GET  /api/members        → Liste membres pour Select2
```

---

## 📂 config/ — Configuration

```
config/
├── app.php
├── auth.php
├── database.php
├── mail.php
├── queue.php
└── services.php                # Twilio, CallMeBot credentials
```

---

## 🗄️ Schéma des Relations entre Tables

```
users ──────────────── roles (N:1)
users ──────────────── departments (N:1)
users ──────────────── profile (1:1)
users ──────────────── academic_infos (1:1)
users ──────────────── skills (N:M) via user_skills
users ──────────────── experiences (1:N)
users ──────────────── documents (1:N)
users ──────────────── leaves (1:N)
users ──────────────── evaluations (1:N)
users ──────────────── team_groups (N:M) via team_group_members

roles ──────────────── permissions (N:M) via role_permission

events ─────────────── users (N:M) via event_assignments
events ─────────────── alerts (1:N)
events ─────────────── departments (N:1)

alerts ─────────────── alert_logs (1:N)
alert_logs ─────────── users (N:1)
```

---

## 🔐 Matrice des Rôles & Accès

| Fonctionnalité              | Manager | Chef Dept | Enseignant | Technicien | Secrétaire |
|-----------------------------|:-------:|:---------:|:----------:|:----------:|:----------:|
| Dashboard complet           | ✅      | ✅        | ❌         | ❌         | ❌         |
| Créer un événement          | ✅      | ✅        | ❌         | ❌         | ✅         |
| Voir les événements         | ✅      | ✅        | ✅         | ✅         | ✅         |
| Envoyer des alertes         | ✅      | ✅        | ❌         | ❌         | ❌         |
| Gérer les utilisateurs      | ✅      | ❌        | ❌         | ❌         | ❌         |
| Gérer les rôles             | ✅      | ❌        | ❌         | ❌         | ❌         |
| Voir tous les profils       | ✅      | ✅        | ❌         | ❌         | ❌         |
| Modifier son propre profil  | ✅      | ✅        | ✅         | ✅         | ✅         |
| Approuver les congés        | ✅      | ✅        | ❌         | ❌         | ❌         |
| Créer des évaluations       | ✅      | ✅        | ❌         | ❌         | ❌         |
| Gérer les groupes           | ✅      | ❌        | ❌         | ❌         | ❌         |
| Annuaire équipe             | ✅      | ✅        | ✅         | ✅         | ✅         |
| Exporter rapports           | ✅      | ✅        | ❌         | ❌         | ❌         |

---

## 📦 Dépendances — composer.json

```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^11.0",
    "laravel/tinker": "^2.9",
    "twilio/sdk": "^8.0",
    "barryvdh/laravel-dompdf": "^2.0",
    "maatwebsite/excel": "^3.1",
    "intervention/image": "^3.0"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/pint": "^1.0",
    "phpunit/phpunit": "^11.0"
  }
}
```

---

## 📦 Dépendances — package.json (JS/CSS)

```json
{
  "devDependencies": {
    "vite": "^5.0",
    "@vitejs/plugin-vue": "^5.0",
    "bootstrap": "^5.3",
    "sass": "^1.70"
  },
  "dependencies": {
    "fullcalendar": "^6.1",
    "chart.js": "^4.4",
    "cropperjs": "^1.6",
    "dropzone": "^6.0",
    "select2": "^4.1",
    "sweetalert2": "^11.0",
    "datatables.net-bs5": "^2.0",
    "orgchart": "^3.8"
  }
}
```

---

## ⚙️ Variables d'Environnement — .env.example

```env
APP_NAME="Gestion Événements Faculté"
APP_ENV=local
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_faculte
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@faculte.ma
MAIL_FROM_NAME="Faculté - Gestion Événements"

QUEUE_CONNECTION=database

# Twilio WhatsApp
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886

# OU CallMeBot (gratuit)
CALLMEBOT_API_KEY=your_api_key
```

---

## 🚀 Commandes d'Installation

```bash
# 1. Cloner et installer
git clone https://github.com/votre-repo/gestion-evenements-faculte.git
cd gestion-evenements-faculte
composer install
cp .env.example .env
php artisan key:generate

# 2. Base de données
php artisan migrate --seed

# 3. Storage
php artisan storage:link

# 4. Assets
npm install && npm run dev

# 5. Queue worker (dans un terminal séparé)
php artisan queue:work

# 6. Scheduler (ajouter au cron du serveur)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1

# 7. Lancer le serveur
php artisan serve
```

---

## 👤 Comptes de Test (après seed)

| Email                        | Mot de passe | Rôle             |
|------------------------------|-------------|------------------|
| manager@faculte.ma           | password    | Manager          |
| chef.dept@faculte.ma         | password    | Chef Département |
| enseignant@faculte.ma        | password    | Enseignant       |
| technicien@faculte.ma        | password    | Technicien       |
| secretaire@faculte.ma        | password    | Secrétaire       |

---

## 🎨 Charte Graphique

```
Couleurs principales :
  Primary   → #1a3c5e  (Bleu marine académique)
  Secondary → #c8a951  (Doré universitaire)
  Success   → #28a745
  Danger    → #dc3545
  Warning   → #ffc107
  Light     → #f8f9fa

Priorités événements :
  Low      → badge-success  (vert)
  Medium   → badge-warning  (jaune)
  High     → badge-danger   (rouge)
  Critical → badge-dark + animation pulse

Statuts événements :
  Draft      → badge-secondary  (gris)
  Published  → badge-primary    (bleu)
  Cancelled  → badge-danger     (rouge)
  Completed  → badge-success    (vert)
```

---

*Architecture générée pour Laravel 11 + Bootstrap 5 — Application Gestion Événements Faculté*