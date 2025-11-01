<!-- Editor mein ye code dale -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLearn Editor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #1a2332;
            color: #e4e6eb;
            height: 100vh;
            overflow: hidden;
        }

        .header {
            background: #2d3748;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #374151;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .mac-buttons {
            display: flex;
            gap: 8px;
        }

        .mac-btn {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .mac-btn.red { background: #ff5f56; }
        .mac-btn.yellow { background: #ffbd2e; }
        .mac-btn.green { background: #27c93f; }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 600;
        }

        .logo svg {
            width: 24px;
            height: 24px;
        }

        .upgrade-btn {
            background: #f59e0b;
            color: #000;
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
        }

        .language-selector {
            background: #374151;
            padding: 8px 16px;
            border-radius: 6px;
            border: 1px solid #4b5563;
            color: #e4e6eb;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .close-btn {
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 24px;
            cursor: pointer;
            padding: 0 10px;
        }

        .main-container {
            display: flex;
            height: calc(100vh - 50px);
        }

        .editor-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #374151;
        }

        .toolbar {
            background: #1f2937;
            padding: 15px 20px;
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #374151;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-run {
            background: #10b981;
            color: #fff;
        }

        .btn-run:hover:not(:disabled) {
            background: #059669;
        }

        .btn-secondary {
            background: #374151;
            color: #e4e6eb;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .editor-wrapper {
            flex: 1;
            position: relative;
            background: #1a2332;
        }

        .editor-header {
            background: #1f2937;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #374151;
        }

        .editor-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #10b981;
            font-size: 14px;
        }

        .line-count {
            color: #6b7280;
            font-size: 12px;
        }

        .code-editor {
            width: 100%;
            height: calc(100% - 45px);
            background: #0d1117;
            color: #e4e6eb;
            padding: 20px;
            border: none;
            outline: none;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.6;
            resize: none;
            tab-size: 4;
        }

        .output-section {
            width: 800px;
            display: flex;
            flex-direction: column;
            background: #1a2332;
        }

        .output-header {
            background: #1f2937;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #374151;
        }

        .output-title {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #60a5fa;
            font-size: 14px;
        }

        .clear-btn {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 13px;
        }

        .output-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .output-placeholder {
            color: #6b7280;
        }

        .output-result {
            color: #10b981;
        }

        .output-error {
            color: #ef4444;
        }

        .output-warning {
            color: #f59e0b;
        }

        .footer {
            background: #000;
            padding: 8px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #9ca3af;
        }

        .settings-icon {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 16px;
            padding: 5px;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #10b981;
            font-size: 14px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }

            .output-section {
                width: 100%;
                height: 300px;
            }

            .toolbar {
                flex-wrap: wrap;
            }

            .upgrade-btn {
                display: none;
            }
        }

        select {
            background: #374151;
            color: #e4e6eb;
            border: 1px solid #4b5563;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        select option {
            background: #1f2937;
        }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #ffffff40;
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <div class="mac-buttons">
                <div class="mac-btn red"></div>
                <div class="mac-btn yellow"></div>
                <div class="mac-btn green"></div>
                
            </div>
            <div class="logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 18L22 12L16 6M8 6L2 12L8 18"/>
                </svg>
                <span>CodeLearn Editor</span><button class="btn btn-secondary" onclick="window.location.href='courses.php'" style="margin-left: 10px;">
    ← Back to Home
</button>

            </div>
            <!-- <button class="upgrade-btn">Upgrade to Pro for more features</button> -->
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <select id="languageSelect" class="language-selector">
                <option value="63">JavaScript (Node.js)</option>
                <option value="71">Python 3</option>
                <option value="62">Java</option>
                <option value="54">C++ (GCC)</option>
                <option value="51">C# (Mono)</option>
                <option value="68">PHP</option>
                <option value="72">Ruby</option>
                <option value="73">Rust</option>
                <option value="60">Go</option>
                <option value="82">SQL (SQLite)</option>
                <option value="78">Kotlin</option>
                <option value="83">Swift</option>
            </select>
            <button class="close-btn">×</button>
        </div>
    </div>

    <div class="main-container">
        <div class="editor-section">
            <div class="toolbar">
                <button class="btn btn-run" id="runBtn" onclick="runCode()">
                    <span></span> Run Code
                </button>
                <button class="btn btn-secondary" onclick="copyCode()">
                    <span></span> Copy
                </button>
                <button class="btn btn-secondary" onclick="saveCode()">
                    <span></span> Save
                </button>
                <button class="btn btn-secondary" onclick="resetCode()">
                    <span></span> Reset
                </button>
                <div style="margin-left: auto; display: flex; align-items: center; gap: 10px;">
                    <div class="status-indicator" id="statusIndicator">
                        <div class="status-dot"></div>
                        <span>Ready</span>
                    </div>
                    <button class="settings-icon">⚙</button>
                </div>
            </div>

            <div class="editor-wrapper">
                <div class="editor-header">
                    <div class="editor-title">
                        <span>📄</span>
                        <span id="editorTitle">JavaScript Editor</span>
                    </div>
                    <div class="line-count">Lines: <span id="lineCount">1</span></div>
                </div>
                <textarea id="codeEditor" class="code-editor" placeholder="Write your code here..." spellcheck="false">// Write your JavaScript code here
