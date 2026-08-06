# Guide d'Intégration & d'Onboarding - Projet de Gestion Financière Paroissiale (Fiangonana)

Bienvenue dans l'équipe de développement ! Ce document a pour but de vous aider à comprendre rapidement le projet, à installer votre environnement de développement local et à réaliser vos premières contributions dans les meilleures conditions.

---

## 1. Présentation du Projet & Contexte

Le projet est une API de gestion financière destinée aux paroisses locales (désignées par le terme **Fiangonana**). Elle permet d'enregistrer et de consolider de manière transparente :
1.  Les offrandes hebdomadaires (**Offerings**) collectées lors du service religieux.
2.  Les dépenses quotidiennes et de Sabbat (**Expenses**).
3.  La validation financière de chaque Sabbat (**SabbatValidation**), accompagnée d'un dépôt d'image binaire (un bordereau de dépôt bancaire ou de transfert mobile money).
4.  L'enregistrement de l'opération de transfert d'argent effective (**Versement**).

---

## 2. La Stack Technique

Le projet repose sur des technologies modernes de l'écosystème PHP :
*   **Langage** : PHP >= 8.2 (recommandé 8.3/8.4)
*   **Framework principal** : Symfony 7.3.*
*   **Framework d'API** : API Platform 4.1.* (pour l'exposition de ressources REST riches en JSON, JSON-LD, OpenAPI, etc.)
*   **ORM** : Doctrine ORM (avec migrations gérées par DoctrineMigrationsBundle)
*   **Base de données** : MariaDB (10.11+) ou MySQL (8.0+)
*   **Serveur Web de dev** : Symfony CLI ou serveur interne PHP

---

## 3. Installation Étape par Étape en Local

Suivez ces étapes pour démarrer l'application sur votre machine de développement :

### Étape 1 : Cloner le dépôt et installer les dépendances PHP
```bash
# Installez toutes les dépendances requises via Composer
composer install
```

### Étape 2 : Configurer les variables d'environnement
Créez ou modifiez le fichier `.env.local` pour y renseigner les accès à votre base de données locale.

```ini
# .env.local
DATABASE_URL="mysql://username:password@127.0.0.1:3306/fiangonana?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```
*(Remplacez `username`, `password`, l'hôte et la version de MariaDB/MySQL selon votre configuration).*

### Étape 3 : Créer la base de données et jouer les migrations
Doctrine va créer la base de données puis exécuter tous les fichiers de migration historiques situés dans le dossier `migrations/` :

```bash
# Créez la base de données
php bin/console doctrine:database:create

# Jouez les migrations pour construire le schéma des tables
php bin/console doctrine:migrations:migrate
```

### Étape 4 : Lancer le serveur de développement
Vous pouvez utiliser le serveur web intégré de Symfony (recommandé si vous avez installé le binaire `symfony`) :

```bash
symfony server:start -d
```

Ou utiliser le serveur PHP interne :
```bash
php -S 127.0.0.1:8000 -t public/
```

L'API est maintenant accessible à l'adresse suivante : [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api) !

---

## 4. Exploration de l'API

L'application fournit une interface de documentation OpenAPI / Swagger UI interactive très utile pour tester vos requêtes. Rendez-vous sur :
👉 [http://127.0.0.1:8000/api/docs](http://127.0.0.1:8000/api/docs)

### Exemple de requêtes courantes :

#### 1. Création d'une église (Fiangonana)
*   **POST** `/api/fiangonanas`
*   **Payload** :
    ```json
    {
      "nom": "Paroisse de Behoririka",
      "adresse": "Lot II M 40 Behoririka",
      "latitude": -18.9012,
      "longitude": 47.5255,
      "code": "FIANG_BEH_01",
      "caution": 500000.0,
      "rar": 0.0
    }
    ```

#### 2. Enregistrement par lot de dépenses (Batch Create)
*   **POST** `/api/expenses/batch`
*   **Payload** :
    ```json
    {
      "expenses": [
        {
          "description": "Achat de fleurs pour le pupitre",
          "amount": 25000,
          "date": "2025-10-12",
          "fiangonana": "/api/fiangonanas/1"
        },
        {
          "description": "Frais de déplacement du prédicateur",
          "amount": 40000,
          "date": "2025-10-12",
          "fiangonana": "/api/fiangonanas/1"
        }
      ]
    }
    ```

---

## 5. Bonnes Pratiques de Contribution

Afin de maintenir une base de code saine et cohérente, veuillez respecter les règles suivantes :

### A. Philosophie d'API Platform (Providers & Processors)
Privilégiez toujours l'utilisation des mécanismes natifs d'API Platform :
*   Pour **intercepter l'écriture de données** (traitement métier, hachage, envoi de mail, décodage base64) : utilisez un **State Processor** (cf. `src/State/SabbatValidationProcessor.php`).
*   Pour **personnaliser la lecture de données** (agrégation complexe, appels d'API tiers) : utilisez un **State Provider** (cf. `src/State/OfferingStatisticsProvider.php`).
*   Évitez autant que possible de créer des contrôleurs customisés (`src/Controller`), sauf si vous y êtes contraints (comme pour les anciennes agrégations d'offrandes).

### B. Gestion des Images & Fichiers
Toutes les images physiques téléversées par les paroisses (bordereaux de validation de Sabbat) doivent être stockées dans le répertoire public : `public/uploads/{fiangonanaId}/bordereau/`.
Ne modifiez pas manuellement ces dossiers ; le `SabbatValidationProcessor` s'occupe de la création récursive du répertoire et du renommage unique des fichiers.

### C. Style de Code
*   Respectez les standards de codage **PSR-12 / PER**.
*   Utilisez des déclarations de types PHP strictes sur les propriétés et les retours de fonctions.
*   Documentez vos classes et méthodes complexes.

---

## 6. Projets & Tâches d'Intégration Conseillés ( Roadmap d'onboarding )

Pour vous familiariser avec le projet, voici deux tâches "Easy-Win" prêtes à être prises en main :

### Tâche 1 : Correction du bug d'agrégation dans `ExpenseStatisticsProvider`
*   **Fichier concerné** : `src/State/ExpenseStatisticsProvider.php`
*   **Description** : Actuellement, ce provider effectue par erreur une requête SQL sur la table `offering` au lieu de la table `expense`. Modifiez la requête SQL SQL pour faire un `SUM` sur `expense` et joindre correctement la table des dépenses, tout en retournant un DTO de statistiques adapté.

### Tâche 2 : Implémentation de la classe manquante `SabbatValidationStatisticsProvider`
*   **Fichiers concernés** : `config/services.yaml` et dossier `src/State/`
*   **Description** : La déclaration de service pour `App\State\SabbatValidationStatisticsProvider` existe déjà dans `services.yaml`, mais la classe physique PHP n'a pas encore été créée dans `src/State/`. Créez cette classe pour fournir des statistiques agrégées sur les validations de Sabbat validées vs rejetées.
