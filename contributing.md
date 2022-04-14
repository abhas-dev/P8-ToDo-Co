# Comment contribuer au projet

Afin de conserver une coherence dans le code et de faciliter la maintenance
voici quelques recommandations qu'il faudra respecter pour contribuer.

##Ajout d'une fonctionnalité

1. Cloner le dépot
``` bash
git clone project_name
```
2. Choisir une issue à réaliser
3. Creer une branche locale en la nommant "feature/nom_de_l'issue"
``` bash
git checkout -b "feature/nom_de_l'issue"
```
4. Ecrire votre code
5. Ecrire les tests, une converture de code d'au moins 70% est attendue
6. Analyser le code avec PHPStan et corriger si necessaire
```bash
vendor/bin/phpstan analyse -l 6 src/
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

##Ajout d'une fonctionnalité