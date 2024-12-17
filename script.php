<?php

// Specify the path to your PHPMD executable (adjust the path if necessary)
$phpmdExecutable = './vendor/bin/phpmd';

// Specify the directory or file to analyze
$directoryToAnalyze = 'app/Http/Controllers/ConferenceController.php';

// Specify the report format (html, xml, txt)
$reportFormat = 'html';

// Specify the output file name for the report
$reportFile = 'phpmd_report.html';

// Specify the rule set (e.g., design includes RFC and other complexity-related metrics)
$ruleset = 'design';

// Construct the PHPMD command
$command = "$phpmdExecutable $directoryToAnalyze $reportFormat --reportfile=$reportFile --ruleset=$ruleset";

// Execute the command
exec($command, $output, $returnCode);

// Check if the command ran successfully
if ($returnCode === 0) {
    echo "PHPMD report generated successfully: $reportFile\n";
    // Optionally, you can read and display the report contents
    echo file_get_contents($reportFile);
} else {
    echo "Error executing PHPMD: " . implode("\n", $output);
}

?>
