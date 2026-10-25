<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Yiisoft\FormModel\FormModelInterface;
use Yiisoft\FormModel\FormModelInputData;
use Blackcube\BridgeForm\Traits\FieldDataTrait;
use Blackcube\BridgeForm\Traits\WrapperAttributesTrait;
use Blackcube\Html\Html;

/**
 * Toggle widget - on/off switch for forms
 *
 * Usage:
 *   Toggle::widget()()->name('setting')->render()
 *   Toggle::widget()()->name('notifications')->checked()->render()
 *   Toggle::widget()()->name('notifications')->label('Enable notifications')->checked()->render()
 *   Toggle::widget()($model, 'notifications')->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Toggle extends AbstractField
{
    use FieldDataTrait;
    use WrapperAttributesTrait;

    protected string $template = "{input}\n{label}\n{hint}\n{error}";

    protected ?string $inputContainerTag = null;
    protected array $inputContainerAttributes = [];

    private ?string $value = null;
    protected ?bool $checked = null;
    protected bool $disabled = false;
    private ?string $ariaLabel = null;

    /**
     * Sets the value
     */

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
    public function inputValue(string $value): static
    {
        $new = clone $this;
        $new->value = $value;

        return $new;
    }

    /**
     * Checks the toggle
     */
    public function checked(bool $checked = true): static
    {
        $new = clone $this;
        $new->checked = $checked;
        return $new;
    }

    /**
     * Disables the toggle
     */
    public function disabled(bool $disabled = true): static
    {
        $new = clone $this;
        $new->disabled = $disabled;
        return $new;
    }

    /**
     * Sets aria-label for mode without visible label
     */
    public function ariaLabel(?string $value): static
    {
        $new = clone $this;
        $new->ariaLabel = $value;

        return $new;
    }

    /**
     * Returns switch container classes
     */
    protected function getSwitchClasses(): array
    {
        return [];
    }

    /**
     * Returns the knob classes
     */
    protected function getKnobClasses(): array
    {
        return [];
    }

    /**
     * Returns the input classes
     */
    protected function getInputClasses(): array
    {
        return [];
    }

    /**
     * Returns the switch background color (off)
     */
    protected function getSwitchBgColor(): string
    {
        return '';
    }

    /**
     * Returns the switch background color (on)
     */
    protected function getSwitchCheckedBgColor(): string
    {
        return '';
    }

    /**
     * Returns the switch ring color
     */
    protected function getSwitchRingColor(): string
    {
        return '';
    }

    /**
     * Returns the switch focus ring color
     */
    protected function getSwitchFocusRingColor(): string
    {
        return '';
    }

    /**
     * Returns the knob ring color
     */
    protected function getKnobRingColor(): string
    {
        return '';
    }

    /**
     * The checked state comes from comparing the value attribute with the
     * field value, as Checkbox does. A list value, several toggles bound to
     * one array property, checks the toggle whose value it holds.
     */
    protected function isChecked(string $inputValue, mixed $fieldValue): bool
    {
        if (is_array($fieldValue) === true) {
            $checked = in_array($inputValue, array_map('strval', $fieldValue), true);
        } elseif (is_bool($fieldValue) === true) {
            $checked = $inputValue === ($fieldValue === true ? '1' : '0');
        } else {
            $checked = $fieldValue !== null && $inputValue === (string) $fieldValue;
        }

        return $checked;
    }

    /**
     * Renders the switch (graphical toggle)
     */
    protected function generateInput(): string
    {
        $switchClasses = implode(' ', $this->getSwitchClasses());
        $knobClasses = implode(' ', $this->getKnobClasses());
        $inputClasses = implode(' ', $this->getInputClasses());

        $inputData = $this->getInputData();
        $name = $this->getName();
        $value = $this->value ?? '1';
        $checked = $this->checked;
        if ($checked === null) {
            $checked = $this->isChecked($value, $this->getValue());
        }

        $inputDefaults = [
            'type' => 'checkbox',
            'class' => $inputClasses,
        ];

        if ($name !== null) {
            $inputDefaults['name'] = $name;
        }

        $inputDefaults['value'] = $value;

        if ($checked) {
            $inputDefaults['checked'] = true;
        }

        if ($this->disabled === true) {
            $inputDefaults['disabled'] = true;
        }

        if ($inputData->getLabel() === null && $this->ariaLabel !== null) {
            $inputDefaults['aria-label'] = $this->ariaLabel;
        }

        $describedBy = $this->describedById();
        if ($describedBy !== null) {
            $inputDefaults['aria-describedby'] = $describedBy;
        }

        $inputAttributes = $this->prepareTagAttributes($inputDefaults);
        // The id goes through yiisoft: inputId(), then the tag attributes, then
        // the input data, and the label is pointed at it.
        $this->prepareIdInInputAttributes($inputAttributes);

        $inputAttributes = [
            ...$inputAttributes,
            ...$this->getFieldDataAttributes(),
        ];

        $input = Html::input('checkbox')
            ->attributes($inputAttributes)
            ->render();

        $hiddenInput = '';

        $wrapperAttributes = $this->prepareWrapperAttributes();
        Html::addCssClass($wrapperAttributes, explode(' ', $switchClasses));

        $knob = Html::span('', ['class' => $knobClasses])->render();

        return Html::div(
            $hiddenInput . $knob . $input,
            $wrapperAttributes
        )->encode(false)->render();
    }

    protected function prepareClasses(): array
    {
        return [];
    }
}
