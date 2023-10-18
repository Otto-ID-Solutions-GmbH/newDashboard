<?php

namespace Cintas\Http\Controllers\Stocktaking;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\MessageTypes\StocktakingDataInput;
use Cintas\Http\Resources\Stocktaking\StocktakingResource;
use Cintas\Repositories\ReaderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JsonMapper;

class StocktakingController extends Controller
{

    private $readerRepository;

    /**
     * DryerReadController constructor.
     * @param $readerRepository
     */
    public function __construct(ReaderRepository $readerRepository)
    {
        $this->readerRepository = $readerRepository;
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
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $mapper = new JsonMapper();
        $json = json_decode(json_encode($request->input('data')));
        $input = $mapper->map($json, new StocktakingDataInput());

        Log::info("Process request " . json_encode($request->input('data')));

        DB::transaction(function () use (&$result, $input, $mapper) {
            $result = $this->readerRepository->registerStocktaking($input);
        }, 5);

        return StocktakingResource::make($result);
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
