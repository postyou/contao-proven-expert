# Contao ProvenExpert

[![](https://img.shields.io/packagist/v/postyou/contao-proven-expert.svg)](https://packagist.org/packages/postyou/contao-proven-expert)
[![](https://img.shields.io/packagist/l/postyou/contao-proven-expert.svg)](https://packagist.org/packages/postyou/contao-proven-expert)

This extension integrates the ProvenExpert API in contao. An account at [ProvenExpert](https://www.provenexpert.com) is required.

## Goal

This extension tries to store ProvenExpert content locally whenever possible, so that no requests are made to third parties when the website is accessed.

> [!NOTE]
> This is currently only implemented for images. If script or link tags are present in the widget HTML (e.g. type `landing`), third party requests will still be made.

## Cache

The API response is cached and automatically updated every hour. You can manually clear the cache by either:

-   saving the corresponding frontend module or
-   purging the ProvenExpert cache via the system maintenance

## Frontend Modules

The following frontend modules can be used with this extension:

### ProvenExpert Widget

Creates a new rating seal. Documentation (german) at [developer.provenexpert.com/#widget](https://developer.provenexpert.com/#widget).

Available types:

| Type        | Docs section                               |
| ----------- | ------------------------------------------ |
| `portrait`  | "Bewertungssiegel hochkant"                |
| `square`    | "Bewertungssiegel quadratisch"             |
| `landscape` | "Bewertungssiegel quer"                    |
| `circle`    | "Qualiätssiegel"                           |
| `logo`      | "ProvenExpert-Logo"                        |
| `bar`       | "Bewertungssiegel am unteren Browser-Rand" |
| `landing`   | "Bewertungs-Widget"                        |
| `awards`    | "Award-Widgets"                            |

#### Type `custom`

This provides a simple HTML field into which you can paste HTML code generated in the ProvenExpert Dashboard, for example. Images contained in the HTML code are downloaded and cached every hour so that only the local version of the image will be output.

### ProvenExpert RichSnippet

Creates a new rich snippet for google rating. Available options at [developer.provenexpert.com/#rating-summary-richsnippet](https://developer.provenexpert.com/#rating-summary-richsnippet).
