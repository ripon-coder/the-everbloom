document.addEventListener("DOMContentLoaded", () => {
    // Restore sidebar state from local storage on load (cleanup any inconsistencies)
    const isCollapsed = localStorage.getItem("sidebar-collapsed") === "true";
    if (isCollapsed) {
        document.documentElement.classList.add("sidebar-collapsed");
    } else {
        document.documentElement.classList.remove("sidebar-collapsed");
    }

    // Mobile sidebar toggle
    const mobileToggleBtn = document.querySelector('[data-drawer-toggle="sidebar"]');
    const sidebar = document.getElementById("sidebar");

    if (mobileToggleBtn && sidebar) {
        mobileToggleBtn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle("-translate-x-full");
            sidebar.classList.toggle("show");
            
            let overlay = document.getElementById("sidebar-overlay");
            if (!overlay) {
                overlay = document.createElement("div");
                overlay.id = "sidebar-overlay";
                overlay.className = "fixed inset-0 bg-black/50 z-30 sm:hidden transition-opacity duration-300";
                overlay.addEventListener("click", () => {
                    sidebar.classList.add("-translate-x-full");
                    sidebar.classList.remove("show");
                    overlay.remove();
                });
                document.body.appendChild(overlay);
            } else {
                overlay.remove();
            }
        });
    }
});

// Sidebar toggle function
function toggleSidebar() {
    const isCollapsed = document.documentElement.classList.toggle("sidebar-collapsed");
    localStorage.setItem("sidebar-collapsed", isCollapsed);
    
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    const expandBtn = document.getElementById("expand-sidebar-btn");

    if (sidebar) {
        if (isCollapsed) {
            sidebar.classList.add("hidden");
        } else {
            sidebar.classList.remove("hidden");
        }
    }

    if (mainContent) {
        if (isCollapsed) {
            mainContent.classList.remove("sm:ml-64");
            mainContent.classList.add("sm:ml-0");
        } else {
            mainContent.classList.remove("sm:ml-0");
            mainContent.classList.add("sm:ml-64");
        }
    }

    if (expandBtn) {
        if (isCollapsed) {
            expandBtn.classList.remove("hidden");
        } else {
            expandBtn.classList.add("hidden");
        }
    }
}
