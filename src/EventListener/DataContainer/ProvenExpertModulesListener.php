<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Postyou\ContaoProvenExpert\Cache\ProvenExpertCacheTags;

class ProvenExpertModulesListener
{
    public function __construct(
        private readonly ProvenExpertCacheTags $provenExpertCacheTags,
    ) {}

    /**
     * @Callback(table="tl_module", target="fields.peWidgetType.options")
     *
     * @return array<string, string>|void
     */
    public function onOptions(mixed $dc)
    {
        if (!$dc instanceof DataContainer) {
            return;
        }

        return [
            'portrait' => 'Bewertungssiegel hochkant',
            'square' => 'Bewertungssiegel quadratisch',
            'landscape' => 'Bewertungssiegel quer',
            'circle' => 'Qualitätssiegel',
            'logo' => 'ProvenExpert-Logo',
            'bar' => 'Bewertungssiegel am unteren Browser-Rand',
            'landing' => 'Bewertungs-Widget',
            'awards' => 'Award-Widgets',
            'custom' => 'Eigenes HTML',
        ];
    }

    /**
     * @Callback(table="tl_module", target="config.onsubmit")
     */
    public function onSubmit(mixed $dc): void
    {
        if (!$dc instanceof DataContainer || null === $dc->activeRecord || !str_starts_with((string) $dc->activeRecord->type, 'proven_expert_')) {
            return;
        }

        $this->provenExpertCacheTags->invalidateTagsForModule($dc->id);
    }
}
