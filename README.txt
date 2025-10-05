nomenclature des classes du modèle de données :
Objet : Mangas
Inventaire : Bibliothèque
Galerie :  Vitrine

Partie 3

============================================================
    Entités et propriétés
============================================================

    Bibliothèque
- id            : entier, auto-incrément, PK
- titre         : string (~100), NOT NULL

Commentaires :
- "titre" suffit pour identifier visuellement la bibliothèque.

    Manga
- id            : entier, auto-incrément, PK
- titre         : string (~150), NOT NULL
- serie         : string (~120), NULLABLE
- tome          : entier, NULLABLE (si renseigné, attendu >= 1)

============================================================
    Associations
============================================================

    Bibliothèque (1) — (0..n) Manga
- Type         : OneToMany (côté "Many" sur Manga)
- Clé étrangère: Manga.bibliotheque (NOT NULL)
- Intégrité    : un Manga appartient obligatoirement à UNE Bibliothèque

Règle de suppression :
- Interdire de supprimer une Bibliothèque si elle contient des Mangas.
  (Pas de cascade de suppression pour l’instant.)

============================================================
    Contraintes et validations 
============================================================
Bibliothèque.titre : non vide (longueur minimale 3 conseillée)
Manga.titre        : non vide (longueur minimale 2 conseillée)
Manga.tome         : NULLABLE ; si renseigné, entier >= 1
Manga.bibliotheque : NOT NULL (FK obligatoire)

Pas d’unicité complexe (ex : (bibliotheque, serie, tome)).
