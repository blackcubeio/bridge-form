<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Field;

use Blackcube\BridgeForm\RequiredConfig;
use Blackcube\BridgeForm\Traits\AttributesTrait;
use Yiisoft\Form\Field\Base\InputField;
use Yiisoft\FormModel\FormModelInputData;
use Yiisoft\Validator\Rule\Required;

/**
 * Field base: the block assembly, without any styling.
 *
 * The name, the id, the value, the label, the hint, the errors and the
 * attributes derived from the validation rules all come from yiisoft/form.
 * What lives here is the structure: the template, the containers, the ids of
 * the parts that explain the field.
 *
 * No CSS class: an interface project inherits and sets its own by overriding
 * the extension points.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
abstract class AbstractField extends InputField
{
    use AttributesTrait;

    private static ?RequiredConfig $requiredConfig = null;

    protected string $template = "{label}\n{input}\n{hint}\n{error}";
    protected ?string $inputContainerTag = 'div';
    protected array $inputContainerAttributes = [];

    /**
     * The component classes, unless the caller has set its own.
     *
     * The field carries its default styling; as soon as the call states its
     * own classes - inputClass() or class() -, those are the ones that count,
     * and the standard steps aside instead of competing with them.
     *
     * @param string[] $classes
     * @return string[]
     */
    protected function fieldClasses(array $classes): array
    {
        $replaced = $this->classesReplaced === true || isset($this->inputAttributes['class']) === true;

        return $replaced ? [] : $classes;
    }

    /**
     * Structure of the three parts PartsField keeps private: the hint and the
     * error are paragraphs, and carry an id derived from the field one so that
     * aria-describedby can point at them.
     */
    public function fieldParts(): static
    {
        $id = $this->getInputData()->getId();

        $hintId = $id === null ? null : $id . '-description';
        $errorId = $id === null ? null : $id . '-error';

        $field = $this
            ->hintConfig([
                'tag()' => ['p'],
                'id()' => [$hintId],
            ])
            ->errorConfig([
                'tag()' => ['p'],
                'id()' => [$errorId],
            ]);

        return $field->markRequired();
    }

    /**
     * Id of the part that explains the field: the error message when there is
     * one, the hint otherwise. This is what aria-describedby carries.
     */
    protected function describedById(): ?string
    {
        $inputData = $this->getInputData();
        $id = $inputData->getId();
        $describedBy = null;

        if ($id !== null) {
            if ($inputData->getValidationErrors() !== []) {
                $describedBy = $id . '-error';
            } elseif ($inputData->getHint() !== null && $inputData->getHint() !== '') {
                $describedBy = $id . '-description';
            }
        }

        return $describedBy;
    }

    /**
     * Sets how a required field is marked, for the whole project.
     *
     * Set once, carried by every field: the label of a field part as well as
     * the standalone Label.
     */
    public static function setRequiredConfig(RequiredConfig $config): void
    {
        self::$requiredConfig = $config;
    }

    public static function getRequiredConfig(): RequiredConfig
    {
        return self::$requiredConfig ?? new RequiredConfig();
    }

    /**
     * Is the field required? The answer comes from the validation rules
     * carried by the InputData, as in yiisoft/form.
     */
    protected function isRequiredField(): bool
    {
        $required = false;
        $inputData = $this->getInputData();

        if ($inputData instanceof FormModelInputData === true) {
            foreach ($inputData->getValidationRules() as $rule) {
                if ($rule instanceof Required) {
                    $required = true;
                }
            }
        }

        return $required;
    }


    /**
     * Appends the required mark to the label content.
     *
     * The label of a field part is rendered by yiisoft/form from its content
     * alone: it never looks at the validation rules. The mark is therefore
     * appended to that content, which is the only opening the framework
     * leaves - renderLabel() is final in InputField.
     *
     * Nothing happens until a project sets a RequiredConfig.
     */
    protected function markRequired(): static
    {
        $field = $this;
        $config = self::getRequiredConfig();

        if ($config->isEmpty() === false && $this->isRequiredField() === true) {
            $content = $this->label ?? $this->getInputData()->getLabel();

            if ($content !== null && $content !== '') {
                $field = $this->label($content . (string) $config);
            }
        }

        return $field;
    }
}
