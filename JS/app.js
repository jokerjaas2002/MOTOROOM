// Funcionalidades globales
document.addEventListener('DOMContentLoaded', function() {
    // Menú móvil
    const menuToggle = document.createElement('div');
    menuToggle.className = 'mobile-menu-toggle';
    menuToggle.innerHTML = '☰';
    document.querySelector('.main-header').appendChild(menuToggle);
    
    menuToggle.addEventListener('click', function() {
        document.querySelector('.main-nav').classList.toggle('active');
    });
    
    // Animación de carga
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
    });
});