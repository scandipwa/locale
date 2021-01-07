# ScandiPWA Locale

This module exposes multiple locale-related APIs for the theme's `phtml` template.

## Feature breakdown:

**1. Determine locale for store**

Exposes `$this->getLocaleCode()` in the generated `Magento_Theme/templates/scandipwa_root.phtml`. Provides the store's locale code.

**2. Determine language for store**

Exposes `$this->getLanguageCode()`, basically takes the part of `$this->getLocaleCode()` before `_`. E.g. `en_US` => `en`.
