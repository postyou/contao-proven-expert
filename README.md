# Contao ProvenExpert

This extension integrates the ProvenExpert API in contao. An account at [ProvenExpert](https://www.provenexpert.com) is required.

## Goal

This extension tries to store ProvenExpert content locally whenever possible, so that no requests are made to third parties when the website is accessed.

## Cache

The API response is cached and automatically updated every hour. You can manually update the content by:

-   Saving the corresponding frontend module
-   Purging the ProvenExpert cache via the system maintenance

## Frontend Modules

The following frontend modules can be used with this extension:

### ProvenExpert Widget

Creates a new rating seal. Available options at [developer.provenexpert.com/#widget](https://developer.provenexpert.com/#widget).

### ProvenExpert RichSnippet

Creates a new rich snippet for google rating. Available options at [developer.provenexpert.com/#rating-summary-richsnippet](https://developer.provenexpert.com/#rating-summary-richsnippet).
