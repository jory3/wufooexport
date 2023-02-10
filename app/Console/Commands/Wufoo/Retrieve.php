<?php

declare(strict_types=1);

namespace App\Console\Commands\Wufoo;


use Illuminate\Console\Command;
use Utilities\Identity;
use Utilities\SFTPCall;
use Helper\LineResponse;
use Log;

use Services\Wufoo;
use Services\Report;


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
        $formsDataSet = [];

        // Get all forms name and hash value, fields, entries and status
        foreach ($availableForms['Forms'] as $forms) {
            array_push($formsDataSet, [
                "formName" => $forms["Name"],
                "formHash" => $forms["Hash"],
                "formFields" => $wufoo->getFormFields($forms["Hash"]),  // Get all of the form fields.
                "formEntries" => $wufoo->getFormEntries($forms["Hash"]), // Get all of the form Entries
                "statusReport" => $this->setFormStatus($forms["Name"], $forms["Hash"])
            ]);
        };

        // Check that the form doesn't have error status
        foreach ($formsDataSet as $forms) {
            if ($forms["statusReport"] == "Error") {
                array_push($errorForms, $forms);
                Log::critical("Form hash with error => \nName:" . $forms['Name'] . "\nHash: " . $forms['Hash'] . "\nStatus: " . $forms["statusReport"]);
                $this->errorFormsCount++;
            }
        }

        // retrieve form fields.
        $formsRetrieved = count($formsDataSet);

        // Let the user know forms were pulled successfully.
        $this->info("\nWufoo Retrieve Process - " . $formsRetrieved . " Forms collected successfully......");

        // Create a report for each form.
        $this->initReport($formsDataSet);
    }



    // Customer method to build the report.
    private function initReport($formsHash)
    {
        // Build the csv file to store the report
        $filePath = storage_path() . '/app/forms';
        $vendorName = 'Form-Entries';


        // Create a report for each form.
        foreach ($formsHash as $forms) {

            // - xlsx is named after form name
            $fileName = $forms["Name"] . "_Form.xlsx";

            $file = "$filePath/$fileName";

            // need a conditional range.
            $range = 'Z';

            $records = "data";
            $columnHeaderTitles = "";

            // for each form an XLSX is generated with header line and data and stored in a local directory
            $reportFile = Report::generate(
                $records,
                $columnHeaderTitles,
                $file,
                $vendorName,
                $range
            );;

            if (!file_exists($reportFile)) {
                // self::sendErrorMail();
                echo "File created";
                return;
            }
        }

        $this->info('Daily report generated.');
    }

    private function setFormStatus($name, $hash)
    {
        if (empty($name) && empty($hash)) {
            return "Error";
        }

        return "Pass";
    }
}
