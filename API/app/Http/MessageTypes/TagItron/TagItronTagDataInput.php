<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 17.10.2018
 * Time: 15:56
 */

namespace Cintas\Http\MessageTypes\TagItron;


use Cintas\Http\MessageTypes\TagDataInput;

class TagItronTagDataInput extends TagDataInput
{
    /**
     * @return string
     */
    public function getEPC(): string
    {
        return $this->epc;
    }

    /**
     * @param string $epc
     */
    public function setEPC(string $epc): void
    {
        $this->epc = $epc;
    }

    /**
     * @return string
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp(string $timestamp): void
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return int|null
     */
    public function getAntenna(): int
    {
        return $this->antenna;
    }

    /**
     * @param int|null $antenna
     */
    public function setAntenna(int $antenna = null): void
    {
        $this->antenna = $antenna;
    }


}