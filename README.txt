# ============================================================
#  Projet : MyMangas — Gestion de collections de mangas
#  Type   : Application Web Symfony (front-office + base de données)
# ============================================================

## 1) Explication générale du site 
MyMangas est un site pour gérer une collection personnelle de mangas (Bibliothèque)
et en publier une partie via des vitrines (plus tard). L’objectif est pédagogique :
appliquer Symfony + Doctrine + Twig, sans sur-ingénierie ni héritage d’entités.

## 2) Public visé
- Visiteur (anonyme) : pages publiques basiques (à venir).
- Membre : consultation et, plus tard, gestion de sa Bibliothèque/Mangas.
- Admin (optionnel) : back-office minimal via EasyAdmin (ultérieur).

## 3) Nomenclature 
- Objet     → Manga
- Inventaire→ Bibliothèque
- Galerie   → Vitrine  (à implémenter plus tard)

## 4) Portée actuelle (MVP)
- Consultation (front) :
  - Liste des Bibliothèques ("/")
  - Fiche d’une Bibliothèque : "/bibliotheque/{id}"
  - Fiche d’un Manga : "/manga/{id}"
- Données : SQLite + Fixtures de démo
- Pas de formulaires publics ni de vitrine pour l’instant (étapes ultérieures)

## 5) Modèle de données (minimal, sans héritage)
------------------------------------------------------------
Entités et propriétés
------------------------------------------------------------

Bibliothèque
- id     : entier, auto-incrément, PK
- titre  : string (~100), NOT NULL

Manga
- id     : entier, auto-incrément, PK
- titre  : string (~150), NOT NULL
- serie  : string (~120), NULLABLE
- tome   : entier, NULLABLE (si renseigné, attendu >= 1)

------------------------------------------------------------
Associations
------------------------------------------------------------

Bibliothèque (1) — (0..n) Manga
- Type          : OneToMany (côté "Many" sur Manga)
- Clé étrangère : Manga.bibliotheque (NOT NULL)
- Intégrité     : un Manga appartient obligatoirement à UNE Bibliothèque

Règle de suppression (actuelle) :
- Interdire de supprimer une Bibliothèque si elle contient des Mangas.
  (Pas de cascade de suppression pour l’instant.)

------------------------------------------------------------
Contraintes et validations
------------------------------------------------------------

- Bibliothèque.titre : non vide (longueur minimale 3 conseillée)
- Manga.titre        : non vide (longueur minimale 2 conseillée)
- Manga.tome         : NULLABLE ; si renseigné, entier >= 1
- Manga.bibliotheque : NOT NULL (FK obligatoire)

- Pas d’unicité complexe (ex : (bibliotheque, serie, tome)) au MVP.

## 6) Architecture logique 
- src/Entity/        : entités Doctrine (Bibliotheque, Manga)
- src/Controller/    : contrôleurs front (BibliothequeController, MangaController)
- templates/         : gabarits Twig
  - templates/base.html.twig
  - templates/bibliotheque/list.html.twig
  - templates/bibliotheque/show.html.twig
  - templates/manga/show.html.twig
- src/DataFixtures/  : données de test (Fixtures)
- .env               : configuration (SQLite en dev/test)

## 7) Lancer le projet (développement local)
Prérequis : PHP, Composer, Symfony CLI.

1) Installer les dépendances :
   composer install

2) Configurer la base SQLite (défaut dans .env) :
   # si besoin, recréer la base propre
   symfony console doctrine:database:drop --force --if-exists
   symfony console doctrine:database:create
   symfony console doctrine:schema:create

3) Charger les données de test (Fixtures) :
   symfony console doctrine:fixtures:load

4) Démarrer le serveur :
   symfony server:start -d
   # Ouvrir http://127.0.0.1:8000/


## 8) Limites (volontaires)
- Pas de fonctionnalités “réseau social” (amis, réputation…).
- Pas de publication “réaliste” fine : focus sur le cœur pédagogique.
- Pas de migrations complexes (dev local, pas de prod).

## 9) Identificaton :

Les idenfiants de connexion sont prenom@example.test et mot de passe 123456




