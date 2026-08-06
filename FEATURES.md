# Propositions de Fonctionnalités - Gestion d'Organisation & Rôles Contextuels

À partir du modèle relationnel robuste défini dans `ORGANIZATION.md`, voici une proposition détaillée de fonctionnalités (Features) à forte valeur ajoutée pour l'application de gestion d'église. Ces fonctionnalités visent à faciliter la vie de la paroisse, à fluidifier la communication interne et à automatiser les tâches administratives.

---

## Feature 1 : Annuaire Connecté & Trombinoscope Intelligent

### Description :
Permet aux administrateurs et aux membres autorisés d'accéder à un annuaire complet de l'église avec filtres multicritères en temps réel.
### Cas d'usage :
*   Trouver rapidement les coordonnées de tous les membres de l'**Association des Femmes**.
*   Afficher le **Trombinoscope** de la direction de l'église (Président, Secrétaire, Trésorier généraux).
*   Visualiser tous les membres rattachés au groupe de la **Chorale**.
### Valeur ajoutée technique :
Grâce à notre indexation optimisée et notre structure de table simplifiée, les requêtes de filtrage (par groupe, par association ou par rôle) s'exécutent en moins de 10 ms sans surcharge de jointures complexes.

---

## Feature 2 : Gestion des Élections & Historique des Mandats

### Description :
Gère le renouvellement démocratique des instances de l'église en automatisant le cycle de vie des rôles contextuels.
### Fonctionnalités clés :
*   **Planification d'Élections** : Définition d'une date d'élection pour une association ou un groupe.
*   **Mandats Temporels** : Attribution automatique d'une date de fin de validité (`expiresAt`) aux rôles.
*   **Historique Permanent** : Possibilité de consulter l'historique de qui a été président de l'**Association des Jeunes** en 2023, 2024, etc.
### Valeur ajoutée technique :
L'entité `RoleAssignment` inclut nativement les colonnes `assignedAt` et `expiresAt`. Un script planifié quotidien (CronJob Symfony Command) peut désactiver automatiquement les rôles expirés et notifier le secrétariat pour la passation de pouvoir.

---

## Feature 3 : Budgets Décentralisés & Soumission de Dépenses par Association

### Description :
Donne de l'autonomie financière contrôlée aux associations en leur permettant de gérer leurs propres lignes budgétaires.
### Fonctionnalités clés :
*   Le trésorier de l'**Association des Hommes** peut saisir des dépenses directement rattachées à son association ou à un de ses sous-groupes.
*   Ces dépenses sont soumises à la validation finale du Trésorier général de l'église lors du processus de validation de Sabbat.
*   Calcul du coût de fonctionnement de chaque association par rapport à son budget alloué.
### Valeur ajoutée technique :
Cette fonctionnalité s'intègre en étendant l'entité `Expense` avec une relation facultative vers `Association` ou `SousGroupe`, s'alignant sur l'architecture existante de `SabbatValidation`.

---

## Feature 4 : Notifications et Messagerie Groupée Ciblée

### Description :
Permet d'envoyer des communications importantes (SMS, Emails, Notifications Push) de manière ultra-ciblée selon l'organigramme de la paroisse.
### Cas d'usage :
*   Le président de l'église veut envoyer un e-mail uniquement à l'ensemble des **Présidents d'Associations** et **Responsables de Groupes**.
*   L'Association des Jeunes veut envoyer un SMS de rappel pour une réunion uniquement aux membres rattachés au sous-groupe **"Jeunes de Behoririka Nord"**.
### Valeur ajoutée technique :
L'utilisation de requêtes API Platform dynamiques avec des filtres comme `/api/membres?associations[]=1&roles[]=SECRETARY` permet de récupérer instantanément la liste des contacts téléphoniques ou courriels qualifiés pour l'envoi en masse (via des services comme Twilio ou Brevo/Sendinblue).

---

## Feature 5 : Contrôle d'Accès Contextuel (CRBAC - Contextual Role-Based Access Control)

### Description :
Sécurise l'utilisation de l'application en accordant des privilèges d'édition dynamiques basés sur les rôles que possède un membre dans un contexte spécifique.
### Règles de sécurité dynamiques :
*   Le président de l'**Association des Femmes** peut modifier la description ou les sous-groupes de son association, mais ne peut pas modifier ceux de l'Association des Jeunes.
*   Le secrétaire d'un **Sous-groupe** peut enregistrer des présences de membres pour ce sous-groupe spécifique.
*   Le président général de l'église a un accès en lecture seule sur l'ensemble des sections mais conserve le droit de valider les comptes globaux.
### Valeur ajoutée technique :
Grâce à un `Voter` Symfony personnalisé qui interroge la table `RoleAssignment`, la sécurité n'est plus statique (liée à des rôles de framework comme `ROLE_ADMIN`), mais dynamique et contextuelle, offrant une sécurité applicative de niveau entreprise.

---

## Feature 6 : Portail d'Administration des Élections, Passations & Mandats

### Description :
Fournit une interface graphique dédiée aux administrateurs pour enregistrer les résultats des élections annuelles et orchestrer la passation de rôles.
### Fonctionnalités clés :
*   **Assistant de Passation** : En un clic, archive le mandat en cours (clôture des `RoleAssignment` de l'année T) et ouvre le nouveau mandat de l'année T+1.
*   **Aperçu d'Historique individuel** : Fiche historique d'un membre retraçant l'ensemble de ses fonctions au cours de sa vie au sein de l'église (ex: 2023 : Président de la Zone Andohanofotsy, 2024 : Trésorier de l'Association des Hommes).
*   **Alerte de vacance de rôle** : Rappelle si une association ou un sous-groupe n'a pas de bureau élu ou de rôles clés (Président ou Trésorier) actifs pour l'année en cours.
### Valeur ajoutée technique :
L'interface consomme l'endpoint `/api/role-assignments` avec des filtres temporels et d'activité (`?isActive=true` ou `?exerciceYear=2025`).

---

## Feature 7 : Cloisonnement Financier Contextuel Automatisé

### Description :
Garantit l'étanchéité stricte des écritures et consultations comptables entre les différentes associations de la paroisse.
### Fonctionnalités clés :
*   **Vues cloisonnées** : Le caissier de l'Association des Femmes voit un tableau de bord épuré, uniquement centré sur les flux financiers de l'Association des Femmes. Les données des Hommes ou de la Chorale lui sont totalement invisibles.
*   **Rapports consolidés automatiques** : Le Trésorier général de l'église ou le Pasteur ont accès à une vue globale consolidée, agrégeant les sous-comptes de toutes les associations pour le rapport de Sabbat.
### Valeur ajoutée technique :
Le cloisonnement financier est opéré directement au niveau de la couche Doctrine ORM grâce à des filtres de requêtes personnalisés ou via API Platform en injectant le contexte de l'utilisateur authentifié dans les requêtes de collection (`Extender / Query Extension`).
