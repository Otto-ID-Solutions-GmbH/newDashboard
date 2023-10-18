<?php

namespace Cintas\Http\Controllers\Read;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\MessageTypes\TagItron\TagItronReadDataInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use JsonMapper;

class PortalController extends Controller
{
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $mapper = new JsonMapper();
        $json = json_decode(json_encode($request->input('data')));
        $input = $mapper->mapArray($json, [], TagItronReadDataInput::class);

        $server = $request->server();


        if ($input[0] && $input[0]->lightbarrier == 'Incoming') {
            $server['REQUEST_URI'] = url('/api/reads/portal-in-read');
            $r = $request->duplicate(null, null, null, null, null, $server);
            return Route::dispatch($r);
        }

        if ($input[0] && $input[0]->lightbarrier == 'Outgoing') {
            $server['REQUEST_URI'] = url('/api/reads/portal-out-read');
            $r = $request->duplicate(null, null, null, null, null, $server);
            return Route::dispatch($r);
        }

        return response('No direction for the Gate was provided in the lightbarrier attribute of the reads! Can\'t process request!', 422);
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
