# Change Log
All notable changes to this project will be documented in this file.

## [Unreleased]



## Release 1.9

- FIX : Erreur 500 sur setup - *24/09/2024* - 1.9.3
- FIX : Entity for kanban - *29/08/2024* - 1.9.2
- FIX : Add the ability to define a new clean filter - *26/08/2024* - 1.9.1
- NEW : Add back menu links and group display - *31/07/2024* - 1.9.0
- NEW : Ajout de la possibilité d'override une partie de l'affichage (utilisateur affecté au card) - *08/08/2024* - 1.9.0

## Release 1.8

- NEW : COMPAT V20 - *22/07/2024* - 1.8.0

## Release 1.7

- FIX : Type de kanban par défaut initialisé et visibilité changé - *24/07/2024* - 1.7.3
- FIX : Suppression d'un mo dans une kanban card+ gestion erreur sur une carte - *22/07/2024* - 1.7.2
- FIX : Integer -> varchar(40) pour le type de kanban - *17/07/2024* - 1.7.1
- NEW : Ajout du type de kanban - *28/06/2024* - 1.7.0

## Release 1.6

- FIX : Mobile display - *07/07/2024* - 1.6.5
- FIX : Add method close and the posibility to skip the cancel button in dialog class- *20/06/2024* - 1.6.4
- FIX : $ismultientitymanaged = 1 - *13/05/2024* - 1.6.3
- FIX : V19 compatibility *18/04/2024* - 1.6.2
- FIX : description - *03/04/2024* - 1.6.1
  le ficher <link rel="stylesheet" type="text/css" href="/theme/common/fontawesome-5/css/v4-shims.min.css?layout=classic&amp;version=17.0.4">
    n'est plus loader pour l'utilisation de font-awesome en v19.

- NEW : Ajout de appendItemFooter dans getAdvKanBanItemObjectFormatted - *03/04/2024* - 1.6.1
- NEW : Ajout de hook et de $dataToResponse pour traiter les informations possiblement renvoyées par dropInKanbanList - *21-02-2024* - 1.6.0

## Release 1.5

- FIX : Css scrollbar style fail after Chrome update - *14/02/2024* - 1.5.9
- FIX : Somme PHP 8 fix - *14/02/2024* - 1.5.8
- FIX : Typo (missing `$`) in code - *16/01/2024* - 1.5.7
- FIX : Ajout type de contact `advancedkanban_advkanban` - *15/01/2024* - 1.5.6
- FIX : Missing rights - *10/01/2024* - 1.5.5
- FIX : Extrafield définition and help key - *20/12/2023* - 1.5.4
- FIX : Missing main_db_prefix - *10/01/2024* - 1.5.3
- FIX : Missing accessibility shortKey - *29/11/2023* - 1.5.2
- FIX : Missing EN_US lang file - *29/11/2023* - 1.5.1
- NEW : Add Dolibarr V19 compatibility - *27/11/2023* - 1.5.0
- NEW : Adding additional elements to the template - *28/09/2023* - 1.4.0
- NEW : Missing Default contact  override capacity- *27/09/2023* - 1.3

## Release 1.2

- FIX : Typo in code (missing `-`) - *16/01/2024* - 1.2.8
- FIX : Glitch on tag filter on Dolibarr < 17  *08/12/2023* - 1.2.7
- FIX : DA024242 - Ajoutde la possibilité de saisir des rôles de contact de type kanban *08/12/2023* - 1.2.6
- FIX : DA024241 - Retrocompatibilité du module avec la version 15 *07/12/2023* - 1.2.5
- FIX : Missing error message management *06/10/2023* - 1.2.4
- FIX : Split dialog close on error instead of stay open *19/07/2023* - 1.2.3
- FIX : Description of delete action  *09/05/2023* - 1.2.2
- FIX : DA023642 - error message on card when selecting the linked element type *10/07/2023* - 1.2.1
- NEW : Refresh kanban process *26/06/2023* - 1.2.0

## Release 1.1

- NEW : [ERG] Reduced status for board lists *07/05/2023* - 1.1.0  
  + some erg and visual fixes  

## Release 1.0

- CREATE MODULE FROM SCRUM PROJECT MODULE  *08/02/2023* - 1.0.0
