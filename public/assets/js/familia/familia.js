function showSection(sectionId, navLink) {
    // Oculta todas as seções
    const sections = document.querySelectorAll('.hidden-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
    });
    document.getElementById(sectionId).style.display = 'block';
    navLink.classList.add('active');
}
document.addEventListener("DOMContentLoaded", function() {
    showSection('renda', document.querySelector('.nav-link.active'));
});