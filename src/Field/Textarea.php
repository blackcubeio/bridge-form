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
use Blackcube\BridgeForm\Traits\FloatingLabelTrait;
use Blackcube\BridgeForm\Traits\WrapperAttributesTrait;
use Blackcube\Html\Html;

/**
 * Textarea widget - multi-line text area for forms
 *
 * Usage:
 *   Textarea::create()->name('comment')->placeholder('Votre commentaire...')->render()
 *   Textarea::create()->name('message')->rows(6)->floatingLabel('Message')->render()
 *   Textarea::create($model, 'message')->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Textarea extends AbstractField
{
    use FieldDataTrait;
    use FloatingLabelTrait;
    use WrapperAttributesTrait;

    private ?string $name = null;
    private ?string $value = null;
    private ?string $placeholder = null;
    private int $rows = 4;
    private ?int $cols = null;
    protected bool $disabled = false;
    protected bool $readonly = false;
    protected bool $required = false;
    private ?string $labelledBy = null;
    private ?string $describedBy = null;

    /**
     * Sets the name
     */


    /**
     * Sets the value (contenu)
     */


    /**
     * Sets the placeholder
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
    public function placeholder(string $placeholder): static
    {
        $new = clone $this;
        $new->placeholder = $placeholder;
        return $new;
    }

    /**
     * Sets the number of visible rows
     */
    public function rows(int $rows): static
    {
        $new = clone $this;
        $new->rows = $rows;
        return $new;
    }

    /**
     * Sets the width in columns
     */
    public function cols(int $cols): static
    {
        $new = clone $this;
        $new->cols = $cols;
        return $new;
    }

    /**
     * Disables the textarea
     */
    public function disabled(bool $disabled = true): static
    {
        $new = clone $this;
        $new->disabled = $disabled;
        return $new;
    }

    /**
     * Makes the textarea read only
     */
    public function readonly(bool $readonly = true): static
    {
        $new = clone $this;
        $new->readonly = $readonly;
        return $new;
    }

    /**
     * Marks the textarea as required
     */
    public function required(bool $required = true): static
    {
        $new = clone $this;
        $new->required = $required;
        return $new;
    }

    /**
     * Sets aria-labelledby
     */
    public function ariaLabelledBy(?string ...$value): static
    {
        $definedValues = [];
        foreach ($value as $oneValue) {
            if ($oneValue !== null) {
                $definedValues[] = $oneValue;
            }
        }

        $new = clone $this;
        $new->labelledBy = $definedValues === [] ? null : implode(' ', $definedValues);

        return $new;
    }

    /**
     * Sets aria-describedby
     */
    public function ariaDescribedBy(?string ...$value): static
    {
        $definedValues = [];
        foreach ($value as $oneValue) {
            if ($oneValue !== null) {
                $definedValues[] = $oneValue;
            }
        }

        $new = clone $this;
        $new->describedBy = $definedValues === [] ? null : implode(' ', $definedValues);

        return $new;
    }

    /**
     * Renders the textarea
     */
    protected function generateInput(): string
    {
        if ($this->floatingLabel !== null) {
            $input = $this->renderFloatingLabel();
        } else {
            $input = $this->renderTextarea($this->prepareClasses());
        }

        return $input;
    }

    /**
     * Renders the textarea HTML
     */
    private function renderTextarea(array $classes): string
    {
        $inputData = $this->getInputData();
        $name = $inputData->getName();
        $tagAttrs = $this->getTagAttributes();
        $id = $tagAttrs['id'] ?? $inputData->getId();
        $value = $inputData->getValue();
        $placeholder = $this->placeholder ?? $inputData->getPlaceholder();
        $required = $this->required;

        $defaults = [
            'rows' => $this->rows,
        ];

        if ($name !== null) {
            $defaults['name'] = $name;
        }

        if ($id !== null) {
            $defaults['id'] = $id;
        }

        if ($placeholder !== null) {
            $defaults['placeholder'] = $placeholder;
        }

        if ($this->cols !== null) {
            $defaults['cols'] = $this->cols;
        }

        if ($this->disabled === true) {
            $defaults['disabled'] = true;
        }

        if ($this->readonly === true) {
            $defaults['readonly'] = true;
        }

        if ($required) {
            $defaults['required'] = true;
        }

        if ($this->labelledBy !== null) {
            $defaults['aria-labelledby'] = $this->labelledBy;
        }

        if ($this->describedBy !== null) {
            $defaults['aria-describedby'] = $this->describedBy;
        }

        $attributes = $this->prepareTagAttributes($defaults);

        Html::addCssClass($attributes, $classes);

        $attributes = [
            ...$attributes,
            ...$this->getFieldDataAttributes(),
        ];

        return Html::textarea($name ?? '', $value ?? '')
            ->attributes($attributes)
            ->render();
    }

    /**
     * Renders with label flottant
     */
    protected function renderFloatingControl(): string
    {
        return $this->renderTextarea($this->prepareFloatingTextareaClasses());
    }

    /**
     * Classes for textarea simple
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }

    /**
     * Classes for textarea en mode floating label
     * @return string[]
     */
    protected function prepareFloatingTextareaClasses(): array
    {
        return [];
    }

    /**
     * Color classes of the floating textarea
     * @return string[]
     */
    protected function getFloatingTextareaColorClasses(): array
    {
        return [];
    }

    /**
     * Color classes of the floating wrapper
     * @return string[]
     */
    protected function getFloatingWrapperColorClasses(): array
    {
        return [];
    }
}
