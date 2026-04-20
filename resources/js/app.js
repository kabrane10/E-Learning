import './bootstrap';
import Sortable from 'sortablejs';
import Dropzone from 'dropzone';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Rendre Sortable disponible globalement
window.Sortable = Sortable;

// Configuration de Dropzone (désactiver l'auto-découverte)
Dropzone.autoDiscover = false;
window.Dropzone = Dropzone;

