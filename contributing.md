# Comment contribuer au projet

Afin de conserver une coherence dans le code et de faciliter la maintenance
voici quelques recommandations qu'il faudra respecter pour contribuer.

##Ajout d'une fonctionnalité

1. Cloner le dépot

```bash
git clone project_name
```

2. Installer le projet
3. Choisir une issue à réaliser
4. Creer une branche locale en la nommant "feature/nom_de_l'issue"

```bash
git checkout -b "feature/nom_de_l'issue"
```

4. Ecrire votre code
5. Ecrire les tests, une converture de code d'au moins 70% est attendue
6. Lancer les tests (le coverage se crée automatiquement dans )

```bash
composer test
```

7. Analyser le code avec PHPStan et corriger si necessaire

```bash
vendor/bin/phpstan analyse -l 5 src/
```

7. Ajouter ou modifier la documentation
8. Enregistrer votre travail dans un commit

```bash
git add .
git commit -m "nom du commit"
```

9. Push le code sur le serveur distant

```bash
git push --set-upstream origin branch_name
```

10. Ouvrir une pull request

##Qualité du code

Lors de l'ouverture d'une pull request une pipeline d'intégration continue se lance
afin de réaliser plusieurs actions sur le code et notamment la vérification de la
qualité du code. Afin de valider votre PR il est donc nécessaire de valider les
étapes d'analyse statique du code, il vous est alors demandé d'utiliser PHPStan
ainsi que PHP-cs-fixer.
