<?php
/**
 * Quick test for default test cases.
 *
 * @author Gustavo Jantsch <jantsch@gmail.com>
 */

ini_set("display_errors", 1);
error_reporting(E_ALL);

if (php_sapi_name() !== 'cli') {
    // if not running on the console:
    header("Content-type: text/plain");
}

date_default_timezone_set("America/Sao_Paulo");

include "../includes/quiz.class.php";

$q = new Quiz("../data.xml");
$q->shuffleQuestions(5);
$q->shuffleOptions();

$default_test_cases = [
    ["options" => [ "o2", "o2", "o0", "o4", "o4" ], "expected" => "o4"],
    ["options" => [ "o4", "o4", "o0", "o2", "o2" ], "expected" => "o2"],
    ["options" => [ "o4", "o3", "o2", "o1", "o0" ], "expected" => "o0"],
    ["options" => [ "o0", "o1", "o2", "o3", "o4" ], "expected" => "o4"],
    ["options" => [ "o0", "o0", "o0", "o1", "o1" ], "expected" => "o0"]
];

foreach ($default_test_cases as $test) {
    $result = $q->checkAnswers($test["options"]);
    echo "Result" . PHP_EOL;
    print_r($result);
    echo "Expected {$test['expected']} is " . ($test["expected"] == $result["option"] ? "" : "not") . " a match" . PHP_EOL;
    echo str_repeat("-", 80) . PHP_EOL;
}