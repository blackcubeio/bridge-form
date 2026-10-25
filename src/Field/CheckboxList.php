<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\FormModelInterface;
use Yiisoft\Widget\Widget;

/**
 * A group of checkboxes: several values, passed as an array.
 *
 * Usage:
 *   CheckboxList::widget()
 *       ->name('features')
 *       ->items(['wifi' => 'WiFi', 'pool' => 'Pool'])
 *       ->values(['wifi'])
 *       ->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class CheckboxList extends AbstractItemList
{
    /** @var string[] */
    protected array $values = [];

    /**
     * Creates the field for a model property.
     */
    public static function create(?FormModelInterface $model = null, ?string $property = null): static
    {
        $field = static::widget();

        if ($model !== null && $property !== null) {
            $field = $field->inputData(new FormModelInputData($model, $property));
        }

        return $field->fieldParts();
    }

    /**
     * @param string[] $values
     */
    public function values(array $values): static
    {
        $new = clone $this;
        $new->values = $values;

        return $new;
    }

    protected function prepareItem(string $value, string $label, string $id, int $position): Widget
    {
        return $this->makeCheckbox()
            ->inputValue($value)
            ->label($label)
            ->checked($this->isSelected($value))
            ->inputId($id);
    }

    protected function isSelected(string $value): bool
    {
        $selected = $this->values;

        if ($selected === []) {
            $modelValue = $this->getInputData()->getValue();
            $selected = is_array($modelValue) === true ? $modelValue : [];
        }

        return in_array($value, $selected, true);
    }

    /**
     * Several values leave under the same name: it carries brackets.
     */
    protected function itemsName(?string $name): ?string
    {
        if ($name !== null && str_ends_with($name, '[]') === false) {
            $name .= '[]';
        }

        return $name;
    }

    protected function defaultBaseId(): string
    {
        return 'checkbox-list';
    }

    /**
     * The checkbox of an item. An interface project renders its own by
     * overriding this method.
     */
    protected function makeCheckbox(): Checkbox
    {
        return Checkbox::widget();
    }
}
