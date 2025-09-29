document.addEventListener("DOMContentLoaded", () => {
    // Theme Toggle Functionality
    const themeToggleDarkIcon = document.getElementById(
        "theme-toggle-dark-icon"
    );
    const themeToggleLightIcon = document.getElementById(
        "theme-toggle-light-icon"
    );
    const themeToggleBtn = document.getElementById("theme-toggle");

    if (themeToggleBtn && themeToggleDarkIcon && themeToggleLightIcon) {
        // Check for saved theme preference or default to light mode
        if (
            localStorage.getItem("color-theme") === "dark" ||
            (!("color-theme" in localStorage) &&
                window.matchMedia("(prefers-color-scheme: dark)").matches)
        ) {
            document.documentElement.classList.add("dark");
            themeToggleLightIcon.classList.remove("hidden");
        } else {
            document.documentElement.classList.remove("dark");
            themeToggleDarkIcon.classList.remove("hidden");
        }

        themeToggleBtn.addEventListener("click", function () {
            // Toggle icons
            themeToggleDarkIcon.classList.toggle("hidden");
            themeToggleLightIcon.classList.toggle("hidden");

            // Toggle dark mode
            if (document.documentElement.classList.contains("dark")) {
                document.documentElement.classList.remove("dark");
                localStorage.setItem("color-theme", "light");
            } else {
                document.documentElement.classList.add("dark");
                localStorage.setItem("color-theme", "dark");
            }
        });
    }

    // Mobile sidebar toggle (hamburger menu)
    const mobileToggleBtn = document.querySelector(
        '[data-drawer-toggle="sidebar"]'
    );
    const sidebar = document.getElementById("sidebar");

    if (mobileToggleBtn && sidebar) {
        mobileToggleBtn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle("show");
            
            // Add overlay for mobile
            let overlay = document.getElementById("sidebar-overlay");
            if (!overlay) {
                overlay = document.createElement("div");
                overlay.id = "sidebar-overlay";
                overlay.className = "fixed inset-0 bg-black bg-opacity-50 z-30 sm:hidden";
                overlay.addEventListener("click", () => {
                    sidebar.classList.remove("show");
                    overlay.remove();
                });
                document.body.appendChild(overlay);
            } else {
                overlay.remove();
            }
        });
    }

    // Initialize expand button state on page load
    const expandBtn = document.getElementById("expand-sidebar-btn");
    if (sidebar && expandBtn) {
        if (sidebar.classList.contains("hidden")) {
            expandBtn.classList.remove("hidden");
            expandBtn.style.display = "flex";
        } else {
            expandBtn.classList.add("hidden");
            expandBtn.style.display = "none";
        }
    }
});

// Sidebar toggle function
function toggleSidebar() {
    console.log("Toggle sidebar clicked");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    const expandBtn = document.getElementById("expand-sidebar-btn");

    if (sidebar) {
        sidebar.classList.toggle("hidden");
        console.log("Sidebar hidden:", sidebar.classList.contains("hidden"));

        // Show/hide expand button based on sidebar state
        if (expandBtn) {
            if (sidebar.classList.contains("hidden")) {
                expandBtn.classList.remove("hidden");
                expandBtn.style.display = "flex";
                console.log("Expand button shown");
            } else {
                expandBtn.classList.add("hidden");
                expandBtn.style.display = "none";
                console.log("Expand button hidden");
            }
        }

        if (mainContent) {
            if (sidebar.classList.contains("hidden")) {
                mainContent.classList.remove("sm:ml-64");
                mainContent.classList.add("sm:ml-0");
            } else {
                mainContent.classList.remove("sm:ml-0");
                mainContent.classList.add("sm:ml-64");
            }
        }
    }
}

