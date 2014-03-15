-- Script a adapter en fonction du paramétrage que l'on va mettre dans le 
-- fichier etc/config.php
-- Si la résolution d'hotes est désactivée dans MySQL, il faut remplacer
-- localhost par 127.0.0.1 ( ou par l'IP si la base MySQL n'est pas sur le 
-- serveur Web

create user 'lmondo'@'localhost' identified by 'IlVaudraitMieuxLeChanger';
-- L'installation et l'upgrade de la base de données est faite par php, il faut 
-- avoir des droits de faire beaucoup dur la BDD.
grant ALL PRIVILEGES ON lmondo.* to 'lmondo'@'localhost';
create database lmondo;
