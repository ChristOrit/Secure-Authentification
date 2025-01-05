# Secure-Authentification

## Interface d'Authentification Sécurisée

### Description

Ce projet est une application web qui fournit une interface d'authentification sécurisée. Il inclut :

* Un **formulaire d'inscription** pour permettre aux utilisateurs de créer un compte.
* Un **formulaire de connexion** pour que les utilisateurs puissent se connecter.
* Une **page tableau de bord** accessible uniquement aux administrateurs.

### Rôles des utilisateurs

* Par défaut, tous les utilisateurs nouvellement créés sont des utilisateurs standards.
* Les administrateurs sont désignés en mettant à jour directement la base de données avec la commande suivante :

```sql
UPDATE users SET is_admin = 1 WHERE id = "<id_de_l'utilisateur>";
```

### Fonctionnalités principales

* Les utilisateurs standards voient un message "Connexion valide" après leur connexion.
* Les administrateurs sont redirigés vers le tableau de bord administrateur.

### Fonctionnalités

* Création de comptes avec validation des données.
* Connexion sécurisée avec gestion des sessions.
* Redirection selon les rôles des utilisateurs (standard ou admin).
* Protection contre les vulnérabilités web courantes.

## Installation

### Prérequis

* **PHP** 8.x ou supérieur.
* **Composer** pour la gestion des dépendances.
* Serveur web (**Apache** ou **Nginx**).
* Base de données **SQLite**.

### Étapes d'installation

1. Clonez le dépôt :

```bash
git clone https://github.com/ChristOrit/Secure-Authentification.git
cd Secure-Authentification
```

2. Installez les dépendances avec Composer :

```bash
composer install
```

3. Assurez-vous que le fichier de la base de données SQLite (`notrebase.db`) est créé et accessible. Vous pouvez utiliser cette commande pour le créer si nécessaire :

```bash
touch notrebase.db
```

4. Configurez votre serveur local pour accéder à l'application.

## Utilisation

1. Accédez à l'application via votre serveur local (par exemple, `http://localhost/Secure-Authentification`).
2. Inscrivez-vous comme utilisateur standard.
3. Pour tester les fonctionnalités admin, promouvez un utilisateur en mettant à jour directement la base de données avec la commande SQL mentionnée dans la section **Description**.

## Tests

Les tests sont réalisés avec PHPUnit et sont divisés en trois grandes catégories :

### 1. Tests Développeur

* **Création de compte** :
  * Vérification de la création réussie.
  * Validation des données en base.
  * Confirmation via des messages.
* **Connexion** :
  * Test avec des identifiants valides.
  * Vérification des sessions et des redirections.
* **Déconnexion** :
  * Suppression des sessions.
  * Redirection vers la page de connexion.
* **Mises à jour du profil** :
  * Validation des modifications des données utilisateur.
* **Gestion des messages d'erreur** :
  * Vérification des messages clairs.
* **Navigation** :
  * Accès contrôlé des pages selon le rôle de l'utilisateur.

### 2. Tests de Validation

* **Format d'email**
* **Complexité des mots de passe**
* **Gestion des doublons**
* **Limites des champs**
* **Mécanismes de verrouillage des comptes**

### 3. Tests de Sécurité

* **Protection des mots de passe** :
  * Hachage sécurisé et salage.
* **Protection contre les injections SQL**
* **Prévention XSS**
* **Protection CSRF** :
  * Validation par token.
* **Sécurité des sessions** :
  * Expiration des sessions et configuration sécurisée.

## Technologies Utilisées

* **Backend** : PHP 8.x
* **Base de données** : SQLite
* **Tests** : PHPUnit
* **Gestion des dépendances** : Composer

## Contributions

Actuellement, nous n'acceptons pas de contributions pour ce projet.

## Licence

Ce projet est un projet scolaire et **n'a pas de licence**. Toute utilisation, modification ou redistribution est strictement interdite sans autorisation explicite de l'auteur.
