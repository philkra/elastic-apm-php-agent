# Breaking Changes

## 6.5.x to 6.7.0
The agent release `v6.7.0` introduces the following breaking changes:
* The API was rewritten and backwards compatibility is no longer given, please refer to the documentation
* The `EventFactory` was replaced by `PhilKra\Factorie/TracesFactory`, in case you created a custom factory, you need to implement the new interface as described [here]
* The layout of the configuration array has changed, please find an example [here]
* Only the `v2` intake API will be supported from this point on
