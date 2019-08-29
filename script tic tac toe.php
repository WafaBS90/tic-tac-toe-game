<?php

$boardSize = 3;
$isWinner = 0;
$countPlayer1 = 0;
$countPlayer2 = 0;


echo " tic-tac-toe board size : ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$value = (int)trim($line);
if ($value !== 0 && $value > 3) {
    $boardSize = $value;
}


$maxCount = floor(($boardSize * $boardSize) / 2);
$gameBoardArray = initializeGameBoard($boardSize);
showGameBoard($gameBoardArray);
$player = 1;
$countPlayer = $countPlayer1;
while (!$isWinner && $countPlayer1 <= $maxCount &&
    $countPlayer2 <= $maxCount) {

    $playGameResult = playGame($player, $countPlayer, $gameBoardArray, $isWinner);

    $alreadySelected = $playGameResult[4];
    $gameBoardArray = $playGameResult[2];
    $isWinner = $playGameResult[3];
    $player = $playGameResult[0];
    if ($player == 1) {
        $countPlayer1 = $playGameResult[1];
        if (!$alreadySelected && !$isWinner) {
            $player = 2;
            $countPlayer = $countPlayer2;
        }
    } else {
        $countPlayer2 = $playGameResult[1];
        if (!$alreadySelected && !$isWinner) {
            $player = 1;
            $countPlayer = $countPlayer1;
        }
    }

    if ($isWinner) {
        echo "Player $player is the Winner\n ";
        break;

    }
}

if (!$isWinner) echo "END OF THE GAME ";


/**
 * Initialize Game Board
 * @param $boardSize
 * @return array
 */
function initializeGameBoard($boardSize)
{
    $gameBoardArray = array();
    $gameBoard = array();
    for ($i = 1; $i <= $boardSize; $i++) {
        $gameBoard[$i] = '-';
    }
    for ($i = 1; $i <= $boardSize; $i++) {
        $gameBoardArray[$i] = $gameBoard;
    }
    return $gameBoardArray;
}

/**
 * Print structured game board on screen
 * @param $gameBoardArray
 */
function showGameBoard($gameBoardArray)
{
    $boardSize = sizeof($gameBoardArray);
    for ($i = 1; $i <= $boardSize; $i++) {
        $line = '';
        for ($j = 1; $j <= $boardSize; $j++) {
            $line .= ' [' . $gameBoardArray[$i][$j] . '] ';
        }
        echo "$line\n";
    }

}

/**
 * Select option in the board game and check for winner
 * @param $player
 * @param $countPlayer
 * @param $gameBoardArray
 * @param $isWinner
 * @return array
 */
function playGame($player, $countPlayer, $gameBoardArray, $isWinner)
{
    $alreadySelected = false;
    $playerChoice = getPlayerChoice($player, sizeof($gameBoardArray));
    $gameBoardArrayUpdated = updateGameBoard($gameBoardArray, $playerChoice);
    showGameBoard($gameBoardArrayUpdated);
    if ($gameBoardArrayUpdated == $gameBoardArray) {
        echo "already selected choose another option\n";
        $alreadySelected = true;
    } else {
        $countPlayer++;
        $gameBoardArray = $gameBoardArrayUpdated;
        $isWinner = isWinner($gameBoardArray, $playerChoice[2]);
    }
    return array($player, $countPlayer, $gameBoardArray, $isWinner, $alreadySelected);
}

/**
 * Format player choice
 * @param $player
 * @param $sizeOfBroad
 * @return array
 */
function getPlayerChoice($player, $sizeOfBroad)
{
    $rowValue = getChoice($player, 'row', $sizeOfBroad);
    $columnValue = getChoice($player, 'column', $sizeOfBroad);
    if ($player == 1) {
        $playerChoice = 'X';
    } else {
        $playerChoice = 'O';
    }
    return array($rowValue, $columnValue, $playerChoice);
}

/**
 * Get player choice from input
 * @param $player
 * @param $columnRow
 * @param $sizeOfBroad
 * @return int
 */
function getChoice($player, $columnRow, $sizeOfBroad)
{
    echo " player $player :\n $columnRow :  ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    $lineValue = (int)trim($line);
    while (!is_numeric($lineValue) ||
        $lineValue > $sizeOfBroad ||
        $lineValue <= 0
    ) {
        echo " player $player :\n $columnRow :  ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $lineValue = (int)trim($line);
    }
    return $lineValue;
}

/**
 * Update game board with new value
 * @param $gameBoardArray
 * @param $playerChoice
 * @return mixed
 */
function updateGameBoard($gameBoardArray, $playerChoice)
{
    $i = $playerChoice[0];
    $j = $playerChoice[1];
    if ($gameBoardArray[$i][$j] == '-') {
        $gameBoardArray[$i][$j] = $playerChoice[2];
    }
    return $gameBoardArray;
}

/**
 * check if player is winner
 * @param $gameBoard
 * @param $playerChoice
 * @return int
 */
function isWinner($gameBoard, $playerChoice)
{
    $isWinner = 0;
    $rowsColumnsArray = rowsAndColumns($gameBoard, $playerChoice);
    $boardSize = sizeof($gameBoard);
    if (in_array($boardSize, $rowsColumnsArray[0])) {
        $isWinner = 1;
    } elseif (in_array($boardSize, $rowsColumnsArray[1])) {
        $isWinner = 1;
    } elseif ((sizeof($rowsColumnsArray[2]) >= $boardSize)
        &&
        (sizeof($rowsColumnsArray[3]) >= $boardSize)
    ) {
        if ($rowsColumnsArray[2] == $rowsColumnsArray[3]) {
            if (checkConsecutiveArray($rowsColumnsArray[2]) &&
                checkConsecutiveArray($rowsColumnsArray[3])
            )
                $isWinner = 1;
        }
    }
    return $isWinner;
}

/**
 * Returns array containing rows and columns arrays from the game board
 * @param $gameBoard
 * @param $playerChoice
 * @return array
 */
function rowsAndColumns($gameBoard, $playerChoice)
{
    $rowsArray = array();
    $columnsArray = array();

    $boardSize = sizeof($gameBoard);

    for ($i = 1; $i <= $boardSize; $i++) {
        for ($j = 1; $j <= $boardSize; $j++) {
            if ($gameBoard[$i][$j] == $playerChoice) {
                $rowsArray[] = $i;
                $columnsArray[] = $j;
            }
        }
    }
    asort($rowsArray);
    asort($columnsArray);
    $rowsCounts = array_count_values($rowsArray);
    $columnsCounts = array_count_values($columnsArray);

    $rowsArray = array_values($rowsArray);
    $columnsArray = array_values($columnsArray);
    return array($rowsCounts, $columnsCounts, $rowsArray, $columnsArray);
}

/**
 * Check if array is consecutive
 * @param $consecutiveArray
 * @return bool
 */
function checkConsecutiveArray($consecutiveArray)
{
    $isConsecutive = true;
    for ($i = 0; $i < count($consecutiveArray) - 1; $i++) {
        if ($consecutiveArray[$i] + 1 != $consecutiveArray[$i + 1]) {
            $isConsecutive = false;
            break;
        }
    }
    return $isConsecutive;
}


?>