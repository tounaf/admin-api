# Spécifications d'Organisation Paroissiale - Membres, Groupes, Associations & Rôles

Ce document détaille la conception technique et fonctionnelle permettant de modéliser la structure organisationnelle d'une paroisse, incluant la gestion des membres, des groupes internes, des associations, des sous-groupes ainsi que la gestion hautement dynamique des rôles et des mandats.

---

## 1. Description du Modèle & Contraintes Métier

L'église fonctionne comme une organisation multi-paliers avec les règles strictes suivantes :

1.  **L'Église (Fiangonana)** : L'entité globale qui chapeaute l'ensemble.
2.  **Membres** : Personnes physiques inscrites à l'église.
3.  **Groupes** : Structures spirituelles ou de service (ex: Chorale, Diaconat, École du Sabbat).
    *   *Contrainte* : **Un membre ne peut appartenir qu'à un seul groupe à la fois** (ou aucun).
4.  **Associations** : Mouvements sectoriels majeurs de l'église (ex: Association des Jeunes, Association des Hommes, Association des Femmes). De nouvelles associations pourront être créées dans le futur.
    *   *Contrainte* : **Un membre peut faire partie de plusieurs associations simultanément** (ou aucune).
5.  **Sous-groupes** : Cellules de travail ou sous-divisions internes rattachées exclusivement à une association parente (ex: Comité des Jeunes Adultes, Cellule Jeunes de Behoririka Nord).
6.  **Attribution des Rôles (RoleAssignment)** :
    *   Les membres peuvent se voir attribuer des rôles de direction ou de responsabilité à différents niveaux de contexte :
        *   **Niveau Église** (ex: *Président de l'église*)
        *   **Niveau Association** (ex: *Président de l'Association des Jeunes*, *Trésorier de l'Association des Femmes*)
        *   **Niveau Groupe** (ex: *Vice-président de la Chorale*)
        *   **Niveau Sous-groupe** (ex: *Secrétaire du Comité Jeunes de Behoririka Nord*)
    *   Un même membre peut cumuler plusieurs rôles distincts dans des contextes différents.

---

## 2. Diagramme de Classes UML (Mermaid)

Le diagramme suivant montre la conception de données proposée. Nous utilisons l'entité de liaison `RoleAssignment` avec des clés étrangères optionnelles vers chaque contexte d'application. Ce pattern, appelé **Polymorphic Context Assignment**, évite le partitionnement complexe de tables tout en assurant l'intégrité référentielle en base de données.

```mermaid
classDiagram
    direction TB

    class Fiangonana {
        +int id
        +string nom
        +string code
    }

    class Membre {
        +int id
        +string nom
        +string prenom
        +string email
        +string telephone
        +DateTime dateNaissance
        +Groupe groupe (nullable)
        +getAssociations() Collection
        +getRoleAssignments() Collection
    }

    class Groupe {
        +int id
        +string nom
        +string description
        +Fiangonana fiangonana
        +getMembres() Collection
    }

    class Association {
        +int id
        +string nom
        +string description
        +Fiangonana fiangonana
        +getSousGroupes() Collection
        +getMembres() Collection
    }

    class SousGroupe {
        +int id
        +string nom
        +string description
        +Association association
    }

    class RoleAssignment {
        +int id
        +string roleName
        +Membre membre
        +Fiangonana fiangonanaContext (nullable)
        +Association associationContext (nullable)
        +Groupe groupeContext (nullable)
        +SousGroupe sousGroupeContext (nullable)
        +DateTimeImmutable assignedAt
        +DateTimeImmutable expiresAt (nullable)
    }

    %% Relations & Cardinalités
    Fiangonana "1" *-- "0..*" Membre : "héberge"
    Fiangonana "1" *-- "0..*" Groupe : "possède"
    Fiangonana "1" *-- "0..*" Association : "comprend"

    Groupe "0..1" --o "0..*" Membre : "contient" (Un membre a max 1 groupe)
    Association "0..*" --o "0..*" Membre : "fédère" (Many-to-Many)

    Association "1" *-- "0..*" SousGroupe : "contient"

    RoleAssignment "0..*" o-- "1" Membre : "est attribué à"
    RoleAssignment "0..*" o-- "0..1" Fiangonana : "contexte Église"
    RoleAssignment "0..*" o-- "0..1" Association : "contexte Association"
    RoleAssignment "0..*" o-- "0..1" Groupe : "contexte Groupe"
    RoleAssignment "0..*" o-- "0..1" SousGroupe : "contexte Sous-groupe"
```

---

## 3. Flux & Processus Clés (Mermaid)

### A. Flux d'inscription d'un membre et validation des règles de groupe

Lorsqu'un membre est créé ou mis à jour, l'API valide les cardinalités : le groupe est associé via une relation standard de type clé étrangère directe, garantissant de façon native en base de données qu'un membre ne possède **jamais plus d'un groupe**.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Administrateur Église
    participant API as API de l'Église
    participant Validator as Validateur de Contraintes
    participant DB as Base de Données

    Admin->>API: POST /api/membres <br/> { nom: "Raoelison", prenom: "Jean", groupe: "/api/groupes/2", associations: ["/api/associations/1", "/api/associations/3"] }
    API->>Validator: Analyse l'intégrité du payload
    Note over Validator: Vérification que groupe est un IRI unique de type Groupe<br/>(La base de données garantit l'unicité de la FK)
    Validator-->>API: Validé avec succès
    API->>DB: INSERT INTO membre (nom, prenom, groupe_id) ...
    API->>DB: INSERT INTO membre_association (membre_id, association_id) ...
    DB-->>API: Enregistrements créés avec succès
    API-->>Admin: HTTP 201 Created (Membre créé)
```

### B. Flux d'attribution de Rôles Multiples (Contextuels)

Ce diagramme montre comment un membre obtient ses rôles de direction. L'API reçoit l'identifiant du membre, le nom du rôle (`PRESIDENT`, `TRESORIER`, etc.) et spécifie l'identifiant du contexte ciblé.

```mermaid
sequenceDiagram
    autonumber
    actor Secret as Secrétariat Église
    participant API as API de l'Église
    participant PM as Processeur RoleAssignment
    participant DB as Base de Données

    Secret->>API: POST /api/role-assignments <br/> { membre: "/api/membres/5", roleName: "PRESIDENT", associationContext: "/api/associations/2" }
    API->>PM: Déclenche la création du rôle
    Note over PM: Validation que le contexte ciblé existe<br/>et qu'il n'y a pas déjà un président actif (si règle d'unicité)
    PM->>DB: INSERT INTO role_assignment (membre_id, role_name, association_id) ...
    DB-->>PM: Confirmation
    PM-->>API: Rôle attribué
    API-->>Secret: HTTP 201 Created <br/> { id: 101, membre: "/api/membres/5", roleName: "PRESIDENT", context: "Association Hommes" }
```

---

## 4. Exemples de Payloads JSON pour l'API

### A. Création d'un Membre
*   **POST** `/api/membres`
*   **Description** : Crée un membre relié à au plus un groupe et à plusieurs associations.
*   **Données** :
```json
{
  "nom": "Rakotomalala",
  "prenom": "Tahina",
  "email": "tahina@example.com",
  "telephone": "+261340000000",
  "dateNaissance": "1995-04-18T00:00:00Z",
  "groupe": "/api/groupes/1",
  "associations": [
    "/api/associations/1",
    "/api/associations/2"
  ]
}
```

### B. Attribution d'un Rôle au niveau Église (Président de l'Église)
*   **POST** `/api/role-assignments`
*   **Données** :
```json
{
  "membre": "/api/membres/12",
  "roleName": "PRESIDENT",
  "fiangonanaContext": "/api/fiangonanas/1"
}
```

### C. Attribution d'un Rôle au niveau Association (Trésorier de l'Association des Jeunes)
*   **POST** `/api/role-assignments`
*   **Données** :
```json
{
  "membre": "/api/membres/12",
  "roleName": "TRESORIER",
  "associationContext": "/api/associations/1"
}
```

### D. Attribution d'un Rôle au niveau Sous-groupe
*   **POST** `/api/role-assignments`
*   **Données** :
```json
{
  "membre": "/api/membres/12",
  "roleName": "SECRETAIRE",
  "sousGroupeContext": "/api/sous-groupes/4"
}
```
