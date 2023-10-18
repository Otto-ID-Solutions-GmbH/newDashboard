<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 14.10.2018
 * Time: 20:24
 */

namespace Cintas\Repositories;


use Cintas\Facades\Statistics;
use Cintas\Models\Identifiables\RFIDTag;
use Cintas\Models\Items\Item;
use Illuminate\Database\Eloquent\Collection;

class EloquentIdentifierRepository implements IdentifierRepository
{

    public function getIdentifiers()
    {
        return RFIDTag::all();
    }

    public function getIdentifiables($since = null)
    {
        $types = RFIDTag::query()->select('identifiable_type')
            ->distinct()->pluck('identifiable_type');
        $allItems = new Collection();

        foreach ($types as $typeName) {
            if ($typeName) {
                $class = Statistics::getMorphedModel($typeName);

                $query = $class::query();

                if ($since) {
                    $query = $query->where('updated_at', '>=', $since);
                }

                if ($class === Item::class) {
                    $query->with(['product.product_type', 'rfid_tags']);
                }

                $items = $query->get();

                $allItems = $allItems->concat($items);
            }
        }

        return $allItems;
    }

    public function getIdentifiable($identifiableType, $identifiableId)
    {
        return Statistics::findOrFailPolymorphModel($identifiableType, $identifiableId);
    }

}