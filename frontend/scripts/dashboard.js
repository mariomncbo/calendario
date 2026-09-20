// ============================================================
// CALENDARIO · Dashboard (vista semanal tipo timeline)
// Recoge los eventos del backend (JSON) y construye la semana
// con bloques proporcionales a la duración de cada evento.
// ============================================================

// Nombres de los días de la semana empezando por el lunes
const NOMBRE_DIAS = ['LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB', 'DOM'];

// URL del endpoint que devuelve los datos de la semana en JSON
const URL_DATOS = '../backend/obtener_semana.php';

// Píxeles que ocupa una hora dentro de la línea de tiempo
const ALTURA_POR_HORA = 40;

// La franja horaria siempre termina a las 24:00
const HORA_FIN_RANGO = 24;

// En la primera carga del móvil el scroll se sitúa en el día de hoy
let desplazarse_a_hoy = true;

window.onload = function () {
  const rejilla_semana = document.getElementById('rejilla_semana');
  const titulo_semana = document.getElementById('titulo_semana');
  const boton_ajustes = document.getElementById('boton_ajustes');
  const boton_cerrar = document.getElementById('boton_cerrar');
  const fondo_modal = document.getElementById('fondo_modal');
  const ventana_modal = document.getElementById('ventana_modal');
  const enlace_semana_actual = document.getElementById('enlace_semana_actual');
  const enlace_siguiente_semana = document.getElementById('enlace_siguiente_semana');

  // --- Controles del modal de ajustes ---
  const interruptor_ocultar_tareas = document.getElementById('interruptor_ocultar_tareas');
  const interruptor_notificacion_diaria = document.getElementById('interruptor_notificacion_diaria');
  const boton_borrar_cuenta = document.getElementById('boton_borrar_cuenta');

  // --- Preferencia de tema (claro / oscuro / automático) ---
  const CLAVE_TEMA = 'tema';
  const opciones_modo = document.querySelectorAll('.opcion-modo');
  const tema_guardado = localStorage.getItem(CLAVE_TEMA) || 'automatico';

  /**
   * Resuelve la preferencia guardada a 'claro' u 'oscuro'.
   * El modo 'automatico' sigue la preferencia del sistema.
   *
   * @param {string} tema  Preferencia ('claro', 'oscuro' o 'automatico').
   * @return {string}      Tema real aplicable ('claro' o 'oscuro').
   */
  function resolver_tema(tema) {
    if (tema === 'claro' || tema === 'oscuro') {
      return tema;
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oscuro' : 'claro';
  }

  /**
   * Aplica el tema resolviendo la preferencia y marca la opción activa
   * en el selector del modal de ajustes.
   *
   * @param {string} tema  Preferencia a aplicar.
   */
  function aplicar_tema(tema) {
    document.documentElement.setAttribute('data-tema', resolver_tema(tema));

    opciones_modo.forEach((opcion) => {
      const es_activa = opcion.dataset.tema === tema;
      opcion.classList.toggle('opcion-modo--activa', es_activa);
      if (es_activa) {
        opcion.setAttribute('aria-checked', 'true');
      } else {
        opcion.removeAttribute('aria-checked');
      }
    });
  }

  // Aplicar el tema guardado al cargar la página
  aplicar_tema(tema_guardado);

  // Cambiar el tema al pulsar una opción del selector
  opciones_modo.forEach((opcion) => {
    opcion.addEventListener('click', function () {
      localStorage.setItem(CLAVE_TEMA, opcion.dataset.tema);
      aplicar_tema(opcion.dataset.tema);
    });
  });

  // Si el modo es automático, reaccionar a cambios del sistema mientras
  // la ventana esté abierta
  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function () {
    if (localStorage.getItem(CLAVE_TEMA) === 'automatico') {
      aplicar_tema('automatico');
    }
  });

  // Abrir el modal de ajustes desde el botón del encabezado
  boton_ajustes.addEventListener('click', function () {
    fondo_modal.classList.add('fondo-modal--visible');
  });

  // Cerrar el modal desde la X de la cabecera
  boton_cerrar.addEventListener('click', function () {
    fondo_modal.classList.remove('fondo-modal--visible');
  });

  // Cerrar el modal al pulsar fuera de la ventana (sobre el fondo)
  fondo_modal.addEventListener('click', function (evento) {
    if (evento.target === fondo_modal) {
      fondo_modal.classList.remove('fondo-modal--visible');
    }
  });

  // Cerrar el modal con la tecla Escape
  document.addEventListener('keydown', function (evento) {
    if (evento.key === 'Escape' && fondo_modal.classList.contains('fondo-modal--visible')) {
      fondo_modal.classList.remove('fondo-modal--visible');
    }
  });

  // --- Preferencia "Ocultar tareas" ---
  // Guarda la preferencia en localStorage y oculta la sección de tareas del
  // calendario con CSS, sin volver a pedir datos al backend.
  const CLAVE_OCULTAR_TAREAS = 'ocultar_tareas';

  function aplicar_ocultar_tareas(activo) {
    rejilla_semana.classList.toggle('rejilla-semana--sin-tareas', activo);
    interruptor_ocultar_tareas.setAttribute('aria-checked', activo ? 'true' : 'false');
  }

  aplicar_ocultar_tareas(localStorage.getItem(CLAVE_OCULTAR_TAREAS) === 'true');

  interruptor_ocultar_tareas.addEventListener('click', function () {
    const activo = localStorage.getItem(CLAVE_OCULTAR_TAREAS) !== 'true';
    localStorage.setItem(CLAVE_OCULTAR_TAREAS, activo ? 'true' : 'false');
    aplicar_ocultar_tareas(activo);
  });

  // --- Notificación diaria (solo visual; la lógica se añadirá más adelante) ---
  interruptor_notificacion_diaria.addEventListener('click', function () {
    const activo = interruptor_notificacion_diaria.getAttribute('aria-checked') === 'true';
    interruptor_notificacion_diaria.setAttribute('aria-checked', activo ? 'false' : 'true');
  });

  // --- Borrar cuenta: elimina los datos del usuario de la base de datos ---
  boton_borrar_cuenta.addEventListener('click', function () {
    const confirmacion = window.confirm(
      '¿Seguro que quieres borrar tu cuenta? Se eliminarán todos tus datos de la aplicación.'
    );
    if (!confirmacion) {
      return;
    }

    fetch('../backend/delete-account.php', { method: 'POST' })
      .then((respuesta) => {
        if (!respuesta.ok) {
          throw new Error('El servidor respondió con el estado ' + respuesta.status);
        }
        return respuesta.json();
      })
      .then((datos) => {
        if (!datos.success) {
          throw new Error('La API devolvió un error: ' + JSON.stringify(datos.errores));
        }
        // Volver al login público una vez borrados los datos
        window.location.href = 'index.php';
      })
      .catch((error) => {
        // Los errores solo se muestran por consola
        console.error('No se pudo borrar la cuenta:', error);
      });
  });

  // Cambiar de semana al pulsar los enlaces de la nav. Solo se alterna entre
  // la semana actual (0) y la siguiente (1) relativa a hoy, sin navegación continua.
  let desplazamiento_actual = 0;

  enlace_semana_actual.addEventListener('click', function (evento) {
    evento.preventDefault();
    desplazamiento_actual = 0;
    desplazarse_a_hoy = false;
    marcar_enlace_activo(enlace_semana_actual);
    cargar_semana(desplazamiento_actual, rejilla_semana, titulo_semana);
  });

  enlace_siguiente_semana.addEventListener('click', function (evento) {
    evento.preventDefault();
    desplazamiento_actual = 1;
    desplazarse_a_hoy = false;
    marcar_enlace_activo(enlace_siguiente_semana);
    cargar_semana(desplazamiento_actual, rejilla_semana, titulo_semana);
  });

  // Al cruzar el breakpoint entre móvil y escritorio se reconstruye la rejilla:
  // en móvil cada día usa su propia altura y en escritorio vuelve a la línea de
  // tiempo compartida (rango alineado para los 7 días).
  window.matchMedia('(max-width: 767px)').addEventListener('change', function () {
    cargar_semana(desplazamiento_actual, rejilla_semana, titulo_semana);
  });

  // Cargar la semana actual al entrar en la página
  cargar_semana(0, rejilla_semana, titulo_semana);
};

