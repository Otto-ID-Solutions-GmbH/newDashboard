<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.10.2018
 * Time: 15:55
 */

namespace Cintas\Http\MessageTypes\TagItron;


use Cintas\Http\MessageTypes\ReadDataInput;

class TagItronReadDataInput extends ReadDataInput
{
    /**
     * @return string
     */
    public function getIP(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIP(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getOptional(): string
    {
        return $this->optional;
    }

    /**
     * @param string|null $optional
     */
    public function setOptional(string $optional = null): void
    {
        $this->optional = $optional;
    }

    /**
     * @return string|null
     */
    public function getLightbarrier(): string
    {
        return $this->optional;
    }

    /**
     * @param string|null $lightbarrier
     */
    public function setLightbarrier(string $lightbarrier = null): void
    {
        $this->lightbarrier = $lightbarrier;
    }

    /**
     * @return TagItronTagDataInput[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param TagItronTagDataInput[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }


}