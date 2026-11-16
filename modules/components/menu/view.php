<?php /* Botón del menú de galerías (en la barra superior). Su panel lo pinta components/menu-panel en el layout. Datos: $m = SiteMenu::values(). */
use App\Core\Module; ?>
<button type="button" class="mnb" id="mnBtn" aria-expanded="false" aria-controls="mnPanel" aria-label="Galerías"><?= Module::render('menu-icons/' . $m['icon'], ['opts' => $m['opts']['icon']]) ?></button>
