      // Function to send the post data to the PHP script
      function submitPost() {
        var postContent = $('#content').val();  // Get the value of the "content" textarea

        $.ajax({
            type: 'POST',
            url: 'PHP/post.php',
            data: {content: postContent }, // Include the content only
            success: function(response) {
                // You can also update the post-feed section with the new post if needed
                location.reload()
            },
            error: function(error) {
                // Handle errors, if any
                alert('Error: ' + error.responseText);
                location.reload()
            }
        });
        
    }

// JavaScript function to check if the user is logged in
function checkLoginStatus() {
    // Make an HTTP request to check login status
    fetch('PHP/check_login.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json(); // Parse the JSON response
        })
        .then(data => {
            if (data.logged_in) {
                // User is logged in, perform appropriate actions
                console.log("User Logged in");
                return true;
            } else {
                console.log("User not logged in");
                
                // Navigate to another page, but only if not already on the login page
                if (window.location.href.indexOf("login.html") === -1) {
                    window.location.href = "login.html?errorParam=3";
                    
                }
                return false;
            }
        })
        .catch(error => {
            console.error('Error checking login status:', error);
        });
}


function get_username() {
    // Fetch user information from the server-side script
    fetch('PHP/get_user_info.php') // Replace with the actual URL of your server-side script
        .then(response => response.json())
        .then(data => {
            if (data.username !== "Unknown") {
                // Update the username elements with the retrieved username
                const usernameElements = document.getElementsByName("username");
                for (const element of usernameElements) {
                    element.innerText = data.username;
                }
            } else {
                // Update the username elements with the retrieved username
                const usernameElements = document.getElementsByName("username");
                for (const element of usernameElements) {
                    element.innerText = "Error getting username"
                }
            }
        })
        .catch(error => {
            console.error('Error fetching user information:', error);
        });
}

function get_profile_picture(){
    // Fetch user information from the server-side script
    fetch('PHP/get_user_info.php') // Replace with the actual URL of your server-side script
        .then(response => response.json())
        .then(data => {
            if (data.username !== "Unknown") {
                const profilePictureElements = document.getElementsByName("profile_picture");
                for (const element of profilePictureElements) {
                    element.src = data.profile_picture; 
                }
            } else {
                const profilePictureElements = document.getElementsByName("profile_picture");
                for (const element of profilePictureElements) {
                    element.src = "Images/Person Icon.png"; 
                }
            }
        })
        .catch(error => {
            console.error('Error fetching user information:', error);
        });
}
             
function fetchData() {
    // This JavaScript code fetches user posts from the provided PHP script and displays them on page load
        // Make an AJAX request to fetch user posts from the PHP script
        $.ajax({
            url: "PHP/get_post.php", // Replace with the actual path to your PHP script
            method: "GET",
            success: function (response) {
                // Assuming the PHP script outputs HTML containing user posts
                // Display the HTML response in the #posts div
                document.getElementById("posts").innerHTML = response;
            },
            error: function (error) {
                console.error("Error fetching posts: " + error);
            }
        });
    }

function createNav() {
    var postDiv = document.createElement("a");
    postDiv.textContent = "Login";
    postDiv.href = "login.html";


    var postDiv9 = document.createElement("div");
postDiv9.className = "search-container";

var searchInput = document.createElement("input");
searchInput.type = "text";
searchInput.id = "search";
searchInput.placeholder = "Search users...";

var searchButton = document.createElement("button");
searchButton.id = "searchBtn";
searchButton.className = "search-button";
searchButton.innerHTML = "<i class='fa fa-search'></i>"; // You can use an icon for the button

postDiv9.appendChild(searchInput);
postDiv9.appendChild(searchButton);


document.getElementById("topnav").appendChild(postDiv9);

    var postDiv2 = document.createElement("a");
    postDiv2.textContent = "Your Page";
    postDiv2.href = "index.html";

    var postDiv3 = document.createElement("a");
    postDiv3.textContent = "Feed";
    postDiv3.href = "feed.html";

    var postDiv4 = document.createElement("img");
    postDiv4.className = "profile_pic_small"
    postDiv4.name = "profile_picture";
    // Add an event listener for the 'error' event
    postDiv4.onerror = function() {
    // This function will be called when the image fails to load
    // You can set an alternative image source here
    postDiv4.src = "Images/Person Icon.png";
  };
  
    // Set the initial src attribute (you can set this to an empty string or any other fallback image)
    postDiv4.src = "";  // You can set this to an empty string or another fallback image source

    var postDiv5 = document.createElement("p");
    postDiv5.name = "username";

    var postDiv6 = document.createElement("a");
    postDiv6.href = "javascript:void(0);";
    postDiv6.id = "expand";
    postDiv6.className = "icon"; // Use className instead of class
    postDiv6.onclick = expansion; // Assign the function directly

    var postDiv7 = document.createElement("p");

    postDiv6.innerHTML = "<i class='fa fa-bars'></i>";
    var postDiv7 = document.createElement("p");


    var postDiv8 = document.createElement("a");
    postDiv8.text = "Logout";
    postDiv8.href = "PHP/logout.php"
    
    document.getElementById("topnav").appendChild(postDiv7);
    document.getElementById("topnav").appendChild(postDiv6);
    document.getElementById("topnav").appendChild(postDiv8);
    document.getElementById("topnav").appendChild(postDiv2);
    document.getElementById("topnav").appendChild(postDiv3);
    
    document.getElementById("topnav").appendChild(postDiv4);
    document.getElementById("topnav").appendChild(postDiv5);
    document.getElementById("topnav").appendChild(postDiv);
    document.getElementById("topnav").appendChild(postDiv9);
    search();
    
}

