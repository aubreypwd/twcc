# Usage

```
twcc examine|update|commands <left file> <middle file> <right file>
```

This command will compare `composer.json` packages in the middle file with those on the left and right and figure out which (of the left and right) have the greater version.

### `examine`

Will ouput suggested updates you need to make to the middle file to update to the latest version of the package it found to be greater in the left or right.

### `update`

Will write it's findings (the best version between the left and right file) to the middle file.

### `commands`

Will give you composer commands to run to update packages to the best versions found between the right and the left files.

------------

# Installation

```bash
composer global config repositories.twcc git git@github.com:aubreypwd/twcc.git
composer global require aubreypwd/twcc
```
