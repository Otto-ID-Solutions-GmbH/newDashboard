<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 27.06.2016
 * Time: 14:35
 */

namespace Cintas\Models;

use Cintas\Traits\CuidPivotModelTrait;
use EndyJasmi\Cuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;


abstract class AbstractModel extends Model
{

    use CuidPivotModelTrait;

    //use CuidForKey;
    public $primaryKey = 'cuid';
    public $incrementing = false;
    protected $keyType = 'char';

    protected $numberFormatter;

    protected $immutable = ['cuid'];

    protected $guarded = [];

    //protected $dateFormat = 'd.m.Y, H:i:s';

    protected static function boot()
    {

        static::creating(function ($model) {

            if ($model->cuid == null) {
                $model->cuid = (string)Cuid::cuid();
            }

            if (!$model->customer_id &&
                Schema::hasTable($model->getTable()) &&
                Schema::hasColumn($model->getTable(), 'customer_id')) {

                $model->customer_id = config('cintas.customer_cuid');

            }

        });

        parent::boot();
    }

    public function getForeignKey()
    {
        return Str::snake(class_basename($this)) . '_id';
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!array_key_exists('label', $this->attributes)) {
            $this->append('label');
        }

        $this->addHidden('display_label');
    }

    public function fill(array $attributes)
    {
        parent::fill($attributes);

        if (!$this->cuid) {
            $this->cuid = Cuid::make();
        }

    }

    public function getLabelAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $label = $this->getAttribute('display_label') ? $this->display_label : get_class($this) . ' #' . $this->cuid;

        return $label;
    }

    public function setLabelAttribute($label)
    {

        if (array_key_exists('display_label', $this->attributes)) {
            $this->display_label = $label;
        }

        $this->attributes['label'] = $label;

    }

    /**
     * @param string|AbstractModel|array $item
     * @return AbstractModel | null
     */
    public static function resolve($item)
    {

        if (is_string($item)) {
            return self::find($item);
        }

        if ($item instanceof self) {
            return $item;
        }

        if (is_array($item) && Arr::isAssoc($item)) {
            return self::find($item['cuid'] ?? null);
        }

        return null;
    }

    /**
     * @param string|AbstractModel|array $item
     * @return AbstractModel
     */
    public static function resolveOrFail($item)
    {
        $item = self::resolve($item);

        if (!$item) {
            $ex = new ModelNotFoundException();
            $ex->setModel(self::class);
            throw $ex;
        }

        return $item;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public static function isJoined($query, $table)
    {
        $joins = collect($query->getQuery()->joins);
        return $joins->pluck('table')->contains($table);
    }

}