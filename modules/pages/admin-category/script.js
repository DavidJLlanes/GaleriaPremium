(() => {
  const a = document.querySelector('[data-cat-id]'); if (!a || !window.Admin) return;
  const id = a.dataset.catId, name = a.dataset.catName, n = a.dataset.catN;
  a.addEventListener('click', async (e) => {
    const b = e.target.closest('[data-cd]'); if (!b) return;
    try {
      if (b.dataset.cd === 'rename') {
        const v = prompt('Nuevo nombre de la categoría:', name); if (v === null || v.trim() === name) return;
        await Admin.post(`/admin/categoria/${id}/renombrar`, { name: v }); location.reload();
      } else if (b.dataset.cd === 'del') {
        if (!confirm(`¿Borrar la categoría «${name}»? Se quita de sus ${n} fotos, que se conservan.`)) return;
        await Admin.post(`/admin/categoria/${id}/borrar`); location.href = '/admin/categorias';
      } else if (b.dataset.cd === 'delall') {
        if (!confirm(`¿Borrar la categoría «${name}» Y sus ${n} fotos? Las fotos se eliminan del servidor y NO se puede deshacer.`)) return;
        if (prompt(`Para confirmar, escribe el nombre de la categoría: ${name}`) !== name) { Admin.toast('Nombre incorrecto: no se ha borrado nada', 'error'); return; }
        const r = await Admin.post(`/admin/categoria/${id}/borrar`, { with_photos: 1 }); Admin.toast(`${r.photos} fotos borradas`); setTimeout(() => location.href = '/admin/categorias', 700);
      }
    } catch (err) { Admin.toast(err.message, 'error'); }
  });
})();
