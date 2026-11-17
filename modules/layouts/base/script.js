/* Sin zoom con los dedos en el móvil (iOS ignora user-scalable=no): se anulan los gestos de página. El zoom de la foto del lightbox usa sus propios eventos táctiles. */
['gesturestart', 'gesturechange', 'gestureend'].forEach(t => document.addEventListener(t, e => e.preventDefault(), { passive: false }));
