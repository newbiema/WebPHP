<?php
session_start();

$questions = [
    [
        "question" => "Siapa penemu bola lampu?",
        "options" => ["Nikola Tesla", "Thomas Edison", "Albert Einstein", "Alexander Graham Bell"],
        "answer" => "Thomas Edison"
    ],
    [
        "question" => "Apa ibu kota negara Jepang?",
        "options" => ["Seoul", "Beijing", "Tokyo", "Bangkok"],
        "answer" => "Tokyo"
    ],
    [
        "question" => "Berapa jumlah planet di tata surya kita?",
        "options" => ["7", "8", "9", "10"],
        "answer" => "8"
    ],
    [
        "question" => "Apa hewan darat tercepat di dunia?",
        "options" => ["Cheetah", "Singa", "Kuda", "Harimau"],
        "answer" => "Cheetah"
    ],
    [
        "question" => "Siapa penulis novel 'Harry Potter'?",
        "options" => ["J.K. Rowling", "George R.R. Martin", "J.R.R. Tolkien", "Suzanne Collins"],
        "answer" => "J.K. Rowling"
    ],
    [
        "question" => "Berapa jumlah warna dalam pelangi?",
        "options" => ["5", "6", "7", "8"],
        "answer" => "7"
    ],
    [
        "question" => "Apa nama sungai terpanjang di dunia?",
        "options" => ["Sungai Nil", "Sungai Amazon", "Sungai Mississippi", "Sungai Yangtze"],
        "answer" => "Sungai Nil"
    ],
    [
        "question" => "Siapa presiden pertama Indonesia?",
        "options" => ["Soeharto", "Megawati", "Sukarno", "Habibie"],
        "answer" => "Sukarno"
    ],
    [
        "question" => "Dimana Menara Eiffel berada?",
        "options" => ["London", "Paris", "Berlin", "Madrid"],
        "answer" => "Paris"
    ],
    [
        "question" => "Apa gas yang paling banyak di atmosfer bumi?",
        "options" => ["Oksigen", "Karbon dioksida", "Nitrogen", "Helium"],
        "answer" => "Nitrogen"
    ]
];

if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}
if (!isset($_SESSION['questions_answered'])) {
    $_SESSION['questions_answered'] = 0;
}
if (!isset($_SESSION['asked_questions'])) {
    $_SESSION['asked_questions'] = [];
}
if (!isset($_SESSION['user_answers'])) {
    $_SESSION['user_answers'] = [];
}

$current_question_index = isset($_GET['question']) ? (int)$_GET['question'] : $_SESSION['questions_answered'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selected_answer = $_POST['answer'] ?? null;
    $current_question = $questions[$current_question_index];

    if ($selected_answer === $current_question['answer']) {
        $_SESSION['score']++;
    }
    $_SESSION['asked_questions'][] = $current_question_index;
    $_SESSION['user_answers'][$current_question_index] = $selected_answer;
    $_SESSION['questions_answered']++;

    header("Location: ?question=" . $_SESSION['questions_answered']);
    exit();
}

// Jika tombol "Sebelumnya" ditekan
if (isset($_GET['prev']) && $_SESSION['questions_answered'] > 0) {
    $_SESSION['questions_answered']--;
    $previous_index = array_search($current_question_index, $_SESSION['asked_questions']) - 1;
    if ($previous_index >= 0) {
        $current_question_index = $_SESSION['asked_questions'][$previous_index];
    } else {
        $current_question_index = 0;
    }
}

if ($_SESSION['questions_answered'] >= count($questions)) {
    $unanswered = array_diff_key($questions, $_SESSION['user_answers']);
    if (count($unanswered) > 0) {
        echo "<div class='unanswered-questions'><h1>Soal yang Belum Terjawab:</h1><ul>";
        foreach ($unanswered as $index => $unanswered_question) {
            echo "<li><a href='?question={$index}'>Soal " . ($index + 1) . "</a></li>";
        }
        echo "</ul></div>";
    } else {
        echo "<div class='final-score'><h1>Hasil Kuis</h1><h2>Skor Anda: " . $_SESSION['score'] . " dari " . count($questions) . "</h2><p>Terima kasih telah mengikuti kuis!</p></div>";
        session_destroy();
    }
    exit();
}

$current_question = $questions[$current_question_index];
$options = $current_question['options'];
shuffle($options);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuis Pengetahuan Umum</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="quiz">
        <div class="question-container">
            <h1>Kuis Pengetahuan Umum</h1>
            <div class="question-number">Soal <?php echo $current_question_index + 1; ?></div>
            <h2><?php echo htmlspecialchars($current_question['question']); ?></h2>
            <form method="post">
                <ul>
                    <?php foreach ($options as $index => $option): ?>
                        <li>
                            <label>
                                <input type="radio" name="answer" value="<?php echo htmlspecialchars($option); ?>" 
                                <?php if (isset($_SESSION['user_answers'][$current_question_index]) && $_SESSION['user_answers'][$current_question_index] == $option) echo 'checked'; ?>>
                                <?php echo chr(65 + $index) . ". " . htmlspecialchars($option); ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit">Selanjutnya</button>
                <?php if (count($_SESSION['asked_questions']) > 0): ?>
                    <button type="button" onclick="window.location.href='?prev=true'">Sebelumnya</button>
                <?php endif; ?>
            </form>
        </div>
        <div class="number-column">
            <?php for ($i = 0; $i < count($questions); $i++): ?>
                <div class="number <?php echo isset($_SESSION['user_answers'][$i]) ? 'answered' : 'unanswered'; ?>" 
                     onclick="window.location.href='?question=<?php echo $i; ?>'">
                    <?php echo $i + 1; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>
