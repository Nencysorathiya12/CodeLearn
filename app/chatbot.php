<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBot AI - Modern Learning Assistant</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
            --success: #10b981;
            --bg-dark: #0f172a;
            --bg-card: #1e293b;
            --bg-hover: #334155;
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #64748b;
            --border: #334155;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--bg-dark) 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        .app {
            display: flex;
            height: 100vh;
            max-width: 1600px;
            margin: 0 auto;
        }

        .sidebar {
            width: 280px;
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
        }

        .brand-text h1 {
            font-size: 18px;
            font-weight: 700;
        }

        .brand-text p {
            font-size: 12px;
            color: var(--text-muted);
        }

        .new-chat {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: transform 0.2s;
        }

        .new-chat:hover {
            transform: translateY(-2px);
        }

        .categories {
            padding: 16px;
            overflow-y: auto;
        }

        .cat-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            margin: 16px 0 10px;
            letter-spacing: 0.5px;
        }

        .cat-btn {
            width: 100%;
            padding: 10px 14px;
            background: var(--bg-hover);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-secondary);
            cursor: pointer;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            text-align: left;
            font-size: 14px;
        }

        .cat-btn:hover {
            background: var(--bg-dark);
            border-color: var(--primary);
            color: var(--text-primary);
        }

        .cat-btn.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-color: var(--primary);
            color: white;
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-dark);
        }

        .header {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h3 {
            font-size: 16px;
            margin-bottom: 4px;
        }

        .status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--success);
        }

        .status-dot {
            width: 6px;
            height: 6px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .header-btn {
            width: 36px;
            height: 36px;
            background: var(--bg-hover);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .header-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .chat {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        .welcome {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            padding: 40px 20px;
        }

        .welcome-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 24px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .welcome h2 {
            font-size: 30px;
            margin-bottom: 12px;
        }

        .welcome p {
            color: var(--text-secondary);
            margin-bottom: 32px;
            font-size: 15px;
        }

        .questions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 12px;
            margin-top: 24px;
        }

        .q-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: left;
        }

        .q-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.2);
        }

        .q-tag {
            display: inline-block;
            padding: 4px 10px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .q-text {
            font-size: 14px;
            color: var(--text-primary);
            font-weight: 500;
        }

        .messages {
            max-width: 800px;
            margin: 0 auto;
        }

        .msg {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            animation: slideIn 0.3s;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .msg.user {
            flex-direction: row-reverse;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .msg.ai .avatar {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        .msg.user .avatar {
            background: var(--bg-hover);
        }

        .bubble {
            max-width: 600px;
            padding: 14px 18px;
            border-radius: 14px;
            line-height: 1.6;
            font-size: 14px;
        }

        .msg.ai .bubble {
            background: var(--bg-card);
            border: 1px solid var(--border);
        }

        .msg.user .bubble {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .time {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
        }

        .typing {
            display: none;
            gap: 4px;
            padding: 8px 0;
        }

        .typing.show {
            display: flex;
        }

        .typing span {
            width: 6px;
            height: 6px;
            background: var(--text-muted);
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing span:nth-child(2) { animation-delay: 0.2s; }
        .typing span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing {
            0%, 80%, 100% { opacity: 0.3; }
            40% { opacity: 1; }
        }

        .follow {
            margin-top: 12px;
        }

        .follow-title {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .follow-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chip {
            padding: 8px 14px;
            background: var(--bg-hover);
            border: 1px solid var(--border);
            border-radius: 16px;
            font-size: 12px;
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s;
        }

        .chip:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .input-area {
            background: var(--bg-card);
            border-top: 1px solid var(--border);
            padding: 16px 24px;
        }

        .input-wrap {
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .input {
            flex: 1;
            background: var(--bg-dark);
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            color: var(--text-primary);
            font-size: 14px;
            resize: none;
            max-height: 100px;
            font-family: inherit;
        }

        .input:focus {
            outline: none;
            border-color: var(--primary);
        }

        .send {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            border-radius: 10px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .send:hover:not(:disabled) {
            transform: scale(1.05);
        }

        .send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-card);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="app">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="brand">
                    <div class="brand-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="brand-text">
                        <h1>EduBot AI</h1>
                        <p>Learning Assistant</p>
                    </div>
                </div>
                <button class="new-chat" onclick="newChat()">
                    <i class="fas fa-plus"></i>
                    New Chat
                </button>
            </div>

            <div class="categories">
                <div class="cat-title">Topics</div>
                <button class="cat-btn active" onclick="showCategory('all')">
                    <i class="fas fa-home"></i>
                    All Topics
                </button>
                <button class="cat-btn" onclick="showCategory('programming')">
                    <i class="fas fa-code"></i>
                    Programming
                </button>
                <button class="cat-btn" onclick="showCategory('web')">
                    <i class="fas fa-globe"></i>
                    Web Dev
                </button>
                <button class="cat-btn" onclick="showCategory('data')">
                    <i class="fas fa-database"></i>
                    Data Science
                </button>
                <button class="cat-btn" onclick="showCategory('ai')">
                    <i class="fas fa-brain"></i>
                    AI & ML
                </button>
                <button class="cat-btn" onclick="showCategory('study')">
                    <i class="fas fa-book"></i>
                    Study Tips
                </button>
            </div>
        </div>

        <div class="main">
            <div class="header">
                <div>
                    <h3>Learning Assistant</h3>
                    <div class="status">
                        <div class="status-dot"></div>
                        <span>Online</span>
                    </div>
                </div>
                <button class="header-btn" onclick="newChat()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="chat" id="chat">
                <div class="welcome" id="welcome">
                    <div class="welcome-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h2>Welcome to EduBot AI</h2>
                    <p>Your intelligent companion for programming, web development, AI, and more. Choose a question to start!</p>

                    <div class="questions" id="questionsList"></div>
                </div>

                <div class="messages" id="messages"></div>
            </div>

            <div class="input-area">
                <div class="input-wrap">
                    <textarea 
                        id="input" 
                        class="input" 
                        placeholder="Ask anything about programming, web dev, AI..."
                        rows="1"
                    ></textarea>
                    <button class="send" id="sendBtn" disabled>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const questionDB = {
            all: [
                {q: "How do I start learning Python?", cat: "programming"},
                {q: "Create 30-day web dev roadmap", cat: "web"},
                {q: "Explain machine learning simply", cat: "ai"},
                {q: "Best study techniques", cat: "study"},
                {q: "How to learn JavaScript?", cat: "programming"},
                {q: "What is React and why use it?", cat: "web"}
            ],
            programming: [
                {q: "Python basics for beginners", cat: "programming"},
                {q: "Object-oriented programming explained", cat: "programming"},
                {q: "Java vs JavaScript differences", cat: "programming"},
                {q: "How to debug code effectively?", cat: "programming"},
                {q: "Best practices for clean code", cat: "programming"}
            ],
            web: [
                {q: "HTML5 new features", cat: "web"},
                {q: "CSS Flexbox vs Grid", cat: "web"},
                {q: "Build responsive website", cat: "web"},
                {q: "What is REST API?", cat: "web"},
                {q: "Frontend frameworks comparison", cat: "web"}
            ],
            data: [
                {q: "Data analysis with Python", cat: "data"},
                {q: "SQL basics tutorial", cat: "data"},
                {q: "Data visualization tips", cat: "data"},
                {q: "Pandas library guide", cat: "data"}
            ],
            ai: [
                {q: "What is machine learning?", cat: "ai"},
                {q: "Neural networks basics", cat: "ai"},
                {q: "Deep learning introduction", cat: "ai"},
                {q: "TensorFlow vs PyTorch", cat: "ai"}
            ],
            study: [
                {q: "Effective study methods", cat: "study"},
                {q: "Time management tips", cat: "study"},
                {q: "How to stay motivated?", cat: "study"},
                {q: "Note-taking techniques", cat: "study"}
            ]
        };

        const responseDB = {
            'python basics': `🐍 **Python for Complete Beginners**

Let me guide you through Python step-by-step!

**Why Python?**
✓ Easy to learn and read
✓ Powerful for data science, web dev, automation
✓ Huge community support
✓ High job demand

**Week 1: Getting Started**
\`\`\`python
# Your first program
print("Hello, World!")

# Variables
name = "Alex"
age = 25
height = 5.9

print(f"Hi, I'm {name}, {age} years old")
\`\`\`

**Week 2: Data Types**
\`\`\`python
# Numbers
x = 10
y = 3.14

# Strings
message = "Learning Python"
print(message.upper())

# Lists
fruits = ["apple", "banana", "orange"]
fruits.append("mango")

# Dictionaries
person = {
    "name": "John",
    "age": 30,
    "city": "New York"
}
\`\`\`

**Week 3: Control Flow**
\`\`\`python
# If statements
score = 85
if score >= 90:
    print("A grade")
elif score >= 80:
    print("B grade")
else:
    print("Keep trying!")

# Loops
for i in range(5):
    print(f"Count: {i}")

# While loop
count = 0
while count < 3:
    print("Python is fun!")
    count += 1
\`\`\`

**Week 4: Functions**
\`\`\`python
def greet(name):
    return f"Hello, {name}!"

def calculate_area(length, width):
    return length * width

# Using functions
print(greet("Alice"))
area = calculate_area(5, 3)
print(f"Area: {area}")
\`\`\`

**Practice Projects:**
1. Calculator
2. Password generator
3. To-do list
4. Number guessing game

**Best Resources:**
• python.org/about/gettingstarted
• realpython.com
• pythontutor.com (visualize code)
• w3schools.com/python

Ready to write your first program?`,

            'web roadmap': `🚀 **30-Day Web Development Mastery Plan**

Transform into a web developer in just one month!

**Days 1-10: HTML & CSS Foundation**

**Day 1-3: HTML Structure**
\`\`\`html
<!DOCTYPE html>
<html>
<head>
    <title>My First Site</title>
</head>
<body>
    <header>
        <nav>
            <a href="#home">Home</a>
            <a href="#about">About</a>
        </nav>
    </header>
    
    <main>
        <h1>Welcome</h1>
        <p>Building my first website!</p>
    </main>
    
    <footer>
        <p>&copy; 2025 My Site</p>
    </footer>
</body>
</html>
\`\`\`

**Day 4-7: CSS Styling**
\`\`\`css
body {
    font-family: Arial, sans-serif;
    margin: 0;
    background: #f0f0f0;
}

header {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 20px;
}

nav a {
    color: white;
    text-decoration: none;
    margin: 0 15px;
    transition: opacity 0.3s;
}

nav a:hover {
    opacity: 0.8;
}
\`\`\`

**Day 8-10: Responsive Design**
\`\`\`css
/* Mobile First */
.container {
    width: 100%;
    padding: 15px;
}

/* Tablet */
@media (min-width: 768px) {
    .container {
        width: 750px;
        margin: 0 auto;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .container {
        width: 960px;
    }
}
\`\`\`

**Days 11-20: JavaScript Power**

**Day 11-14: JS Basics**
\`\`\`javascript
// Variables
let name = "Sarah";
const age = 25;

// Functions
function calculateTax(price) {
    return price * 1.18;
}

// Arrays
const colors = ["red", "blue", "green"];
colors.forEach(color => {
    console.log(color);
});

// Objects
const car = {
    brand: "Tesla",
    model: "Model 3",
    year: 2024
};
\`\`\`

**Day 15-17: DOM Manipulation**
\`\`\`javascript
// Select elements
const btn = document.querySelector('#myButton');
const output = document.getElementById('output');

// Event listener
btn.addEventListener('click', () => {
    output.textContent = 'Button clicked!';
    output.style.color = 'green';
});

// Create new elements
const newDiv = document.createElement('div');
newDiv.innerHTML = '<p>Dynamic content!</p>';
document.body.appendChild(newDiv);
\`\`\`

**Day 18-20: API Integration**
\`\`\`javascript
// Fetch data
async function getUsers() {
    try {
        const response = await fetch('https://api.example.com/users');
        const data = await response.json();
        
        data.forEach(user => {
            console.log(user.name);
        });
    } catch (error) {
        console.error('Error:', error);
    }
}

getUsers();
\`\`\`

**Days 21-25: Build Real Projects**

**Project 1: Portfolio Website**
• About section
• Projects showcase
• Contact form
• Responsive design

**Project 2: Weather App**
• API integration
• Search functionality
• Display forecast
• Clean UI

**Project 3: To-Do App**
• Add/delete tasks
• Mark complete
• Local storage
• Filter options

**Days 26-30: Advanced & Deploy**

**Learn:**
• Git and GitHub
• Chrome DevTools
• Performance optimization
• Accessibility basics

**Deploy Your Projects:**
• GitHub Pages (free)
• Netlify (free)
• Vercel (free)

**Daily Schedule:**
• Morning: 1.5 hours theory
• Afternoon: 2 hours coding
• Evening: 30 min review

**Essential Tools:**
• VS Code
• Chrome DevTools
• Git
• Figma (design)

Ready to start day 1?`,

            'machine learning': `🤖 **Machine Learning Demystified**

Let me explain ML in the simplest way possible!

**The Core Concept**
Instead of writing rules, we teach computers to learn patterns from data.

**Real-Life Analogy:**
Teaching a child to identify animals:
• Show 100 cat photos → child notices patterns (whiskers, pointed ears, meow)
• Show 100 dog photos → child notices differences (bark, wagging tail)
• Show new animal → child can identify it!

That's exactly how ML works!

**Three Types of ML:**

**1. Supervised Learning (Learn with a Teacher)**
You provide labeled data: "This is a cat", "This is a dog"

**Example: Email Spam Filter**
\`\`\`python
from sklearn.naive_bayes import MultinomialNB

# Training data
emails = [
    "Win free money now!",
    "Meeting at 3pm tomorrow",
    "Click here for prize",
    "Project deadline update"
]

labels = ["spam", "not spam", "spam", "not spam"]

# Train model
model = MultinomialNB()
model.fit(emails_vectorized, labels)

# Predict new email
new_email = ["Free lottery winner"]
prediction = model.predict(new_email)
print(prediction)  # "spam"
\`\`\`

**Real Applications:**
• Image recognition
• Price prediction
• Medical diagnosis
• Voice recognition

**2. Unsupervised Learning (Find Hidden Patterns)**
No labels - algorithm finds patterns on its own.

**Example: Customer Segmentation**
\`\`\`python
from sklearn.cluster import KMeans

# Customer data: [age, income]
customers = [
    [25, 35000], [28, 40000],  # Young, lower income
    [45, 90000], [50, 95000],  # Older, higher income
    [23, 30000], [48, 88000]
]

# Find 2 groups
kmeans = KMeans(n_clusters=2)
groups = kmeans.fit_predict(customers)

print(groups)  # [0, 0, 1, 1, 0, 1]
# Group 0: Young professionals
# Group 1: Experienced professionals
\`\`\`

**Real Applications:**
• Recommendation systems (Netflix, Amazon)
• Market segmentation
• Anomaly detection
• Data compression

**3. Reinforcement Learning (Learn by Trial & Error)**
Agent learns from rewards and penalties.

**Think of it like:**
Training a dog:
• Good behavior → Treat (reward)
• Bad behavior → No treat (penalty)
• Dog learns what gets rewards

**Real Applications:**
• Game AI (AlphaGo, Chess)
• Self-driving cars
• Robot navigation
• Trading algorithms

**Popular ML Algorithms:**

**Classification:**
• Logistic Regression
• Decision Trees
• Random Forest
• Neural Networks

**Regression:**
• Linear Regression
• Polynomial Regression

**Clustering:**
• K-Means
• Hierarchical Clustering

**How Netflix Recommends Shows:**
\`\`\`
1. Track what you watch
2. Find patterns (you like sci-fi)
3. Find similar users
4. Recommend what they liked
\`\`\`

**Getting Started Path:**

**Month 1: Python Basics**
• Variables, functions, loops
• Lists and dictionaries

**Month 2: Essential Libraries**
• NumPy (math operations)
• Pandas (data manipulation)
• Matplotlib (visualization)

**Month 3: ML Algorithms**
• Scikit-learn library
• Train models
• Make predictions

**Month 4: Projects**
• Iris flower classification
• House price prediction
• Movie recommender

**Simple First Project:**
\`\`\`python
from sklearn.linear_model import LinearRegression

# Study hours vs exam scores
hours = [[1], [2], [3], [4], [5]]
scores = [50, 55, 65, 75, 85]

# Train model
model = LinearRegression()
model.fit(hours, scores)

# Predict
new_hours = [[6]]
prediction = model.predict(new_hours)
print(f"Expected score: {prediction[0]:.0f}")
# Output: Expected score: 95
\`\`\`

**Best Resources:**
• Coursera: Machine Learning by Andrew Ng
• Fast.ai: Practical Deep Learning
• Kaggle: Practice datasets
• Scikit-learn documentation

Want to build your first ML model together?`,

            'study techniques': `📚 **Science-Backed Study Techniques**

Master these methods to learn faster and retain more!

**1. Active Recall (Most Powerful!)**

**What is it?**
Testing yourself instead of passive re-reading.

**How to do it:**
\`\`\`
❌ Bad: Reading notes 5 times
✅ Good: Close book, write what you remember

Example for Python:
- Learn: functions, loops, variables
- Close tutorial
- Code from memory
- Check mistakes
- Repeat tomorrow
\`\`\`

**Why it works:**
Retrieval strengthens memory pathways in your brain.

**2. Spaced Repetition**

**The Schedule:**
• Day 1: Learn new material
• Day 2: First review (10 min)
• Day 7: Second review (5 min)
• Day 14: Third review (3 min)
• Day 30: Fourth review (2 min)

**Use Anki or RemNote** for automatic scheduling.

**3. Pomodoro Technique**

**The Process:**
\`\`\`
25 min → DEEP FOCUS (no phone, no distractions)
5 min → BREAK (walk, stretch, water)
25 min → FOCUS
5 min → BREAK
25 min → FOCUS
5 min → BREAK
25 min → FOCUS
15 min → LONG BREAK

= 4 Pomodoros = 2 hours productive work
\`\`\`

**During Focus Time:**
• Phone on airplane mode
• Close all browser tabs except learning material
• Use white noise if needed
• Single task only

**4. Feynman Technique (Learn Like a Teacher)**

**4 Steps:**

Step 1: Pick a concept (e.g., "APIs")

Step 2: Explain it simply to a 10-year-old
\`\`\`
"An API is like a waiter in a restaurant.
You don't go to the kitchen yourself.
You tell the waiter what you want.
Waiter tells the kitchen.
Kitchen makes your food.
Waiter brings it back.

Similarly, your app asks the API for data.
API gets data from server.
API brings data to your app."
\`\`\`

Step 3: Identify gaps - Where did you struggle?

Step 4: Review and simplify those parts

**5. Interleaving (Mix Topics)**

**Wrong Way (Blocked Practice):**
\`\`\`
Monday: Only HTML (3 hours)
Tuesday: Only CSS (3 hours)
Wednesday: Only JS (3 hours)
\`\`\`

**Right Way (Interleaved Practice):**
\`\`\`
Monday: HTML (1h) + CSS (1h) + JS (1h)
Tuesday: CSS (1h) + JS (1h) + HTML (1h)
Wednesday: JS (1h) + HTML (1h) + CSS (1h)
\`\`\`

**Why?** Your brain learns to distinguish between concepts better.

**6. Build Immediately (80/20 Rule)**

After learning any concept:
• 20% theory
• 80% practice/building

**Example:**
Learned React hooks? Build a counter app NOW.
Don't watch 10 more tutorials.

**7. Optimize Your Environment**

**Physical:**
• Dedicated study space
• Good lighting
• 20-22°C temperature
• Clean desk
• Comfortable chair

**Digital:**
• Use website blockers (Freedom, Cold Turkey)
• Separate browser profiles
• Organized files and bookmarks

**8. Memory Techniques**

**Mnemonics for Coding:**
\`\`\`
HTTP Methods: "Get Post Pudding, Please Delete"
- GET
- POST
- PUT
- PATCH
- DELETE

CSS Box Model: "My Pretty Border Covers"
- Margin
- Padding
- Border
- Content
\`\`\`

**Chunking:**
Break 192.168.1.1 into: 192 | 168 | 1 | 1

**Daily Study Schedule:**

**Morning (7-9 AM):**
• 15 min: Review yesterday's notes
• 90 min: Learn new concept (most productive time)
• 15 min: Summarize what you learned

**Afternoon (2-5 PM):**
• 2 hours: Code/practice
• 30 min: Debug and improve
• 30 min: Watch supplementary video

**Evening (8-9 PM):**
• 20 min: Anki flashcards
• 20 min: Write blog post or teach someone
• 20 min: Plan tomorrow

**Avoid These Mistakes:**

• Highlighting without understanding
• Re-reading passively
• Studying marathon sessions without breaks
• Not applying immediately
• Perfectionism
• Comparing yourself to others

**Track Your Progress:**
• GitHub contribution graph
• Learning journal
• Project portfolio
• Daily checkboxes

Want a personalized study plan for your specific goal?`,

            'javascript learn': `**JavaScript Mastery Guide**

Let me teach you JavaScript from zero to hero!

**What is JavaScript?**
The programming language that makes websites interactive and dynamic.

**Week 1: Fundamentals**

**Variables**
\`\`\`javascript
// Three ways to declare variables
var oldWay = "Don't use this";
let changeable = "Can be changed";
const permanent = "Cannot be changed";

// Examples
let age = 25;
age = 26;  // OK

const name = "Alex";
name = "John";  // ERROR!
\`\`\`

**Data Types**
\`\`\`javascript
// Numbers
let score = 95;
let price = 19.99;

// Strings
let message = "Hello World";
let template = \`My score is \${score}\`;  // Template literal

// Booleans
let isStudent = true;
let hasGraduated = false;

// Arrays
let colors = ["red", "blue", "green"];
let mixed = [1, "hello", true, null];

// Objects
let person = {
    name: "Sarah",
    age: 28,
    city: "NYC",
    isEmployed: true
};
\`\`\`

**Week 2: Functions**

**Function Declaration**
\`\`\`javascript
function greet(name) {
    return "Hello, " + name;
}

console.log(greet("Alex"));  // Hello, Alex
\`\`\`

**Arrow Functions (Modern)**
\`\`\`javascript
const add = (a, b) => a + b;
const square = x => x * x;

console.log(add(5, 3));      // 8
console.log(square(4));      // 16
\`\`\`

**Week 3: DOM Manipulation**

**Select Elements**
\`\`\`javascript
// Select by ID
const header = document.getElementById('header');

// Select by class
const buttons = document.querySelectorAll('.btn');

// Select by tag
const paragraphs = document.getElementsByTagName('p');
\`\`\`

**Modify Elements**
\`\`\`javascript
// Change text
header.textContent = "New Title";

// Change HTML
header.innerHTML = "<h1>New Title</h1>";

// Change styles
header.style.color = "blue";
header.style.fontSize = "24px";

// Add/remove classes
header.classList.add('active');
header.classList.remove('hidden');
header.classList.toggle('dark-mode');
\`\`\`

**Event Listeners**
\`\`\`javascript
const button = document.querySelector('#myButton');

button.addEventListener('click', function() {
    alert('Button clicked!');
});

// Modern arrow function way
button.addEventListener('click', () => {
    console.log('Clicked!');
});
\`\`\`

**Week 4: Array Methods (Super Important!)**

\`\`\`javascript
const numbers = [1, 2, 3, 4, 5];

// map - Transform each element
const doubled = numbers.map(num => num * 2);
// [2, 4, 6, 8, 10]

// filter - Keep elements that match condition
const evenNumbers = numbers.filter(num => num % 2 === 0);
// [2, 4]

// reduce - Combine all elements into one value
const sum = numbers.reduce((total, num) => total + num, 0);
// 15

// forEach - Do something with each element
numbers.forEach(num => {
    console.log(num);
});

// find - Get first element that matches
const found = numbers.find(num => num > 3);
// 4
\`\`\`

**Week 5: Async JavaScript**

**Promises**
\`\`\`javascript
function fetchData() {
    return new Promise((resolve, reject) => {
        setTimeout(() => {
            resolve("Data loaded!");
        }, 2000);
    });
}

fetchData()
    .then(data => console.log(data))
    .catch(error => console.error(error));
\`\`\`

**Async/Await (Cleaner)**
\`\`\`javascript
async function getData() {
    try {
        const response = await fetch('https://api.example.com/data');
        const data = await response.json();
        console.log(data);
    } catch (error) {
        console.error('Error:', error);
    }
}

getData();
\`\`\`

**Real Project: To-Do List**
\`\`\`javascript
const taskInput = document.getElementById('taskInput');
const addBtn = document.getElementById('addBtn');
const taskList = document.getElementById('taskList');

addBtn.addEventListener('click', () => {
    const taskText = taskInput.value;
    
    if (taskText === '') return;
    
    const li = document.createElement('li');
    li.innerHTML = \`
        <span>\${taskText}</span>
        <button class="delete">Delete</button>
    \`;
    
    taskList.appendChild(li);
    taskInput.value = '';
    
    // Delete functionality
    li.querySelector('.delete').addEventListener('click', () => {
        li.remove();
    });
});
\`\`\`

**Modern JavaScript Features (ES6+)**

**Destructuring**
\`\`\`javascript
// Array destructuring
const [first, second] = [1, 2, 3];

// Object destructuring
const person = {name: "John", age: 30};
const {name, age} = person;
\`\`\`

**Spread Operator**
\`\`\`javascript
const arr1 = [1, 2, 3];
const arr2 = [...arr1, 4, 5];  // [1, 2, 3, 4, 5]

const obj1 = {a: 1, b: 2};
const obj2 = {...obj1, c: 3};  // {a: 1, b: 2, c: 3}
\`\`\`

**Practice Projects:**
1. Calculator
2. Weather app with API
3. Quiz application
4. Image slider
5. Shopping cart

**Resources:**
• javascript.info (best tutorial)
• MDN Web Docs
• JavaScript30.com (free projects)
• Eloquent JavaScript (free book)

Ready to build something?`,

            'react basics': `**React - The Modern Way to Build UIs**

React makes building interactive UIs simple and efficient!

**What is React?**
A JavaScript library for building user interfaces with reusable components.

**Why React?**
• Component-based architecture
• Virtual DOM for fast updates
• Huge ecosystem
• Used by Facebook, Netflix, Airbnb

**Setup**
\`\`\`bash
npx create-react-app my-app
cd my-app
npm start
\`\`\`

**Your First Component**
\`\`\`javascript
function Welcome() {
    return <h1>Hello, React!</h1>;
}

export default Welcome;
\`\`\`

**JSX Syntax**
\`\`\`javascript
function Greeting() {
    const name = "Sarah";
    const age = 25;
    
    return (
        <div>
            <h1>Hi, I'm {name}</h1>
            <p>I'm {age} years old</p>
        </div>
    );
}
\`\`\`

**Props - Pass Data to Components**
\`\`\`javascript
function UserCard(props) {
    return (
        <div className="card">
            <h2>{props.name}</h2>
            <p>Age: {props.age}</p>
            <p>City: {props.city}</p>
        </div>
    );
}

// Using the component
<UserCard name="John" age={30} city="NYC" />
\`\`\`

**State - Make Components Interactive**
\`\`\`javascript
import { useState } from 'react';

function Counter() {
    const [count, setCount] = useState(0);
    
    return (
        <div>
            <p>Count: {count}</p>
            <button onClick={() => setCount(count + 1)}>
                Increment
            </button>
            <button onClick={() => setCount(count - 1)}>
                Decrement
            </button>
        </div>
    );
}
\`\`\`

**Lists and Keys**
\`\`\`javascript
function TodoList() {
    const todos = [
        { id: 1, text: "Learn React" },
        { id: 2, text: "Build a project" },
        { id: 3, text: "Deploy it" }
    ];
    
    return (
        <ul>
            {todos.map(todo => (
                <li key={todo.id}>{todo.text}</li>
            ))}
        </ul>
    );
}
\`\`\`

**useEffect Hook**
\`\`\`javascript
import { useState, useEffect } from 'react';

function DataFetcher() {
    const [data, setData] = useState([]);
    
    useEffect(() => {
        fetch('https://api.example.com/data')
            .then(res => res.json())
            .then(data => setData(data));
    }, []);  // Empty array = run once on mount
    
    return (
        <div>
            {data.map(item => (
                <p key={item.id}>{item.name}</p>
            ))}
        </div>
    );
}
\`\`\`

**Complete Todo App**
\`\`\`javascript
import { useState } from 'react';

function TodoApp() {
    const [todos, setTodos] = useState([]);
    const [input, setInput] = useState('');
    
    const addTodo = () => {
        if (input.trim()) {
            setTodos([...todos, {
                id: Date.now(),
                text: input,
                completed: false
            }]);
            setInput('');
        }
    };
    
    const deleteTodo = (id) => {
        setTodos(todos.filter(todo => todo.id !== id));
    };
    
    const toggleTodo = (id) => {
        setTodos(todos.map(todo => 
            todo.id === id 
                ? {...todo, completed: !todo.completed}
                : todo
        ));
    };
    
    return (
        <div>
            <h1>My Todos</h1>
            <input 
                value={input}
                onChange={(e) => setInput(e.target.value)}
                placeholder="Add a todo"
            />
            <button onClick={addTodo}>Add</button>
            
            <ul>
                {todos.map(todo => (
                    <li key={todo.id}>
                        <input 
                            type="checkbox"
                            checked={todo.completed}
                            onChange={() => toggleTodo(todo.id)}
                        />
                        <span style={{
                            textDecoration: todo.completed ? 'line-through' : 'none'
                        }}>
                            {todo.text}
                        </span>
                        <button onClick={() => deleteTodo(todo.id)}>
                            Delete
                        </button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
\`\`\`

**Project Ideas:**
1. Weather app
2. Recipe finder
3. Movie database
4. E-commerce cart
5. Social media feed

**Next Steps:**
• React Router (navigation)
• Context API (state management)
• Custom hooks
• API integration

Ready to build your first React app?`,

            'default': `I can help you learn:

**Programming**
• Python, JavaScript, Java
• Data structures & algorithms
• Debugging techniques
• Code best practices

**Web Development**
• HTML, CSS, JavaScript
• React, Vue frameworks
• Responsive design
• API integration

**Data Science**
• Python for data analysis
• Pandas, NumPy
• Data visualization
• SQL databases

**AI & Machine Learning**
• ML fundamentals
• Neural networks
• TensorFlow, PyTorch
• Real-world applications

**Study & Career**
• Effective learning methods
• Time management
• Interview preparation
• Portfolio building

What topic interests you most?`
        };

        const followUpDB = {
            'python': [
                "Show me more Python examples",
                "Python project ideas for beginners",
                "How to practice Python daily?"
            ],
            'web': [
                "Build a portfolio website",
                "JavaScript framework to learn?",
                "Responsive design best practices"
            ],
            'machine learning': [
                "Math needed for ML?",
                "Simple ML project ideas",
                "TensorFlow vs PyTorch?"
            ],
            'javascript': [
                "Advanced JavaScript concepts",
                "Async/await explained",
                "JavaScript project ideas"
            ],
            'react': [
                "React hooks deep dive",
                "State management options",
                "React vs Vue comparison"
            ],
            'study': [
                "Stay consistent with learning",
                "Avoid learning burnout",
                "Best coding practice routine"
            ],
            'default': [
                "Explain this more",
                "Show practical examples",
                "What should I learn next?"
            ]
        };

        // App state
        let currentCategory = 'all';

        // Initialize
        document.addEventListener('DOMContentLoaded', init);

        function init() {
            loadQuestions('all');
            setupInputHandlers();
        }

        function loadQuestions(cat) {
            const list = document.getElementById('questionsList');
            const questions = questionDB[cat] || questionDB.all;
            
            list.innerHTML = questions.map(q => `
                <div class="q-card" onclick="ask('${q.q.replace(/'/g, "\\'")}')">
                    <div class="q-tag">${q.cat}</div>
                    <div class="q-text">${q.q}</div>
                </div>
            `).join('');
        }

        function showCategory(cat) {
            currentCategory = cat;
            document.querySelectorAll('.cat-btn').forEach(btn => btn.classList.remove('active'));
            event.target.closest('.cat-btn').classList.add('active');
            loadQuestions(cat);
        }

        function setupInputHandlers() {
            const input = document.getElementById('input');
            const btn = document.getElementById('sendBtn');
            
            input.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 100) + 'px';
                btn.disabled = !this.value.trim();
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    send();
                }
            });
            
            btn.addEventListener('click', send);
        }

        function ask(question) {
            document.getElementById('input').value = question;
            send();
        }

        function send() {
            const input = document.getElementById('input');
            const text = input.value.trim();
            
            if (!text) return;
            
            hideWelcome();
            addMsg(text, 'user');
            input.value = '';
            input.style.height = 'auto';
            document.getElementById('sendBtn').disabled = true;
            
            showTyping();
            
            setTimeout(() => {
                hideTyping();
                const response = getResponse(text);
                addMsg(response, 'ai');
                addFollowUps(text);
                scrollDown();
            }, 1200 + Math.random() * 800);
        }

        function addMsg(text, sender) {
            const msgs = document.getElementById('messages');
            const time = new Date().toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
            
            const div = document.createElement('div');
            div.className = `msg ${sender}`;
            div.innerHTML = `
                <div class="avatar">
                    <i class="fas fa-${sender === 'user' ? 'user' : 'robot'}"></i>
                </div>
                <div class="bubble">
                    ${format(text)}
                    <div class="time">${time}</div>
                </div>
            `;
            
            msgs.appendChild(div);
            scrollDown();
        }

        function format(text) {
            text = text.replace(/```(\w+)?\n?([\s\S]*?)```/g, (_, lang, code) => {
                return `<pre style="background: #1a1a2e; padding: 12px; border-radius: 6px; overflow-x: auto; margin: 8px 0;"><code>${esc(code.trim())}</code></pre>`;
            });
            text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            text = text.replace(/^[•✓✗]\s+(.+)$/gm, '<div style="margin: 3px 0;">• $1</div>');
            text = text.replace(/\n/g, '<br>');
            return text;
        }

        function esc(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function showTyping() {
            const msgs = document.getElementById('messages');
            const div = document.createElement('div');
            div.id = 'typingMsg';
            div.className = 'msg ai';
            div.innerHTML = `
                <div class="avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="bubble">
                    <div class="typing show">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            `;
            msgs.appendChild(div);
            scrollDown();
        }

        function hideTyping() {
            const typing = document.getElementById('typingMsg');
            if (typing) typing.remove();
        }

        function addFollowUps(text) {
            const lower = text.toLowerCase();
            let key = 'default';
            
            if (lower.includes('python')) key = 'python';
            else if (lower.includes('web') || lower.includes('html') || lower.includes('css')) key = 'web';
            else if (lower.includes('machine learning') || lower.includes('ml') || lower.includes('ai')) key = 'machine learning';
            else if (lower.includes('javascript') || lower.includes('js')) key = 'javascript';
            else if (lower.includes('react')) key = 'react';
            else if (lower.includes('study') || lower.includes('learn')) key = 'study';
            
            const questions = followUpDB[key];
            const msgs = document.getElementById('messages');
            
            const div = document.createElement('div');
            div.className = 'follow';
            div.innerHTML = `
                <div class="follow-title">
                    <i class="fas fa-lightbulb"></i>
                    You might also ask:
                </div>
                <div class="follow-chips">
                    ${questions.map(q => `
                        <div class="chip" onclick="ask('${q.replace(/'/g, "\\'")}')">
                            ${q}
                        </div>
                    `).join('')}
                </div>
            `;
            msgs.appendChild(div);
            scrollDown();
        }

        function getResponse(text) {
            const lower = text.toLowerCase();
            
            if (lower.match(/python.*start|start.*python|python.*begin|learn python|python basic/)) {
                return responseDB['python basics'];
            }
            if (lower.match(/web.*roadmap|30.*day.*web|web.*dev.*plan|learn web/)) {
                return responseDB['web roadmap'];
            }
            if (lower.match(/machine learning|what.*ml|explain.*ml|ml.*work/)) {
                return responseDB['machine learning'];
            }
            if (lower.match(/javascript|learn.*js|js.*start/)) {
                return responseDB['javascript learn'];
            }
            if (lower.match(/react|what.*react|react.*work/)) {
                return responseDB['react basics'];
            }
            if (lower.match(/study.*technique|learn.*method|study.*tip/)) {
                return responseDB['study techniques'];
            }
            
            return responseDB['default'];
        }

        function hideWelcome() {
            const w = document.getElementById('welcome');
            if (w) w.style.display = 'none';
        }

        function scrollDown() {
            const chat = document.getElementById('chat');
            setTimeout(() => {
                chat.scrollTop = chat.scrollHeight;
            }, 100);
        }

        function newChat() {
            document.getElementById('messages').innerHTML = '';
            document.getElementById('welcome').style.display = 'block';
            document.getElementById('input').value = '';
        }
    </script>
</body>
</html>