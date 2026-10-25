<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Blackcube\BridgeForm\Traits\AttributesTrait;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\Widget\Widget;
use Yiisoft\FormModel\FormModelInterface;
use Blackcube\Html\Html;
use Yiisoft\Validator\Rule\Required;

/**
 * Label widget - Label for form elements
 *
 * Usage:
 *   (new Label('Nom'))->for('input-name')->render()
 *   (new Label('Email', $model, 'email'))->render()
 *   Label::widget()->content('Requis')->required()->render()
 *   (new Label('', $model, 'email'))->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Label extends Widget
{
    use AttributesTrait;

    private string $content = '';
    private ?string $for = null;
    protected bool $required = false;

    public function __construct(
        string $content = '',
        private ?FormModelInterface $model = null,
        private ?string $property = null,
    ) {
        $this->content = $content;
    }

    /**
     * Sets the content of the label
     */
    public function content(string $content): static
    {
        $new = clone $this;
        $new->content = $content;
        return $new;
    }

    /**
     * Returns the content of the label
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Sets the for attribute (lie to the field)
     */
    public function for(string $for): static
    {
        $new = clone $this;
        $new->for = $for;
        return $new;
    }

    /**
     * Marks the field as required (adds *)
     */
    public function required(bool $required = true): static
    {
        $new = clone $this;
        $new->required = $required;
        return $new;
    }

    public function render(): string
    {
        $inputData = null;
        if ($this->model !== null && $this->property !== null) {
            $inputData = new FormModelInputData($this->model, $this->property);
        }

        $content = $this->content !== '' ? $this->content : ($inputData?->getLabel() ?? '');
        $for = $this->for ?? $inputData?->getId();
        $required = $this->required || $this->isRequired($inputData) === true;

        $innerHtml = Html::encode($content);

        if ($required) {
            $innerHtml .= (string) AbstractField::getRequiredConfig();
        }

        $defaults = [];
        if ($for !== null) {
            $defaults['for'] = $for;
        }

        $attributes = $this->prepareTagAttributes($defaults);
        Html::addCssClass($attributes, $this->prepareClasses());

        return Html::tag('label', $innerHtml, $attributes)->encode(false)->render();
    }

    /**
     * Is the field required? The answer comes from the validation rules carried
     * by the InputData, as in yiisoft/form.
     */
    private function isRequired(?FormModelInputData $inputData): bool
    {
        $required = false;

        if ($inputData !== null) {
            foreach ($inputData->getValidationRules() as $rule) {
                if ($rule instanceof Required) {
                    $required = true;
                }
            }
        }

        return $required;
    }

    /**
     * @return string[]
     */
    /**
     * @return string[]
     */
    protected function prepareClasses(): array
    {
        return [];
    }

    /**
     * @return string
     */
    protected function getRequiredClasses(): string
    {
        return '';
    }
}
