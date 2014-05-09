
CREATE TABLE IF NOT EXISTS `$prefix$users` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `login` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL,
  UNIQUE INDEX `idx_$prefix$users_login` (`login`)
  ) ;
INSERT INTO `$prefix$users` VALUES (NULL, 'adminlmondo', '', NULL, 1);
CREATE TABLE IF NOT EXISTS `$prefix$config` (
  `cle` varchar(100) PRIMARY KEY,
  `valeur` varchar(100) NOT NULL
  ) ;
INSERT INTO `$prefix$config` VALUES ('version', '0.1');
INSERT INTO `$prefix$config` VALUES ('reco_name', 'marvin');
INSERT INTO `$prefix$config` VALUES ('reco_spell', 'mm aa rr vv ii nn ee');
CREATE TABLE IF NOT EXISTS `$prefix$menu` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nom` varchar(100) NOT NULL,
  `link` varchar(100) NOT NULL,
  `level` tinyint(2) NOT NULL,
  `parent` INT NOT NULL,
  `ordre` INT NOT NULL,
  UNIQUE INDEX `idx_$prefix$menu_nom_parent` (`nom`, `parent`)
  ) ;
INSERT INTO `$prefix$menu` VALUES (1, 'Reconnaissance vocale', '#', 0, 0, 1);
INSERT INTO `$prefix$menu` VALUES (2, 'Paramètres', '#', 0, 0, 2);
INSERT INTO `$prefix$menu` VALUES (NULL, 'Gestion de grammaire', 'grammar.php', 1, 1, 3);
INSERT INTO `$prefix$menu` VALUES (NULL, 'Paramètres', 'settings.php', 1, 2, 4);
INSERT INTO `$prefix$menu` VALUES (NULL, 'Gestion des actions', 'action.php', 1, 1, 5);
CREATE TABLE IF NOT EXISTS `$prefix$rules` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nom` varchar(100) NOT NULL,
  `content` varchar(100) NOT NULL,
  UNIQUE INDEX `idx_$prefix$rules_nom` (`nom`)
  ) ;
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume la lumière de la cuisine', 'marvin allume la lumière de la cuisine');
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume la lumière du salon', 'marvin allume la lumière du salon');
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume la lumière de la salle a manger', 'marvin allume la lumière de la salle a manger');
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume la lampe la cuisine', 'marvin allume la lampe la cuisine');
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume la lampe du salon', 'marvin allume la lampe du salon');
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume la lampe de la salle a manger', 'marvin allume la lampe de la salle a manger');
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume l ampoule la cuisine', 'marvin allume l ampoule la cuisine');
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume l ampoule du salon', 'marvin allume l ampoule du salon');
INSERT INTO `$prefix$rules` VALUES (NULL, 'allume l ampoule de la salle a manger', 'marvin allume l ampoule de la salle a manger');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint la lumière la cuisine', 'marvin éteint la lumière la cuisine');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint la lumière du salon', 'marvin éteint la lumière du salon');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint la lumière de la salle a manger', 'marvin éteint la lumière de la salle a manger');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint la lampe la cuisine', 'marvin éteint la lampe la cuisine');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint la lampe du salon', 'marvin éteint la lampe du salon');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint la lampe de la salle a manger', 'marvin éteint la lampe de la salle a manger');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint l ampoule la cuisine', 'marvin éteint l ampoule la cuisine');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint l ampoule du salon', 'marvin éteint l ampoule du salon');
INSERT INTO `$prefix$rules` VALUES (NULL, 'éteint l ampoule de la salle a manger', 'marvin éteint l ampoule de la salle a manger');
INSERT INTO `$prefix$rules` VALUES (NULL, 'comment ca va', 'marvin comment ca va');

CREATE TABLE IF NOT EXISTS `$prefix$actions` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nom` varchar(100) NOT NULL,
  `command` varchar(100) NOT NULL,
  `args` varchar(100) NOT NULL,
  UNIQUE INDEX `idx_$prefix$actions_nom` (`nom`)
  ) ;
INSERT INTO `$prefix$actions` VALUES (NULL, 'Dire', '/usr/bin/espeak', '"$1$" -v mb/mb-fr1 -s 150');