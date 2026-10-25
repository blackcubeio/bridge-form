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
 * A group of radio buttons: a single value for the whole list.
 *
 * Usage:
 *   RadioList::widget()
 *       ->name('plan')
 *       ->items(['small' => 'Small', 'medium' => 'Medium'])
 *       ->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class RadioList extends AbstractItemList
{
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

    protected function prepareItem(string $value, string $label, string $id, int $position): Widget
    {
        $radio = $this->makeRadio()
            ->value($value)
            ->label($label)
            ->checked($this->isSelected($value))
            ->id($id);

        if ($this->required === true && $position === 0) {
            $radio = $radio->required();
        }

        return $radio;
    }

    protected function isSelected(string $value): bool
    {
        return (string) $this->getInputData()->getValue() === $value;
    }

    protected function defaultBaseId(): string
    {
        return 'radio-list';
    }

    /**
     * The radio button of an item. An interface project renders its own by
     * overriding this method.
     */
    protected function makeRadio(): Radio
    {
        return Radio::widget();
    }
}
