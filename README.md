lmondo
======

Les murs ont des oreilles

## Pré requis ##
* Pas encore de prérequis

## Keskecékça ##
* Essai de construction d'un logiciel permet des input/output dans le cadre de la domotique

## Buts : ##
* Reconnaissance vocale basée sur sphinx
* Construction d'un fichier de grammaire via l'interface web
* Extraction du motif à partir de l'entrée vocale
* Possibilitée de déclencher un scénario à partir du motif :
```
Appel d'un webservice
Exécution du'un commande prédéfinie sur l'hote (Ex : espeak)
```

## Installation ##
* Vous avez besoin d'un serveur web (Ex: apache) qui est configuré pour exécuter du php.
* Une base de données MariaDB.
* sur debian, pour exécuter espeak : sudo usermod -a -G audio www-data
* ajouter l'utilisateur et la base de données lmondo, un script exemple est sité dans var/DB/createLmondoUser.sql (pensez à changer le mot de passe !).
* Exécuter bin/install.php
* Ouvrir l'interface
