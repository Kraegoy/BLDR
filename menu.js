function toggleSubMenu(event) {
    event.preventDefault(); // prevent the link from navigating to a new page
    
    var subMenu = event.currentTarget.nextElementSibling;
    
    // check if the sub-menu is currently visible
    if (subMenu.style.display === "block") {
      // close the sub-menu only if the category link is clicked
      if (event.target.classList.contains("category-link")) {
        subMenu.style.display = "none";
      }
    } else {
      subMenu.style.display = "block";
    }
  }

  // Get all the category links
const categoryLinks = document.querySelectorAll('.category-link');

// Add click event listener to each category link
categoryLinks.forEach((link) => {
  link.addEventListener('click', (event) => {
    event.preventDefault();

    // Get the sub-menu for this category
    const subMenu = link.nextElementSibling;

    // Close all the sub-menus except for this category's sub-menu
    const allSubMenus = document.querySelectorAll('.sub-menu');
    allSubMenus.forEach((menu) => {
      if (menu !== subMenu) {
        menu.classList.remove('open');
      }
    });

    // Toggle the open class for this category's sub-menu
    subMenu.classList.toggle('open');
  });
});

// Add click event listener to the document to close any open sub-menu when clicked outside
document.addEventListener('click', (event) => {
  const target = event.target;
  const isCategoryLink = target.classList.contains('category-link');
  const isSubMenu = target.classList.contains('sub-menu');

  if (!isCategoryLink && !isSubMenu) {
    const openSubMenu = document.querySelector('.sub-menu.open');
    if (openSubMenu) {
      openSubMenu.classList.remove('open');
    }
  }
});
