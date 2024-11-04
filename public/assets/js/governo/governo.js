function showSection(sectionId, link) {
   
    document.querySelectorAll('.card').forEach(card => card.classList.add('hidden-section'));

    
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));

 
    document.getElementById(sectionId).classList.remove('hidden-section');
    link.classList.add('active');
}