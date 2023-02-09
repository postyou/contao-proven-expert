<?php

declare(strict_types=1);

/*
 * This file is part of postyou/contao-proven-expert
 *
 * (c) POSTYOU Digital- & Filmagentur
 *
 * @license LGPL-3.0+
 */

use Doctrine\DBAL\Types\Types;
use Postyou\ContaoProvenExpert\Controller\FrontendModule\ProvenExpertRichSnippet;
use Postyou\ContaoProvenExpert\Controller\FrontendModule\ProvenExpertWidget;

$GLOBALS['TL_DCA']['tl_module']['fields']['peWidgetType'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50', 'submitOnChange' => true],
    'sql' => ['type' => Types::STRING, 'length' => 64, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['peWidgetWidth'] = [
    'inputType' => 'text',
    'exclude' => true,
    'eval' => ['mandatory' => true, 'maxlength' => 128, 'tl_class' => 'w50', 'rgxp' => 'natural'],
    'sql' => ['type' => Types::STRING, 'length' => 128, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['peWidgetOptions'] = [
    'inputType' => 'keyValueWizard',
    'exclude' => true,
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => Types::TEXT, 'length' => 65535, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['peHtml'] = [
    'inputType' => 'textarea',
    'eval' => ['allowHtml' => true],
    'sql' => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_module']['fields']['html']['eval']['tl_class'] .= ' clr';

$GLOBALS['TL_DCA']['tl_module']['palettes'][ProvenExpertWidget::TYPE] = '
    {title_legend},name,headline,type;
    {config_legend},peWidgetType;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},cssID
';

$GLOBALS['TL_DCA']['tl_module']['palettes'][ProvenExpertRichSnippet::TYPE] = '
    {title_legend},name,headline,type;
    {config_legend},peWidgetOptions;
    {template_legend:hide},customTpl;
    {protected_legend:hide},protected;
    {expert_legend:hide},cssID
';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'peWidgetType';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_portrait'] = 'peWidgetWidth,peWidgetOptions';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_square'] = 'peWidgetWidth,peWidgetOptions';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_landscape'] = 'peWidgetWidth,peWidgetOptions';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_circle'] = 'peWidgetWidth,peWidgetOptions';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_logo'] = 'peWidgetWidth,peWidgetOptions';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_bar'] = 'peWidgetOptions';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_landing'] = 'peWidgetOptions';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_awards'] = 'peWidgetOptions';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['peWidgetType_custom'] = 'html';
