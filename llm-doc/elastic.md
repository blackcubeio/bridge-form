# bridge-form — `Elastic`

`Field\Elastic` (`src/Field/Elastic.php`) is not a field: a plain class built with `new Elastic($model, $property, $elasticOptions)` whose `render()` picks the field a `BridgeFormModel` property asks for in its schema meta, and renders it in a block.

## 1. Dispatch

`render()` needs a `BridgeFormModel` and a property declared in `$model->getProperties()`; otherwise it returns `''`. The meta comes from `getProperties()[$name]->getMeta()`; `meta['field']` (default `text`) decides:

| `meta['field']` | Rendered through |
|---|---|
| `textarea` | `makeTextarea()` |
| `wysiwyg` | `makeLabel()` + `makeWysiwyg()` + hint |
| `email`, `number`, `date`, `datetime-local` | `makeInput()->type(...)` |
| `dropdownList` / `dropdownlist` | `makeSelect()->optionsData(items)` |
| `radioList` / `radiolist` | `makeRadioList()->items(items)` |
| `checkboxList` / `checkboxlist` | `makeCheckboxList()->items(items)` |
| `checkbox` | `makeCheckbox()->uncheckValue('0')->inputValue('1')` |
| `file` / `files` | `makeUpload()`, `multiple()` for `files` |
| anything else | `makeInput()->type('text')` |

- `items` = `extractItems($meta)`: each `meta['items'][]` with `value` and `title` becomes `value ⇒ title`.
- `file` / `files`: `meta['fileType']` (comma list) → `accept()`; `$elasticOptions['upload']`, `['preview']`, `['delete']` → `endpoint()`, `previewEndpoint()`, `deleteEndpoint()` on the upload field (the native `Upload` has none of these: the interface package's upload field does).
- Every branch wraps its field in `Html::div($field, ['class' => implode(' ', $this->blockClasses())])`.

## 2. Factories, one per field

`makeInput()`, `makeTextarea()`, `makeSelect()`, `makeRadioList()`, `makeCheckboxList()`, `makeCheckbox()`, `makeUpload()` — all `protected`, `(FormModelInterface $model, string $property)`, returning `X::create($model, $property)`; `makeLabel(string $content): Label`. An interface package overrides them to return its own fields.

## 3. The wysiwyg

The block is built in `renderWysiwyg()`: `div.block > [label] + div.control > editor + [p.hint]`, from the `FormModelInputData` of the property (id, name, label, hint). The editor is `makeWysiwyg(string $id, string $name, string $value, array $meta): string` — here `Html::textarea($name, $value, ['id' => $id])`. The package built on top renders an editor host instead (a hidden input carrying `$id` / `$name` / `$value` plus the editor element), with `meta['options']` as editor options.

## 4. Class hooks

`blockClasses()` (the block around each field), `controlClasses()` (the control container of the wysiwyg), `hintClasses()` (the hint under the wysiwyg): all return `[]` here.

## 5. Measured

No test here: the dispatch is exercised by the packages that give it fields with semantics.
