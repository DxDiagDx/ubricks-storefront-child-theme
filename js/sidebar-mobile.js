let handheld_sidebar_toggle = document.querySelector('.handheld-sidebar-toggle');
let secondary = document.querySelector('#secondary');
let back = document.querySelector('body');

let toggleMenu = function() {
    secondary.classList.toggle('active');
    back.classList.toggle('lock');
}

handheld_sidebar_toggle.addEventListener('click', function(e) {
    e.stopPropagation();
    toggleMenu();
});

document.addEventListener('click', function(e) {
    let target = e.target;
    let its_secondary = target == secondary || secondary.contains(target);
    let its_hst = target == handheld_sidebar_toggle;
    let secondary_is_active = secondary.classList.contains('active');
    
    if (!its_secondary && !its_hst && secondary_is_active) {
        toggleMenu();
    }
});