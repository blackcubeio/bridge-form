<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm\Traits;

use Blackcube\BridgeForm\Field\Label;
use Blackcube\Html\Html;

/**
 * The floating label: a label set above the control, inside the same wrapper,
 * so that it looks like it floats within the field.
 *
 * The field using it only says how to render its control.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
trait FloatingLabelTrait
{
    protected string|Label|null $floatingLabel = null;

    public function floatingLabel(string|Label $label): static
    {
        $new = clone $this;
        $new->floatingLabel = $label;
        $new->template = "{input}\n{hint}\n{error}";

        return $new;
    }

    /**
     * The field control, styled for the floating label.
     */
    abstract protected function renderFloatingControl(): string;

    protected function renderFloatingLabel(): string
    {
        $label = $this->floatingLabel;
        if (is_string($label) === true) {
            $label = new Label($label);
        }

        $id = $this->getInputAttributes()['id'] ?? null;
        $id ??= $this->getTagAttributes()['id'] ?? null;
        $id ??= $this->getInputData()->getId();

        if ($id !== null) {
            $label = $label->for($id);
        }

        $label = $label->addClass(...$this->floatingLabelClasses());

        $wrapperAttributes = $this->prepareWrapperAttributes();
        Html::addCssClass($wrapperAttributes, $this->prepareFloatingWrapperClasses());

        return Html::div($label->render() . $this->renderFloatingControl(), $wrapperAttributes)
            ->encode(false)
            ->render();
    }

    /**
     * Classes of the floating label.
     *
     * @return string[]
     */
    protected function floatingLabelClasses(): array
    {
        return [];
    }

    /**
     * Classes of the wrapper holding the label and the control together.
     *
     * @return string[]
     */
    protected function prepareFloatingWrapperClasses(): array
    {
        return [];
    }
}
