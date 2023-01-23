<?php
namespace RI5\Services\Traits;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface ;
use Symfony\Contracts\Service\Attribute\Required;

trait ConfigAwareTrait{
    private $parameters;
   
    #[Required]
    public function setParameterBag(ParameterBagInterface $parameterBag){
        $this->parameters = $parameterBag->get("RI5");
    }

    public function getConfigItem(string $key, $default = null) {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
        return $default;
    }

   
}
