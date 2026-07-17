<?php
session_start();
require_once '../conexion.php';

// Validar que exista una sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit();
}

// ==============================================================================
// 📡 MINI-API INTERNA: BÚSQUEDA EN TIEMPO REAL (AJAX)
// Este bloque solo se ejecuta cuando JavaScript pide buscar un producto
// ==============================================================================
if (isset($_GET['q'])) {
    header('Content-Type: application/json');
    $q = trim($_GET['q']);
    
    try {
        // Buscamos por código de barras exacto, código interno o coincidencia en nombre
        // IMPORTANTE: Filtramos que el stock sea mayor a 0
        $sql = "SELECT id, codigo, codigo_barras, nombre, precio, stock 
                FROM Producto 
                WHERE stock > 0 AND (codigo_barras = :exacto OR codigo = :exacto OR nombre LIKE :parcial)
                LIMIT 10";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':exacto' => $q,
            ':parcial' => '%' . $q . '%'
        ]);
        
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit(); // Detenemos la ejecución aquí para devolver solo JSON
}
// ==============================================================================
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Punto de Venta | Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .table-cart th { position: sticky; top: 0; background: #212529; color: white; z-index: 1;}
        .cart-container { max-height: 450px; overflow-y: auto; }
        .search-results { position: absolute; z-index: 1000; width: 100%; max-height: 250px; overflow-y: auto; display: none; }
        .cursor-pointer { cursor: pointer; }
        .cursor-pointer:hover { background-color: #f8f9fa; }
    </style>
</head>
<body class="vh-100 d-flex flex-column">

<nav class="navbar navbar-dark bg-dark shadow-sm py-3">
    <div class="container-fluid">
        <a class="navbar-brand mb-0 h1 fs-4"><i class="bi bi-cart-check me-2"></i>Terminal de Punto de Venta</a>
        <div class="d-flex align-items-center text-white gap-4">
            <span class="fs-5"><i class="bi bi-person-badge"></i> Cajero: <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario'); ?></span></span>
            <a href="../caja/procesar_apertura.php" class="btn btn-outline-light d-flex align-items-center gap-2">
                <i class="bi bi-box-arrow-left"></i> Volver a Caja
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid flex-grow-1 p-3 d-flex flex-column">
    <div class="row flex-grow-1 g-3">
        
        <div class="col-lg-8 d-flex flex-column">
            
            <div class="card shadow-sm border-0 mb-3 position-relative">
                <div class="card-body p-3">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc-scan text-primary fs-3"></i></span>
                        <input type="text" id="buscador" class="form-control border-start-0 fs-5" 
                               placeholder="Escanear código de barras o buscar por nombre del producto..." 
                               autocomplete="off" autofocus>
                    </div>
                    <ul id="listaResultados" class="list-group shadow search-results border-0 mt-1"></ul>
                </div>
            </div>

            <div class="card shadow-sm border-0 flex-grow-1">
                <div class="card-body p-0 cart-container">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-cart">
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th width="160" class="text-center">Cantidad</th>
                                <th width="120" class="text-end">Precio U.</th>
                                <th width="120" class="text-end">Subtotal</th>
                                <th width="80" class="text-center pe-3">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaCarrito">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-flex flex-column">
            <div class="card shadow-sm border-0 flex-grow-1">
                <div class="card-body d-flex flex-column p-0">
                    
                    <div class="bg-primary text-white text-center p-4 rounded-top">
                        <h5 class="mb-1 opacity-75">TOTAL A PAGAR</h5>
                        <h1 class="display-3 fw-bold mb-0" id="displayTotal">$0.00</h1>
                    </div>

                    <div class="p-4 flex-grow-1 d-flex flex-column">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Efectivo Recibido:</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white">$</span>
                                <input type="number" id="efectivo" class="form-control fs-3 fw-bold text-end" placeholder="0.00" min="0" step="0.01">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Cambio a Devolver:</label>
                            <input type="text" id="cambio" class="form-control form-control-lg fs-3 fw-bold text-end text-danger bg-light" value="$0.00" readonly>
                        </div>
                        
                        <div class="mt-auto">
                            <form action="guardar_venta.php" method="POST" id="formVenta">
                                <input type="hidden" name="carrito_datos" id="carritoDatos">
                                <input type="hidden" name="total_venta" id="inputTotalVenta">
                                <button type="button" id="btnCobrar" class="btn btn-success btn-lg w-100 py-3 fs-4 fw-bold disabled shadow-sm">
                                    <i class="bi bi-check-circle-fill me-2"></i> PROCESAR VENTA
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
    </div>
</div>

<script>
    // --- LÓGICA DEL PUNTO DE VENTA (JS Vainilla) ---
    let carrito = [];
    
    // Referencias al DOM
    const buscador = document.getElementById('buscador');
    const listaResultados = document.getElementById('listaResultados');
    const tablaCarrito = document.getElementById('tablaCarrito');
    const displayTotal = document.getElementById('displayTotal');
    const inputEfectivo = document.getElementById('efectivo');
    const displayCambio = document.getElementById('cambio');
    const btnCobrar = document.getElementById('btnCobrar');

    // 1. Buscador con "Debounce" (evita saturar la base de datos al teclear rápido)
    let temporizadorBuscador;
    buscador.addEventListener('input', (e) => {
        clearTimeout(temporizadorBuscador);
        const query = e.target.value.trim();
        
        if (query.length === 0) {
            listaResultados.style.display = 'none';
            return;
        }

        temporizadorBuscador = setTimeout(async () => {
            try {
                // Llamamos a este mismo archivo pero pasando el parámetro "q"
                const res = await fetch(`realizar_venta.php?q=${encodeURIComponent(query)}`);
                const productos = await res.json();
                
                listaResultados.innerHTML = '';
                
                if (productos.length > 0) {
                    // Si el escáner lee exactamente 1 código de barras, lo agrega directo al carrito
                    if (productos.length === 1 && (productos[0].codigo_barras === query || productos[0].codigo === query)) {
                        agregarProducto(productos[0]);
                        buscador.value = '';
                        listaResultados.style.display = 'none';
                        return;
                    }

                    // Si hay múltiples resultados (búsqueda por nombre), los listamos
                    productos.forEach(prod => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center cursor-pointer p-3';
                        li.innerHTML = `
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">${prod.nombre}</h6>
                                <small class="text-muted">Stock Disponible: <span class="fw-bold text-success">${prod.stock}</span> | Ref: ${prod.codigo}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill fs-6">$${parseFloat(prod.precio).toFixed(2)}</span>
                        `;
                        li.onclick = () => {
                            agregarProducto(prod);
                            buscador.value = '';
                            listaResultados.style.display = 'none';
                            buscador.focus();
                        };
                        listaResultados.appendChild(li);
                    });
                    listaResultados.style.display = 'block';
                } else {
                    listaResultados.innerHTML = `<li class="list-group-item text-muted text-center py-3">No se encontraron productos con stock.</li>`;
                    listaResultados.style.display = 'block';
                }
            } catch (error) {
                console.error('Error buscando:', error);
            }
        }, 200); // Espera 200ms antes de buscar
    });

    // Ocultar resultados si se hace clic fuera del buscador
    document.addEventListener('click', (e) => {
        if (!buscador.contains(e.target) && !listaResultados.contains(e.target)) {
            listaResultados.style.display = 'none';
        }
    });

    // 2. Controladores del Carrito
    function agregarProducto(producto) {
        const existente = carrito.find(p => p.id === producto.id);
        
        if (existente) {
            if (existente.cantidad < producto.stock) {
                existente.cantidad++;
            } else {
                alert(`¡Alerta! Solo hay ${producto.stock} unidades de ${producto.nombre} en stock.`);
            }
        } else {
            // Clonamos el producto y le asignamos cantidad inicial 1
            carrito.push({ ...producto, cantidad: 1 });
        }
        renderizarCarrito();
    }

    function cambiarCantidad(id, cambio) {
        const item = carrito.find(p => p.id === id);
        if (item) {
            const nuevaCantidad = item.cantidad + cambio;
            if (nuevaCantidad > 0 && nuevaCantidad <= item.stock) {
                item.cantidad = nuevaCantidad;
            } else if (nuevaCantidad > item.stock) {
                alert(`¡Stock máximo alcanzado! Solo hay ${item.stock} unidades disponibles.`);
            }
            renderizarCarrito();
        }
    }

    function eliminarProducto(id) {
        carrito = carrito.filter(p => p.id !== id);
        renderizarCarrito();
    }

    // 3. Pintar en pantalla y Calcular Totales
    function renderizarCarrito() {
        tablaCarrito.innerHTML = '';
        let totalGeneral = 0;

        if (carrito.length === 0) {
            tablaCarrito.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-cart-x display-1 d-block mb-3 opacity-25"></i>El carrito está vacío</td></tr>`;
            btnCobrar.classList.add('disabled');
            displayTotal.innerText = `$0.00`;
            document.getElementById('inputTotalVenta').value = 0;
            calcularCambio();
            return;
        }

        carrito.forEach(item => {
            const subtotal = parseFloat(item.precio) * item.cantidad;
            totalGeneral += subtotal;

            tablaCarrito.innerHTML += `
                <tr>
                    <td class="align-middle ps-3 fw-bold text-dark">
                        ${item.nombre}<br>
                        <small class="text-muted fw-normal">${item.codigo}</small>
                    </td>
                    <td class="align-middle">
                        <div class="input-group input-group-sm w-100 mx-auto" style="max-width: 120px;">
                            <button class="btn btn-outline-secondary px-2" onclick="cambiarCantidad(${item.id}, -1)">
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="text" class="form-control text-center fw-bold bg-white" value="${item.cantidad}" readonly>
                            <button class="btn btn-outline-secondary px-2" onclick="cambiarCantidad(${item.id}, 1)">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </td>
                    <td class="align-middle text-end text-muted">$${parseFloat(item.precio).toFixed(2)}</td>
                    <td class="align-middle text-end fw-bold text-dark fs-6">$${subtotal.toFixed(2)}</td>
                    <td class="align-middle text-center pe-3">
                        <button class="btn btn-sm btn-light text-danger border" onclick="eliminarProducto(${item.id})">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        // Actualizamos totales y guardamos el carrito en formato JSON en el input oculto
        btnCobrar.classList.remove('disabled');
        displayTotal.innerText = `$${totalGeneral.toFixed(2)}`;
        document.getElementById('inputTotalVenta').value = totalGeneral.toFixed(2);
        document.getElementById('carritoDatos').value = JSON.stringify(carrito);
        
        calcularCambio();
    }

    // 4. Lógica de Cobro y Cambio
    inputEfectivo.addEventListener('input', calcularCambio);

    function calcularCambio() {
        const total = parseFloat(document.getElementById('inputTotalVenta').value) || 0;
        const efectivo = parseFloat(inputEfectivo.value) || 0;
        
        if (efectivo >= total && total > 0) {
            const cambio = efectivo - total;
            displayCambio.value = `$${cambio.toFixed(2)}`;
            displayCambio.classList.replace('text-danger', 'text-success');
        } else {
            displayCambio.value = "$0.00";
            displayCambio.classList.replace('text-success', 'text-danger');
        }
    }

   // 5. Enviar el formulario a PHP para guardar en Base de Datos (AJAX)
    btnCobrar.addEventListener('click', async () => {
        if (carrito.length === 0) return;
        
        const total = parseFloat(document.getElementById('inputTotalVenta').value);
        const efectivo = parseFloat(inputEfectivo.value) || 0;

        if (efectivo < total) {
            alert('❌ El efectivo recibido es menor al total a pagar.');
            inputEfectivo.focus();
            return;
        }

        if (confirm('✅ ¿Confirmar el pago y procesar la venta?')) {
            try {
                // Preparamos los datos
                const formData = new FormData();
                formData.append('carrito_datos', document.getElementById('carritoDatos').value);
                formData.append('total_venta', total);

                // Enviamos mediante fetch (AJAX)
                const response = await fetch('guardar_venta.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    // Abrimos el ticket en una pestaña nueva para imprimir
                    window.open('ticket.php?id=' + result.id, '_blank');
                    
                    // Limpiamos la pantalla para la siguiente venta
                    carrito = [];
                    renderizarCarrito();
                    inputEfectivo.value = '';
                    alert('✅ Venta procesada con éxito.');
                } else {
                    alert('❌ Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Ocurrió un error al procesar la venta.');
            }
        }
    });

    // Iniciar con el carrito vacío
    renderizarCarrito();
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>