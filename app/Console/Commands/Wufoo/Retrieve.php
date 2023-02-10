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

    protected $formsRetrieved = 0;

    protected $errorForms = [];

    protected $errorFormsCount = 0;


    /**
     * @throws Exception
     */
    public function handle()
    {
        $this->info("Wufoo Retrieve Process - Starting process......");

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

        // Create the empty array to hold the forms name and hash value to pull each entry.
        $formsHash = [];

        // Get all forms name and hash value
        foreach ($availableForms['Forms'] as $forms) {
            array_push($formsHash, [
                "formName" => $forms["Name"],
                "formHash" => $forms["Hash"],
                "formFields" => $wufoo->getFormFields($forms["Hash"]),  // Get all of the form fields.
                "formEntries" => $wufoo->getFormEntries($forms["Hash"]), // Get all of the form Entries
                "statusReport" => $this->checkFormStatus($forms["Name"], $forms["Hash"])
            ]);
        };

        foreach ($formsHash as $forms) {
            if ($forms["statusReport"] == "Error") {
                array_push($errorForms, $forms);
                Log::critical("Form hash with error => \nName:" . $forms['Name'] . "\nHash: " . $forms['Hash'] . "\nStatus: " . $forms["statusReport"]);
            }
        }

        // retrieve form fields.
        $formsRetrieved = count($formsHash);

        // Let the user know forms were pulled successfully.
        $this->info("\nWufoo Retrieve Process - " . $formsRetrieved . " Forms collected successfully......");

        // Each form has their records.

        dd($formsHash);


        // for each form an XLSX is generated with header line and data and stored in a local directory
        // - xlsx is named after form name
    }



    private function checkFormStatus($name, $hash)
    {
        if (empty($name) && empty($hash)) {
            return "Error";
        }

        return "Pass";
    }
}
