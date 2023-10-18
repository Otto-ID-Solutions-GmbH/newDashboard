<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemStatusesToActions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('skipped_item_scan_action', function (Blueprint $table) {

            $table->char('old_status_id', 25)->nullable();
            $table->foreign('old_status_id')
                ->references('cuid')->on('item_statuses')
                ->onDelete('set null');

            $table->char('new_status_id', 25)->nullable();
            $table->foreign('new_status_id')
                ->references('cuid')->on('item_statuses')
                ->onDelete('set null');

        });

        Schema::table('item_scan_action', function (Blueprint $table) {

            $table->char('old_status_id', 25)->nullable();
            $table->foreign('old_status_id')
                ->references('cuid')->on('item_statuses')
                ->onDelete('set null');

            $table->char('new_status_id', 25)->nullable();
            $table->foreign('new_status_id')
                ->references('cuid')->on('item_statuses')
                ->onDelete('set null');

        });

        //$this->computeMissingData();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

    private function computeMissingData()
    {
        $statuses = \Cintas\Models\Items\ItemStatus::query()
        ->select(['cuid', 'created_at', 'item_id'])
        ->get();

        $actions = \Cintas\Models\Actions\ScanAction::query()
            ->onlyOfType('DirtyInScan')
            ->OrOnlyOfType('CleanInScan')
            ->with(['items', 'skipped_items'])
            ->get();

        foreach ($actions as $action) {
            foreach ($action->items as $item) {

                $itemStatuses = $statuses
                    ->filter(function ($status) use ($item) {
                        return $status->item_id == $item->cuid;
                    });

                $newStatus = $itemStatuses->first(function ($status) use ($action) {
                    return $status->created_at > $action->created_at;
                });
                $oldStatus = $itemStatuses->last(function ($status) use ($action) {
                    return $status->created_at <= $action->created_at;
                });

                $item->pivot->old_status()->associate($oldStatus);
                $item->pivot->new_status()->associate($newStatus);
                $item->save();

            }
        }
    }
}
