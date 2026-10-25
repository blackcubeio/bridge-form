# Blackcube Bridge Form

Form fields for Yii 3, in native HTML, without any styling.

[![License](https://img.shields.io/badge/license-BSD--3--Clause-blue.svg)](LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/blackcube/bridge-form.svg)](https://packagist.org/packages/blackcube/bridge-form)

Version française : [README.fr.md](README.fr.md).

## Installation

```bash
composer require blackcube/bridge-form
```

## Requirements

- PHP 8.2
- yiisoft/form, yiisoft/form-model, yiisoft/widget
- blackcube/html, blackcube/bridge-form-model

## What it is

The package builds the field: the block template, the containers, the
attributes, the name, the id, the value, the label, the hint and the errors,
everything `yiisoft/form` provides, assembled into a block ready to render.

It renders nothing but native HTML: an `input`, a `textarea`, a `select` with
its `optgroup`, a checkbox, a radio button, a file field. No CSS class, no
icon, nothing drawn.

```
blackcube/html                the tags
blackcube/bridge-form-model   the model
blackcube/bridge-form         the fields
an interface project          inherits and styles them
```

## Usage

```php
use Blackcube\BridgeForm\Field\Input;

echo Input::create($model, 'email')->email()->render();
```

`create()` binds the field to a model property: name, id, value, label,
hint, placeholder and errors derive from it. Without a model, the field is
driven by hand:

```php
echo Input::widget()->name('email')->email()->render();
```

The rendering produces the whole block: label, field, hint, error.

In a list, `RadioList` or `CheckboxList`, each item takes an id that yiisoft
generates from the one of the field (`Html::generateId()`). An application
that wants sequential ids, 1, 2, 3, sets `IdGenerator::$useSeed = false` at
bootstrap.

## The fields

| field | class |
|-------|-------|
| text, password, email, date, number, telephone, hidden | `Input` |
| text area | `Textarea` |
| dropdown list, its options and its groups | `Select` |
| checkbox | `Checkbox` |
| switch, a checkbox the interface draws | `Toggle` |
| radio button | `Radio` |
| group of radio buttons | `RadioList` |
| group of checkboxes, sent as an array | `CheckboxList` |
| file field | `Upload` |
| field derived from a JSON schema | `Elastic` |
| label | `Label` |
| buttons | `Button`, `SubmitButton`, `ResetButton` |
| joined buttons | `ButtonGroup` |

## Styling the fields

Each styling hook is a `protected` method returning an empty array. A project
sets its classes by inheriting:

```php
class Input extends \Blackcube\BridgeForm\Field\Input
{
    protected function prepareClasses(): array
    {
        return ['block', 'w-full', 'rounded-md'];
    }
}
```

When the call sets its own classes, `inputClass()`, `class()`, they replace
the ones of the component; the adding setters add to them.

The fields that go beyond native HTML, an icon inside the field, a search
panel, a drop zone, are built the same way: inherit and render your own. That
is what [blackcube/bleet](https://github.com/blackcubeio/bleet) does.

## Tests

```bash
vendor/bin/codecept run
```

## License

BSD-3-Clause. See [LICENSE.md](LICENSE.md).

Copyright (c) 2026 Blackcube - Philippe Gaultier.

## Author

Philippe Gaultier <philippe@blackcube.io>
