<?php

namespace Cintas\Http\Controllers\Identifiers;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\Resources\Identifiers\RfidTag;
use Cintas\Repositories\IdentifierRepository;
use Illuminate\Http\Request;
use Cintas\Models\Items\Item;

class IdentifiersController extends Controller
{

    private $identifierRepo;

    public function __construct(IdentifierRepository $identifierRepo)
    {
        $this->identifierRepo = $identifierRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = $this->identifierRepo->getIdentifiers();
        return RfidTag::collection($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $timeStamp = \Carbon\Carbon::now('UTC')->toDateTimeString();
        $newStatus = \Cintas\Models\Items\ItemStatusType::findByName('NewStatus');

        $epcs = $request['EPC'];
        $product_id = $request['product_id'];
        
        foreach ($epcs as $epc) {
            $statusCuid = \EndyJasmi\Cuid::make();
            $itemCuid = \EndyJasmi\Cuid::make();


            \Cintas\Models\Items\Item::create([
                'cuid' => $itemCuid,
                'created_at' => $timeStamp,
                'updated_at' => $timeStamp,
                'cycle_count' => 0,
                'product_id' => $product_id,
                // 'last_status_id' => $statusCuid,
                'customer_id' => config('cintas.customer_cuid')
            ]);
    
            // Create and save RFIDTag
            \Cintas\Models\Identifiables\RFIDTag::create([
                'cuid' => \EndyJasmi\Cuid::make(),
                'created_at' => $timeStamp,
                'updated_at' => $timeStamp,
                'epc' => $epc,
                'epc_type' => 'SGTIN',
                'identifiable_id' => $itemCuid,
                'identifiable_type' => 'Item',
                'customer_id' => config('cintas.customer_cuid')
            ]);

        }
        return response()->json([
            'message' => 'Added successfully',
        ], 201); 
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
