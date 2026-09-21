RBC68 — thème WordPress 1.6.0
================================

INSTALLATION

1. Dans WordPress : Apparence > Thèmes > Ajouter un thème > Téléverser un thème.
2. Sélectionner le fichier rbc68-wordpress-theme-1.6.0.zip.
3. Cliquer sur Installer maintenant, puis Activer.

Le thème utilise automatiquement front-page.php pour afficher le one-page à la racine du site.

PERSONNALISATION

Nouveautés 1.6.0 :
- « Organigramme » devient « Comité », alimenté uniquement par les membres publiés dans WordPress.
- Menu Comité > Ajouter un membre : nom de famille dans le titre, prénom et rôle libre dans les informations du membre.
- Liste d'administration classée par nom par défaut ; affichage public par nom puis prénom, sur une, deux ou trois colonnes selon la largeur.
- À la première visite de l'administration par un administrateur, les cinq membres de l'ancien thème sont importés une seule fois. Les modifier, les dépublier ou les supprimer ne les recrée pas. Une liste vide masque le bloc Comité.

Nouveautés 1.5.0 :
- Événements à venir par défaut, lien « Passés — saison en cours », puis archives par saison (septembre à août). Les onglets rechargent la page et fonctionnent sans JavaScript. Aucun événement n’est supprimé.
- Sélection des horaires dorée et lisible dans les deux modes.
- Titres d’actualités sur plusieurs lignes ; illustration SVG de badminton sans texte quand aucune image n’est choisie. Les photos existantes gardent la priorité.
- Carte principale responsive dans Contact, avec chargement différé ; le bloc Où jouer conserve l’adresse et les liens.
- Les réglages, événements et contenus existants sont conservés.


- Apparence > Personnaliser > Identité du site : logo.
- Apparence > Personnaliser > Page d’accueil RBC68 : titre, introduction, adresse, e-mail, réseaux sociaux et photos.
- Les emplacements « Photo horizontale du bandeau d’accueil » et « Photo d’illustration de la section Le club » restent invisibles tant qu’aucune image n’est choisie. Une image horizontale, idéalement au format 16:9, est recommandée.
- Apparence > Personnaliser > Informations pratiques : lieu, liens cartographiques, résumés des créneaux, tarifs, disponibilités des trois sections et texte « Nous rejoindre ».
- Apparence > Personnaliser > Bannière d’information : message temporaire et lien facultatif. Un message vide masque la bannière.
- Événements : renseignez catégorie, dates, heures, adresse, coordonnées OpenStreetMap, précisions, équipe et éventuel article dédié. Les trois prochains événements (hors entraînements) alimentent automatiquement le bloc « En ce moment ».
- Articles : les trois derniers articles publiés alimentent automatiquement la section Actualités. Ajoutez une image mise en avant pour chaque article.
- Réglages > Général : vérifiez le titre du site et l’adresse e-mail d’administration.

FORMULAIRE DE CONTACT

Le formulaire envoie un e-mail à l’adresse réglée dans « Page d’accueil RBC68 ». Il ne stocke pas les messages dans la base WordPress. La remise des e-mails dépend de la configuration mail de l’hébergement ; un plugin SMTP peut être nécessaire si l’hébergeur ne configure pas PHP mail correctement.

CONTENU FIXE

Les tableaux détaillés des horaires restent inclus dans front-page.php. Les résumés de la zone « Informations pratiques », les tarifs, la bannière, les événements et les membres du comité sont modifiables depuis WordPress.

NOUVEAUTÉS 1.1.0

- En-tête plus compact et navigation recentrée sur les besoins des visiteurs.
- Bloc immédiatement visible : lieu, créneaux, tarifs et inscription.
- Bande « En ce moment » avec le prochain événement et le dernier article WordPress.
- Signal visuel discret dans la navigation pour les événements et actualités.

NOUVEAUTÉS 1.2.0

- Bandeau principal encore réduit, sans mention de saison.
- Emplacements facultatifs pour une photo dans le bandeau et une photo dans la section « Le club ».
- Palette verrouillée sur les couleurs extraites du logo RBC68 : bleu nuit, bleus acier, or, blanc et gris.
- Libellé d’action harmonisé en « Nous rejoindre » dans la navigation et les appels à l’action.

NOUVEAUTÉS 1.3.0

- Mini-carte cliquable avec ouverture de l’itinéraire et lien vers les informations sur la salle.
- Présentation séparée des groupes et des tarifs, ligne par ligne.
- Contraste corrigé pour les petits titres en mode sombre ; lien de contact déplacé dans le pied de page.
- Nouveau type de contenu WordPress « Événements » avec date, résumé et distinction événement/entraînement.
- Affichage automatique des trois prochains événements, hors entraînements.
- Dernière actualité enrichie par son extrait WordPress et visuellement séparée des événements.
- Bannière d’information facultative administrable depuis WordPress.

NOUVEAUTÉS 1.4.0

- Mode sombre appliqué par défaut et choix mémorisé au moyen d’un interrupteur accessible.
- Fond gris clair en mode clair et contraste renforcé des onglets Horaires sur mobile.
- Événements enrichis : heures, lieu, mini-carte, précisions et lien facultatif vers un article.
- Catégorie Interclubs avec équipe concernée.
- Les événements terminés sont absents de l’accueil et grisés dans la liste complète.
- Une seule carte principale pour la salle ; l’adresse et le lien d’itinéraire restent répétés dans Contact.
- Disponibilité administrable pour chaque section ; seule la pastille « Complet » est affichée sur le site.
- Mention des deux séances d’essai gratuites dans Nous rejoindre.
- Ordre initial du menu conservé ; les sections de la page suivent désormais cet ordre.

CORRECTIF 1.4.1

- Restauration de la fin du fichier functions.php, tronquée par erreur dans l’archive 1.4.0.
- Rétablissement de l’ordre initial du menu et réorganisation visuelle des sections selon ce menu.

NOUVEAUTÉS 1.4.2

- Ordre de sortie HTML des sections conforme au menu : Infos pratiques, Horaires, Évènements, Actualités, Le club, Contact, Nous rejoindre. La galerie complémentaire vient ensuite.
- Pastilles de catégorie : Public en vert, Interclub en orange et Interne en rouge.
- La disponibilité n’affiche plus rien lorsqu’il reste des places ; seule la pastille « Complet » apparaît.
- Éditeur simplifié des événements : tous les champs pratiques apparaissent directement sous le titre, sans éditeur de blocs.