/**
 * Marca un enlace de la nav como activo y limpia el otro.
 *
 * @param {HTMLElement} enlace_activo  Enlace de la semana seleccionada.
 */
function marcar_enlace_activo(enlace_activo) {
  const enlaces = document.querySelectorAll('.enlace-nav');
  enlaces.forEach((enlace) => {
    const es_activo = enlace === enlace_activo;
    enlace.classList.toggle('enlace-nav--activo', es_activo);
    if (es_activo) {
      enlace.setAttribute('aria-current', 'page');
    } else {
      enlace.removeAttribute('aria-current');
    }
  });
}

/**
 * Pide al backend los datos de una semana (desplazada respecto a la actual)
 * y reconstruye la rejilla semanal con la respuesta.
 *
 * @param {number} desplazamiento  Semanas a añadir a la semana actual (0 o 1).
 * @param {HTMLElement} rejilla    Contenedor de la rejilla semanal.
 * @param {HTMLElement} titulo     Elemento con el título de la semana.
 */
function cargar_semana(desplazamiento, rejilla, titulo) {
  rejilla.innerHTML = '';

  fetch(URL_DATOS + '?desplazamiento=' + desplazamiento)
    .then((respuesta) => {
      if (!respuesta.ok) {
        throw new Error('El servidor respondió con el estado ' + respuesta.status);
      }
      return respuesta.json();
    })
    .then((datos) => {
      if (!datos.success) {
        throw new Error('La API devolvió un error: ' + JSON.stringify(datos.errores));
      }

      renderizar_semana(datos, rejilla, titulo);
    })
    .catch((error) => {
      // Los errores solo se muestran por consola
      console.error('No se pudieron cargar los datos de la semana:', error);
    });
}

