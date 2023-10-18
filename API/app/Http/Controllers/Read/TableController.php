<?php

namespace Cintas\Http\Controllers\Read;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\MessageTypes\TagItron\TagItronReadDataInput;
use Cintas\Http\Resources\Actions\ScanAction as ScanActionResource;
use Cintas\Http\Resources\Identifiers\RfidTag as RfidTagResource;
use Cintas\Repositories\ReaderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JsonMapper;

class TableController extends Controller
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
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $mapper = new JsonMapper();
        $json = json_decode(json_encode($request->input('data')));
        $input = $mapper->mapArray($json, [], TagItronReadDataInput::class);

        DB::transaction(function () use (&$result, $input) {
            $result = $this->readerRepository->registerBundlingOfItems($input);
        }, 5);

        $result->load(['reader']);

        $allUnknownEpcs = $result->flatMap(function ($e) {
            return $e->unknown_rfid_tags;
        });

        return ScanActionResource::collection($result)
            ->additional([
                'error' => ['unknown_epcs' => RfidTagResource::collection($allUnknownEpcs)]
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
