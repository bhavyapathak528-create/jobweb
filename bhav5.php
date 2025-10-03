<?php
session_start();

// --- 1. MOCK DATA SIMULATION (Database) ---

$companies = [
    "Tata Consultancy Services (TCS)", "Infosys", "Reliance Industries", 
    "HDFC Bank", "HCLTech", "Wipro", "Amazon India", "Flipkart"
];

$locations = [
    "Bangalore, Karnataka", "Mumbai, Maharashtra", "Hyderabad, Telangana", 
    "Pune, Maharashtra", "Chennai, Tamil Nadu", "Delhi NCR (Gurgaon)"
];

$mock_users = [
    'user@example.com' => ['password' => 'pass123', 'name' => 'Priya Sharma']
];

$jobs = [
    ['id' => 1, 'title' => "Senior Software Engineer (PHP/Laravel)", 'company' => $companies[0], 'location' => $locations[0], 'salary' => "?18-25 LPA", 'type' => "Full-Time", 'exp' => "4+ years"],
    ['id' => 2, 'title' => "Data Scientist - AI/ML", 'company' => $companies[1], 'location' => $locations[3], 'salary' => "?22-30 LPA", 'type' => "Full-Time", 'exp' => "5+ years"],
    ['id' => 3, 'title' => "Financial Analyst - Investment Banking", 'company' => $companies[3], 'location' => $locations[1], 'salary' => "?12-16 LPA", 'type' => "Full-Time", 'exp' => "2+ years"],
    ['id' => 4, 'title' => "Cloud Engineer (AWS/Azure)", 'company' => $companies[4], 'location' => $locations[2], 'salary' => "?15-20 LPA", 'type' => "Contract", 'exp' => "3+ years"],
    ['id' => 5, 'title' => "Marketing Manager - Digital", 'company' => $companies[2], 'location' => $locations[5], 'salary' => "?10-14 LPA", 'type' => "Full-Time", 'exp' => "3+ years"],
    ['id' => 6, 'title' => "HR Business Partner", 'company' => $companies[5], 'location' => $locations[4], 'salary' => "?8-12 LPA", 'type' => "Full-Time", 'exp' => "6+ years"],
    ['id' => 7, 'title' => "UX Designer", 'company' => $companies[6], 'location' => $locations[0], 'salary' => "?14-20 LPA", 'type' => "Full-Time", 'exp' => "3+ years"],
];

$is_logged_in = isset($_SESSION['user']);
$user_name = $is_logged_in ? explode(' ', $_SESSION['user']['name'])[0] : '';
$user_full_name = $is_logged_in ? $_SESSION['user']['name'] : '';
$user_email = $is_logged_in ? key($mock_users) : '';
$error_message = '';
$success_message = '';

// --- 2. AUTHENTICATION & FORM HANDLING (Backend Logic) ---

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (isset($mock_users[$email]) && $mock_users[$email]['password'] === $password) {
            $_SESSION['user'] = $mock_users[$email];
            $is_logged_in = true;
            $success_message = "Welcome back, " . $_SESSION['user']['name'] . "!";
            // Redirect to remove POST data from URL
            header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
            exit();
        } else {
            $error_message = "Invalid email or password. Use 'user@example.com' and 'pass123'.";
        }
    } elseif ($action === 'logout') {
        session_destroy();
        $is_logged_in = false;
        $success_message = "You have been logged out successfully.";
        // Redirect to remove POST data from URL
        header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
        exit();
    }
}

// --- 3. JOB SEARCH & FILTERING LOGIC ---

$search_term = $_GET['search'] ?? '';
$location_filter = $_GET['location'] ?? 'All';

