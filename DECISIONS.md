# Décisions techniques

Points ambigus ou écarts avec la spécification, et l'option retenue.

## Environnement

- **Laravel 13 au lieu de 12** : le projet fourni était déjà en Laravel 13. Filament 3.3.57 accepte Laravel 13 et Livewire 3.8 aussi ; on garde 13 plutôt que de revenir en arrière.
- **Starter kit React retiré** : le projet fourni était le starter kit React (Inertia, Fortify, Wayfinder, Chisel). C'est incompatible avec la stack imposée (Blade + Livewire pour l'espace membre, connexion Discord sans mot de passe), donc ces packages et fichiers ont été supprimés.
- **PostgreSQL** : la base cible est PostgreSQL 16 (Docker/K8s). Le poste de développement n'a ni serveur PostgreSQL ni `pdo_pgsql`, donc le développement et les tests tournent en SQLite. Les migrations n'utilisent que du SQL compatible avec les deux (index uniques partiels `WHERE ...`).

## Modèle de données

- **Candidature « active »** : toute candidature dont le statut n'est pas `withdrawn`, `rejected` ou `not_retained`. Un candidat `hired` ne peut donc pas repostuler à la même offre. Contrainte assurée par un index unique partiel `applications_active_unique`.
- **Un seul créneau `pending` par entretien** : vérifié dans le service et garanti en base par l'index unique partiel `interview_slots_one_pending`.
- **Un entretien par candidature** (`interviews.application_id` unique) : la négociation ajoute des créneaux au même entretien.
- **`sectors.discord_webhook_url`** : colonne optionnelle ajoutée, prioritaire sur `DISCORD_WEBHOOK_SECTORS`.
- **Responsable de secteur** : il voit les candidatures des secteurs dont il est `manager_id` **et** de son `sector_id`.
- **`users.email` nullable** : Discord peut ne pas renvoyer d'e-mail (compte non vérifié).
- **Rôles stockés en français** (`Membre`, `Responsable de secteur`, `Direction`, `DRH`, `Admin`), via l'enum `App\Enums\Role`.
- **Historique du compte-rendu** : le texte du compte-rendu (`interviews.report`), sauvegardé toutes les 10 s, n'est pas journalisé à chaque sauvegarde. Seule la finalisation l'est.
