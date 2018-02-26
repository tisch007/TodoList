ToDoList
========

Eighth project of my php developer training on Openclassrooms.
Installation
1. Récupérer le code

    Via Git en clonant ce dépôt.

2. Télécharger les vendors et définir les paramètres d'application

Avec Composer bien évidemment :

composer update

3. Créez la base de données

Si la base de données que vous avez renseignée dans l'étape 2 n'existe pas déjà, créez-la :

php bin/console doctrine:database:create

Puis créez les tables correspondantes au schéma Doctrine :

php bin/console doctrine:schema:update --force

## Comment contribuer au projet
Pour contribuer au projet, vsuiver les étapes décrite dans le document [HowToContribute.md].

Enjoy it !
