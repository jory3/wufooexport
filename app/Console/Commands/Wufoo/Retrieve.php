<?php

declare(strict_types=1);

namespace App\Console\Commands\Wufoo;


use Illuminate\Console\Command;
use Utilities\Identity;
use Utilities\SFTPCall;
use Helper\LineResponse;
use Log;

use Services\Wufoo;


class Retrieve extends Command
{

    protected $name = 'wufoo:retrieve';

    protected $signature = 'wufoo:retrieve';

    protected $description = 'Gets all the form entries for each form.';

    // protected $formsRetrieved = 0;


    /**
     * @throws Exception
     */
    public function handle()
    {
        $this->info("Wufoo Retrieve  Process - Starting process......");

        // Get the configuration files from the .env
        $APIKEY = \env('APIKEY');
        $PASSWORD = \env('PASSWORD');
        $SUBDOMAIN = \env('SUBDOMAIN');

        // Create and instance to connect to the api.
        $wufoo = new Wufoo($APIKEY, $PASSWORD, $SUBDOMAIN);

        // Get the all forms on the account.
        $availableForms = $wufoo->getForms();

        if (!isset($availableForms)) {
            $this->info("\n Pause Process - Wufoo Retrieve process......");
            die("There was an error in getting the forms");
        }

        // Let the user know forms were pulled successfully.
        $this->info("\n Wufoo Retrieve  Process - Forms collected successfully......");

        // Each form has their records.
    }
}
