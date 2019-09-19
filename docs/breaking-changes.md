
# Breaking Changes

## 6.x to 7.x
* The `EventFactoryInterface` has been changed, in case you are injecting your custom Event Factory, you will be affected.
* The methods `Transaction::setSpans`, `Transaction::getSpans`, `Transaction::getErrors` and `Transaction::setErrors` has been removed given the schema change rendered the these method unnecessary.
* `Agent::__desctruct` triggers a flushing of the payload queue, you don't need to call `send()` manually anymore.
