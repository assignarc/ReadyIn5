<?php
namespace RI5\Services\Traits;

trait RI5ServiceTrait{
    use MessengerTrait;
    use EntityAwareTrait;
    use LoggerAwareTrait;
    use ConfigAwareTrait;
    use EventDispatcherTrait;
    use CacheAwareTrait;
}
