# bridge-form — Extension

## 1. Style a field

```php
namespace App\Form;

use Blackcube\BridgeForm\Field\Input as BaseInput;

class Input extends BaseInput
{
    protected function prepareClasses(): array
    {
        return [
            'block',
            'w-full',
            'rounded-md',
        ];
    }
}

echo Input::create($model, 'email')->email()->render();
```

- One hook per visual part, all `protected`, all returning `[]` or `''` here: `fields.md` lists them field by field.
- `create()` and `widget()` build `static`: `Input::create()` on the subclass returns the subclass; the fluent methods return `static`.
- The caller keeps the last word: `class()` / `inputClass()` / `buttonClass()` replace the component classes (`$classesReplaced`, `src/Traits/AttributesTrait.php:40`, `src/Field/AbstractButton.php:95`), the adding setters add.
- A field that goes beyond native HTML — an icon inside the input, a drop zone, a search panel — overrides `generateInput()` and renders its own markup with `Blackcube\Html\Html`; the yiisoft parts (label, hint, error, container) stay.

## 2. `Elastic`

Override the factories to return the styled fields, and `makeWysiwyg()` to render an editor host:

```php
namespace App\Form;

use Blackcube\BridgeForm\Field\Elastic as BaseElastic;
use Blackcube\BridgeForm\Field\Input as BaseInput;
use Blackcube\Html\Html;
use Yiisoft\FormModel\FormModelInterface;

class Elastic extends BaseElastic
{
    protected function makeInput(FormModelInterface $model, string $property): BaseInput
    {
        return Input::create($model, $property);
    }

    protected function makeWysiwyg(string $id, string $name, string $value, array $meta): string
    {
        $html = Html::openTag('div', [
            'data-editor' => 'rich',
        ]);
        $html .= Html::hiddenInput($name, $value, [
            'id' => $id,
        ])->render();
        $html .= Html::div('')->render();
        $html .= Html::closeTag('div');

        return $html;
    }

    protected function blockClasses(): array
    {
        return [
            'mb-4',
        ];
    }
}
```

The return types of the factories are the bridge-form classes: the styled field must extend the matching one.

## 3. The required mark

`AbstractField::setRequiredConfig(new RequiredConfig(' *'))` once at bootstrap; every field and the standalone `Label` carry it. The label of a yiisoft part is rendered from its content alone (`renderLabel()` is `final` in `InputField`), hence the mark is appended to the content (`src/Field/AbstractField.php:154-168`).

## 4. What does not extend

- The yiisoft/form bases are not copied: what they lock stays locked (`renderLabel()`, the private part attributes of `PartsField` — reached through `hintConfig()`, `errorConfig()`, `labelConfig()` as `fieldParts()` does).
- `AttributesTrait::$tagAttributes` and `WrapperAttributesTrait::$wrapperAttributes` are `private`: read them with `getTagAttributes()` / `getWrapperAttributes()`, merge with `prepareTagAttributes()` / `prepareWrapperAttributes()`.
- `Radio`, `Label`, `ButtonGroup` are `Yiisoft\Widget\Widget`, not fields: no `create()`, no parts.
- `RenderViewTrait::renderView()` looks for `src/Views/<name>.php` inside this package (`src/Traits/RenderViewTrait.php:34`); a package that uses it carries its own copy of the trait with its own views directory.
