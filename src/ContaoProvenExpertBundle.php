<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ContaoProvenExpertBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