/**
 * Construye la rejilla de 7 días, el título del rango y la línea de
 * tiempo compartida (mismo rango horario para toda la semana).
 *
 * @param {Object} datos         Respuesta JSON del backend.
 * @param {HTMLElement} rejilla  Contenedor de la rejilla semanal.
 * @param {HTMLElement} titulo   Elemento con el título de la semana.
 */
function renderizar_semana(datos, rejilla, titulo) {
  // Título del rango de la semana, p. ej. "14 — 20 Septiembre 2026"
  titulo.textContent = datos.semana.titulo;

  // Fecha de hoy en formato Y-m-d para marcar el día actual
  const hoy = new Date();
  const hoy_iso = formatear_fecha_iso(hoy);

  // Lunes de la semana que devuelve la API
  const partes_inicio = datos.semana.inicio.split('-').map(Number);
  const lunes = new Date(partes_inicio[0], partes_inicio[1] - 1, partes_inicio[2]);

  // Rango horario de la semana: empieza en la hora del evento más
  // temprano (redondeada hacia abajo) y termina siempre a las 24:00
  const eventos_con_hora = datos.eventos.filter((evento) => !evento.todo_el_dia);

  let rango_inicio_min = 0;
  if (eventos_con_hora.length > 0) {
    const horarios = eventos_con_hora.map((evento) => horas_a_minutos(evento.hora_inicio));
    rango_inicio_min = Math.floor(Math.min.apply(null, horarios) / 60) * 60;
  }
  const rango_min_totales = HORA_FIN_RANGO * 60 - rango_inicio_min;

  // Altura de la línea de tiempo (px), igual para los 7 días
  const altura_px = (rango_min_totales / 60) * ALTURA_POR_HORA;
  rejilla.style.setProperty('--altura-linea', altura_px + 'px');

  // En móvil cada día usa su propio rango y altura para que la tarjeta crezca
  // solo lo que ocupan sus eventos (en lugar del rango completo de la semana).
  const es_movil = window.matchMedia('(max-width: 767px)').matches;

  // Generar una tarjeta por cada día de lunes a domingo
  for (let i = 0; i < 7; i++) {
    const fecha = new Date(lunes.getFullYear(), lunes.getMonth(), lunes.getDate() + i);
    const dia_iso = formatear_fecha_iso(fecha);
    const eventos = datos.eventos.filter((evento) => evento.dia === dia_iso);
    const tareas = datos.tareas.filter((tarea) => tarea.dia === dia_iso);

    // Rango y altura propios del día (solo en móvil); en escritorio se usa el
    // rango global de la semana.
    let rango_inicio_dia = rango_inicio_min;
    let rango_min_dia = rango_min_totales;
    let altura_linea_dia = null;

    if (es_movil) {
      const eventos_con_hora_dia = eventos.filter((evento) => !evento.todo_el_dia);
      const rango_dia = calcular_rango_dia(eventos_con_hora_dia);
      if (rango_dia) {
        rango_inicio_dia = rango_dia.inicio;
        rango_min_dia = rango_dia.total;
        altura_linea_dia = (rango_dia.total / 60) * ALTURA_POR_HORA;
      }
    }

    rejilla.appendChild(
      crear_tarjeta_dia(fecha, dia_iso, hoy_iso, eventos, tareas, rango_inicio_dia, rango_min_dia, altura_linea_dia)
    );
  }

  // En la primera carga del móvil el scroll va directo al día de hoy
  if (desplazarse_a_hoy && es_movil) {
    const tarjeta_hoy = document.getElementById('hoy');
    if (tarjeta_hoy) {
      tarjeta_hoy.scrollIntoView({ block: 'start' });
    }
    desplazarse_a_hoy = false;
  }
}

