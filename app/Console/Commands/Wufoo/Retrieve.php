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
        // $testData = [

        //     [
        //         "formName" => "Another FormToTest",
        //         "formHash" => "m13e7xuh1dfgnx8",
        //         "formFields" => [

        //             [
        //                 "FieldTitle" => "Entry Id",
        //                 "FieldType" => "text",
        //                 "FieldID" => "EntryId"
        //             ],
        //             [
        //                 "FieldTitle" => "Name",
        //                 "Instructions" => "",
        //                 "IsRequired" => "1",
        //                 "ClassNames" => "",
        //                 "DefaultVal" => "",
        //                 "Page" => "1",
        //                 "SubFields" => [
        //                     [
        //                         "DefaultVal" => "",
        //                         "ID" => "Field1",
        //                         "Label" => "First"
        //                     ],
        //                     [
        //                         "DefaultVal" => "",
        //                         "ID" => "Field2",
        //                         "Label" => "Last"
        //                     ]
        //                 ],
        //                 "FieldType" => "shortname",
        //                 "FieldID" => "Field1"
        //             ],
        //             [
        //                 "FieldTitle" => "Are you able to attend?",
        //                 "Instructions" => "",
        //                 "IsRequired" => "1",
        //                 "ClassNames" => "",
        //                 "DefaultVal" => "",
        //                 "Page" => "1",
        //                 "Choices" => [
        //                     [
        //                         "Label" => "Yes"
        //                     ],
        //                     [
        //                         "Label" => "No"
        //                     ],
        //                     [
        //                         "Label" => "Not sure"
        //                     ]
        //                 ],
        //                 "FieldType" => "radio",
        //                 "FieldID" => "Field3",
        //                 "HasOtherField" => false
        //             ],
        //             [
        //                 "FieldTitle" => "How many guests will you be bringing? Please write a number below.",
        //                 "Instructions" => "",
        //                 "IsRequired" => "1",
        //                 "ClassNames" => "",
        //                 "DefaultVal" => "",
        //                 "Page" => "1",
        //                 "FieldType" => "number",
        //                 "FieldID" => "Field4"
        //             ],
        //             [
        //                 "FieldTitle" => "Do you or your guest(s) have any dietary restrictions? Please check all that apply.",
        //                 "Instructions" => "",
        //                 "IsRequired" => "1",
        //                 "ClassNames" => "",
        //                 "DefaultVal" => "0",
        //                 "Page" => "1",
        //                 "SubFields" => [
        //                     [
        //                         "DefaultVal" => "0",
        //                         "ID" => "Field5",
        //                         "Label" => "Vegetarian"
        //                     ],
        //                     [
        //                         "DefaultVal" => "0",
        //                         "ID" => "Field6",
        //                         "Label" => "Vegan"
        //                     ],
        //                     [
        //                         "DefaultVal" => "0",
        //                         "ID" => "Field7",
        //                         "Label" => "Gluten-free"
        //                     ],
        //                     [
        //                         "DefaultVal" => "0",
        //                         "ID" => "Field8",
        //                         "Label" => "Dairy-free"
        //                     ],
        //                     [
        //                         "DefaultVal" => "0",
        //                         "ID" => "Field9",
        //                         "Label" => "None"
        //                     ],
        //                     [
        //                         "DefaultVal" => "0",
        //                         "ID" => "Field10",
        //                         "Label" => "Other (please elaborate below)"
        //                     ]
        //                 ],
        //                 "FieldType" => "checkbox",
        //                 "FieldID" => "Field5"
        //             ],
        //             [
        //                 "FieldTitle" => "Please elaborate on any dietary restrictions that apply, or let us know if there's anything else you'd like to tell us. Hope to see you soon!",
        //                 "Instructions" => "",
        //                 "IsRequired" => "0",
        //                 "ClassNames" => "",
        //                 "DefaultVal" => "",
        //                 "Page" => "1",
        //                 "FieldType" => "textarea",
        //                 "FieldID" => "Field105"
        //             ],
        //             [
        //                 "FieldTitle" => "Date Created",
        //                 "FieldType" => "date",
        //                 "FieldID" => "DateCreated"
        //             ],
        //             [
        //                 "FieldTitle" => "Created By",
        //                 "FieldType" => "text",
        //                 "FieldID" => "CreatedBy"
        //             ],
        //             [
        //                 "FieldTitle" => "Last Updated",
        //                 "FieldType" => "date",
        //                 "FieldID" => "LastUpdated"
        //             ],
        //             [
        //                 "FieldTitle" => "Updated By",
        //                 "FieldType" => "text",
        //                 "FieldID" => "UpdatedBy"
        //             ]


        //         ],
        //         "formEntries" => [
        //             "Entries" => [
        //                 [
        //                     "EntryId" => "1",
        //                     "Field1" => "Test",
        //                     "Field2" => "Name",
        //                     "Field3" => "Yes",
        //                     "Field4" => "33",
        //                     "Field5" => "Vegetarian",
        //                     "Field6" => "Vegan",
        //                     "Field7" => "",
        //                     "Field8" => "",
        //                     "Field9" => "",
        //                     "Field10" => "",
        //                     "Field105" => "nan",
        //                     "DateCreated" => "2023-02-09 10=>41=>50",
        //                     "CreatedBy" => "public",
        //                     "DateUpdated" => "",
        //                     "UpdatedBy" => null
        //                 ],
        //                 [
        //                     "EntryId" => "2",
        //                     "Field1" => "ewgfw",
        //                     "Field2" => "qwergfwio",
        //                     "Field3" => "Yes",
        //                     "Field4" => "8",
        //                     "Field5" => "",
        //                     "Field6" => "",
        //                     "Field7" => "",
        //                     "Field8" => "",
        //                     "Field9" => "",
        //                     "Field10" => "Other (please elaborate below)",
        //                     "Field105" => "A long Text\r\n\r\nwith multiple lines\r\n\r\nis entered \r\n\r\nhere!!",
        //                     "DateCreated" => "2023-02-09 10=>42=>16",
        //                     "CreatedBy" => "public",
        //                     "DateUpdated" => "",
        //                     "UpdatedBy" => null
        //                 ],
        //                 [
        //                     "EntryId" => "3",
        //                     "Field1" => "Testing",
        //                     "Field2" => "Form",
        //                     "Field3" => "Yes",
        //                     "Field4" => "32",
        //                     "Field5" => "Vegetarian",
        //                     "Field6" => "Vegan",
        //                     "Field7" => "Gluten-free",
        //                     "Field8" => "Dairy-free",
        //                     "Field9" => "",
        //                     "Field10" => "",
        //                     "Field105" => "This is a great form.",
        //                     "DateCreated" => "2023-02-11 02=>47=>17",
        //                     "CreatedBy" => "public",
        //                     "DateUpdated" => "",
        //                     "UpdatedBy" => null
        //                 ]
        //             ],
        //         ],
        //         "statusReport" => "Pass",
        //         [
        //             "formName" => "Another Form",
        //             "formHash" => "m13e7xuh1dfgnx8",
        //             "formFields" => [
        //                 [
        //                     "FieldTitle" => "Entry Id",
        //                     "FieldID" => "EntryId",
        //                     "FieldType" => "text",
        //                     "SubFields" => null
        //                 ],
        //                 [
        //                     "FieldTitle" => "field #1",
        //                     "FieldID" => "Field1",
        //                     "FieldType" => "text",
        //                     "SubFields" => null,
        //                 ],
        //                 [
        //                     "FieldTitle" => "Select a Choice",
        //                     "FieldID" => "Field2",
        //                     "FieldType" => "radio",
        //                     "SubFields" => null,
        //                 ],
        //                 [
        //                     "FieldTitle" => "Date Created",
        //                     "FieldID" => "DateCreated",
        //                     "FieldType" => "date",
        //                     "SubFields" => [
        //                         [
        //                             "DefaultVal" =>  "0",
        //                             "ID" => "Field108",
        //                             "Label" =>  "Check One"
        //                         ],
        //                         [
        //                             "DefaultVal" =>  "0",
        //                             "ID" =>  "Field109",
        //                             "Label" => "Check Two"
        //                         ],
        //                         [
        //                             "DefaultVal" =>  "0",
        //                             "ID" =>  "Field110",
        //                             "Label" =>  "Check Three"
        //                         ]
        //                     ],
        //                 ],
        //                 [
        //                     "FieldTitle" => "Created By",
        //                     "FieldID" => "CreatedBy",
        //                     "FieldType" => "text",
        //                     "SubFields" => null,
        //                 ],
        //                 [
        //                     "FieldTitle" => "Last Updated",
        //                     "FieldID" => "LastUpdated",
        //                     "FieldType" => "date",
        //                     "SubFields" => null,
        //                 ],
        //                 [
        //                     "FieldTitle" => "Updated By",
        //                     "FieldID" => "UpdatedBy",
        //                     "FieldType" => "text",
        //                     "SubFields" => null,
        //                 ]
        //             ],
        //             "formEntries" => [
        //                 "Entries" =>  [
        //                     [
        //                         "EntryId" => "1",
        //                         "Field1" => "A tester",
        //                         "Field2" => "First Choice",
        //                         "DateCreated" => "2023-02-09 10=>43=>13",
        //                         "CreatedBy" => "public",
        //                         "DateUpdated" => "",
        //                         "UpdatedBy" => null,
        //                     ],
        //                     [
        //                         "EntryId" => "2",
        //                         "Field1" => "Me",
        //                         "Field2" => "Third Choice",
        //                         "DateCreated" => "2023-02-09 10=>43=>34",
        //                         "CreatedBy" => "public",
        //                         "DateUpdated" => "",
        //                         "UpdatedBy" => null,
        //                     ]
        //                 ]
        //             ],
        //             "statusReport" => "Pass",
        //         ]
        //     ]
        // ];

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
                Log::critical("Form hash with error => \nName=>" . $forms['Name'] . "\nHash=> " . $forms['Hash'] . "\nStatus=> " . $forms["statusReport"]);
                $this->errorFormsCount++;
            }
        }

        // retrieve form fields.
        $formsRetrieved = count($formsDataSet);

        // Let the user know forms were pulled successfully.
        $this->info("\nWufoo Retrieve Process - " . $formsRetrieved . " Forms collected successfully......");

        // Let the user know form report are starting.
        $this->info("\nWufoo Report Process - Starting process......");

        // Create a report for each form.
        $this->initReport($formsDataSet);

        // run the test data.
        // $this->initReport($testData);

        $this->info("\n\nReports generated.");

        $this->info("\n\nThere were $this->errorFormsCount forms with errors. {See log for any errors that may occur}");
    }


    // Customer method to build the report.
    private function initReport($formsHash)
    {
        $filePath = $this->createFormStorage();

        // Create a report for each form.
        foreach ($formsHash as $forms) {
            if (!isset($forms['formFields'])) {
                echo "skipped, no formFields";
                continue;
            }

            // - xlsx is named after form name
            $removeSpaceName = str_replace(" ", "-", $forms["formName"]);
            $removeSpaceName = str_replace("/", "", $removeSpaceName);
            $vendorName = $removeSpaceName . '_Form-Entries';
            $vendorName = strlen($vendorName) >= 31 ? substr($vendorName, 0, 28) . '...' : $vendorName;
            $fileName =  $removeSpaceName . "_Form.xlsx";

            $file = "$filePath/$fileName";

            if (file_exists($file)) {
                srand(time());
                $randval = rand(1, 1000);
                $file = str_replace('_Form.xlsx', '_' . $randval . '_Form.xlsx', $file);
            }

            // need a conditional range.
            $range = 'AZ';

            $columnHeaderTitles = [];
            foreach ($forms["formFields"] as $formFields) {
                array_push($columnHeaderTitles, substr($formFields["FieldTitle"], 0, 20));

                // Check if there are subfields in the form
                if (isset($formFields["SubFields"])) {

                    // Create an array for each form field that has subfields
                    $subFieldIDs = [];

                    foreach ($formFields["SubFields"] as $subFields) {
                        array_push($subFieldIDs, $subFields["ID"]);
                    }

                    $entryCounter = 0;
                    // Get all the entries that is apart of the subfields.
                    foreach ($forms["formEntries"]["Entries"] as $entryLabels) {

                        $tempEntry = [];
                        // create counter to track the first entry name
                        $counter = 0;
                        $firstEntryField = null;

                        // Check if the there is a  /r (return) or a /n (new line) and remove to add regular space.
                        foreach ($entryLabels as $tempLabel => $value) {

                            if (isset($value) && (str_contains($value, "\r") || str_contains($value, "\n"))) {
                                $value = trim(preg_replace('/\s+/', ' ', $value));

                                $forms["formEntries"]["Entries"][$entryCounter][$tempLabel] = $value;
                            }
                        }

                        foreach ($subFieldIDs as $entryID) {
                            // Confirm that is in the array of subfields
                            if (in_array($entryID, $subFieldIDs)) {
                                if ($counter < 1) {
                                    $firstEntryField = $entryID;
                                    $counter++;
                                }
                                // push to temp array
                                array_push($tempEntry, $entryID);
                            }
                        }

                        if (isset($tempEntry)) {
                            $conCatFields = [];
                            // Join the values
                            foreach ($tempEntry as $tempEntryLabel) {
                                array_push($conCatFields, $entryLabels[$tempEntryLabel]);

                                // remove element from the entries array.
                                if ($tempEntryLabel !== $firstEntryField) {
                                    unset($forms["formEntries"]["Entries"][$entryCounter][$tempEntryLabel]);
                                }
                            }
                            $multiChoiceEntry = implode(" ", $conCatFields);

                            // Rename the label value
                            $forms["formEntries"]["Entries"][$entryCounter][$firstEntryField] = trim($multiChoiceEntry);
                        }

                        $entryCounter++;
                    }
                }
            }

            $records = $forms["formEntries"]["Entries"];

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
    }

    private function setFormStatus($name, $hash)
    {
        if (empty($name) && empty($hash)) {
            return "Error";
        }

        return "Pass";
    }

    private function createFormStorage()
    {
        // Build the csv file to store the report
        if (!file_exists(storage_path() . '/app/forms')) {
            mkdir(storage_path() . '/app/forms', 0777);
        }
        return storage_path() . '/app/forms';
    }
}
