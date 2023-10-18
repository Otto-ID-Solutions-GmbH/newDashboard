<?php

namespace Cintas\Http\Controllers\Facility;

use Cintas\Http\Controllers\Controller;
use Cintas\Http\Resources\Facility\LaundryCustomer as LaundryCustomerResource;
use Cintas\Repositories\FacilityRepository;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    private $facilityRepo;

    public function __construct(FacilityRepository $facilityRepo)
    {
        $this->facilityRepo = $facilityRepo;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $facilityId)
    {
        $customers = $this->facilityRepo->getCustomers($facilityId);
        return LaundryCustomerResource::collection($customers);
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
        $customer = $this->facilityRepo->getCustomer($id);
        return LaundryCustomerResource::make($customer);
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
