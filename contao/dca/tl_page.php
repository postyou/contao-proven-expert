<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert.
 *
 * (c) POSTYOU Werbeagentur
 *
 * @license LGPL-3.0+
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'usePeApi';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['usePeApi'] = 'peApiId,peApiKey,peUploadDirectory';

$GLOBALS['TL_DCA']['tl_page']['fields']['usePeApi'] = [
    'default' => 0,
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true],
    'sql' => ['type' => 'string', 'length' => 1, 'fixed' => true, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['peApiId'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 128, 'tl_class' => 'w50', 'rgxp' => 'alnum'],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['peApiKey'] = [
    'inputType' => 'text',
    'eval' => ['maxlength' => 128, 'tl_class' => 'w50', 'rgxp' => 'alnum'],
    'sql' => ['type' => 'string', 'length' => 128, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_page']['fields']['peUploadDirectory'] = [
    'inputType' => 'fileTree',
    'default' => null,
    'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr', 'mandatory' => true],
    'sql' => ['type' => 'binary', 'length' => 16, 'notnull' => false],
];

PaletteManipulator::create()
    ->addLegend('pe_legend', 'publish_legend', PaletteManipulator::POSITION_BEFORE, true)
    ->addField('usePeApi', 'pe_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('rootfallback', 'tl_page')
;
