<?php

namespace Cintas\Http\Resources\Items;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
{
    private $missingEPCs;

    public function __construct($resource, array $missingEPCs = [])
    {
        parent::__construct($resource);
        $this->missingEPCs = $missingEPCs;
    }


    public function toArray($request)
    {
        return [
            'data' => $this->collection
        ];
    }

    public function with($request)
    {
        return [
            'error' => [
                'unknown_epcs' => $this->missingEPCs,
            ]
        ];
    }

}
