<?php

require "../includes/config.php";

class UnitTests extends PHPUnit_Framework_TestCase
{

    const VALID_QUIZ = "test-data/valid-file.xml";
    const ANSWERS_FOR_VALID_QUIZ = [
        ["options" => ["o2", "o2", "o0", "o4", "o4"], "expected" => "o4"],
        ["options" => ["o2", "o2", "o4", "o4", "o4"], "expected" => "o4"],
        ["options" => ["o2", "o2", "o4", "o0", "o4"], "expected" => "o4"],
        ["options" => ["o2", "o4", "o0", "o2", "o4"], "expected" => "o4"],
        ["options" => ["o4", "o0", "o2", "o2", "o4"], "expected" => "o4"],
        ["options" => ["o4", "o0", "o2", "o2", "o4"], "expected" => "o4"],
        ["options" => ["o4", "o4", "o0", "o2", "o2"], "expected" => "o2"],
        ["options" => ["o4", "o3", "o2", "o1", "o0"], "expected" => "o0"],
        ["options" => ["o4", "o3", "o1", "o1", "o0"], "expected" => "o1"],
        ["options" => ["o4", "o3", "o2", "o1", "o2"], "expected" => "o2"],
        ["options" => ["o4", "o3", "o3", "o1", "o2"], "expected" => "o3"],
        ["options" => ["o0", "o1", "o2", "o3", "o4"], "expected" => "o4"],
        ["options" => ["o0", "o0", "o0", "o1", "o1"], "expected" => "o0"]
    ];

    /**
     * Load a valid quiz.
     *
     * @return Quiz
     */
    private function getValidQuiz()
    {
        return new Quiz(self::VALID_QUIZ);
    }

    /**
     * Check if class is loadable.
     *
     */
    public function testEmptyObjectCreation()
    {
        $this->assertInstanceOf('Quiz', new Quiz());
    }

    /**
     * Check if is validating the file.
     *
     */
    public function testValidXMLFileException()
    {
        $q = $this->getValidQuiz();
        $this->assertInstanceOf('Quiz', $q);

        $questions = $q->getQuestions();
        $this->assertNotEmpty($questions);
        $this->assertTrue(is_array($questions));
        $first_question = array_pop($questions);
        $this->assertArrayHasKey("id", $first_question);
        $this->assertArrayHasKey("text", $first_question);
        $this->assertArrayHasKey("options", $first_question);

        // check cache data goes back and forth
        $cache = $q->getSerializedData();
        $this->assertNotEmpty($cache);
        $this->assertTrue($q->loadSerializedData($cache));
        // check that object still sane after unserialization
        $questions = $q->getQuestions();
        $this->assertNotEmpty($questions);
        $this->assertTrue(is_array($questions));
        $first_question = array_pop($questions);
        $this->assertArrayHasKey("id", $first_question);
        $this->assertArrayHasKey("text", $first_question);
        $this->assertArrayHasKey("options", $first_question);
    }


    /**
     * Check default answers.
     *
     * @throws Exception
     */
    public function testDefaultAnswerCheck()
    {
        $q = $this->getValidQuiz();
        foreach (self::ANSWERS_FOR_VALID_QUIZ as $test) {
            $result = $q->checkAnswers($test["options"]);

            $this->assertTrue(is_array($result));
            $this->assertArrayHasKey("option", $result);
            $this->assertArrayHasKey("score", $result);
            $this->assertArrayHasKey("title", $result);
            $this->assertArrayHasKey("description", $result);
            $this->assertEquals($result["option"], $test["expected"]);

        }
    }

    /**
     * Check if properly fails on non existent files.
     *
     * @expectedException Exception
     */
    public function testInvalidFileException()
    {
        $q = new Quiz("foo_some_inexistent_file");
    }

    /**
     * Check if properly fails on invalid files.
     *
     * @expectedException Exception
     */
    public function testNoResultsException()
    {
        $q = new Quiz("test-data/invalid-no-results.xml");
    }
}
