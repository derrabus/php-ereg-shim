# php ereg shim

This package emulates the old php extension for POSIX regular expressions
(ereg) if it's not available. Internally, the PCRE extension is used to process
regular expressions.

## Requirements and Usage

The library requires php 5.3 or newer. To add it to your projects, simply add
`derrabus/ereg-shim` to your composer dependencies.

```
composer require derrabus/ereg-shim
```

This should auto-register the shim for you. However, it remains inactive as
long as the ereg extension is active. It should be safe to include it in
projects that should be tested on php 5 and php 7 simultaneously.

You can also force the usage of the shim implementation on php 5 by directly
calling the corresponding methods on the `Rabus\EregShim\Ereg` class.

Global function call        | Forced shimmed function call
--------------------------- | -------------------------------------------------
`ereg('[0-9][^0-9]', '2a')` | `\Rabus\EregShim\Ereg::ereg('[0-9][^0-9]', '2a')`

## Purpose and limitations

This library can be helpful if you need to migrate a legacy codebase to php 7.0
quickly. It will be slower than the native implementation and is not intended
to be a permanent solution. Code that depends on the ereg extension should be
refactored to use the corresponding PCRE functions instead.

The library has been developed against the test suite of the php 5.6 ereg
extension in order to get as close to the original behavior as possible. While
PCRE and POSIX regular expressions are very similar, they're not 100%
compatible. There are certain edge cases that this library cannot cover.
