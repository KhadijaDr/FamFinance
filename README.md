# FamFinance

FamFinance est une application web de gestion financière familiale développée avec Laravel, React, Tailwind CSS et MongoDB. Cette application permet aux utilisateurs de suivre leurs revenus et dépenses, gérer leurs budgets, planifier leurs objectifs financiers et recevoir des notifications.

![FamFinance Logo](public/images/logo.png)

## Fonctionnalités

- 💰 **Suivi des transactions** : Enregistrez vos revenus et dépenses avec des catégories personnalisables
- 📊 **Gestion de budget** : Créez et suivez vos budgets par catégorie
- 📅 **Factures récurrentes** : Suivez vos factures régulières avec rappels automatiques
- 🎯 **Objectifs financiers** : Définissez des objectifs d'épargne et suivez votre progression
- 📈 **Rapports et analyses** : Visualisez vos finances avec des graphiques et rapports
- 🔔 **Système de notifications** : Restez informé des événements importants
- 👨‍👩‍👧‍👦 **Gestion multi-utilisateurs** : Adaptée aux besoins des familles

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- MongoDB 4.4 ou supérieur
- Node.js et NPM

## Installation

### 1. Clonez le dépôt

```bash
git clone https://github.com/Duja1323/FamFinance.git
cd FamFinance
```

### 2. Installez les dépendances

```bash
composer install
npm install
```

### 3. Créez le fichier d'environnement

```bash
cp .env.example .env
```

### 4. Générez la clé d'application

```bash
php artisan key:generate
```

### 5. Configurez votre base de données MongoDB

Modifiez le fichier `.env` pour configurer votre connexion à MongoDB :

```
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=famfinance
DB_USERNAME=
DB_PASSWORD=
```

### 6. Exécutez les migrations et les seeders

```bash
php artisan migrate --seed
```

### 7. Compilez les assets React

```bash
npm run dev
```

### 8. Démarrez le serveur

```bash
php artisan serve
```

L'application sera accessible à l'adresse [http://localhost:8000](http://localhost:8000).

## Utilisation

### Initialisation des catégories

Lorsque vous vous connectez pour la première fois, vous devrez initialiser les catégories par défaut en cliquant sur le bouton "Créer catégories par défaut" dans la section Catégories.

### Création de transactions

1. Allez dans la section "Transactions"
2. Cliquez sur "Ajouter une transaction"
3. Remplissez les détails (montant, catégorie, date, etc.)
4. Enregistrez la transaction

### Interface React

L'interface utilisateur de FamFinance est construite avec React, offrant une expérience interactive et réactive :

1. Tableaux de bord dynamiques avec mises à jour en temps réel
2. Formulaires interactifs avec validation côté client
3. Transitions et animations fluides entre les différentes vues

### Gestion des budgets

1. Allez dans la section "Budgets"
2. Créez un nouveau budget en associant une catégorie et un montant
3. Suivez vos dépenses par rapport à ce budget

### Création d'objectifs financiers

1. Allez dans la section "Objectifs financiers"
2. Définissez un nouvel objectif avec un montant cible et une date d'échéance
3. Suivez votre progression au fil du temps

## Commandes utiles

### Création des catégories par défaut

Pour initialiser les catégories par défaut pour un utilisateur spécifique :

```bash
php artisan app:populate-categories
```

Ou spécifiez l'email d'un utilisateur :

```bash
php artisan app:populate-categories nom@exemple.com
```

## Licence

FamFinance est un logiciel open-source sous licence [MIT](https://opensource.org/licenses/MIT).