console.log("Hello, World!");
console.log("This is real code execution!");

// Try some calculations
let sum = 5 + 10;
console.log("Sum:", sum);</textarea>
            </div>
        </div>

        <div class="output-section">
            <div class="output-header">
                <div class="output-title">
                    <span>▶</span>
                    <span>Output</span>
                </div>
                <button class="clear-btn" onclick="clearOutput()">Clear</button>
            </div>
            <div class="output-content" id="output">
                <div class="output-placeholder">Click "Run Code" to see output here...

🚀 Real Code Execution Powered by Judge0 API
✅ All languages compile and run on real servers
⚡ Fast execution with detailed error messages</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div>Ready to execute code • <span style="color: #f59e0b;">Real compilation & execution for all languages</span></div>
        <div>Powered by CodeLearn 🚀</div>
    </div>

    <script>
        const editor = document.getElementById('codeEditor');
        const output = document.getElementById('output');
        const languageSelect = document.getElementById('languageSelect');
        const editorTitle = document.getElementById('editorTitle');
        const lineCount = document.getElementById('lineCount');
        const runBtn = document.getElementById('runBtn');
        const statusIndicator = document.getElementById('statusIndicator');

        const codeTemplates = {
            63: '// JavaScript (Node.js)\nconsole.log("Hello, World!");\nconsole.log("JavaScript is running!");\n\n// Try some code\nlet arr = [1, 2, 3, 4, 5];\nconsole.log("Array:", arr);',
            71: '# Python 3\nprint("Hello, World!")\nprint("Python is running!")\n\n# Try some code\nnumbers = [1, 2, 3, 4, 5]\nprint("Numbers:", numbers)',
            62: '// Java\npublic class Main {\n    public static void main(String[] args) {\n        System.out.println("Hello, World!");\n        System.out.println("Java is running!");\n    }\n}',
            54: '// C++ (GCC)\n#include <iostream>\nusing namespace std;\n\nint main() {\n    cout << "Hello, World!" << endl;\n    cout << "C++ is running!" << endl;\n    return 0;\n}',
            51: '// C# (Mono)\nusing System;\n\nclass Program {\n    static void Main() {\n        Console.WriteLine("Hello, World!");\n        Console.WriteLine("C# is running!");\n    }\n}',
            68: '<?php\n// PHP\necho "Hello, World!\\n";\necho "PHP is running!\\n";\n\n// Try some code\n$arr = [1, 2, 3, 4, 5];\nprint_r($arr);\n?>',
            72: '# Ruby\nputs "Hello, World!"\nputs "Ruby is running!"\n\n# Try some code\narr = [1, 2, 3, 4, 5]\nputs "Array: #{arr}"',
            73: '// Rust\nfn main() {\n    println!("Hello, World!");\n    println!("Rust is running!");\n}',
            60: '// Go\npackage main\nimport "fmt"\n\nfunc main() {\n    fmt.Println("Hello, World!")\n    fmt.Println("Go is running!")\n}',
            82: '-- SQL (SQLite)\nCREATE TABLE users (id INTEGER, name TEXT);\nINSERT INTO users VALUES (1, "Alice");\nINSERT INTO users VALUES (2, "Bob");\nSELECT * FROM users;',
            78: '// Kotlin\nfun main() {\n    println("Hello, World!")\n    println("Kotlin is running!")\n}',
            83: '// Swift\nimport Foundation\nprint("Hello, World!")\nprint("Swift is running!")'
        };

        const languageNames = {
            63: 'JavaScript (Node.js)',
            71: 'Python 3',
            62: 'Java',
            54: 'C++ (GCC)',
            51: 'C# (Mono)',
            68: 'PHP',
            72: 'Ruby',
            73: 'Rust',
            60: 'Go',
            82: 'SQL (SQLite)',
            78: 'Kotlin',
            83: 'Swift'
        };

        editor.addEventListener('input', () => {
            const lines = editor.value.split('\n').length;
            lineCount.textContent = lines;
        });

        languageSelect.addEventListener('change', (e) => {
            const langId = e.target.value;
            editorTitle.textContent = `${languageNames[langId]} Editor`;
            editor.value = codeTemplates[langId];
            lineCount.textContent = editor.value.split('\n').length;
        });

        function updateStatus(status, text) {
            const dot = statusIndicator.querySelector('.status-dot');
            const span = statusIndicator.querySelector('span');
            
            if (status === 'running') {
                dot.style.background = '#f59e0b';
                span.textContent = text || 'Running...';
            } else if (status === 'error') {
                dot.style.background = '#ef4444';
                span.textContent = text || 'Error';
            } else {
                dot.style.background = '#10b981';
                span.textContent = text || 'Ready';
            }
        }

        async function runCode() {
            const code = editor.value.trim();
            const languageId = languageSelect.value;

            if (!code) {
                output.innerHTML = '<div class="output-error">❌ Error: No code to execute!</div>';
                return;
            }

            runBtn.disabled = true;
            runBtn.innerHTML = '<div class="spinner"></div> Running...';
            updateStatus('running', 'Executing...');
            output.innerHTML = '<div style="color: #60a5fa;">⏳ Compiling and executing code on server...\n\nPlease wait...</div>';

            try {
                // Submit code to Judge0
                const submitResponse = await fetch('https://judge0-ce.p.rapidapi.com/submissions?base64_encoded=false&wait=true', {
                    method: 'POST',
                    headers: {
                        'content-type': 'application/json',
                        'X-RapidAPI-Key': '034915d52dmsh7bd1fcc44ddcf58p13bae1jsn43c93cb0915b', // Users need to get their own key from RapidAPI
                        'X-RapidAPI-Host': 'judge0-ce.p.rapidapi.com'
                    },
                    body: JSON.stringify({
                        language_id: parseInt(languageId),
                        source_code: code,
                        stdin: ""
                    })
                });

                if (!submitResponse.ok) {
                    throw new Error('Failed to submit code. Please check your API key.');
                }

                const result = await submitResponse.json();
                displayResult(result);

            } catch (error) {
                output.innerHTML = `<div class="output-error">❌ Error: ${error.message}\n\n💡 Note: You need to add your own RapidAPI key for Judge0.\n\n📝 Steps:\n1. Go to https://rapidapi.com/judge0-official/api/judge0-ce\n2. Subscribe (free tier available)\n3. Copy your API key\n4. Replace 'YOUR_RAPIDAPI_KEY_HERE' in the code</div>`;
                updateStatus('error', 'API Error');
            } finally {
                runBtn.disabled = false;
                runBtn.innerHTML = '<span>▶</span> Run Code';
            }
        }

        function displayResult(result) {
            let outputText = '';

            if (result.status.id === 3) {
                // Success
                outputText = `<div class="output-result">✅ Execution Successful!\n\n📤 Output:\n${result.stdout || '(No output)'}</div>`;
                updateStatus('ready', 'Success');
            } else if (result.status.id === 6) {
                // Compilation Error
                outputText = `<div class="output-error">❌ Compilation Error:\n\n${result.compile_output || result.stderr || 'Unknown error'}</div>`;
                updateStatus('error', 'Compile Error');
            } else if (result.status.id === 11 || result.status.id === 12 || result.status.id === 13) {
                // Runtime Error
                outputText = `<div class="output-error">❌ Runtime Error:\n\n${result.stderr || result.message || 'Unknown runtime error'}</div>`;
                updateStatus('error', 'Runtime Error');
            } else if (result.status.id === 5) {
                // Time Limit Exceeded
                outputText = `<div class="output-warning">⚠️ Time Limit Exceeded\n\nYour code took too long to execute.</div>`;
                updateStatus('error', 'Timeout');
            } else {
                outputText = `<div class="output-warning">⚠️ ${result.status.description}\n\n${result.stderr || result.message || ''}</div>`;
                updateStatus('error', result.status.description);
            }

            output.innerHTML = outputText;
            setTimeout(() => updateStatus('ready', 'Ready'), 3000);
        }

        function copyCode() {
            editor.select();
            document.execCommand('copy');
            const originalStatus = statusIndicator.querySelector('span').textContent;
            updateStatus('ready', 'Copied!');
            setTimeout(() => updateStatus('ready', originalStatus), 2000);
        }

        function saveCode() {
            const code = editor.value;
            const languageId = languageSelect.value;
            const extensions = {
                63: 'js', 71: 'py', 62: 'java', 54: 'cpp', 51: 'cs',
                68: 'php', 72: 'rb', 73: 'rs', 60: 'go', 82: 'sql',
                78: 'kt', 83: 'swift'
            };
            
            const blob = new Blob([code], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `code.${extensions[languageId]}`;
            a.click();
            URL.revokeObjectURL(url);
            
            updateStatus('ready', 'Saved!');
            setTimeout(() => updateStatus('ready', 'Ready'), 2000);
        }

        function resetCode() {
            const languageId = languageSelect.value;
            editor.value = codeTemplates[languageId];
            lineCount.textContent = editor.value.split('\n').length;
            clearOutput();
        }

        function clearOutput() {
            output.innerHTML = '<div class="output-placeholder">Click "Run Code" to see output here...</div>';
            updateStatus('ready', 'Ready');
        }

        // Initialize line count
        lineCount.textContent = editor.value.split('\n').length;

        onclick="window.location.href='index.php'"
    </script>
</body>
</html>