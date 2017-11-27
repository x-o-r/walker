<?php

namespace Walker;

/**
 * Class Walker
 */
class Walker
{
    /**
     * @var array
     */
    private $dataEntryPoint;
    /**
     * @var array
     */
    private $founds = [];
    /**
     * @var array
     */
    private $targets = [];

    /**
     * @param array $founds
     */
    private function setFounds(array $founds) : void
    {
        $this->founds = $founds;
    }

    /**
     * @param string $json
     * @return Walker
     */
    public function fromJson(string $json) : self
    {
        $this->setFounds([]);
        $this->dataEntryPoint = json_decode($json);

        return $this;
    }

    /**
     * @param array $rawData
     * @return Walker
     */
    public function from(array $rawData) : self
    {
        $this->setFounds([]);
        $this->dataEntryPoint = $rawData;

        return $this;
    }

    /**
     * @param $target
     * @return Walker
     */
    public function with($target) : self
    {
        $this->targets[] = $target;

        return $this;
    }

    /**
     * @param callable|null $formatter
     * @return string
     */
    public function asString(callable $formatter = null) : string
    {
        $this->run();

        return (is_null($formatter)) ? implode(', ', $this->founds) : $formatter($this->founds);
    }

    /**
     * @return array
     */
    public function asArray() : array
    {
        $this->run();

        return $this->founds;
    }

    /**
     * @param $target
     * @return array
     */
    protected function parseTarget($target) : array
    {
        $separator = '->';

        return explode($separator, $target);
    }

    /**
     * @param $node
     * @param array $matches
     * @return void
     */
    protected function walk($node, array $matches) : void
    {
        if (is_object($node)) {
            $match = array_shift($matches);
            if (property_exists($node, $match)) {
                if(count($matches) === 0 && isset($node->$match)) {
                    $this->founds[] = $node->$match;

                    return;
                }
                $this->walk($node->$match, $matches);
            }
        } elseif (is_array($node)) {
            foreach ($node as $n) {
                $this->walk($n, $matches);
            }
        }
    }

    private function run() : void
    {
        foreach ($this->targets as $target) {
            $this->walk($this->dataEntryPoint, $this->parseTarget($target));
        }
    }
}
