let currentPromptData = null;

// Function to show the popup
function showPopup() {
    document.getElementById('duplicateKeyPopup').style.display = 'flex';
}

// Function to hide the popup
function hidePopup() {
    document.getElementById('duplicateKeyPopup').style.display = 'none';
}

// Function to parse the input text
function parseInput(text) {
    // Check if the text contains ':::'
    if (text.includes(':::')) {
        const [prompt, ...answerParts] = text.split(':::');
        return {
            prompt: prompt.trim(),
            answer: answerParts.join(':::').trim()
        };
    }

    // Check for regular colon
    if (text.includes(':')) {
        const [prompt, ...answerParts] = text.split(':');
        return {
            prompt: prompt.trim(),
            answer: answerParts.join(':').trim()
        };
    }

    return null;
}

// Function to handle duplicate key decision
async function handleDuplicateKey(action) {
    hidePopup();

    if (action === 'cancel') {
        return;
    }

    if (currentPromptData) {
        try {
            const response = await fetch("save.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    ...currentPromptData,
                    action: action
                }),
            });
            const result = await response.json();
            document.getElementById("response-box").innerText = result.message;
        } catch (error) {
            document.getElementById("response-box").innerText = "Error saving the data. Please try again.";
            console.error("Error:", error);
        }
    }
    currentPromptData = null;
}

// Function to save the prompt and answer
async function handleSave() {
    const inputText = document.getElementById("prompt").value.trim();
    const parsedInput = parseInput(inputText);

    if (!parsedInput) {
        alert("Please enter a valid prompt and answer using either ':' or ':::' as separator.");
        return;
    }

    const { prompt, answer } = parsedInput;

    if (!prompt || !answer) {
        alert("Please enter both a prompt and an answer.");
        return;
    }

    // Store the current prompt data
    currentPromptData = { prompt, answer };

    try {
        const checkResponse = await fetch("check_key.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ prompt }),
        });
        const checkResult = await checkResponse.json();

        if (checkResult.exists) {
            showPopup();
        } else {
            // If key doesn't exist, save directly
            const response = await fetch("save.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(currentPromptData),
            });
            const result = await response.json();
            document.getElementById("response-box").innerText = result.message;
            currentPromptData = null;
        }
    } catch (error) {
        document.getElementById("response-box").innerText = "Error saving the data. Please try again.";
        console.error("Error:", error);
        currentPromptData = null;
    }
}

// Function to get a reply for the prompt
async function handleReply() {
    const prompt = document.getElementById("prompt").value.trim();
    if (!prompt) {
        alert("Please enter a valid prompt.");
        return;
    }
    try {
        const response = await fetch("reply.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ prompt }),
        });
        const data = await response.json();
        document.getElementById("response-box").innerText = data.message;
    } catch (error) {
        document.getElementById("response-box").innerText = "Error retrieving the reply. Please try again.";
        console.error("Error:", error);
    }
}

// Function to handle find operation
async function handleFind() {
    const prompt = document.getElementById("prompt").value.trim();
    if (!prompt) {
        alert("Please enter search terms.");
        return;
    }
    try {
        const response = await fetch("find.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ prompt }),
        });
        const data = await response.json();

        // Format the results
        if (data.results && data.results.length > 0) {
            const formattedResults = data.results
                .map(result => `Prompt: ${result.prompt}\nAnswer: ${result.answer}`)
                .join('\n\n');
            document.getElementById("response-box").innerText = formattedResults;
        } else {
            document.getElementById("response-box").innerText = "No matching entries found.";
        }
    } catch (error) {
        document.getElementById("response-box").innerText = "Error searching the data. Please try again.";
        console.error("Error:", error);
    }
}



// ... (keep all existing functions) ...

// Add this new function for AI handling
async function handleAI() {
    const prompt = document.getElementById("prompt").value.trim();
    if (!prompt) {
        alert("Please enter a question for the AI.");
        return;
    }

    // Show loading message
    document.getElementById("response-box").innerText = "Thinking...";

    try {
        const response = await fetch("ai.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ prompt }),
        });
        const data = await response.json();

        if (data.error) {
            document.getElementById("response-box").innerText = "Error: " + data.error;
        } else {
            document.getElementById("response-box").innerText = data.response;
        }
    } catch (error) {
        document.getElementById("response-box").innerText = "Error connecting to AI service. Please try again.";
        console.error("Error:", error);
    }
}
