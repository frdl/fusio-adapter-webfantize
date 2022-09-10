Fusio-Adapter-Webfantize
=====

[Fusio] adapter which provides connections to communicate with Frdlweb API Endpoints and Webfan Services or Entities. You can install the adapter with the following steps inside your Fusio 
project:

    composer require frdl/fusio-adapter-webfantize
    php bin/fusio system:register Fusio\Adapter\Webfantize\Adapter

[Fusio]: http://fusio-project.org/
[Frdlweb API]: https://apps.api.frdl.de/
[Webfan]: https://webfan.de/

## Connections:
- KeychainRegistry/KeychainRegistryWrapper
- PleskApiClient
- Database/DatabaseWrapper (Medoo/kdbv)
- FilesystemCache
- NextcloudApiWrapper
- CircuitBreakerProtectedConnectionWrapper (todo: rewrite/redesign if usecase!?!)
- TemporaryDirectory
- LazyEventHandlers - Directoy for [`frdl\event-module`](https://github.com/frdl/event-module)
- Content-addressable storage server [`frdl\cta`](https://github.com/frdl/cta)

### ToDo/Notes
* It should more INdependent from `KeychainRegistry/KeychainRegistryWrapper`!?!
* Parameterize the connections with Context (e.g Wrapper->connect($context[AppId, RouteId, UserId, ...]))
