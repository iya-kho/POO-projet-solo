## cvtic-solo

Boilerplate du projet micro-framework à réaliser en solo.

### licences
Vous retrouverez dans le dossier _LICENSES/_, toutes les licences des 
bibliothèques et outils utilisés dans ce projet.

### qu'est ce qu'on utilise ici
On utilise une version de PHP qui est `>=8.2`.  

Vous avez aussi, directement dans le repo, deux scripts PHP à votre disposition :
- [composer](https://getcomposer.org/doc/00-intro.md) - Un gestionnaire de projet PHP
- [pretty-php](https://github.com/lkrms/pretty-php) - Un outil de mise en page, avec une opinion, pour le code PHP

Apres avoir fait votre setup via `composer`, vous aurez aussi à votre disposition [phpunit](https://phpunit.de/index.html).

Je vous invite **chaleureusement** à lire les documentations de tout ces petits
projets, histoire de savoir avec quoi vous allez travailler :)

### installer les dépendences
Il suffit de demander à `composer` de le faire :
```sh
./scripts/composer install
```
Vous devriez ensuite avoir un dossier _vendor_, avec plusieurs sous-dossier.  
Vous pouvez maintenant lancer les tests unitaires du projet (il n'y en a pas, mais ça ne fait rien) :
```sh
./vendor/bin/phpunit tests
```
La commande devrait se terminer sans erreurs.

### quelques bonnes lectures
Toujours bien d'apprendre :)

- [ArrayAccess](https://www.php.net/manual/fr/class.arrayaccess.php)
- [Iterator](https://www.php.net/manual/fr/class.iterator.php) / [IteratorAggregate](https://www.php.net/manual/fr/class.iteratoraggregate.php)
- [SplFixedArray](https://www.php.net/manual/fr/class.splfixedarray.php)
- [Countable](https://www.php.net/manual/fr/class.countable.php)
- [Stringable](https://www.php.net/manual/fr/class.stringable.php)
- [__construct](https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor) / [__destruct](https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.destructor)
- [call_user_func](https://www.php.net/manual/fr/function.call-user-func.php) / [call_user_func_array](https://www.php.net/manual/fr/function.call-user-func-array.php)
