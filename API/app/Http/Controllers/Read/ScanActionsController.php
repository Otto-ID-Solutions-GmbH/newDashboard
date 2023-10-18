<?php

namespace Cintas\Http\Controllers\Read;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\Resources\Actions\ScanAction;
use Cintas\Repositories\ReaderRepository;
use Illuminate\Http\Request;

class ScanActionsController extends Controller
{

    private $readerRepository;

    /**
     * GetScanActionsController constructor.
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
    public function index(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        $type = $request->get('type');
        $sortBy = $request->get('sort_by');
        $sortDir = $request->get('sort_dir');

        if ($request->has('page')) {
            $result = $this->readerRepository->getScanActionsInTimePaginated($start, $end, $type, $sortBy, $sortDir);
        } else {
            $result = $this->readerRepository->getScanActionsInTime($start, $end, $type, $sortBy, $sortDir);
        }
        $result->load(['reader', 'out_scan_action.location']);
        return ScanAction::collection($result);

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
