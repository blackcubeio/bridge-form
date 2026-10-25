<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeForm;

use Stringable;

/**
 * How a required field is marked, for the whole project.
 *
 * The mark is set once on AbstractField and every field carries it, so a
 * project does not repeat itself field by field. Nothing is marked until a
 * configuration is set.
 *
 * The content is a Stringable: whether it ends up escaped or not is the label
 * decision, through its own encode().
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class RequiredConfig implements Stringable
{
    public function __construct(
        protected string $append = '',
    ) {
    }

    public function getAppend(): string
    {
        return $this->append;
    }

    /**
     * Nothing set means nothing marked.
     */
    public function isEmpty(): bool
    {
        return $this->append === '';
    }

    /**
     * The mark itself, as plain text: the label decides about escaping.
     */
    public function __toString(): string
    {
        return $this->append;
    }
}
