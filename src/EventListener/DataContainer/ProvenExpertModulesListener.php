<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

namespace Postyou\ContaoProvenExpert\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Postyou\ContaoProvenExpert\Cache\ProvenExpertCacheTags;

class ProvenExpertModulesListener
{
    private $provenExpertCacheTags;

    public function __construct(ProvenExpertCacheTags $provenExpertCacheTags)
    {
        $this->provenExpertCacheTags = $provenExpertCacheTags;
    }

    /**
     * @Callback(table="tl_module", target="fields.peWidgetType.options")
     *
     * @param mixed $dc
     */
    public function onOptions($dc)
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
     *
     * @param mixed $dc
     */
    public function onSubmit($dc): void
    {
        if (!$dc instanceof DataContainer || !str_starts_with($dc->activeRecord->type, 'proven_expert_')) {
            return;
        }

        $this->provenExpertCacheTags->invalidateTagsForModule($dc->id);
    }
}
