<?php

namespace Cintas\Http\Controllers\Items;

use Carbon\Carbon;
use Cintas\Http\Controllers\Controller;
use Cintas\Http\Resources\Items\Item as ItemResource;
use Cintas\Models\Items\Item;
use Cintas\Repositories\ItemRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
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
    public function index(Request $request)
    {
        $itemsQuery = Item::query();
        if ($request->has('since')) {
            $timestamp = Carbon::make($request->query('since'));
            $itemsQuery = $itemsQuery->where('updated_at', '>=', $timestamp);
        }

        return \Cintas\Http\Resources\Items\Item::collection($itemsQuery->paginate(500));
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param string | array $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $itemsResult = $this->itemRepo->identifyItems($id);
        $missingEpcs = $itemsResult['unknown_epcs'];
        $items = $itemsResult['items'];
        $items->load('last_status', 'location');

        $responseData = [];

        foreach ($items as $item) {
            $label = $item->label;
            $delimiterPos = strrpos($label, ', #'); // Find delimiter from the right side
            $labelName = substr($label, 0, $delimiterPos); // Extract label name
            $epc = substr($label, $delimiterPos + 3); // Extract EPC after the delimiter
            $responseData[] = [
                'label' => trim($labelName),
                'epc' => $epc,
            ];
        }
    
        $response = [
            'data' => $responseData,
            'error' => ['unknown_epcs' => $missingEpcs]
        ];
    
        return new JsonResponse($response);
    }

    public function findByEPC(Request $request)
    {
        $epcs = $request->input('epcs');
        return $this->show($epcs);
    }

    /**
     * Display the specified resource or retrieve product names for bundled EPCs.
     *
     * @param string|array $id
     * @return \Illuminate\Http\Response
     */
    public function getBundledItemsByEPC(Request $request)
    {
        // Retrieve the bundled items based on the provided EPCs
        $epcs = $request->input('epcs');
        $itemsResult = $this->itemRepo->identifyBundledItems($epcs);

        // Extract the necessary information from the itemsResult and return the response
        $items = $itemsResult['items'];
        $missingEpcs = $itemsResult['unknown_epcs'];

        $items->load('last_status', 'location');

        $responseData = [];

        foreach ($items as $item) {
            $label = $item->label;
            $delimiterPos = strrpos($label, ', #'); // Find delimiter from the right side
            $labelName = substr($label, 0, $delimiterPos); // Extract label name
            $epc = substr($label, $delimiterPos + 3); // Extract EPC after the delimiter
            $responseData[] = [
                'label' => trim($labelName),
                'epc' => $epc,
            ];
        }
    
        $response = [
            'data' => $responseData,
            'error' => ['unknown_epcs' => $missingEpcs]
        ];
    
        return new JsonResponse($response);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
