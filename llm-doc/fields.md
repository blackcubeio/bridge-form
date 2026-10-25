# bridge-form — Fields

All fields extend `AbstractField` (`Yiisoft\Form\Field\Base\InputField`) unless said otherwise; `create(?FormModelInterface $model, ?string $property): static` binds the model property and calls `fieldParts()`. Every setter clones and returns `static`.

## 1. `Input` (`src/Field/Input.php`)

- Types: `type(string)`, `text()`, `password()`, `email()`, `number()`, `date()`, `telephone()` (`tel`), `hidden()` — hidden drops the template to `{input}` and the container (`:123-131`).
- State: `disabled()`, `readonly()`, `required()`; aria: `ariaLabelledBy(...)`, `ariaDescribedBy(...)`; `autocomplete(string)`, otherwise implicit by type: `email` → `email`, `password` → `current-password`, `tel` → `tel` (`:365-373`).
- A `DateTimeImmutable` value renders `Y-m-d` for `date`, `Y-m-d\TH:i` for `datetime-local` (`:304-310`).
- `aria-invalid="true"` when the input data carries errors (`:269-271`); validation class through `ValidationClassTrait`.
- Floating label: `floatingLabel(string|Label)` (`FloatingLabelTrait`) wraps label and control in one `div`.
- Hooks: `prepareClasses()`, `prepareFloatingInputClasses()`, `getFloatingOutlineColorClass()`, `getFloatingFocusWithinOutlineColorClass()`, `floatingLabelClasses()`, `prepareFloatingWrapperClasses()`.

## 2. `Textarea` (`src/Field/Textarea.php`)

`placeholder()`, `rows()`, `cols()`, `disabled()`, `readonly()`, `required()`, `ariaLabelledBy()`, `ariaDescribedBy()`; floating label as `Input`. Hooks: `prepareClasses()`, `prepareFloatingTextareaClasses()`, `getFloatingTextareaColorClasses()`, `getFloatingWrapperColorClasses()`.

## 3. `Select` (`src/Field/Select.php`)

`prompt(?string)`, `optionsData(array)` (value ⇒ label, nested arrays become `optgroup`), `disabled()`, `multiple()`, `ariaLabelledBy()`, `ariaDescribedBy()`. Hook: `prepareClasses()`.

## 4. `Checkbox` (`src/Field/Checkbox.php`)

`inputValue()`, `uncheckValue(string)` (hidden input before the box), `checked()`, `disabled()`, `required()`. Hooks: `getInputClasses()`, `decorateBox(string $box)` (wraps the rendered box, identity here), `getInputColorClasses()`, `getSvgDisabledStrokeClass()`.

## 5. `Toggle` (`src/Field/Toggle.php`)

A checkbox drawn as a switch: template `{input}\n{label}\n{hint}\n{error}`, no input container. `inputValue(string)` (default `1`), `checked()`, `disabled()`, `ariaLabel()` (used when the field has no label). The checked state compares the input value with the field value: array → in the list, bool → `1`/`0`, else string equality (`:172-183`). Markup: `div.wrapper > span.knob + input[type=checkbox]` (`:240-254`). Hooks: `getSwitchClasses()`, `getKnobClasses()`, `getInputClasses()`, `getSwitchBgColor()`, `getSwitchCheckedBgColor()`, `getSwitchRingColor()`, `getSwitchFocusRingColor()`, `getKnobRingColor()`.

## 6. `Radio` (`src/Field/Radio.php`) — a `Widget`, not a field

`name()`, `value()`, `checked()`, `disabled()`, `required()`, `label(string|Label)`, `description()`; `render()` builds input + label + description. Hooks: `prepareClasses()`, `wrapperClasses()`, `labelTextClasses()`, `requiredMarkClasses()`, `descriptionClasses()`, `getInputClasses()`, `getInputColorClasses()`.

## 7. Lists — `AbstractItemList` (`src/Field/AbstractItemList.php`)

`items(array $items)` (value ⇒ label), `disabled()`, `required()`. `generateInput()` loops: one item per entry, id `Html::generateId($baseId . '-')`, the field `name` passed to each, `fieldData` propagated (`:75-115`). The list label points at no input (`shouldSetInputId(false)`, `:70-73`). Abstract per list: `prepareItem(string $value, string $label, string $id, int $position): Widget`, `isSelected(string $value): bool`, `defaultBaseId(): string`; `itemsName(?string)` keeps the name. Hooks: `listClasses()`, `prepareClasses()`.

| List | Item | Name | Own |
|---|---|---|---|
| `RadioList` | `makeRadio(): Radio` | the field name | — |
| `CheckboxList` | `makeCheckbox(): Checkbox` | `name[]` (`itemsName()`, `:85`) | `values(array)` |
| `ToggleList` (extends `CheckboxList`) | `makeToggle(): Toggle` | `name[]` | — |

## 8. `Upload` (`src/Field/Upload.php`)

`accept(string[])` (a bare extension becomes `.ext`, `normalizeAccept()`, `:139-156`), `multiple()` (name gets `[]`, `:98-100`), `disabled()`, `required()`. Renders `Html::input('file', …)` (`:131`). Hook: `prepareClasses()`.

## 9. `Label` (`src/Field/Label.php`) — a `Widget`

`content()`, `getContent()`, `for()`, `required()`; `render()`. Hooks: `prepareClasses()`, `getRequiredClasses()`.

## 10. Buttons — `AbstractButton` (`src/Field/AbstractButton.php`, extends `PartsField`)

`Button`, `SubmitButton`, `ResetButton` set `getType()`. The button tag is assembled in `generateInput()` (`:243-277`) from `Html::button()` or the tag given to `button(?Button)`: `buttonAttributes()`, `addButtonAttributes()`, `buttonId()`, `buttonClass(...)` (replaces, sets `$classesReplaced`), `addButtonClass(...)`, `name()`, `value()`, `ariaDescribedBy()`, `ariaLabel()`, `autofocus()`, `tabIndex()`, `disabled()`, `form()`, `text()`, `icon(name, type)`, `badge(int)`, `outline()`, `ghost()`, `inverse()`; `fieldData()`. Content = `renderBeforeText()` + encoded text + `renderAfterText()` (`:300-324`). Hooks: `prepareClasses()`, `iconClasses()`, `badgeClasses()`, `getColorClasses()`, `getSizeClasses()`, `roundedClass()`, `renderBeforeText()`, `renderAfterText()`.

`ButtonGroup` (`src/Field/ButtonGroup.php`) — a `Widget`: `addButton(Widget|Tag)` takes a form button (a `Yiisoft\Widget\Widget`) or an anchor (a `Blackcube\Html\Tag\Base\Tag`), `buttons(array)`; `renderButton()` per position calls `class(...)` and `render()` on each item. Hooks: `itemClasses()`, `getRingColorClass()`, `getHoverColorClass()`, `getTextColorClass()`, `prepareClasses()`.

## 11. Measured

`tests/Model/ChampsCest.php`, `ToggleCest.php` (25 tests): each field renders its native tag, name, value, label, hint and error; the toggle checked state follows the model value.
