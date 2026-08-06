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
