// Function to handle iframe load event and update localStorage
function iframeLoaded() {
    var iframe = document.getElementById('iframeContent');
    var currentURL = iframe.contentWindow.location.href;
    localStorage.setItem('lastVisitedURL', currentURL);
}

// Function to load a page into the iframe
function loadPage(url) {
    var iframe = document.getElementById('iframeContent');
    iframe.src = url;
}

// Function to load the last visited URL into the iframe on window load
function loadIframeContent() {
    var iframe = document.getElementById('iframeContent');
    var storedURL = localStorage.getItem('lastVisitedURL');
    if (storedURL) {
        iframe.src = storedURL;
    } else {
        iframe.src = '../views/dashboard.php';
    }
}
