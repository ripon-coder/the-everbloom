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
        '[data-drawer-toggle="sidebar-multi-level-sidebar"]'
    );
    const sidebar = document.getElementById("sidebar");

    if (mobileToggleBtn && sidebar) {
        mobileToggleBtn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();
            sidebar.classList.toggle("show");
        });
    }
});

// SideBar section

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
            dropdown.classList.remove("show");
            dropdown.classList.add("hidden");
        });
        document
            .querySelectorAll('[id^="arrow-dropdown-"]')
            .forEach((arrow) => {
                arrow.classList.remove("rotate-180");
            });
    }
});

// Initialize expand button state on page load
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
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

    // Check current URL and open products dropdown if on products or brands pages
    const currentPath = window.location.pathname;
    const productsDropdown = document.getElementById("products-dropdown");
    const productsArrow = document.getElementById("arrow-products-dropdown");
    const productsButton = document.getElementById("products-button");

    if (productsDropdown) {
        // Check if current path contains /admin/products or /admin/brands
        if (
            currentPath.includes("/admin/products") ||
            currentPath.includes("/admin/brands")
        ) {
            productsDropdown.classList.remove("hidden");
            productsDropdown.classList.add("show");
            productsDropdown.classList.add("keep-open");

            if (productsArrow) {
                productsArrow.classList.add("rotate-180");
            }

            // Add active styling to products button
            if (productsButton) {
                productsButton.classList.remove(
                    "text-gray-600",
                    "dark:text-gray-300",
                    "hover:bg-gray-100",
                    "dark:hover:bg-gray-800"
                );
                productsButton.classList.add(
                    "text-blue-600",
                    "bg-blue-50",
                    "dark:text-blue-400",
                    "dark:bg-blue-900/20"
                );
            }
        } else {
            productsDropdown.classList.add("hidden");
            productsDropdown.classList.remove("show");
            productsDropdown.classList.remove("keep-open");

            if (productsArrow) {
                productsArrow.classList.remove("rotate-180");
            }

            // Remove active styling from products button
            if (productsButton) {
                productsButton.classList.remove(
                    "text-blue-600",
                    "bg-blue-50",
                    "dark:text-blue-400",
                    "dark:bg-blue-900/20"
                );
                productsButton.classList.add(
                    "text-gray-600",
                    "dark:text-gray-300",
                    "hover:bg-gray-100",
                    "dark:hover:bg-gray-800"
                );
            }
        }
    }
});
