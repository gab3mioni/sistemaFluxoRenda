
function showSection(sectionId) {
    document.querySelectorAll('.card').forEach(card => card.classList.add('hidden-section'));
    document.getElementById(sectionId).classList.remove('hidden-section');
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    document.querySelector(`.nav-link[href="#${sectionId}"]`).classList.add('active');
}
