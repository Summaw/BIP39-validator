<?php
require_once 'vendor/autoload.php';

use BitWasp\Bitcoin\Mnemonic\Bip39\Bip39SeedGenerator;
use BitWasp\Bitcoin\Mnemonic\Bip39\Wordlist\EnglishWordList;

function validateBip39Phrase($phrase) {
    try {
        // Check if GMP extension is loaded
        if (!extension_loaded('gmp')) {
            return [
                'valid' => false,
                'error' => 'PHP GMP extension is required but not enabled.'
            ];
        }

        // Trim and convert to lowercase
        $phrase = strtolower(trim($phrase));
        
        // Split into words
        $words = explode(' ', $phrase);
        
        // Check word count
        if (!in_array(count($words), [12, 15, 18, 21, 24])) {
            return [
                'valid' => false,
                'error' => 'Seed phrase must be 12, 15, 18, 21, or 24 words'
            ];
        }
        
        // Check for duplicate words
        if (count($words) !== count(array_unique($words))) {
            return [
                'valid' => false,
                'error' => 'Duplicate words found in seed phrase'
            ];
        }

        // Get the word list
        $wordList = new EnglishWordList();
        $allWords = $wordList->getWords();
        
        // Check each word
        foreach ($words as $word) {
            if (!in_array($word, $allWords)) {
                return [
                    'valid' => false,
                    'error' => "Invalid word found: '$word' is not in BIP39 wordlist"
                ];
            }
        }

        try {
            $seedGenerator = new Bip39SeedGenerator();
            $seedGenerator->getSeed($phrase);
            return [
                'valid' => true,
                'error' => null
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'error' => 'Invalid checksum or phrase structure'
            ];
        }

    } catch (Exception $e) {
        return [
            'valid' => false,
            'error' => 'Error validating seed phrase: ' . $e->getMessage()
        ];
    }
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seedPhrase = $_POST['seed_phrase'] ?? '';
    $result = validateBip39Phrase($seedPhrase);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BIP39 Seed Phrase Validator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .input-area {
            margin: 20px 0;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        textarea {
            width: 100%;
            height: 100px;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            font-weight: bold;
            white-space: pre-line;
        }
        .valid {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .invalid {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        button {
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>BIP39 Seed Phrase Validator</h1>
    
    <div class="input-area">
        <label for="seed-phrase">Enter your seed phrase:</label><br>
        <textarea id="seed-phrase" placeholder="Enter your 12 or 24 word seed phrase..."></textarea><br>
        <button onclick="validatePhrase()">Validate</button>
    </div>

    <div id="result" class="result" style="display: none;"></div>

    <script>
        function validatePhrase() {
            const phrase = document.getElementById('seed-phrase').value.trim();
            const resultDiv = document.getElementById('result');
            
            // Basic client-side validation
            if (!phrase) {
                resultDiv.style.display = 'block';
                resultDiv.className = 'result invalid';
                resultDiv.textContent = 'Error: Please enter a seed phrase';
                return;
            }

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'seed_phrase=' + encodeURIComponent(phrase)
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.style.display = 'block';
                resultDiv.className = 'result ' + (data.valid ? 'valid' : 'invalid');
                resultDiv.textContent = data.valid ? 
                    'Valid BIP39 seed phrase!' : 
                    'Invalid seed phrase: ' + data.error;
            })
            .catch(error => {
                resultDiv.style.display = 'block';
                resultDiv.className = 'result invalid';
                resultDiv.textContent = 'Error: ' + error.message;
            });
        }
    </script>
</body>
</html>
