<?php

/**
 * Manage quizes.
 *
 * @author Gustavo Jantsch <jantsch@gmail.com>
 */
class Quiz
{
    const PREFIX_OPTION_ID = "o";
    const PREFIX_QUESTION_ID = "q";

    private $questions;
    private $results;
    private $max_options;

    public function __construct($file = null)
    {
        if (is_null($file)) {
            return;
        }

        $this->loadFromFile($file);

    }

    public function loadFromFile($file) {

        $file = trim(filter_var($file, FILTER_SANITIZE_STRING));

        if (!is_readable($file)) {
            throw new Exception("File is not readable: $file");
        }

        $this->loadDataFromXML($file);

    }

    protected function loadDataFromXML($file) {

        if (!extension_loaded("simplexml")) {
            throw new Exception("Required SimpleXML extension is missing.");
        }

        $data = simplexml_load_file($file);
        if ($data === false) {
            throw new Exception("The file is not an valid XML.");
        }

        $this->questions = [];
        $question_count = 0;
        $max_options = null;
        if (property_exists($data, "questions") && property_exists($data->questions, "question")) {
            foreach ($data->questions->question as $question) {
                $text = (string)$question->text;
                $options = [];
                if (property_exists($question, "options")) {
                    $id = 0;
                    $options = [];
                    foreach ($question->options->option as $key => $value) {
                        $options[self::PREFIX_OPTION_ID . $id++] = (string)$value;

                    }

                    if (is_null($max_options)) {
                        $max_options = $id;

                    } elseif ($max_options != $id) {
                        throw new Exception("XML is not valid, questions must have same number of options.");
                    }
                }
                $question_id = self::PREFIX_QUESTION_ID . $question_count++;
                $this->questions[$question_id] = [
                    "id" => $question_id,
                    "text" => $text,
                    "options" => $options
                ];
            }

        } else {
            throw new Exception("XML is not valid, question broken.");
        }

        if (property_exists($data, "results")) {
            $result_count = 0;
            $this->results = [];
            foreach ($data->results->result as $result) {
                $this->results[self::PREFIX_OPTION_ID . ($result_count++)] = [
                    "title" => (string)$result->title,
                    "description" => (string)$result->description
                ];
            }

            if ($result_count !== $question_count) {
                throw new Exception("Results count doen't match questions ($result_count x $question_count).");
            }


        } else {
            throw new Exception("XML is not valid, results missing.");
        }

        $this->max_options = $max_options;
    }

    /**
     * Get questions array.
     *
     * @return array
     */
    public function getQuestions()
    {
        return is_array($this->questions) ? $this->questions : [];
    }

    public function setQuestions(array $questions)
    {
        $this->questions = $questions;
    }

    public function setResults(array $results)
    {
        $this->results = $results;
    }

    /**
     * Reset object state.
     */
    public function reset() {
        $this->questions = null;
        $this->results = null;
    }

    /**
     * Serialize current state.
     *
     * @return string
     */
    public function getSerializedData() {

        return base64_encode(
            serialize([
                "questions" => $this->questions,
                "results" => $this->results
            ])
        );

    }

    /**
     * Load serialized data.
     *
     * @param string $data
     */
    public function loadSerializedData($data) {

        $this->reset();
        if ($data = base64_decode($data)) {
            if ($data = @unserialize($data)) {
                $this->questions = isset($data["questions"]) ? $data["questions"] : [];
                $this->results = isset($data["results"]) ? $data["results"] : [];
                return true;
            }
        }
        return false;
    }

    /**
     * Shuffle question array.
     *
     * @param int $steps
     */
    public function shuffleQuestions($steps = 1)
    {
        $steps = (int)$steps;
        if ($steps <= 0 || $steps > 50) {
            $steps = 1;
        }

        if (!is_array($this->questions)) {
            return;
        }

        for ($i = 0; $i < $steps; $i++) {
            // The function shuffle destroys the original keys,
            // so we need this workaround to keep them intact.
            $keys = array_keys($this->questions);
            shuffle($keys);
            $random = array();
            foreach ($keys as $key) {
                $random[$key] = $this->questions[$key];
            }
        }

        $this->questions = $random;
    }

    /**
     * Shuffle the options on the questions.
     * 
     * Note:
     *   To avoid collision of answers when user select same option,
     *   we shift the pattern array
     *
     * * @param int $steps
     */
    public function shuffleOptions($steps = 1)
    {
        $steps = (int)$steps;
        if ($steps <= 0 || $steps > 50) {
            $steps = 1;
        }

        $pattern = [];
        for ($i = 0; $i < $this->max_options; $i++) {
            $pattern[] = self::PREFIX_OPTION_ID . (string)$i;
        }

        for ($i = 0; $i < $steps; $i++) {
            shuffle($pattern);
        }

        // shuffle options based on pattern
        foreach ($this->questions as &$question) {
            $new_order = [];
            foreach ($pattern as $index) {
                $new_order[$index] = $question["options"][$index];
            }
            $question["options"] = $new_order;

            // save the first element before shift
            $first_key = reset($pattern);
            $first_value = array_shift($pattern);
            // put first element back
            $pattern[$first_key] = $first_value;
        }
    }

    public function checkAnswers(array $answers) {

        if (count($answers) == 0) {
            throw new Exception("Invalid answers.");
        }

        $score = [];
        $top = 0;
        $last_option_id = null;

        foreach ($answers as $option_id) {
            // check if ids exists
            if (!isset($this->results[$option_id])) {
                throw new Exception("Invalid set of answers.");
            }
            $score[$option_id] = isset($score[$option_id]) ? $score[$option_id] + 1 : 1;
            if ($score[$option_id] >= $top) {
                $top = $score[$option_id];
                $last_option_id = $option_id;
            }

        }

        return [
            "option" => $last_option_id,
            "score" => $top,
            "title" => $this->results[$last_option_id]["title"],
            "description" => $this->results[$last_option_id]["description"]
        ];
    }

}