/**
 * Crea la tarjeta de un día con su cabecera, los chips de día completo
 * (si los hay) y la línea de tiempo con los eventos posicionados.
 *
 * @param {Date} fecha                 Fecha del día.
 * @param {string} dia_iso             Fecha en formato Y-m-d.
 * @param {string} hoy_iso             Fecha de hoy en formato Y-m-d.
 * @param {Array} eventos              Eventos de Google Calendar del día.
 * @param {Array} tareas               Tareas de Google Tasks con vencimiento ese día.
 * @param {number} rango_inicio_min    Inicio del rango horario en minutos.
 * @param {number} rango_min_totales   Duración del rango en minutos.
 * @param {number|null} altura_linea_dia  Altura de la línea (px) para móvil, o null en escritorio.
 * @return {HTMLElement}               Tarjeta de día lista para insertar.
 */
function crear_tarjeta_dia(fecha, dia_iso, hoy_iso, eventos, tareas, rango_inicio_min, rango_min_totales, altura_linea_dia) {
  const tarjeta = document.createElement('article');
  tarjeta.className = 'tarjeta-dia';

  const es_hoy = dia_iso === hoy_iso;
  const es_fin_de_semana = fecha.getDay() === 0 || fecha.getDay() === 6;

  if (es_hoy) {
    tarjeta.classList.add('tarjeta-dia--hoy');
    tarjeta.id = 'hoy';
  }
  if (es_fin_de_semana) {
    tarjeta.classList.add('tarjeta-dia--fin-de-semana');
  }

  // --- Cabecera: día de la semana, número e insignia HOY ---
  const cabecera = document.createElement('div');
  cabecera.className = 'cabecera-dia';

  const contenedor_nombre = document.createElement('div');
  if (es_hoy) {
    contenedor_nombre.className = 'grupo-dia';
  }

  const bloque_fechas = document.createElement('div');

  const nombre_dia = document.createElement('span');
  nombre_dia.className = 'nombre-dia';
  if (es_hoy) {
    nombre_dia.classList.add('nombre-dia--hoy');
  }
  nombre_dia.textContent = NOMBRE_DIAS[(fecha.getDay() + 6) % 7];

  const numero_dia = document.createElement('span');
  numero_dia.className = 'numero-dia';
  if (es_hoy) {
    numero_dia.classList.add('numero-dia--hoy');
  }
  if (es_fin_de_semana) {
    numero_dia.classList.add('numero-dia--fin-de-semana');
  }
  numero_dia.textContent = fecha.getDate();

  bloque_fechas.appendChild(nombre_dia);
  bloque_fechas.appendChild(numero_dia);
  contenedor_nombre.appendChild(bloque_fechas);

  if (es_hoy) {
    const insignia_hoy = document.createElement('span');
    insignia_hoy.className = 'insignia-hoy';
    insignia_hoy.textContent = 'HOY';
    contenedor_nombre.appendChild(insignia_hoy);
  }

  const contador_dia = document.createElement('span');
  contador_dia.className = 'contador-dia';
  if (es_hoy) {
    contador_dia.classList.add('contador-dia--hoy');
  }
  contador_dia.textContent = calcular_texto_contador(eventos.length + tareas.length);

  cabecera.appendChild(contenedor_nombre);
  cabecera.appendChild(contador_dia);
  tarjeta.appendChild(cabecera);

  // --- Eventos de día completo (chips superiores) ---
  const eventos_todo_dia = eventos.filter((evento) => evento.todo_el_dia);
  const eventos_con_hora_dia = eventos.filter((evento) => !evento.todo_el_dia);

  if (eventos_todo_dia.length > 0) {
    tarjeta.appendChild(crear_franja_todo_dia(eventos_todo_dia));
  }

  // --- Línea de tiempo con los eventos posicionados ---
  // Se pinta siempre (aunque el día esté vacío) para que el calendario
  // conserve su tamaño mínimo de pantalla completa.
  tarjeta.appendChild(
    crear_linea_tiempo(eventos_con_hora_dia, rango_inicio_min, rango_min_totales, altura_linea_dia)
  );

  // --- Sección de tareas de Google Tasks (si el día tiene) ---
  if (tareas.length > 0) {
    tarjeta.appendChild(crear_seccion_tareas(tareas));
  }

  return tarjeta;
}

