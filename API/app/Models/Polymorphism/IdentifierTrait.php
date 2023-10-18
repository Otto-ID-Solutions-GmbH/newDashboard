<?php
/**
 * Created by PhpStorm.
 * User: afuhr
 * Date: 09.10.2018
 * Time: 17:28
 */

namespace Cintas\Models\Polymorphism;


use Cintas\Models\Actions\ScanAction;

trait IdentifierTrait
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->append(['identifier', 'identifier_type']);
    }

    public function getLabelAttribute($value)
    {
        return $this->identifier;
    }

    public function identifiable()
    {
        return $this->morphTo();
    }

    public function not_identified_in_scans()
    {
        return $this->morphToMany(ScanAction::class, 'identifier', 'identifier_scan_action');
    }

}