<?php

declare(strict_types=1);

namespace App\Console\Commands\Wufoo;


use Illuminate\Console\Command;
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
        //TEST DATA WHILE OUT OF API USUAGE
        $testData = [

            [
                "formName" => "Another Form",
                "formHash" => "m13e7xuh1dfgnx8",
                "formFields" => [
                    [
                        "FieldTitle" => "Entry Id",
                        "FieldID" => "EntryId",
                        "FieldType" => "text",
                        "SubFields" => null
                    ],
                    [
                        "FieldTitle" => "field #1",
                        "FieldID" => "Field1",
                        "FieldType" => "text",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Select a Choice",
                        "FieldID" => "Field2",
                        "FieldType" => "radio",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Date Created",
                        "FieldID" => "DateCreated",
                        "FieldType" => "date",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Created By",
                        "FieldID" => "CreatedBy",
                        "FieldType" => "text",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Last Updated",
                        "FieldID" => "LastUpdated",
                        "FieldType" => "date",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Updated By",
                        "FieldID" => "UpdatedBy",
                        "FieldType" => "text",
                        "SubFields" => null,
                    ]
                ],
                "formEntries" => [
                    "Entries" =>  [
                        [
                            "EntryId" => "1",
                            "Field1" => "A tester",
                            "Field2" => "First Choice",
                            "DateCreated" => "2023-02-09 10:43:13",
                            "CreatedBy" => "public",
                            "DateUpdated" => "",
                            "UpdatedBy" => null,
                        ],
                        [
                            "EntryId" => "2",
                            "Field1" => "Me",
                            "Field2" => "Third Choice",
                            "DateCreated" => "2023-02-09 10:43:34",
                            "CreatedBy" => "public",
                            "DateUpdated" => "",
                            "UpdatedBy" => null,
                        ]
                    ]
                ],
                "statusReport" => "Pass",
            ],
            [
                "formName" => "Another Form",
                "formHash" => "m13e7xuh1dfgnx8",
                "formFields" => [
                    [
                        "FieldTitle" => "Entry Id",
                        "FieldID" => "EntryId",
                        "FieldType" => "text",
                        "SubFields" => null
                    ],
                    [
                        "FieldTitle" => "field #1",
                        "FieldID" => "Field1",
                        "FieldType" => "text",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Select a Choice",
                        "FieldID" => "Field2",
                        "FieldType" => "radio",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Date Created",
                        "FieldID" => "DateCreated",
                        "FieldType" => "date",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Created By",
                        "FieldID" => "CreatedBy",
                        "FieldType" => "text",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Last Updated",
                        "FieldID" => "LastUpdated",
                        "FieldType" => "date",
                        "SubFields" => null,
                    ],
                    [
                        "FieldTitle" => "Updated By",
                        "FieldID" => "UpdatedBy",
                        "FieldType" => "text",
                        "SubFields" => null,
                    ]
                ],
                "formEntries" => [
                    "Entries" =>  [
                        [
                            "EntryId" => "1",
                            "Field1" => "A tester",
                            "Field2" => "First Choice",
                            "DateCreated" => "2023-02-09 10:43:13",
                            "CreatedBy" => "public",
                            "DateUpdated" => "",
                            "UpdatedBy" => null,
                        ],
                        [
                            "EntryId" => "2",
                            "Field1" => "Me",
                            "Field2" => "Third Choice",
                            "DateCreated" => "2023-02-09 10:43:34",
                            "CreatedBy" => "public",
                            "DateUpdated" => "",
                            "UpdatedBy" => null,
                        ]
                    ]
                ],
                "statusReport" => "Pass",
            ]
        ];


        $this->info("Wufoo Retrieve Process - Starting process......");

        // Get the configuration files from the .env
        $APIKEY = \env('APIKEY');
        $PASSWORD = \env('PASSWORD');
        $SUBDOMAIN = \env('SUBDOMAIN');

        // // Create and instance to connect to the api.
        // $wufoo = new Wufoo($APIKEY, $PASSWORD, $SUBDOMAIN);

        // // Get the all forms on the account.
        // $availableForms = $wufoo->getForms();

        // if (!isset($availableForms)) {
        //     $this->info("\n Pause Process - Wufoo Retrieve process......");
        //     die("There was an error in getting the forms");
        // }

        // // Create the empty array to hold the forms name and hash value to pull each entry.
        // $formsDataSet = [];

        // // Get all forms name and hash value, fields, entries and status
        // foreach ($availableForms['Forms'] as $forms) {
        //     array_push($formsDataSet, [
        //         "formName" => $forms["Name"],
        //         "formHash" => $forms["Hash"],
        //         "formFields" => $wufoo->getFormFields($forms["Hash"]),  // Get all of the form fields.
        //         "formEntries" => $wufoo->getFormEntries($forms["Hash"]), // Get all of the form Entries
        //         "statusReport" => $this->setFormStatus($forms["Name"], $forms["Hash"])
        //     ]);
        // };

        // // Check that the form doesn't have error status
        // foreach ($formsDataSet as $forms) {
        //     if ($forms["statusReport"] == "Error") {
        //         array_push($errorForms, $forms);
        //         Log::critical("Form hash with error => \nName:" . $forms['Name'] . "\nHash: " . $forms['Hash'] . "\nStatus: " . $forms["statusReport"]);
        //         $this->errorFormsCount++;
        //     }
        // }

        // // retrieve form fields.
        // $formsRetrieved = count($formsDataSet);

        // Let the user know forms were pulled successfully.
        // $this->info("\nWufoo Retrieve Process - " . $formsRetrieved . " Forms collected successfully......");

        // Let the user know form report are starting.
        $this->info("\nWufoo Report Process - Starting process......");
        // Create a report for each form.
        // $this->initReport($formsDataSet);
        $this->initReport($testData);
    }


    // Customer method to build the report.
    private function initReport($formsHash)
    {
        // Build the csv file to store the report
        if (!file_exists(storage_path() . '/app/forms')) {
            mkdir(storage_path() . '/app/forms', 0777);
        }
        $filePath = storage_path() . '/app/forms';



        // Create a report for each form.
        foreach ($formsHash as $forms) {

            // - xlsx is named after form name
            $removeSpaceName = str_replace(" ", "-", $forms["formName"]);
            $vendorName = $removeSpaceName . '_Form-Entries';
            $fileName =  $removeSpaceName . "_Form.xlsx";
            $file = "$filePath/$fileName";

            // need a conditional range.
            $range = 'AZ';

            $records = $forms["formEntries"]["Entries"];
            $columnHeaderTitles = [];
            foreach ($forms["formFields"] as $formFields) {
                array_push($columnHeaderTitles, $formFields["FieldTitle"]);
            }

            // for each form an XLSX is generated with header line and data and stored in a local directory
            $reportFile = Report::generate(
                $records,
                $columnHeaderTitles,
                $file,
                $vendorName,
                $range
            );;

            if (!file_exists($reportFile)) {
                echo "File not created";
                return;
            }
        }

        $this->info("\n\nReports generated.");
    }

    private function setFormStatus($name, $hash)
    {
        if (empty($name) && empty($hash)) {
            return "Error";
        }

        return "Pass";
    }
}
