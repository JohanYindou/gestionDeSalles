# RoomManager

![logo](./assets/media/RoomManagerLogo.png)

## Description

RoomManager est un gestionnaire de salle. Il à un proriétaire de lister, ajouter, modifier, et supprimer des salles. Les utilisateurs peuvent effectuer des reservations.

## Installation

Pour installer RoomManager, vous avez besoin de Composer et Symfony.
Après avoir cloné le dépôt sur votre machine locale, ouvrez un terminal et tapez les lignes de code suivantes :

```bash
composer install
```

Cela permet d'installer les dépendances de l'application.

Après avoir installé les dépendances, il est nécessaire d'initialiser la base de données de l'application ainsi que le mailer en créeant un fichier '.env.local' avec les informations suivantes :

```env
DATABASE_URL=" Votre URL de la base de données "
MAILER_DSN= Votre DSN du mailer
```

Vous pouvez créer la base de données en utilisant la commande suivante :

```bash
symfony console doctrine:database:create
```

Ensuite, pour créer les migrations :

```bash
symfony console make:migration
```

Pour appliquer les migrations à la base de données :

```bash
symfony console doctrine:migrations:migrate
```

Enfin, il faut charger les fixtures générées par Faker dans la base de données :

```bash
symfony console doctrine:fixtures:load
```

## Fonctionalitées

Depuis la page Home vous pouvez accéder aux fonctionnalitées suivantes :

- Créer et Se connecter à l'application
- Aller sur son profil
- Accéder aux rooms disponibles
- Accéder au dashboard (pour les administrateurs)

Depuis la page Rooms vous pouvez accéder aux fonctionnalitées suivantes :

- Rechercher des rooms
- Accéder aux pages des rooms

Depuis la page Room vous pouvez accéder aux fonctionnalitées suivantes :

- Accéder aux information de la room
- Réserver la room

Depuis la page Profil vous pouvez accéder aux fonctionnalitées suivantes :

- Voir les information de votre profil
- Modifier les informations de votre profil

## Auteurs

Ce projet est reéalisé par :

- Johan Yindou - [GitHub](https://github.com/JohanYindou)
- Ikrame Nguyen - [GitHub](https://github.com/Ikybn)
- Hamady Sakho - [GitHub](https://github.com/Sakhooo71)
