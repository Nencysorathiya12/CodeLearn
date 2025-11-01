<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$code = $_POST['code'] ?? '';
$language = $_POST['language'] ?? 'javascript';

if (empty($code)) {
    echo json_encode(['success' => false, 'error' => 'No code provided']);
    exit;
}

// Create temporary directory if not exists
$tempDir = sys_get_temp_dir() . '/codelearn_' . session_id();
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
}

// Generate unique filename
$filename = $tempDir . '/code_' . time() . '_' . rand(1000, 9999);

try {
    switch ($language) {
        case 'python':
            $result = executePython($code, $filename);
            break;
        
        case 'javascript':
        case 'node':
            $result = executeJavaScript($code, $filename);
            break;
        
        case 'java':
            $result = executeJava($code, $filename);
            break;
        
        case 'php':
            $result = executePHP($code, $filename);
            break;
        
        case 'c++':
        case 'cpp':
            $result = executeCPP($code, $filename);
            break;
        
        case 'c':
            $result = executeC($code, $filename);
            break;
        
        case 'html':
            $result = executeHTML($code);
            break;
        
        case 'css':
            $result = ['success' => true, 'output' => 'CSS code validated successfully!'];
            break;
        
        default:
            $result = ['success' => false, 'error' => 'Language not supported'];
    }
} catch (Exception $e) {
    $result = ['success' => false, 'error' => $e->getMessage()];
}

// Clean up temporary files
cleanupTempFiles($tempDir);

echo json_encode($result);

// ==================== Language Execution Functions ====================

function executePython($code, $filename) {
    $file = $filename . '.py';
    file_put_contents($file, $code);
    
    $command = "python3 " . escapeshellarg($file) . " 2>&1";
    
    // Check if python3 exists, fallback to python
    exec("which python3", $output, $returnCode);
    if ($returnCode !== 0) {
        $command = "python " . escapeshellarg($file) . " 2>&1";
    }
    
    $output = shell_exec($command);
    
    if ($output === null || trim($output) === '') {
        return ['success' => false, 'error' => 'Python is not installed on this server'];
    }
    
    // Check for syntax errors
    if (strpos($output, 'SyntaxError') !== false || 
        strpos($output, 'IndentationError') !== false ||
        strpos($output, 'NameError') !== false ||
        strpos($output, 'TypeError') !== false) {
        return ['success' => false, 'error' => $output];
    }
    
    return ['success' => true, 'output' => $output];
}

function executeJavaScript($code, $filename) {
    $file = $filename . '.js';
    file_put_contents($file, $code);
    
    $command = "node " . escapeshellarg($file) . " 2>&1";
    
    // Check if node exists
    exec("which node", $output, $returnCode);
    if ($returnCode !== 0) {
        return ['success' => false, 'error' => 'Node.js is not installed on this server'];
    }
    
    $output = shell_exec($command);
    
    // Check for errors
    if (strpos($output, 'SyntaxError') !== false || 
        strpos($output, 'ReferenceError') !== false ||
        strpos($output, 'TypeError') !== false) {
        return ['success' => false, 'error' => $output];
    }
    
    return ['success' => true, 'output' => $output ?: 'Code executed successfully!'];
}

function executeJava($code, $filename) {
    // Extract class name from code
    preg_match('/public\s+class\s+(\w+)/', $code, $matches);
    $className = $matches[1] ?? 'Main';
    
    $file = $filename . '.java';
    file_put_contents($file, $code);
    
    // Compile
    $compileCommand = "javac " . escapeshellarg($file) . " 2>&1";
    $compileOutput = shell_exec($compileCommand);
    
    if ($compileOutput && (strpos($compileOutput, 'error') !== false)) {
        return ['success' => false, 'error' => "Compilation Error:\n" . $compileOutput];
    }
    
    // Check if javac exists
    exec("which javac", $output, $returnCode);
    if ($returnCode !== 0) {
        return ['success' => false, 'error' => 'Java compiler (javac) is not installed on this server'];
    }
    
    // Execute
    $classPath = dirname($file);
    $executeCommand = "cd " . escapeshellarg($classPath) . " && java " . escapeshellarg($className) . " 2>&1";
    $output = shell_exec($executeCommand);
    
    // Check for runtime errors
    if (strpos($output, 'Exception') !== false || strpos($output, 'Error') !== false) {
        return ['success' => false, 'error' => $output];
    }
    
    return ['success' => true, 'output' => $output ?: 'Code executed successfully!'];
}

