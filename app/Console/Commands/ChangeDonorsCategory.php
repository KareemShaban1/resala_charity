<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donor;

class ChangeDonorsCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donors:change-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change donor_category to "random" for a specific range of donors';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $randomDonors = Donor::whereBetween('id', [11390, 12387])->get();

        foreach ($randomDonors as $donor) {
            $donor->donor_category = 'random';
            $donor->save();
        }

        $this->info('Donor categories updated successfully.');
    }
}
