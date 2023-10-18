<?php

namespace Cintas\Http\Controllers\Items;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\Resources\Items\Item as ItemResource;
use Cintas\Models\Items\Item;
use Cintas\Repositories\ItemRepository;
use Illuminate\Http\Request;

class ItemDetailsController extends Controller
{

    private $itemRepo;

    public function __construct(ItemRepository $itemRepo)
    {
        $this->itemRepo = $itemRepo;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string | array $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $item = Item::findOrFail($id);
        $item->load(['product', 'status_history', 'location']);

        return ItemResource::make($item);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
