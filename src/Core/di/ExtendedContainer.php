<?php

namespace suffi\Simple\Core\di;

/**
 * Class ExtendedContainer
 * @package suffi\Simple\Core\di
 */
class ExtendedContainer extends Container
{

    /**
     * @var Container
     */
    protected $parentsContainer = null;

    /**
     * @return Container
     */
    public function getParentsContainer()
    {
        return $this->parentsContainer;
    }

    /**
     * @param Container $parentsContainer
     */
    public function setParentsContainer(Container $parentsContainer)
    {
        $this->parentsContainer = $parentsContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key)
    {
        $obj = parent::get($key);
        if (!$obj && !is_null($this->parentsContainer)) {
            $obj = $this->parentsContainer->get($key);
        }
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key)
    {
        $has = parent::has($key);

        if (!$has && !is_null($this->parentsContainer)) {
            $has = $this->parentsContainer->has($key);
        }
        return $has;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(string $key)
    {
        $obj = parent::getDefinition($key);
        if (!$obj && !is_null($this->parentsContainer)) {
            $obj = $this->parentsContainer->getDefinition($key);
        }
        return $obj;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefinition(string $key)
    {
        $has = parent::hasDefinition($key);

        if (!$has && !is_null($this->parentsContainer)) {
            $has = $this->parentsContainer->hasDefinition($key);
        }
        return $has;
    }
}