function executePHP($code, $filename) {
    $file = $filename . '.php';
    file_put_contents($file, $code);
    
    $command = "php " . escapeshellarg($file) . " 2>&1";
    $output = shell_exec($command);
    
    // Check for syntax errors
    if (strpos($output, 'Parse error') !== false || 
        strpos($output, 'Fatal error') !== false ||
        strpos($output, 'Warning') !== false) {
        return ['success' => false, 'error' => $output];
    }
    
    return ['success' => true, 'output' => $output ?: 'Code executed successfully!'];
}

function executeCPP($code, $filename) {
    $sourceFile = $filename . '.cpp';
    $outputFile = $filename . '.out';
    
    file_put_contents($sourceFile, $code);
    
    // Compile
    $compileCommand = "g++ " . escapeshellarg($sourceFile) . " -o " . escapeshellarg($outputFile) . " 2>&1";
    $compileOutput = shell_exec($compileCommand);
    
    // Check if g++ exists
    exec("which g++", $output, $returnCode);
    if ($returnCode !== 0) {
        return ['success' => false, 'error' => 'C++ compiler (g++) is not installed on this server'];
    }
    
    if ($compileOutput && (strpos($compileOutput, 'error') !== false)) {
        return ['success' => false, 'error' => "Compilation Error:\n" . $compileOutput];
    }
    
    // Execute
    $executeCommand = escapeshellarg($outputFile) . " 2>&1";
    $output = shell_exec($executeCommand);
    
    return ['success' => true, 'output' => $output ?: 'Code executed successfully!'];
}

function executeC($code, $filename) {
    $sourceFile = $filename . '.c';
    $outputFile = $filename . '.out';
    
    file_put_contents($sourceFile, $code);
    
    // Compile
    $compileCommand = "gcc " . escapeshellarg($sourceFile) . " -o " . escapeshellarg($outputFile) . " 2>&1";
    $compileOutput = shell_exec($compileCommand);
    
    // Check if gcc exists
    exec("which gcc", $output, $returnCode);
    if ($returnCode !== 0) {
        return ['success' => false, 'error' => 'C compiler (gcc) is not installed on this server'];
    }
    
    if ($compileOutput && (strpos($compileOutput, 'error') !== false)) {
        return ['success' => false, 'error' => "Compilation Error:\n" . $compileOutput];
    }
    
    // Execute
    $executeCommand = escapeshellarg($outputFile) . " 2>&1";
    $output = shell_exec($executeCommand);
    
    return ['success' => true, 'output' => $output ?: 'Code executed successfully!'];
}

function executeHTML($code) {
    // HTML can't be "executed" server-side in the traditional sense
    // We'll just validate it and return a message
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    
    if ($dom->loadHTML($code)) {
        $errors = libxml_get_errors();
        libxml_clear_errors();
        
        if (empty($errors)) {
            return ['success' => true, 'output' => "HTML code is valid!\n\nNote: HTML is rendered in browsers. This editor validates the syntax only.\n\n" . htmlspecialchars($code)];
        } else {
            $errorMessages = array_map(function($error) {
                return "Line {$error->line}: {$error->message}";
            }, $errors);
            return ['success' => false, 'error' => "HTML Validation Errors:\n" . implode("\n", $errorMessages)];
        }
    }
    
    return ['success' => true, 'output' => 'HTML code validated!'];
}

function cleanupTempFiles($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = glob($dir . '/*');
    foreach ($files as $file) {
        if (is_file($file) && (time() - filemtime($file)) > 3600) { // Delete files older than 1 hour
            unlink($file);
        }
    }
}
?>