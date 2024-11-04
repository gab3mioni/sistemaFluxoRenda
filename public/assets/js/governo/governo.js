function showSection(sectionId, link) {
    // Oculta todas as seções
    document.querySelectorAll('.card').forEach(card => card.classList.add('hidden-section'));

    // Remove a classe 'active' de todos os links
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));

    // Exibe a seção correspondente
    document.getElementById(sectionId).classList.remove('hidden-section');
    link.classList.add('active');

    // Rola a página para o topo após um pequeno delay
    setTimeout(() => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 0);
}