$filtered_jobs = array_filter($jobs, function($job) use ($search_term, $location_filter) {
    // Location Filter
    if ($location_filter !== 'All' && $job['location'] !== $location_filter) {
        return false;
    }

    // Search Term Filter (case-insensitive search across title, company, location)
    if ($search_term) {
        $search = strtolower($search_term);
        if (
            strpos(strtolower($job['title']), $search) === false &&
            strpos(strtolower($job['company']), $search) === false &&
            strpos(strtolower($job['location']), $search) === false
        ) {
            return false;
        }
    }

    return true;
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GetJobsNow | India's Leading Job Portal</title>
    <!-- Load Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Use Inter font -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        /* Simple animation for message box */
        @keyframes fade-in-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navbar -->
    <header class="bg-white shadow-lg sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <h1 class="text-3xl font-extrabold text-blue-600">
                GetJobs<span class="text-gray-800">Now</span>
            </h1>

            <div>
                <?php if ($is_logged_in): ?>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm font-medium text-gray-700 hidden sm:inline">
                            Welcome, <?php echo htmlspecialchars($user_name); ?>!
                        </span>
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="flex items-center bg-red-500 hover:bg-red-600 text-white text-sm font-semibold py-2 px-4 rounded-full transition shadow-md">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                Logout
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="?page=login" class="flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded-full transition shadow-md shadow-blue-500/50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Login / Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Custom JavaScript Message Container (Replaces alert()) -->
        <div id="js-message-container" class="fixed top-0 left-0 right-0 z-50 p-4 max-w-lg mx-auto pointer-events-none">
            <!-- Messages will be injected here -->
        </div>

        <!-- System Messages -->
        <?php if ($error_message): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($error_message); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($success_message); ?></span>
            </div>
        <?php endif; ?>


        <?php 
        $page = $_GET['page'] ?? 'jobs';
        
        // --- 4. LOGIN PAGE VIEW ---
        if ($page === 'login' && !$is_logged_in): 
        ?>
            <div class="flex justify-center items-center py-16">
                <div class="bg-white p-8 rounded-xl shadow-2xl w-full max-w-md border-t-4 border-blue-600">
                    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Candidate Login</h2>
                    <p class="text-center text-sm text-gray-500 mb-6">Use mock credentials: **user@example.com** / **pass123**</p>
                    <form method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" value="user@example.com" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" id="password" name="password" value="pass123" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-blue-500/50">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            Log In
                        </button>
                    </form>
                </div>
            </div>

        <?php 
        // --- 5. JOB LISTING PAGE VIEW ---
        else: 
        ?>
            <!-- Hero Section -->
            <section class="text-center mb-12 py-12 bg-blue-50 rounded-2xl border-b-4 border-blue-600">
                <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-4">
                    Find Your Next Indian Job with GetJobsNow
                </h2>
                <p class="text-xl text-gray-600">
                    Connecting talent with top companies across all Indian states.
                </p>
            </section>

            <!-- Search and Filter Form -->
            <div class="bg-white p-6 rounded-xl shadow-xl mb-8 border border-gray-200">
                <form method="GET" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="flex flex-col md:flex-row gap-4">
                    <div class="relative w-full md:w-2/3">
                        <svg class="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input
                            type="text"
                            name="search"
                            placeholder="Search by Title, Company (e.g., Infosys), or Role..."
                            value="<?php echo htmlspecialchars($search_term); ?>"
                            class="w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                    </div>

                    <div class="w-full md:w-1/3 flex space-x-2">
                        <select
                            name="location"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="All">All Locations</option>
                            <?php foreach ($locations as $loc): ?>
                                <option 
                                    value="<?php echo htmlspecialchars($loc); ?>" 
                                    <?php echo $location_filter === $loc ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($loc); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition shrink-0">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Job Listings -->
            <section>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">
                    <?php echo count($filtered_jobs); ?> Jobs Found
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php if (count($filtered_jobs) > 0): ?>
                        <?php foreach ($filtered_jobs as $job): ?>
                            <!-- Job Card -->
                            <div id="job-card-<?php echo $job['id']; ?>" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border border-gray-200 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <p class="text-blue-600 font-medium mb-3"><?php echo htmlspecialchars($job['company']); ?></p>
                                    
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243m.707-10.707l4.243-4.243a1.998 1.998 0 012.828 0l4.243 4.243m-4.5 4.5l4.5 4.5"></path></svg>
                                        <span><?php echo htmlspecialchars($job['location']); ?></span>
                                    </div>
                                    
                                    <div class="flex items-center text-sm text-gray-600 mb-4">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15.375a23.93 23.93 0 01-9-2.125m17.5 7.915A25.86 25.86 0 0112 21.625c-3.141 0-6.173-.846-8.75-2.205M15.5 12a3.5 3.5 0 11-7 0 3.5 3.5 0 017 0z"></path></svg>
                                        <span><?php echo htmlspecialchars($job['exp']); ?> | <?php echo htmlspecialchars($job['type']); ?></span>
                                    </div>
                                    
                                    <p class="text-2xl font-extrabold text-green-600 mb-4"><?php echo htmlspecialchars($job['salary']); ?></p>
                                </div>

                                <!-- Application Form (Initially hidden) -->
                                <div id="apply-form-<?php echo $job['id']; ?>" class="mt-4 pt-4 border-t border-gray-100 hidden">
                                    <h4 class="text-lg font-bold text-blue-600 mb-3">Apply for this Job</h4>
                                    <form id="form-<?php echo $job['id']; ?>" onsubmit="submitApplication(event, <?php echo $job['id']; ?>, '<?php echo htmlspecialchars($job['title']); ?>'); return false;">
                                        
                                        <div class="mb-2">
                                            <label class="block text-xs font-medium text-gray-700">Full Name</label>
                                            <input type="text" name="name" required class="w-full p-2 border border-gray-300 rounded-md text-sm" value="<?php echo htmlspecialchars($user_full_name); ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label class="block text-xs font-medium text-gray-700">Email</label>
                                            <input type="email" name="email" required class="w-full p-2 border border-gray-300 rounded-md text-sm" value="<?php echo htmlspecialchars($user_email); ?>">
                                        </div>
                                        <div class="mb-2">
                                            <label class="block text-xs font-medium text-gray-700">Highest Qualification</label>
                                            <input type="text" name="qualification" placeholder="e.g., B.Tech CSE" required class="w-full p-2 border border-gray-300 rounded-md text-sm">
                                        </div>
                                        <div class="mb-4">
                                            <label class="block text-xs font-medium text-gray-700">Upload Resume (PDF/DOC)</label>
                                            <!-- File upload simulation -->
                                            <input type="file" name="resume" accept=".pdf,.doc,.docx" required class="w-full text-sm border border-gray-300 rounded-md p-2 file:mr-4 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        </div>
                                        
                                        <button type="submit" class="w-full font-semibold py-2 rounded-lg transition bg-green-500 hover:bg-green-600 text-white shadow-md">
                                            Submit Application
                                        </button>
                                        <button type="button" onclick="document.getElementById('apply-form-<?php echo $job['id']; ?>').classList.add('hidden');" class="w-full mt-2 font-semibold py-2 rounded-lg transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                                            Cancel
                                        </button>
                                    </form>
                                </div>
                                
                                <button 
                                    id="apply-btn-<?php echo $job['id']; ?>"
                                    onclick="
                                        <?php if ($is_logged_in): ?>
                                            // Toggle the visibility of the application form
                                            document.getElementById('apply-form-<?php echo $job['id']; ?>').classList.toggle('hidden');
                                        <?php else: ?>
                                            // Redirect to login page
                                            window.location.href = '?page=login';
                                        <?php endif; ?>
                                    "
                                    class="w-full font-semibold py-3 rounded-lg transition 
                                        <?php echo $is_logged_in ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/50' : 'bg-gray-400 text-gray-700'; ?>"
                                >
                                    <?php echo $is_logged_in ? 'Apply Now' : 'Login to Apply'; ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-10 bg-white rounded-xl shadow-inner">
                            <p class="text-xl text-gray-600">
                                Sorry, no jobs match your criteria. Try clearing the search and filters.
                            </p>
                            <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="mt-4 text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center">
                                Clear Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 mt-12 py-6">
        <p class="text-center text-gray-400 text-sm">
            &copy; 2024 GetJobsNow | PHP Backend & Simulated Database Demo
        </p>
    </footer>

    <script>
        // Function to display a custom notification message (replacing alert())
        function displayMessage(type, message) {
            const container = document.getElementById('js-message-container');
            if (!container) return;

            // Use 'pointer-events-auto' so the message can be seen and clicked, but only temporarily
            container.classList.remove('pointer-events-none');

            const baseClass = 'px-4 py-3 rounded-xl relative mb-2 shadow-lg animate-fade-in-down';
            let colorClass = '';

            if (type === 'success') {
                colorClass = 'bg-green-100 border border-green-400 text-green-700';
            } else if (type === 'error') {
                colorClass = 'bg-red-100 border border-red-400 text-red-700';
            }

            const messageHtml = `
                <div class="${baseClass} ${colorClass}" role="alert">
                    <strong class="font-bold">${type.charAt(0).toUpperCase() + type.slice(1)}!</strong>
                    <span class="block sm:inline">${message}</span>
                </div>
            `;

            container.innerHTML = messageHtml;

            // Automatically hide the message after 5 seconds
            setTimeout(() => {
                container.innerHTML = '';
                container.classList.add('pointer-events-none');
            }, 5000);
        }

        // Function to handle simulated form submission
        function submitApplication(event, jobId, jobTitle) {
            event.preventDefault(); // Stop default form submission

            const form = document.getElementById('form-' + jobId);
            const name = form.elements['name'].value;
            const qualification = form.elements['qualification'].value;
            const email = form.elements['email'].value;
            const resumeFile = form.elements['resume'].files[0];
            
            // Simple form validation check
            if (!name || !qualification || !email || !resumeFile) {
                displayMessage('error', 'Please fill in all fields and upload a resume to apply.');
                return;
            }

            // Simulate submission success
            console.log(`Simulating application for Job #${jobId} (${jobTitle}):`);
            console.log(`\tName: ${name}`);
            console.log(`\tEmail: ${email}`);
            console.log(`\tQualification: ${qualification}`);
            console.log(`\tResume: ${resumeFile.name} (File size: ${resumeFile.size} bytes)`);

            // Display success message
            displayMessage('success', `Application for '${jobTitle}' submitted successfully, ${name}! We will contact you at ${email}.`);

            // Hide the form and update the button
            document.getElementById('apply-form-' + jobId).classList.add('hidden');
            
            const applyBtn = document.getElementById('apply-btn-' + jobId);
            applyBtn.innerText = 'Applied! ??'; 
            applyBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'shadow-blue-500/50');
            applyBtn.classList.add('bg-green-500', 'opacity-80', 'cursor-default');
            applyBtn.onclick = null; // Prevent re-opening the form
        }
    </script>

</body>
</html>


