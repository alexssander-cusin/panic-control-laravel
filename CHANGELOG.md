# Changelog

All notable changes to `alexssander-cusin/panic-control-laravel` will be documented in this file.

## v1.3.1 - 2023-10-19
- [FIXED] Use Abstract Class

## v1.3.0 - 2023-09-26
- [FEATURE] Add support of Facade Manager

## v1.2.0 - 2023-08-09
- [FEATURE] Add support ENDPOINT Store [#5](https://github.com/alexssander-cusin/panic-control-laravel/issues/5)
- [FEATURE] Add support a database connections [#7](https://github.com/alexssander-cusin/panic-control-laravel/issues/7)
- [FEATURE] Create helper for tests createPanic($count, $parameters) for create panics depending on the store [#5](https://github.com/alexssander-cusin/panic-control-laravel/issues/5)
- [FEATURE] Create helper for tests makeFakeEndpoint($response, $status) for create fake endpoint with parameters [#5](https://github.com/alexssander-cusin/panic-control-laravel/issues/5)
- [FIXED] Add empty rules on factory [#5](https://github.com/alexssander-cusin/panic-control-laravel/issues/5)
- [FIXED] Import PanicControl\Stores\FileStore on resources/stubs/PanicControlServiceProvider.php.stub [#5](https://github.com/alexssander-cusin/panic-control-laravel/issues/5)
- [FIXED] Remove Final Keyword ¶ for Store Classes [#5](https://github.com/alexssander-cusin/panic-control-laravel/issues/5)
- [REFACTOR] change config('panic-control.cache.time') to config('panic-control.cache.ttl') [#5](https://github.com/alexssander-cusin/panic-control-laravel/issues/5)

## v1.1.0 - 2023-07-04
- [FEATURE] Support File Store

## v1.0.0 - 2023-05-26

- [FEATURE] Create Panic Control with [Facade]/[Command]/[Helper];
- [FEATURE] Update Panic Control with [Facade]/[Command]/[Helper];
- [FEATURE] Check Panic Control with [Facade]/[Command]/[Helper];
- [FEATURE] [Supplementary rules support](https://github.com/alexssander-cusin/panic-control-laravel#rules);
- [FEATURE] Rule: [Route Name](https://github.com/alexssander-cusin/panic-control-laravel#route-name);
- [FEATURE] Rule: [URL Path](https://github.com/alexssander-cusin/panic-control-laravel#url-path);
- [FEATURE] Rule: [User Logged](https://github.com/alexssander-cusin/panic-control-laravel#user-logged);
- [FEATURE] Rule: [Sampling](https://github.com/alexssander-cusin/panic-control-laravel#sampling);
- [FEATURE] Create [Custom Rules](https://github.com/alexssander-cusin/panic-control-laravel#custom-rules)

[Facade]: https://github.com/alexssander-cusin/panic-control-laravel#facade
[Helper]: https://github.com/alexssander-cusin/panic-control-laravel#helper
[Command]: https://github.com/alexssander-cusin/panic-control-laravel#command
