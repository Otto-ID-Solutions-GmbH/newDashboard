<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 01.09.2017
 * Time: 15:42
 */

namespace Cintas\Traits;

use EndyJasmi\Cuid;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class VisbosMorphsToMany extends MorphToMany
{


    public function attach($id, array $attributes = array(), $touch = true)
    {
        if ($this->hasCuidColumn() && !array_key_exists('cuid', $attributes)) {
            $attributes = array_merge($attributes, ['cuid' => Cuid::cuid()]);
        }

        parent::attach($id, $attributes, $touch);

    }

    private function hasCuidColumn()
    {
        $cols = $this->getTableColumns();
        $cuidCol = array_first($cols, function ($el) {
            return $el == 'cuid';
        });

        return !($cuidCol == null);
    }

    private function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

}