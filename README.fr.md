# Blackcube Bridge Form

Les champs de formulaire pour Yii 3, en HTML natif, sans habillage.

[![License](https://img.shields.io/badge/license-BSD--3--Clause-blue.svg)](LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/blackcube/bridge-form.svg)](https://packagist.org/packages/blackcube/bridge-form)

English version: [README.md](README.md). Ce document est une traduction, le
README anglais fait foi.

## Installation

```bash
composer require blackcube/bridge-form
```

## Prérequis

- PHP 8.2
- yiisoft/form, yiisoft/form-model, yiisoft/widget
- blackcube/html, blackcube/bridge-form-model

## Ce que c'est

Le paquet construit le champ : le gabarit du bloc, les conteneurs, les
attributs, le nom, l'identifiant, la valeur, le libellé, l'aide et les
erreurs, tout ce que `yiisoft/form` fournit, assemblé en un bloc prêt à
rendre.

Il ne rend que du natif : un `input`, un `textarea`, un `select` avec ses
`optgroup`, une case à cocher, un bouton radio, un champ de fichier. Aucune
classe CSS, aucune icône, rien de dessiné.

```
blackcube/html                les balises
blackcube/bridge-form-model   le modèle
blackcube/bridge-form         les champs
un projet d'interface         en hérite et les habille
```

## Usage

```php
use Blackcube\BridgeForm\Field\Input;

echo Input::create($model, 'email')->email()->render();
```

`create()` relie le champ à une propriété du modèle : nom, identifiant,
valeur, libellé, aide, texte indicatif et erreurs en découlent. Sans modèle,
le champ se pilote à la main :

```php
echo Input::widget()->name('email')->email()->render();
```

Le rendu produit le bloc complet : libellé, champ, aide, erreur.

Dans une liste, `RadioList` ou `CheckboxList`, chaque item prend un
identifiant que yiisoft génère à partir de celui du champ
(`Html::generateId()`). Une application qui veut des identifiants qui se
suivent, 1, 2, 3, pose `IdGenerator::$useSeed = false` au démarrage.

## Les champs

| champ | classe |
|-------|--------|
| texte, mot de passe, courriel, date, nombre, téléphone, caché | `Input` |
| zone de texte | `Textarea` |
| liste déroulante, ses options et ses groupes | `Select` |
| case à cocher | `Checkbox` |
| interrupteur, une case que l'interface dessine | `Toggle` |
| bouton radio | `Radio` |
| groupe de boutons radio | `RadioList` |
| groupe de cases, transmis en tableau | `CheckboxList` |
| champ de fichier | `Upload` |
| champ dérivé d'un schéma JSON | `Elastic` |
| libellé | `Label` |
| boutons | `Button`, `SubmitButton`, `ResetButton` |
| boutons joints | `ButtonGroup` |

## Habiller les champs

Chaque point d'habillage est une méthode `protected` qui retourne un tableau
vide. Un projet pose ses classes en héritant :

```php
class Input extends \Blackcube\BridgeForm\Field\Input
{
    protected function prepareClasses(): array
    {
        return ['block', 'w-full', 'rounded-md'];
    }
}
```

Si l'appel pose ses propres classes, `inputClass()`, `class()`, elles
remplacent celles du composant ; les setters d'ajout s'y ajoutent.

Les champs qui vont plus loin que le natif, une icône dans le champ, un
panneau de recherche, une zone de dépôt, se construisent de la même façon :
on hérite et on rend le sien. C'est ce que fait
[blackcube/bleet](https://github.com/blackcubeio/bleet).

## Tests

```bash
vendor/bin/codecept run
```

## Licence

BSD-3-Clause. Voir [LICENSE.md](LICENSE.md).

Copyright (c) 2026 Blackcube - Philippe Gaultier.

## Auteur

Philippe Gaultier <philippe@blackcube.io>
