<?php

use Carbon\Carbon;
use Cintas\Models\Actions\ScanAction;
use Cintas\Models\Items\Item;
use Cintas\Models\Items\ItemStatus;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CintasTestDataSeeder extends Seeder
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

        $this->call(CintasCustomerSeeder::class);

        factory(\Cintas\Models\Items\Item::class, $this->itemCount)
            ->create()
            ->each(function ($i) {
                $i->rfid_tags()->save(factory(\Cintas\Models\Identifiables\RFIDTag::class)->create());
            });

        // Reads

        // Outdated items
        $this->createReadProcess(
            $this->faker->dateTimeBetween('-4 months', '-3 months')->format('y-m-d H:m')
        );

        for ($i = 0; $i < 2; $i++) {
            try {
                $this->createReadProcess();
            } catch (Exception $e) {
                Log::error('Error creating test data', ['message' => $e]);
            }
        }

        // Today's reads
        for ($i = 0; $i < 3; $i++) {
            try {
                $dateString = $this->faker->unique()->dateTimeBetween('today', 'now')->format('y-m-d H:m');
                $date = Carbon::parse($dateString);
                $this->createReadProcess($date);
            } catch (Exception $e) {
                Log::error('Error creating test data', ['message' => $e]);
            }
        }



    }

    private function createReadProcess($date = null)
    {
        DB::transaction(function () use ($date) {
            $i = $this->itemCount;
            $bundleSize = 10;

            $itemsSoil = Item::with(['rfid_tags'])->get()->random($this->faker->numberBetween(0.1 * $i, 0.6 * $i));
            $dateString = $date ? $date : $this->faker->dateTimeBetween('-3 months', '-1 day')->format('y-m-d H:m');
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
            $dateBundle = (clone $dateBundle)->addHour(2);
            $this->createRead($outItems, $dateBundle, 'OutRead');
        });
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
            $action = $this->readerRepository->registerDryerIncomingItems([$input]);
            $this->updateReadDate($action->first(), $date);
        } else if ($action === 'CleanInRead') {
            $action = $this->readerRepository->registerCleanIncomingItems([$input]);
            $this->updateReadDate($action->first(), $date);
        } else if ($action === 'OutRead') {
            $action = $this->readerRepository->registerOutgoingItems([$input], $customer);
            $this->updateReadDate($action->first(), $date);
        } else if ($action === 'BundleRead') {
            $action = $this->readerRepository->registerBundlingOfItems([$input]);
            $this->updateReadDate($action->first(), $date);
        }

        return $action;
    }

    private function updateReadDate(ScanAction $action, Carbon $date)
    {
        $action->created_at = $date;
        $action->updated_at = $date;
        $action->save();

        ItemStatus::query()
            ->whereHas('item.scan_actions', function ($query) use ($action) {
                return $query->where('scan_actions.cuid', '=', $action->cuid);
            })
            ->update([
                'created_at' => $date->toDateTimeString(),
                'updated_at' => $date->toDateTimeString()
            ]);
    }

}