/**
 * Crea la sección de tareas de un día: un título y una fila por tarea.
 *
 * @param {Array} tareas  Tareas de Google Tasks del día.
 * @return {HTMLElement}  Contenedor de la sección de tareas.
 */
function crear_seccion_tareas(tareas) {
  const seccion = document.createElement('div');
  seccion.className = 'seccion-tareas';

  const titulo = document.createElement('span');
  titulo.className = 'titulo-seccion-tareas';
  titulo.textContent = 'Tareas';

  seccion.appendChild(titulo);

  tareas.forEach((tarea) => {
    const fila = document.createElement('div');
    fila.className = 'evento--tarea';

    const punto = document.createElement('span');
    punto.className = 'punto-evento punto-evento--contorno';

    const texto = document.createElement('div');
    texto.className = 'evento--tarea-texto';

    const titulo_tarea = document.createElement('span');
    titulo_tarea.className = 'evento-titulo';
    titulo_tarea.textContent = tarea.titulo;
    texto.appendChild(titulo_tarea);

    if (tarea.detalle) {
      const detalle = document.createElement('span');
      detalle.className = 'evento-tarea-detalle';
      detalle.textContent = tarea.detalle;
      texto.appendChild(detalle);
    }

    fila.appendChild(punto);
    fila.appendChild(texto);
    seccion.appendChild(fila);
  });

  return seccion;
}

/**
 * Crea la franja con los chips de los eventos de día completo.
 *
 * @param {Array} eventos  Eventos con todo_el_dia = true.
 * @return {HTMLElement}   Contenedor de chips.
 */
