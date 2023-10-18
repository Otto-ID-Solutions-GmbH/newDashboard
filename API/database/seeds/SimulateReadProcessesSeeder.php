<?php

use Carbon\Carbon;
use Cintas\Models\Items\Item;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SimulateReadProcessesSeeder extends Seeder
{

    private $itemCount = 1000;
    private $readerRepository;
    private $faker;

    /**
     * CintasTestDataSeeder constructor.
     * @param $readerRepository
     */
    public function __construct(\Cintas\Repositories\ReaderRepository $readerRepository, Faker $faker)
    {
        $this->readerRepository = $readerRepository;
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 5; $i++) {
            DB::transaction(function () {
                try {
                    $this->createReadProcess();
                } catch (Exception $e) {
                    Log::error('Error creating test data', ['message' => $e]);
                }
            });
        }
    }

    private function createReadProcess()
    {
        $i = $this->itemCount;
        $bundleSize = 10;

        $itemsSoil = Item::with(['rfid_tags'])->get()->random($this->faker->numberBetween(0.1 * $i, 0.6 * $i));
        $dateString = $this->faker->dateTimeBetween('-12 months', '- 3 weeks')->format('y-m-d H:m');
        $dateDryerRead = Carbon::parse($dateString);
        $this->createRead($itemsSoil, $dateDryerRead, 'DryerRead');

        $itemsClean = Item::with(['rfid_tags'])->get()->random($this->faker->numberBetween(0, 0.15 * $i));
        $itemsClean = $itemsClean->diff($itemsSoil);
        $dateCleanIn = (clone $dateDryerRead)->subHour(2);
        $this->createRead($itemsClean, $dateCleanIn, 'CleanInRead');

        $itemsToBundle = $itemsSoil->concat($itemsClean);
        $itemsToBundleChunked = $itemsToBundle->random(0.8 * $itemsToBundle->count())->shuffle()->chunk($bundleSize);
        $dateBundle = (clone $dateDryerRead)->addHour(1);
        foreach ($itemsToBundleChunked as $i) {
            $this->createRead($i, $dateBundle, 'BundleRead');
        }

        $outItems = $itemsToBundle->random(0.95 * $itemsToBundle->count());
        $dateBundle = (clone $dateBundle)->addHour(2)->toDateTimeString();
        $this->createRead($outItems, $dateBundle, 'OutRead');
    }

    private function createRead($items, $date, $action)
    {
        $tags = collect();

        $customer = \Cintas\Models\Facility\LaundryCustomer::all()->random();

        foreach ($items as $item) {
            $t = new \Cintas\Http\MessageTypes\TagItron\TagItronTagDataInput();
            $t->antenna = $this->faker->numberBetween(1, 4);
            $t->epc = $item->rfid_tags()->first()->epc;
            $t->timestamp = \Carbon\Carbon::make($date)->timezone('UTC')->toIso8601String();
            $tags->push($t);
        }

        $input = new \Cintas\Http\MessageTypes\TagItron\TagItronReadDataInput();
        $input->name = 'Dryer 1';
        $input->ip = $this->faker->localIpv4;
        $input->tags = $tags->toArray();

        if ($action === 'DryerRead') {
            $input->name = 'Dryer 1';
            $result = $this->readerRepository->registerDryerIncomingItems([$input]);
        } else if ($action === 'CleanInRead') {
            $input->name = 'Gate';
            $result = $this->readerRepository->registerCleanIncomingItems([$input]);
        } else if ($action === 'OutRead') {
            $input->name = 'Gate';
            $result = $this->readerRepository->registerOutgoingItems([$input], $customer);
        } else if ($action === 'BundleRead') {
            $input->name = 'Table 1';
            $result = $this->readerRepository->registerBundlingOfItems([$input]);
        }

        $resultAction = $result->first();
        $resultAction->created_at = $date;
        $resultAction->updated_at = $date;
        $resultAction->save();

        return $result;
    }
}
