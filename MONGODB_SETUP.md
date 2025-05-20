# Configuration de MongoDB pour FamFinance

Ce guide explique comment configurer et utiliser MongoDB avec Laravel pour le projet FamFinance.

## Prérequis

1. MongoDB installé sur votre système (version 4.4 ou supérieure)
2. Extension PHP MongoDB installée
3. Composer installé

## Étape 1 : Installation de l'extension MongoDB pour PHP

### Windows (avec XAMPP ou Laragon)

1. Téléchargez l'extension MongoDB pour PHP correspondant à votre version de PHP depuis [le site officiel des DLL PECL](https://pecl.php.net/package/mongodb)
2. Placez le fichier `php_mongodb.dll` dans le dossier `ext` de votre installation PHP
3. Ajoutez `extension=mongodb` à votre fichier `php.ini`
4. Redémarrez votre serveur web

### Linux (Ubuntu/Debian)

```bash
sudo apt-get update
sudo apt-get install php-mongodb
sudo systemctl restart apache2  # ou nginx selon votre serveur
```

### macOS (avec Homebrew)

```bash
brew install php-mongodb
```

## Étape 2 : Installation du package Laravel MongoDB

Installez le package Jenssegers/MongoDB qui facilite l'intégration de MongoDB avec Laravel :

```bash
composer require jenssegers/mongodb
```

## Étape 3 : Configuration de Laravel

### Mise à jour du fichier .env

Modifiez votre fichier `.env` pour configurer la connexion MongoDB :

```
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27017
DB_DATABASE=famfinance
DB_USERNAME=
DB_PASSWORD=
```

### Configuration du fichier database.php

Modifiez le fichier `config/database.php` pour ajouter la configuration MongoDB :

```php
'connections' => [
    // Autres connexions...
    
    'mongodb' => [
        'driver' => 'mongodb',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', 27017),
        'database' => env('DB_DATABASE', 'famfinance'),
        'username' => env('DB_USERNAME', ''),
        'password' => env('DB_PASSWORD', ''),
        'options' => [
            'database' => env('DB_AUTHENTICATION_DATABASE', 'admin'),
            // Décommentez si vous utilisez une réplique MongoDB
            // 'replicaSet' => 'rs0',
        ],
    ],
],
```

## Étape 4 : Adaptation des modèles

Pour utiliser MongoDB avec vos modèles Laravel, vous devez étendre la classe MongoDB du package au lieu du modèle Eloquent par défaut.

Exemple pour le modèle User :

```php
<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Eloquent implements AuthenticatableContract
{
    use Authenticatable, Notifiable;

    protected $connection = 'mongodb';
    
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
```

Pour les autres modèles comme Category, Transaction, Budget, etc. :

```php
<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Category extends Model
{
    protected $connection = 'mongodb';
    
    protected $fillable = [
        'name', 'icon', 'color', 'type', 'user_id', 'is_system',
    ];
    
    // Relations et autres méthodes...
}
```

## Étape 5 : Migrations avec MongoDB

Les migrations Laravel standard ne fonctionnent pas exactement de la même façon avec MongoDB car MongoDB est une base de données orientée document sans schéma fixe.

Vous pouvez adapter vos migrations comme ceci :

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Jenssegers\Mongodb\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesCollection extends Migration
{
    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection = 'mongodb';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)
            ->create('categories', function (Blueprint $collection) {
                $collection->index('user_id');
                $collection->index('type');
                $collection->unique(['user_id', 'name', 'type']);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection($this->connection)->drop('categories');
    }
}
```

## Étape 6 : Interactions avec la base de données

Avec MongoDB, vous pouvez utiliser les méthodes Eloquent habituelles tout en bénéficiant des fonctionnalités de MongoDB :

```php
// Création d'un document
Category::create([
    'name' => 'Alimentation',
    'icon' => 'fa-shopping-cart',
    'color' => '#4CAF50',
    'type' => 'expense',
    'user_id' => Auth::id(),
    'is_system' => true,
]);

// Requête avec opérateurs MongoDB
$transactions = Transaction::raw(function ($collection) use ($user_id, $month, $year) {
    return $collection->aggregate([
        [
            '$match' => [
                'user_id' => $user_id,
                'transaction_date' => [
                    '$gte' => new MongoDB\BSON\UTCDateTime(strtotime("{$year}-{$month}-01") * 1000),
                    '$lt' => new MongoDB\BSON\UTCDateTime(strtotime("+1 month", strtotime("{$year}-{$month}-01")) * 1000)
                ]
            ]
        ],
        [
            '$group' => [
                '_id' => '$category_id',
                'total' => ['$sum' => '$amount']
            ]
        ]
    ]);
});
```

## Étape 7 : Considérations spécifiques pour MongoDB

1. **IDs des documents** : MongoDB utilise des ObjectID au lieu des entiers auto-incrémentés
   ```php
   // Récupérer un document par son ID
   $category = Category::find('60c72b2fcf1cd123456789ab');
   ```

2. **Relations** : Les relations fonctionnent de manière similaire avec MongoDB
   ```php
   // Relation un-à-plusieurs
   public function transactions()
   {
       return $this->hasMany(Transaction::class);
   }
   ```

3. **Requêtes géospatiales** : MongoDB excelle dans ce domaine
   ```php
   // Exemple de requête géospatiale
   $nearbyPlaces = Place::where('location', 'near', [
       '$geometry' => [
           'type' => 'Point',
           'coordinates' => [$longitude, $latitude]
       ],
       '$maxDistance' => 5000 // 5km en mètres
   ])->get();
   ```

## Résolution des problèmes courants

### Problème : La connexion à MongoDB échoue

1. Vérifiez que le service MongoDB est en cours d'exécution :
   ```bash
   # Windows
   net start MongoDB
   
   # Linux/Mac
   sudo systemctl status mongod
   ```

2. Vérifiez que les informations de connexion dans `.env` sont correctes
3. Assurez-vous que l'extension PHP MongoDB est correctement installée :
   ```php
   php -m | grep mongodb
   ```

### Problème : Les modèles ne fonctionnent pas correctement

Assurez-vous que tous vos modèles MongoDB étendent `Jenssegers\Mongodb\Eloquent\Model` et non `Illuminate\Database\Eloquent\Model`.

### Problème : Les migrations échouent

Si les migrations échouent, vous pouvez créer les collections directement en utilisant le shell MongoDB :
```bash
mongosh
use famfinance
db.createCollection("categories")
db.createCollection("transactions")
db.createCollection("budgets")
```

## Ressources utiles

- [Documentation Jenssegers/MongoDB](https://github.com/jenssegers/laravel-mongodb)
- [Documentation officielle MongoDB](https://docs.mongodb.com/)
- [Tutoriel MongoDB avec Laravel](https://www.mongodb.com/compatibility/mongodb-laravel-intergration) 