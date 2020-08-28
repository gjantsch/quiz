<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Quiz Sample</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="css/demo.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">Quiz Sample</h3>
        </div>

        <?php
        require "includes/config.php";

        if (isset($_POST['answers']) && is_array($_POST['answers'])) {

            $q = new Quiz();
            $answers = filter_var_array( $_POST['answers'], FILTER_SANITIZE_STRING);

            if (!isset($_SESSION["QUIZ_CACHE"]) || !$q->loadSerializedData($_SESSION["QUIZ_CACHE"])) {
                // caso o acesso a sessão falhe... carrega novamente do arquivo XML
                $q->loadFromFile("data.xml");
            }

            try {
                $resposta = $q->checkAnswers($answers);
                ?>
                <div class="jumbotron">
                    <p class="lead"><?php echo $resposta["title"]; ?></p>
                    <p><?php echo $resposta["description"]; ?></p>
                </div>
                <?php

            } catch (Exception $ex) {
                ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Ops...</strong> Houve algum problema ao verificar suas respostas.
                </div>
                <?php
            }

            ?>
            <p>
             <a href="./" class="btn btn-lg btn-success">Tentar novamente</a>
            </p>
            <?php

        } else {
            $q = new Quiz("data.xml");
            // $q->shuffleQuestions(5);
            $q->shuffleOptions();
            // persiste estado do objeto na sessão
            $_SESSION["QUIZ_CACHE"] = $q->getSerializedData();


            echo "<form method=\"post\" action=\"./\">";
            foreach ($q->getQuestions() as $question) {
                echo "<div class=\"jumbotron question\"><p class=\"lead\">{$question['text']}</p>";
                foreach ($question["options"] as $id => $option) {
                    echo "<div class=\"option\"><input type=\"radio\" name=\"answers[{$question['id']}]\" value=\"{$id}\">{$option}</div>";
                }
                echo "</div>";
            }
            ?>
            <p>
                <input type="submit" name="avaliar" disabled="disabled" class="btn btn-lg btn-success" value="Avaliar personalidade">
            </p>
            </form>
        <?php } ?>
    </div>
    <br>
    <br>
    <br>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    <script>
        $(function(){
            $(document).on('change', 'input[type=radio]', function(){
                // verifica se todos questões foram respondidas
                if ($('input[type=radio]:checked').length == $('div.question').length) {
                    // habilita o botão de envio
                    $('input[name="avaliar"]').attr("disabled", false);
                } else {
                    $('input[name="avaliar"]').attr("disabled", true);
                }

            });
        });
    </script>
</body>
</html>