function crear_franja_todo_dia(eventos) {
  const contenedor = document.createElement('div');
  contenedor.className = 'franja-todo-dia';

  eventos.forEach((evento) => {
    const chip = document.createElement('div');
    chip.className = 'evento--todo-dia';

    const punto = document.createElement('span');
    punto.className = 'punto-evento punto-evento--secundario';

    const titulo = document.createElement('span');
    titulo.className = 'evento-titulo';
    titulo.textContent = evento.titulo;

    chip.appendChild(punto);
    chip.appendChild(titulo);
    contenedor.appendChild(chip);
  });

  return contenedor;
}

/**
 * Crea la línea de tiempo de un día con los bloques de evento.
 *
 * @param {Array} eventos             Eventos con hora del día.
 * @param {number} rango_inicio_min   Inicio del rango horario en minutos.
 * @param {number} rango_min_totales  Duración del rango en minutos.
 * @param {number|null} altura_linea_dia  Altura propia del día (px) en móvil, o null en escritorio.
 * @return {HTMLElement}              Línea de tiempo del día.
 */
function crear_linea_tiempo(eventos, rango_inicio_min, rango_min_totales, altura_linea_dia) {
  const linea = document.createElement('div');
  linea.className = 'linea-tiempo';

  // Marcar la línea como vacía para tratarla distinto en pantallas pequeñas
  if (eventos.length === 0) {
    linea.classList.add('linea-tiempo--vacia');
  }

  // En móvil la línea mide lo que ocupen los eventos de ese día
  if (altura_linea_dia !== null) {
    linea.style.setProperty('--altura-linea', altura_linea_dia + 'px');
  }

  // --- Bloques de evento proporcionales a la duración ---
  calcular_franjas(eventos, rango_inicio_min, rango_min_totales).forEach((franja) => {
    linea.appendChild(crear_bloque_evento(franja));
  });

  return linea;
}

/**
 * Crea el bloque absoluto de un evento dentro de la línea de tiempo.
 *
 * @param {Object} franja  Posición calculada (top, altura, izquierda, ancho) y evento.
 * @return {HTMLElement}   Bloque del evento.
 */
function crear_bloque_evento(franja) {
  const bloque = document.createElement('div');
  bloque.className = 'evento--franja';
  bloque.style.top = franja.top;
  bloque.style.height = franja.altura;
  bloque.style.left = franja.izquierda;
  bloque.style.width = franja.ancho;

  const hora = document.createElement('span');
  hora.className = 'evento-franja-hora';
  hora.textContent = franja.evento.hora_inicio + ' — ' + franja.evento.hora_fin;

  const titulo = document.createElement('p');
  titulo.className = 'evento-titulo';
  titulo.textContent = franja.evento.titulo;

  bloque.appendChild(hora);
  bloque.appendChild(titulo);

  return bloque;
}

/**
 * Calcula la posición (en %) de cada evento dentro del rango horario y
 * reparte el ancho lado a lado cuando varios eventos se solapan.
 *
 * @param {Array} eventos             Eventos con hora del día.
 * @param {number} rango_inicio_min   Inicio del rango en minutos.
 * @param {number} rango_min_totales  Duración del rango en minutos.
 * @return {Array}                    Franjas con evento, top, altura, izquierda, ancho.
 */