// Dropdown toggle function
function toggleDropdown(dropdownId) {
    console.log("Toggle dropdown clicked:", dropdownId);
    const dropdown = document.getElementById(dropdownId);
    const arrow = document.getElementById("arrow-" + dropdownId);

    if (dropdown) {
        // Check if this dropdown should stay open (has active routes)
        const shouldStayOpen = dropdown.classList.contains("keep-open");

        // Close all other dropdowns and reset their arrows, except those that should stay open
        document.querySelectorAll('[id^="dropdown-"]').forEach((d) => {
            if (d !== dropdown && !d.classList.contains("keep-open")) {
                d.classList.remove("show");
                d.classList.add("hidden");
                // Reset arrow for this specific dropdown
                const otherArrow = document.getElementById("arrow-" + d.id);
                if (otherArrow) {
                    otherArrow.classList.remove("rotate-180");
                }
            }
        });

        // Only toggle if this dropdown shouldn't stay open
        if (!shouldStayOpen) {
            const isCurrentlyHidden = dropdown.classList.contains("hidden");
            if (isCurrentlyHidden) {
                dropdown.classList.remove("hidden");
                dropdown.classList.add("show");
            } else {
                dropdown.classList.remove("show");
                dropdown.classList.add("hidden");
            }
            console.log("Dropdown show:", dropdown.classList.contains("show"));
            console.log(
                "Dropdown hidden:",
                dropdown.classList.contains("hidden")
            );

            // Rotate arrow if exists (only if we're opening the dropdown)
            if (arrow) {
                if (isCurrentlyHidden) {
                    arrow.classList.add("rotate-180");
                } else {
                    arrow.classList.remove("rotate-180");
                }
            }
        }
    }
}

// Close dropdowns when clicking outside
document.addEventListener("click", function (e) {
    if (
        !e.target.closest("button[onclick]") &&
        !e.target.closest('[id^="dropdown-"]')
    ) {
        document.querySelectorAll('[id^="dropdown-"]').forEach((dropdown) => {
            // Don't close dropdowns that should stay open
            if (!dropdown.classList.contains("keep-open")) {
                dropdown.classList.remove("show");
                dropdown.classList.add("hidden");
            }
        });
        document
            .querySelectorAll('[id^="arrow-dropdown-"]')
            .forEach((arrow) => {
                // Don't reset arrows for dropdowns that should stay open
                const dropdownId = arrow.id.replace("arrow-", "");
                const dropdown = document.getElementById(dropdownId);
                if (!dropdown || !dropdown.classList.contains("keep-open")) {
                    arrow.classList.remove("rotate-180");
                }
            });
    }
});

// Initialize active states for dropdowns
document.addEventListener("DOMContentLoaded", function () {
    const currentPath = window.location.pathname;

    // Handle Products dropdown
    const productsDropdown = document.getElementById("products-dropdown");
    const productsArrow = document.getElementById("arrow-products-dropdown");
    const productsButton = document.getElementById("products-button");

    if (productsDropdown) {
        // Check if current path is relevant to products dropdown
        const isRelevantPath = currentPath.includes("/admin/products") || 
                              currentPath.includes("/admin/brands") || 
                              currentPath.includes("/admin/attributes") || 
                              currentPath.includes("/admin/categories") ||
                              currentPath.includes("/admin/attribute-values");
        
        if (isRelevantPath) {
            productsDropdown.classList.remove("hidden");
            productsDropdown.classList.add("show");
            productsDropdown.classList.add("keep-open");

            if (productsArrow) {
                productsArrow.classList.add("rotate-180");
            }
        } else {
            productsDropdown.classList.add("hidden");
            productsDropdown.classList.remove("show");
            productsDropdown.classList.remove("keep-open");

            if (productsArrow) {
                productsArrow.classList.remove("rotate-180");
            }
        }
    }

    // Handle Marketing dropdown
    const marketingDropdown = document.getElementById("dropdown-marketing");
    const marketingArrow = document.getElementById("arrow-dropdown-marketing");
    const marketingButton = document.getElementById("marketing-button");

    if (marketingDropdown) {
        // Check if current path is relevant to marketing dropdown
        const isMarketingRelevantPath = currentPath.includes("/admin/coupons") || 
                                      currentPath.includes("/admin/flash-sales") || 
                                      currentPath.includes("/admin/campaigns") || 
                                      currentPath.includes("/admin/promotions") || 
                                      currentPath.includes("/admin/discounts");
        
        if (isMarketingRelevantPath) {
            marketingDropdown.classList.remove("hidden");
            marketingDropdown.classList.add("show");
            marketingDropdown.classList.add("keep-open");

            if (marketingArrow) {
                marketingArrow.classList.add("rotate-180");
            }
        } else {
            marketingDropdown.classList.add("hidden");
            marketingDropdown.classList.remove("show");
            marketingDropdown.classList.remove("keep-open");

            if (marketingArrow) {
                marketingArrow.classList.remove("rotate-180");
            }
        }
    }
});
