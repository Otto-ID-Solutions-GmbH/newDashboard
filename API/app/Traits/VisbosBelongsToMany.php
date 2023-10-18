<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 01.09.2017
 * Time: 15:42
 */

namespace Cintas\Traits;


use Carbon\Carbon;
use EndyJasmi\Cuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class VisbosBelongsToMany extends BelongsToMany
{

    /**
     * Indicates if soft-delete timestamps are available on the pivot table.
     *
     * @var bool
     */
    public $withSoftDeletes = false;


    /**
     * The custom pivot table column for the deleted_at timestamp.
     *
     * @var string
     */
    protected $pivotDeletedAt;


    /**
     * Specify that the pivot table has deletion timestamps.
     *
     * @param string $deletedAt
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function withSoftDeletes($deletedAt = null)
    {
        $this->withSoftDeletes = true;
        $this->pivotDeletedAt = $deletedAt;

        return $this->withPivot($this->deletedAt());
    }

    public function withTrashed()
    {
        $this->withSoftDeletes = false;
        return $this;
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function deletedAt()
    {

        if ($this->pivotDeletedAt) {
            return $this->pivotDeletedAt;
        }

        if (method_exists($this->parent, 'getDeletedAtColumn')) {
            $this->parent->getDeletedAtColumn();
        }

        return 'deleted_at';
    }

    /**
     * Create a new belongs to many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Database\Eloquent\Model $parent
     * @param  string $table
     * @param  string $foreignPivotKey
     * @param  string $relatedPivotKey
     * @param  string $parentKey
     * @param  string $relatedKey
     * @param  string $relationName
     * @return void
     */
    public function __construct(Builder $query, Model $parent, string $table, string $foreignPivotKey, string $relatedPivotKey, string $parentKey, string $relatedKey, string $relationName = null)
    {
        parent::__construct($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }

    public function attach($id, array $attributes = [], $touch = true)
    {
        if ($this->hasCuidColumn() && !array_key_exists('cuid', $attributes)) {
            $attributes = array_merge($attributes, ['cuid' => Cuid::cuid()]);
        }


        if ($this->withSoftDeletes) {

            // First we need to attach any of the associated models that are not currently
            // in this joining table. We'll spin through the given IDs, checking to see
            // if they exist in the array of current ones, and if not we will insert.
            $current = $this->newPivotQuery()->pluck(
                $this->relatedPivotKey
            )->all();

            if (!in_array($id, $current)) {
                parent::attach($id, $attributes, $touch);

                $changes['attached'][] = $this->castKey($id);
            }

            // Now we'll try to update an existing pivot record with the attributes that were
            // given to the method. If the model is actually updated we will add it to the
            // list of updated pivot records so we return them back out to the consumer.
            elseif (count($attributes) > 0 &&
                parent::updateExistingPivot($id, $attributes, $touch)) {
                $changes['updated'][] = $this->castKey($id);
            }

        } else {
            parent::attach($id, $attributes, $touch);
        }

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

    public function detach($ids = null, $touch = true)
    {

        $query = $this->newPivotQuery();

        // If associated IDs were passed to the method we will only delete those
        // associations, otherwise all of the association ties will be broken.
        // We'll return the numbers of affected rows when we do the deletes.
        if (!is_null($ids)) {
            $ids = $this->parseIds($ids);

            if (empty($ids)) {
                return 0;
            }

            $query->whereIn($this->relatedPivotKey, (array)$ids);
        }

        // Once we have all of the conditions set on the statement, we are ready
        // to run the delete on the pivot table. Then, if the touch parameter
        // is true, we will go ahead and touch all related models to sync.
        if ($this->withSoftDeletes) {
            $results = $query->update([$this->deletedAt() => Carbon::now()]);
        } else {
            $results = $query->delete();
        }


        if ($touch) {
            $this->touchIfTouching();
        }

        return $results;

    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param  array $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get($columns = ['*'])
    {
        if ($this->withSoftDeletes) {
            $this->query = $this->query->whereNull($this->getQualifiedDeletedAtColumn());
            return parent::get($columns);
        } else {
            return parent::get($columns);
        }
    }

    /**
     * Handle dynamic method calls to the relationship.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        $result = $this->query->{$method}(...$parameters);

        if ($result === $this->query) {
            return $this;
        }

        return $result;
    }

    /**
     * Determine if the current model implements soft deletes.
     * @param $related Pivot
     * @return bool
     */
    protected function useSoftDeletes(Pivot $related = null)
    {
        $instance = $this;
        if ($related) {
            $instance = $related;
        }
        return method_exists($instance, 'runSoftDelete');
    }

    public function getQualifiedDeletedAtColumn()
    {
        return $this->getTable() . '.' . $this->deletedAt();
    }

}