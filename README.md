# Projet 6 - OpenClassrooms - SnowTricks
## _Parcours Développeur d'application - PHP / Symfony_

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/0b52607b8f6a4fca9b27e4f42283014e)](https://www.codacy.com/gh/AxelVllR/SnowTricks/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=AxelVllR/SnowTricks&amp;utm_campaign=Badge_Grade)

### Descriptif du besoin
Vous êtes chargé de développer le site répondant aux besoins de Jimmy. Vous devez ainsi implémenter les fonctionnalités suivantes : 

    - Un annuaire des figures de snowboard. Vous pouvez vous inspirer de la liste des figures sur Wikipédia. Contentez-vous d'intégrer 10 figures, le reste sera saisi par les internautes
    - La gestion des figures (création, modification, consultation)
    - Un espace de discussion commun à toutes les figures.

Pour implémenter ces fonctionnalités, vous devez créer les pages suivantes :

    - La page d’accueil où figurera la liste des figures
    - La page de création d'une nouvelle figure
    - La page de modification d'une figure
    - La page de présentation d’une figure (contenant l’espace de discussion commun autour d’une figure).


Voici la liste des pages qui devront être accessibles depuis votre site web :

    - la page d'accueil
    - la page listant l’ensemble des blogs posts
    - la page affichant un blog post
    - la page permettant d’ajouter un blog post
    - la page permettant de modifier un blog post
    - les pages permettant de modifier/supprimer un blog post

## Installer le Projet

- Si vous ne disposez pas de mails catcher en local, installez 'MailDev', [cliquez-ici](https://nodejs.org/) pour en savoir plus 
- Clonez le Repo sur votre machine
- Rendez-vous dans l'invite de commande, puis dans le dossier du projet, lancez la commande
```sh
composer install
```
- Modifiez le fichier .env à la racine du projet afin d'entrer votre configuration (MAILER_URL et DATABASE_URL)

- Lancez les commandes suivante :
```
php bin/console d:d:c (creation de la db)
php bin/console d:m:m (Migrations)
php bin/console doctrine:fixtures:load (enregistrement des données de tests)
```

- Il ne vous restes plus qu'à lancer le serveur :

```
php bin/console server:run OU symfony serve
```

- Si l'url générée est différente de 'localhost:8000', veillez à bien changer la valeur de APP_URL dans le .env !! Dans le cas contraire, les bouton de réinitialisation du mot de passe et validation de compte dans les mails ne fonctionneront pas.

- ENJOY !

VOIR EN LIGNE : [cliquez-ici](https://snowtricks.axelvallier.fr/)
 
## Identifiants de l'utilisateur par défaut

MAIL :

> admin@admin.fr

Mot de passe :

> admin 
