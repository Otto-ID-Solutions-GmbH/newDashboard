<?php

namespace Cintas\Http\Controllers\Identifiers;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\Resources\Facility\LocationRessource;
use Cintas\Http\Resources\Identifiers\Identifiable;
use Cintas\Models\Facility\Facility;
use Cintas\Models\Facility\LaundryCustomer;
use Cintas\Models\Items\Item;
use Cintas\Repositories\IdentifierRepository;
use Illuminate\Http\Request;
use Violet\StreamingJsonEncoder\BufferJsonEncoder;
use Violet\StreamingJsonEncoder\JsonStream;

class IdentifiableController extends Controller
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
        $data = $this->identifierRepo->getIdentifiables();
        $accept = request()->header('accept');

        if ($accept === 'application/stream+json') {

            $encoder = (new BufferJsonEncoder(Identifiable::collection($data)));
            $stream = new JsonStream($encoder);

            return response()
                ->stream(function () use ($stream) {
                    while (!$stream->eof()) {
                        $string = $stream->read(1024 * 8);
                        echo $string;
                    }
                }, 200, ['content-type' => 'application/json']);
        } else {
            return Identifiable::collection($data);
        }

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
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $type, $id)
    {
        $identifiable = $this->identifierRepo->getIdentifiable($type, $id);

        if ($identifiable instanceof Item) {
            return \Cintas\Http\Resources\Items\Item::make($identifiable);
        }

        if ($identifiable instanceof Facility) {
            return LocationRessource::make($identifiable);
        }

        if ($identifiable instanceof LaundryCustomer) {
            return \Cintas\Http\Resources\Facility\LaundryCustomer::make($identifiable);
        }

        return Identifiable::make($identifiable);
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
