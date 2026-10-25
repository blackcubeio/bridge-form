<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Yiisoft\Widget\Widget;

/**
 * A group of toggles: several values, passed as an array. The list is a
 * CheckboxList whose items are toggles, so an array property gets one switch
 * per value, selected when the property holds it.
 *
 * Usage:
 *   ToggleList::create($model, 'features')
 *       ->items(['wifi' => 'WiFi', 'pool' => 'Pool'])
 *       ->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ToggleList extends CheckboxList
{
    protected function prepareItem(string $value, string $label, string $id, int $position): Widget
    {
        return $this->makeToggle()
            ->inputValue($value)
            ->label($label)
            ->checked($this->isSelected($value))
            ->inputId($id);
    }

    protected function defaultBaseId(): string
    {
        return 'toggle-list';
    }

    /**
     * The toggle of an item. An interface project renders its own by
     * overriding this method.
     */
    protected function makeToggle(): Toggle
    {
        return Toggle::widget();
    }
}
