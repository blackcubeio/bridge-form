# blackcube/bridge-form — LLM index

**Role:** the form fields of Blackcube in native HTML, without any styling. Each field assembles the block `yiisoft/form` describes — label, input, hint, error — on the tags of `blackcube/html`, and exposes `protected` hooks returning empty class lists for an interface package to fill. `Elastic` derives a field from the JSON schema meta of a `BridgeFormModel` property.

## Package card

| | |
|---|---|
| Namespace | `Blackcube\BridgeForm` |
| Fields | `Field\Input`, `Textarea`, `Select`, `Checkbox`, `Toggle`, `Radio`, `RadioList`, `CheckboxList`, `ToggleList`, `Upload`, `Elastic`, `Label`, `Button`, `SubmitButton`, `ResetButton`, `ButtonGroup` |
| Bases | `Field\AbstractField` (extends `Yiisoft\Form\Field\Base\InputField`), `Field\AbstractItemList` (extends `AbstractField`), `Field\AbstractButton` (extends `Yiisoft\Form\Field\Base\PartsField`) |
| Traits | `Traits\AttributesTrait`, `WrapperAttributesTrait`, `FieldDataTrait`, `FloatingLabelTrait`, `RenderViewTrait` |
| Config | `RequiredConfig`: the mark of a required field, set once with `AbstractField::setRequiredConfig()` |
| Requires | PHP ^8.2, `blackcube/bridge-form-model` ^1.0, `blackcube/html` ^1.0, `yiisoft/form` ^1.5, `yiisoft/form-model` ^1.1, `yiisoft/widget` ^2.2 |
| Yii config | none |
| License | BSD-3-Clause |

## Rules

- A field bound to a model: `Input::create($model, 'email')` (`src/Field/Input.php:63-72`) sets `inputData(new FormModelInputData($model, $property))` then `fieldParts()`: hint and error become `<p>` with ids `<id>-description` and `<id>-error`, the ones `aria-describedby` points at (`src/Field/AbstractField.php:66-105`). Without a model: `Input::widget()->name('email')`.
- The field renders the whole block: template `{label}\n{input}\n{hint}\n{error}`, input container `div` (`AbstractField.php:40-42`). `Toggle` puts the input first (`src/Field/Toggle.php:37`); the buttons render `{input}` alone, without container (`src/Field/AbstractButton.php:38-39`).
- No class by default: every class hook returns `[]`. The caller's classes — `class()`, `inputClass()` — replace the component ones (`AbstractField::fieldClasses()`, `:54-59`, on `AttributesTrait::$classesReplaced`, `src/Traits/AttributesTrait.php:40`); the adding setters add to them.
- Attributes: `AttributesTrait` mirrors `Blackcube\Html\Tag\Base\Tag` on the main element (`attribute()`, `attributes()`, `addAttributes()`, `unionAttributes()`, `id()`, `addClass()`, `class()`, `addStyle()`, `removeStyle()`); `WrapperAttributesTrait` the same, prefixed `wrapper`, on the wrapper; `FieldDataTrait::fieldData(['key' => 'value'])` renders `data-key="value"` on the field element.
- Required mark: `AbstractField::setRequiredConfig(new RequiredConfig(' *'))` once for the project; `markRequired()` appends it to the label when the property carries a `Required` rule (`AbstractField.php:127-168`). Nothing is marked until a config is set.
- The fluent methods return `static`: a subclass keeps its class through the calls; `create()` builds `static::widget()`.
- Tags come from `Blackcube\Html\Html`. The yiisoft/form bases, not copied, keep their own `Yiisoft\Html\Html` calls for the containers and `addCssClass()`.
- `Elastic` renders native HTML only: no colour, no CSS class, no editor host (`elastic.md`).

## Parts

| File | Content | Read when |
|---|---|---|
| `fields.md` | Each field: base, factory, own methods, class hooks; the lists and their items; the buttons. | before using or styling a field |
| `elastic.md` | The schema-driven field: dispatch on `meta['field']`, the `make*()` factories, `makeWysiwyg()`, the class hooks, the upload endpoints. | before rendering or styling elastic properties |
| `extension.md` | How an interface package styles the fields (one hook per visual part), the `Elastic` override, what does not extend. | before extending |
