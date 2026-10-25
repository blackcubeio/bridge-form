<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Blackcube\BridgeFormModel\BridgeFormModel;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\FormModel\FormModelInterface;
use Yiisoft\FormModel\ParsedProperty;
use Blackcube\Html\Html;

/**
 * Elastic field widget — auto-renders based on JSON Schema meta.
 *
 * Usage:
 *   Elastic::widget()($model, $attribute, $elasticOptions)->render()
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class Elastic
{
    public function __construct(
        private ?FormModelInterface $model = null,
        private ?string $property = null,
        private array $elasticOptions = [],
    ) {
    }

    public function render(): string
    {
        $html = '';
        $model = $this->model;
        $attribute = $this->property;
        $propertyName = $attribute === null ? null : (new ParsedProperty($attribute))->name;

        if ($model instanceof BridgeFormModel === true && $propertyName !== null) {
            $properties = $model->getProperties();
            if (isset($properties[$propertyName]) === true) {
                $meta = $properties[$propertyName]->getMeta();
                $field = $meta['field'] ?? 'text';

                $html = match ($field) {
                    'textarea' => $this->renderTextarea($model, $attribute),
                    'wysiwyg' => $this->renderWysiwyg($model, $attribute, $propertyName, $meta),
                    'email' => $this->renderInput('email', $model, $attribute),
                    'number' => $this->renderInput('number', $model, $attribute),
                    'date' => $this->renderInput('date', $model, $attribute),
                    'datetime-local' => $this->renderInput('datetime-local', $model, $attribute),
                    'dropdownList', 'dropdownlist' => $this->renderDropdown($model, $attribute, $meta),
                    'radioList', 'radiolist' => $this->renderRadioList($model, $attribute, $meta),
                    'checkboxList', 'checkboxlist' => $this->renderCheckboxList($model, $attribute, $meta),
                    'checkbox' => $this->renderCheckbox($model, $attribute),
                    'file' => $this->renderFileUpload($model, $attribute, $meta, false),
                    'files' => $this->renderFileUpload($model, $attribute, $meta, true),
                    default => $this->renderInput('text', $model, $attribute),
                };
            }
        }

        return $html;
    }

    private function renderInput(string $type, BridgeFormModel $model, string $attribute): string
    {
        $field = $this->makeInput($model, $attribute)
            ->type($type)
            ->render();

        return Html::div($field, ['class' => implode(' ', $this->blockClasses())])->encode(false)->render();
    }

    private function renderTextarea(BridgeFormModel $model, string $attribute): string
    {
        $field = $this->makeTextarea($model, $attribute)
            ->render();

        return Html::div($field, ['class' => implode(' ', $this->blockClasses())])->encode(false)->render();
    }

    private function renderWysiwyg(BridgeFormModel $model, string $attribute, string $propertyName, array $meta): string
    {
        $inputData = new FormModelInputData($model, $attribute);
        $id = $inputData->getId();
        $name = $inputData->getName();
        $value = (string) ($model->{$propertyName} ?? '');

        $label = $inputData->getLabel();
        $hint = $inputData->getHint();

        $html = Html::openTag('div', ['class' => implode(' ', $this->blockClasses())]);

        if ($label !== null && $label !== '') {
            $html .= $this->makeLabel($label)->for($id)->render();
        }

        $html .= Html::openTag('div', ['class' => implode(' ', $this->controlClasses())]);
        $html .= $this->makeWysiwyg($id, $name, $value, $meta);
        $html .= Html::closeTag('div');

        if ($hint !== null && $hint !== '') {
            $html .= Html::tag('p', Html::encode($hint), ['class' => implode(' ', $this->hintClasses())]);
        }

        $html .= Html::closeTag('div');

        return $html;
    }

    private function renderDropdown(BridgeFormModel $model, string $attribute, array $meta): string
    {
        $options = $this->extractItems($meta);

        $field = $this->makeSelect($model, $attribute)
            ->optionsData($options)
            ->render();

        return Html::div($field, ['class' => implode(' ', $this->blockClasses())])->encode(false)->render();
    }

    private function renderRadioList(BridgeFormModel $model, string $attribute, array $meta): string
    {
        $items = $this->extractItems($meta);

        $field = $this->makeRadioList($model, $attribute)
            ->items($items)
            ->render();

        return Html::div($field, ['class' => implode(' ', $this->blockClasses())])->encode(false)->render();
    }

    private function renderCheckboxList(BridgeFormModel $model, string $attribute, array $meta): string
    {
        $items = $this->extractItems($meta);

        $field = $this->makeCheckboxList($model, $attribute)
            ->items($items)
            ->render();

        return Html::div($field, ['class' => implode(' ', $this->blockClasses())])->encode(false)->render();
    }

    private function renderCheckbox(BridgeFormModel $model, string $attribute): string
    {
        $field = $this->makeCheckbox($model, $attribute)
            ->uncheckValue('0')
            ->inputValue('1')
            ->render();

        return Html::div($field, ['class' => implode(' ', $this->blockClasses())])->encode(false)->render();
    }

    private function renderFileUpload(BridgeFormModel $model, string $attribute, array $meta, bool $multiple): string
    {
        $accept = [];
        if (empty($meta['fileType']) === false) {
            $accept = array_map('trim', explode(',', $meta['fileType']));
        }

        $upload = $this->makeUpload($model, $attribute);

        if (isset($this->elasticOptions['upload']) === true) {
            $upload = $upload->endpoint($this->elasticOptions['upload']);
        }
        if (isset($this->elasticOptions['preview']) === true) {
            $upload = $upload->previewEndpoint($this->elasticOptions['preview']);
        }
        if (isset($this->elasticOptions['delete']) === true) {
            $upload = $upload->deleteEndpoint($this->elasticOptions['delete']);
        }

        if (empty($accept) === false) {
            $upload = $upload->accept($accept);
        }

        if ($multiple) {
            $upload = $upload->multiple();
        }

        return Html::div($upload->render(), ['class' => implode(' ', $this->blockClasses())])
            ->encode(false)
            ->render();
    }

    protected function makeInput(FormModelInterface $model, string $property): Input
    {
        return Input::create($model, $property);
    }

    protected function makeTextarea(FormModelInterface $model, string $property): Textarea
    {
        return Textarea::create($model, $property);
    }

    protected function makeSelect(FormModelInterface $model, string $property): Select
    {
        return Select::create($model, $property);
    }

    protected function makeRadioList(FormModelInterface $model, string $property): RadioList
    {
        return RadioList::create($model, $property);
    }

    protected function makeCheckboxList(FormModelInterface $model, string $property): CheckboxList
    {
        return CheckboxList::create($model, $property);
    }

    protected function makeCheckbox(FormModelInterface $model, string $property): Checkbox
    {
        return Checkbox::create($model, $property);
    }

    protected function makeUpload(FormModelInterface $model, string $property): Upload
    {
        return Upload::create($model, $property);
    }

    protected function makeLabel(string $content): Label
    {
        return new Label($content);
    }

    /**
     * The editor of a wysiwyg field: a native textarea here, an editor host in
     * the packages built on top. The id and the name are the ones of the field;
     * the meta carries the options declared by the schema.
     */
    protected function makeWysiwyg(string $id, string $name, string $value, array $meta): string
    {
        return Html::textarea($name, $value, ['id' => $id])->render();
    }

    /**
     * Classes of the block wrapping each field derived from the schema.
     *
     * @return string[]
     */
    protected function blockClasses(): array
    {
        return [];
    }

    /**
     * Classes of the control container, for the fields Blackcube does not
     * render itself.
     *
     * @return string[]
     */
    protected function controlClasses(): array
    {
        return [];
    }

    /**
     * Classes of the hint under the wysiwyg field.
     *
     * @return string[]
     */
    protected function hintClasses(): array
    {
        return [];
    }

    private function extractItems(array $meta): array
    {
        $options = [];
        if (empty($meta['items']) === false && is_array($meta['items']) === true) {
            foreach ($meta['items'] as $item) {
                if (isset($item['value'], $item['title']) === true) {
                    $options[(string) $item['value']] = $item['title'];
                }
            }
        }
        return $options;
    }
}