function fetchRandomPosts() {
    // Replace 'random_posts.php' with the actual path to your PHP script
    fetch('PHP/get_all_posts.php')
        .then(response => response.text())
        .then(data => {
            // Assuming the PHP script returns HTML content with the shuffled posts
            document.querySelector('.posts').innerHTML = data;
        })
        .catch(error => {
            console.error('Error fetching posts:', error);
        });
}

function uploadProfilePicture() {
    // Get the form and the file input element
    const form = document.getElementById('upload-form');
    const fileInput = form.querySelector('input[type="file"]');
    
    // Check if a file was selected
    if (fileInput.files.length > 0) {
        // Create a FormData object to send the file to the server
        const formData = new FormData();
        formData.append('profile_picture', fileInput.files[0]);

        // Send a POST request to the server (upload.php in this case)
        fetch('PHP/upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Handle the response from the server here
            if (data.success) {
                alert('Profile picture uploaded successfully');
                // You can perform additional actions here, like updating the UI
            } else {
                alert('Error: ' + data.message);
                // Handle the error case, e.g., display an error message
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Handle network or other errors
        });
    } else {
        alert('Please select an image to upload.');
    }
}

function expansion() {
    var x = document.getElementById("topnav");
    if (x.className === "topnav") {
      x.className += " expanded";
    } else {
      x.className = "topnav";
    }
  }

function fetchUserPosts(userId) {
    // Make an AJAX request to fetch user posts using the provided user ID
    $.ajax({
        url: "PHP/get_post.php?user_id=" + userId,
        
        method: "GET",
        success: function (response) {
            // Assuming the PHP script outputs HTML containing user posts
            // Display the HTML response in the #posts div
            $("#posts").html(response);
        },
        error: function (error) {
            console.error("Error fetching posts: " + error);
        }
    });
}

function search() {
    // Add your JavaScript for handling the search functionality here
    document.getElementById("searchBtn").addEventListener("click", function() {
        var searchInput = document.getElementById("search").value;

        // Handle the AJAX response and display search results
        fetch('PHP/search.php?username=' + searchInput, {
            method: 'POST',
            body: new URLSearchParams({
                search: searchInput
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json()) // Parse JSON response
        .then(data => {
            // Check if there are search results
            if (data.users.length > 0) {
                // Store search results in localStorage to pass to the new page
                localStorage.setItem('searchResults', JSON.stringify(data.users));

                // Redirect to the search.html page
                window.location.href = 'search.html';
            } else {
                // Handle the case when there are no search results
                alert('No results found.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
}
// Function to check for URL parameters and display them as an error message
function checkAndDisplayError() {
    // Get the URL parameters
    var urlParams = new URLSearchParams(window.location.search);
    var message = urlParams.get('message');

    if (message) {
        // Create a paragraph element for the error message
        var errorDiv = document.createElement("div");
        errorDiv.className = "error-message";
        errorDiv.innerText = decodeURIComponent(message);

        // Append the error message to the document body
        document.body.appendChild(errorDiv);
    }
}

// Call the error handler function when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    createNav();
    checkLoginStatus();
    get_profile_picture();
    get_username();
    checkAndDisplayError();

});