function calcular_franjas(eventos, rango_inicio_min, rango_min_totales) {
  const preparados = eventos.map((evento) => {
    // Hora de inicio y fin del evento en minutos, recortadas al rango
    let inicio_min = horas_a_minutos(evento.hora_inicio);
    let fin_min = horas_a_minutos(evento.hora_fin);
    inicio_min = Math.max(inicio_min, rango_inicio_min);
    fin_min = Math.min(fin_min, rango_inicio_min + rango_min_totales);
    fin_min = Math.max(fin_min, inicio_min);
    return { evento, inicio_min, fin_min };
  });

  // Orden: primero los que empiezan antes; a igual inicio, los más largos
  preparados.sort((a, b) => a.inicio_min - b.inicio_min || b.fin_min - a.fin_min);

  // Agrupar en clústeres de eventos solapados
  const clusters = [];
  let cluster_actual = [];
  let fin_max_cluster = 0;

  preparados.forEach((preparado) => {
    if (cluster_actual.length === 0 || preparado.inicio_min < fin_max_cluster) {
      cluster_actual.push(preparado);
      fin_max_cluster = Math.max(fin_max_cluster, preparado.fin_min);
    } else {
      clusters.push(cluster_actual);
      cluster_actual = [preparado];
      fin_max_cluster = preparado.fin_min;
    }
  });
  if (cluster_actual.length > 0) {
    clusters.push(cluster_actual);
  }

  // Repartir el ancho en carriles dentro de cada clúster
  const resultado = [];
  clusters.forEach((cluster) => {
    const fin_carriles = [];

    cluster.forEach((preparado) => {
      let carril = fin_carriles.findIndex((fin) => fin <= preparado.inicio_min);
      if (carril === -1) {
        carril = fin_carriles.length;
        fin_carriles.push(0);
      }
      fin_carriles[carril] = preparado.fin_min;
      preparado.carril = carril;
    });

    const num_carriles = fin_carriles.length;
    const ancho = 100 / num_carriles;

    cluster.forEach((preparado) => {
      const top = ((preparado.inicio_min - rango_inicio_min) / rango_min_totales) * 100;
      const alto = ((preparado.fin_min - preparado.inicio_min) / rango_min_totales) * 100;

      resultado.push({
        evento: preparado.evento,
        top: top.toFixed(3) + '%',
        altura: Math.max(alto, 1.5).toFixed(3) + '%',
        izquierda: (preparado.carril * ancho).toFixed(3) + '%',
        ancho: ancho.toFixed(3) + '%',
      });
    });
  });

  return resultado;
}

/**
 * Calcula el rango horario de un día a partir de sus eventos: desde la hora
 * del evento más temprano (redondeada hacia abajo) hasta la del más tardío
 * (redondeada hacia arriba), con un mínimo de 1 hora.
 *
 * @param {Array} eventos  Eventos con hora del día (puede estar vacío).
 * @return {Object|null}   { inicio, total } en minutos, o null sin eventos.
 */
function calcular_rango_dia(eventos) {
  if (eventos.length === 0) {
    return null;
  }

  let inicio_min = Math.min.apply(null, eventos.map((evento) => horas_a_minutos(evento.hora_inicio)));
  let fin_min = Math.max.apply(null, eventos.map((evento) => horas_a_minutos(evento.hora_fin)));

  inicio_min = Math.floor(inicio_min / 60) * 60;
  fin_min = Math.min(Math.ceil(fin_min / 60) * 60, HORA_FIN_RANGO * 60);

  if (fin_min <= inicio_min) {
    fin_min = inicio_min + 60;
  }

  return { inicio: inicio_min, total: fin_min - inicio_min };
}

/**
 * Convierte una hora "HH:MM" en minutos desde la medianoche.
 *
 * @param {string} hora  Hora en formato HH:MM.
 * @return {number}
 */
function horas_a_minutos(hora) {
  const partes = hora.split(':').map(Number);
  return partes[0] * 60 + partes[1];
}

/**
 * Devuelve el texto del contador de compromisos de un día:
 * "Sin agenda" si no hay nada, o "1 compromiso" / "N compromisos".
 *
 * @param {number} total  Número de eventos del día.
 * @return {string}
 */
function calcular_texto_contador(total) {
  if (total === 0) {
    return 'Sin agenda';
  }
  return total + ' compromiso' + (total > 1 ? 's' : '');
}

/**
 * Convierte una fecha en texto con formato Y-m-d.
 *
 * @param {Date} fecha  Fecha a formatear.
 * @return {string}
 */
function formatear_fecha_iso(fecha) {
  const anio = fecha.getFullYear();
  const mes = String(fecha.getMonth() + 1).padStart(2, '0');
  const dia = String(fecha.getDate()).padStart(2, '0');
  return anio + '-' + mes + '-' + dia;
}