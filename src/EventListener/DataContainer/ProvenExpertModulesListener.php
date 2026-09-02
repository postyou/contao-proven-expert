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

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Postyou\ContaoProvenExpert\Cache\ProvenExpertCache;

class ProvenExpertModulesListener
{
    public function __construct(
        private readonly ProvenExpertCache $provenExpertCache,
    ) {}

    /**
     * @return array<string, string>|void
     */
    #[AsCallback('tl_module', 'fields.peWidgetType.options')]
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

    #[AsCallback('tl_module', 'config.onsubmit')]
    public function onSubmit(mixed $dc): void
    {
        if (!$dc instanceof DataContainer) {
            return;
        }

        $type = $dc->getCurrentRecord()['type'] ?? null;

        if (!\is_string($type) || !str_starts_with($type, 'proven_expert_')) {
            return;
        }

        $this->provenExpertCache->invalidateTagsForModule($dc->id);
    }
}
