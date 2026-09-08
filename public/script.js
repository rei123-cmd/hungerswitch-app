document.addEventListener("DOMContentLoaded", function() {

    let scrollTopBtn = document.getElementById("scrollTopBtn");

    if (scrollTopBtn) {
        
        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
                scrollTopBtn.style.display = "block";
            } else {
                scrollTopBtn.style.display = "none";
            }
        }

        scrollTopBtn.onclick = function(e) {
            e.preventDefault(); 
            document.body.scrollTop = 0; 
            document.documentElement.scrollTop = 0; 
        };
    }

});