# Plan: Implement AJAX Navigation to Prevent Sidebar Reload

## Problem

When clicking on a section in the sidebar, the entire page reloads, including the sidebar. This causes the sidebar to "reload" and potentially show animation. The user wants only the content area to reload, not the sidebar.

## Solution

Implement AJAX navigation to load content without reloading the entire page.

## Implementation Steps

### 1. Add AJAX Navigation JavaScript

- Add JavaScript code to intercept link clicks in the sidebar
- Load content via AJAX using `fetch()` API
- Replace only the content area (`.main-content`) with the new content
- Update the URL using History API (`pushState`)
- Update the active state in the sidebar

### 2. Modify Content Area

- Add an ID to the main content area for easy targeting (e.g., `id="main-content"`)
- Ensure the content area is properly structured for AJAX updates

### 3. Handle Browser Navigation

- Add `popstate` event listener to handle browser back/forward buttons
- Load the appropriate content when navigating via browser history

### 4. Update Active State

- Update the active class on sidebar links when content is loaded
- Highlight the current section in the sidebar

### 5. Handle Page Load

- On initial page load, ensure the correct content is displayed
- Set the active state based on the current URL

## Benefits

- Sidebar does not reload when navigating between sections
- Faster navigation (no full page reload)
- Smoother user experience
- Sidebar state is preserved (collapsed/expanded)

## Files to Modify

- `resources/views/layouts/app.blade.php` - Add AJAX navigation JavaScript

## Code Structure

```javascript
// Intercept sidebar link clicks
document.querySelectorAll(".sidebar .nav-link").forEach((link) => {
    link.addEventListener("click", function (e) {
        e.preventDefault();
        const url = this.href;

        // Load content via AJAX
        fetch(url)
            .then((response) => response.text())
            .then((html) => {
                // Parse the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, "text/html");

                // Extract the main content
                const newContent = doc.querySelector(".main-content").innerHTML;

                // Replace the content
                document.querySelector(".main-content").innerHTML = newContent;

                // Update the URL
                history.pushState({}, "", url);

                // Update active state
                updateActiveState(url);
            });
    });
});

// Handle browser back/forward
window.addEventListener("popstate", function () {
    loadContent(window.location.href);
});

// Update active state
function updateActiveState(url) {
    document.querySelectorAll(".sidebar .nav-link").forEach((link) => {
        link.classList.remove("active");
        if (link.href === url) {
            link.classList.add("active");
        }
    });
}
```

## Testing

- Test navigation between sections
- Test browser back/forward buttons
- Test sidebar state preservation
- Test active state updates
